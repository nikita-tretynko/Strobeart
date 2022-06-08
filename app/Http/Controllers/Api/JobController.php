<?php

namespace App\Http\Controllers\Api;

use App\Enums\ImageJobStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\WorkedImagesStatusEnum;
use App\Enums\WorkJobStatusEnum;
use App\Exceptions\ApiValidationException;
use App\Http\Requests\Api\NewJobRequest;
use App\Http\Requests\ApiRequest;
use App\Http\Requests\DeclineImageRequest;
use App\Http\Requests\SetTimeWorkImageRequest;
use App\Http\Requests\UpdateTimeDownloadRequest;
use App\Http\Requests\UploadWorkImageRequest;
use App\Http\Requests\WorkImageRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Jobs\SendEmailJob;
use App\Jobs\TransferMoneyToEditor;
use App\Libraries\CookieInstagram\InstaLite\Exception;
use App\Models\ChatImage;
use App\Models\ChatJob;
use App\Models\DeclineJob;
use App\Models\Image;
use App\Models\ImageJob;
use App\Http\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ReviewsAboutEditor;
use App\Models\User;
use App\Repositories\ImageRepository;
use App\Repositories\JobRepository;
use App\Models\UserWorkJob;
use App\Models\WorkedImage;
use App\Services\ImageJobService;
use App\Services\ImageService;
use App\Services\PaymentService;
use App\Services\StripeService;
use App\Services\TwilioService;
use App\Traits\DateTimeTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;


class JobController extends Controller
{
    use DateTimeTrait;

    /**
     * @var ImageService
     */
    private ImageService $imageService;

    private ImageJobService $imageJobService;

    private StripeService $stripeService;

    private PaymentService $paymentService;

    private TwilioService $twilioService;
    /**
     * @var JobRepository
     */
    private JobRepository $jobRepository;

    /**
     * @var ImageRepository
     */
    private ImageRepository $imageRepository;

    /**
     * JobController constructor
     */
    public function __construct()
    {
        $this->imageService = new ImageService();
        $this->jobRepository = new JobRepository();
        $this->imageRepository = new ImageRepository();
        $this->twilioService = new TwilioService();
        $this->imageJobService = new ImageJobService();
        $this->stripeService = new StripeService();
        $this->paymentService = new PaymentService();
    }


    /**
     * Create new job
     *
     * @param NewJobRequest $request
     * @throws \App\Exceptions\ApiValidationException
     */
    public function createJob(NewJobRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $price = $this->imageJobService->limitPlanPrice($user, count($request->file('files')), $data['editing_level']);
        $payment_intent = $this->stripeService->paymentIntents($user, $price, 'Payment Job images');
        $payment_job = $this->paymentService->createPayment($payment_intent, $user, $price, PaymentTypeEnum::$JOB_IMAGE);
        try {
            DB::beginTransaction();
            $images = $request->file('files');
            $data['user_id'] = $user->id;
            $data['due_date'] = $this->getIsoDateTime($data['due_date']);
            $data['status'] = ImageJobStatusEnum::$NEW;
            $job = ImageJob::create($data);
            $payment_job->update(['job_image_id' => $job->id]);
            $path = $user->id . '/image/jobs/' . $job->id;
            $this->imageService->saveImages($images, $path, $job->id, $user);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return new ApiErrorResponse($e->getMessage());
        }

        return new ApiResponse(compact('job'));
    }

    /**
     * Get User (owned) image jobs
     *
     * @param ApiRequest $request
     *
     * @return ApiErrorResponse|ApiResponse
     */
    public function getUserOwnedJobs(ApiRequest $request)
    {
        $user = $request->user();
        $user->load('plan');
        try {
            $jobs = $user->image_jobs_active()
                ->with('images', 'finished_worked_images')
                ->get();
        } catch (\Exception $e) {
            return new ApiErrorResponse($e->getMessage(), $e->getCode());
        }

        return new ApiResponse(compact('jobs', 'user'));
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function getJobs(Request $request): ApiResponse
    {
        $user = $request->user();
        $decline_jobs = DeclineJob::where('user_id', $user->id)
            ->get('image_jobs_id')
            ->pluck('image_jobs_id')
            ->toArray();

        $work_job = UserWorkJob::whereNotIn('user_id', [$user->id])
            ->get()
            ->pluck('image_jobs_id')
            ->toArray();

        $jobs = ImageJob::whereNotIn('id', array_merge($decline_jobs, $work_job))
            ->with('images', 'user_work', 'user')
            ->whereNotIn('status', [ImageJobStatusEnum::$FINISHED])
            ->where('due_date', '>', Carbon::today()->setTimezone(config('app.timezone'))->toIso8601String())
            ->orderByDesc('created_at')
            ->get();

        return new ApiResponse($jobs);
    }

    /**
     * Gte job by ID
     *
     * @param ApiRequest $request
     * @param int $id
     *
     * @return ApiErrorResponse|ApiResponse
     */
    public function getJobById(ApiRequest $request, int $id)
    {
        $user = $request->user();
        $filtered = $request->filtered ?? false;

        $job = $this->jobRepository->getJobWithImages($id, $filtered);

        if (!$job || $job->user_id !== $user->id) {
            return new ApiErrorResponse('This work does not belong to the user', 401);
        }
        $worked_image = WorkedImage::where('image_jobs_id', $job->id)
            ->where('status', WorkedImagesStatusEnum::$FINISHED)->get();

        $job['worked_image'] = $worked_image;

        return new ApiResponse(compact('job'));
    }

    /**
     * Approval or Decline images in job
     *
     * @param ApiRequest $request
     *
     * @return ApiErrorResponse|ApiResponse
     */
    public function approvalOrDeclineImages(ApiRequest $request)
    {
        $user = $request->user();
        $job_req = $request->job;
        $message = $request->message;
        $filtered = $request->filtered ?? false;

        if (!$job_req || $job_req['user_id'] !== $user->id) {
            return new ApiErrorResponse('This work does not belong to the user', 401);
        }

        try {
            $this->imageRepository->approveOrDeclineImages(
                $job_req['images'],
                $message ?? null
            );
        } catch (\Exception $e) {
            return new ApiErrorResponse($e->getMessage(), $e->getCode());
        }

        $job = $this->jobRepository->getJobWithImages($job_req['id'], $filtered);

        return new ApiResponse(compact('job'));
    }

    /**
     * @param ApiRequest $request
     * @param int $id
     * @return ApiErrorResponse|ApiResponse
     */
    public function getWorkJobById(ApiRequest $request, int $id)
    {
        $user = $request->user();
        $job = ImageJob::with('user_work')->find($id);
        $index_image = 0;
        $user_work_job = $job->user_work;
        $work_image = null;
        $image = null;
        if ($user_work_job) {
            if ($user_work_job->status === WorkJobStatusEnum::$FINISHED) {
                return new ApiErrorResponse('This work Finished');
            }
            $worked_images = WorkedImage::where('user_work_id', $user_work_job->id)
                ->where('status', WorkJobStatusEnum::$FINISHED)
                ->get()
                ->pluck('image_id')->toArray();

            $image = Image::where('image_job_id', $id)
                ->whereNotIn('id', $worked_images)
                ->first();

            $work_image = WorkedImage::where('user_id', $user->id)
                ->where('user_work_id', $user_work_job->id)
                ->whereNotIn('status', [
                    WorkedImagesStatusEnum::$CANCELED,
                    WorkedImagesStatusEnum::$FINISHED,
                    WorkedImagesStatusEnum::$DECLINED])
                ->first();
        }

        if (!$job || !$user_work_job) {
            return new ApiErrorResponse('This work does not belong to the user', 401);
        }
        $job->load('images');

        return new ApiResponse(compact('job', 'user_work_job', 'work_image', 'index_image', 'image'));
    }


    /**
     * @param Request $request
     * @return ApiErrorResponse|ApiResponse
     */
    public function acceptJob(Request $request)
    {
        try {
            $user = $request->user();
            $job_id = $request->get('job_id');
            $user_work_job = UserWorkJob::where('image_jobs_id', $job_id)->first();
            if (!$user_work_job) {
                $user_work_job = UserWorkJob::create([
                    'user_id' => $user->id,
                    'image_jobs_id' => $job_id,
                    'status' => WorkJobStatusEnum::$STARTED]);
                $user_work_job->image_job->status = ImageJobStatusEnum::$ACCEPTED;
                $user_work_job->image_job->save();
                $this->twilioService->createChannelChat($job_id);
            } elseif ($user->id !== $user_work_job->user_id) {
                return new ApiErrorResponse('Sorry, this job is already taken');
            } elseif ($user_work_job->status === WorkJobStatusEnum::$FINISHED) {
                return new ApiErrorResponse('Job finished!');
            }

        } catch (Exception $exception) {
            return new ApiErrorResponse($exception);
        }

        return new ApiResponse($user_work_job);
    }


    /**
     * @param WorkImageRequest $request
     * @return ApiErrorResponse|ApiResponse
     */
    public function createWorkImage(WorkImageRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        try {
            $user_work_job = UserWorkJob::where('id', $data['work_job'])
                ->where('user_id', $user->id)->first();
            $user_work_job->timer = null;
            $user_work_job->save();
            $work_image = WorkedImage::where('user_id', $user->id)
                ->where('user_work_id', $data['work_job'])
                ->where('image_id', $data['image_id'])->first();
            if (($work_image && $work_image->status === 'DECLINED') || !$work_image) {
                $new_work_image = WorkedImage::create([
                    'user_id' => $user->id,
                    'user_work_id' => $data['work_job'],
                    'number_file' => $data['number_file'],
                    'image_id' => $data['image_id'],
                    'image_jobs_id' => $data['image_jobs_id'],
                    'status' => WorkedImagesStatusEnum::$START_TIME,
                    'timer' => 0,
                    'sum_timers' => $work_image->timer ?? 0,
                    'start_timer' => $this->getIsoDateTime(),
                ]);
            }
        } catch (\Exception $exception) {
            return new ApiErrorResponse($exception);
        }

        return new ApiResponse($new_work_image);
    }

    /**
     * @param Request $request
     * @param int $job_id
     * @return ApiErrorResponse|ApiResponse
     */
    public function declineJob(Request $request, int $job_id)
    {
        try {
            $user = $request->user();
            $job = ImageJob::find($job_id);
            if ($job) {
                DeclineJob::create(['user_id' => $user->id, 'image_jobs_id' => $job_id]);
            }
        } catch (Exception $exception) {
            return new ApiErrorResponse($exception);
        }

        return new ApiResponse();
    }

    private function isFinishedTimerWorkImg(WorkedImage $work_image)
    {
        $start_date = Carbon::parse(new Carbon($work_image->start_timer));
        $date = new Carbon();
        $count_second = $date->diffInSeconds($start_date);

        if ($work_image->add_time && $count_second >= 1800) {
            $work_image->status = WorkedImagesStatusEnum::$END_TIME;
        } elseif (!$work_image->add_time && $count_second >= 900) {
            $work_image->status = WorkedImagesStatusEnum::$END_TIME;
        }
        $work_image->save();

        return $count_second;
    }

    /**
     * @param SetTimeWorkImageRequest $request
     * @return ApiErrorResponse|ApiResponse
     */
    public function updateTimeWorkImage(SetTimeWorkImageRequest $request)
    {
        try {
            $data = $request->validated();
            $user = $request->user();

            $work_image = WorkedImage::where('id', $data['work_image_id'])
                ->where('user_id', $user->id)
                ->whereNotIn('status', [WorkedImagesStatusEnum::$END_TIME])
                ->first();
            if ($work_image && $work_image->status != WorkedImagesStatusEnum::$FINISHED) {
                if ($data['status'] ?? null) {
                    $work_image->status = $data['status'];
                }
                if ($data['add_time'] ?? null) {
                    $work_image->add_time = $data['add_time'];
                }
                $work_image->timer += $this->isFinishedTimerWorkImg($work_image);

                $work_image->save();
            }
        } catch (Exception $exception) {
            return new ApiErrorResponse($exception);
        }

        return new ApiResponse($work_image);
    }

    /**
     * @param UploadWorkImageRequest $request
     * @return ApiResponse
     */
    public function uploadWorkImage(UploadWorkImageRequest $request): ApiResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $work_image = WorkedImage::where('id', $data['work_image_id'])
            ->where('user_id', $user->id)
            ->first();
        $start_date = Carbon::parse(new Carbon($work_image->start_timer));
        $date = new Carbon();
        $count_second = $date->diffInSeconds($start_date);
        if ($request->hasfile('file')) {
            $file = $request->file('file');
            $images_url_array = $this->imageService->saveImageWork($file, ['path' => 'works/work_image_id' . $work_image->id]);
            $work_image->image_url = $images_url_array['image_url'];
            $work_image->status = WorkedImagesStatusEnum::$UPLOADED_FILE;
            $work_image->timer = $count_second;
            $work_image->sum_timers += $count_second;
            $work_image->save();
        }

        return new ApiResponse($work_image);
    }

    /**
     * @param Request $request
     * @return ApiErrorResponse|ApiResponse
     */
    public function finishWorkImage(Request $request)
    {
        $user = $request->user();
        $finish_job = false;
        $work_image_id = $request->only('work_image_id');
        try {
            $work_image = WorkedImage::where('id', $work_image_id)
                ->where('user_id', $user->id)
                ->first();
            $images = Image::where('image_job_id', $work_image->image_jobs_id)->get();
            $images_count = count($images);
            $work_image->status = WorkedImagesStatusEnum::$FINISHED;
            $work_image->save();
            $worked_images_count = WorkedImage::where('image_jobs_id', $work_image->image_jobs_id)
                ->where('status', WorkJobStatusEnum::$FINISHED)->count();
            if ($images_count == $worked_images_count) {
                $user_work_jobs = UserWorkJob::find($work_image->user_work_id);
                $user_work_jobs->status = WorkJobStatusEnum::$FINISHED;
                $user_work_jobs->save();
                $finish_job = true;
            }

            foreach ($images as $image) {
                if ($image->decline === 1) {
                    $image->decline = 2;
                    $image->save();
                }
            }
        } catch (Exception $exception) {
            return new ApiErrorResponse($exception);
        }

        return new ApiResponse(compact('work_image', 'finish_job'));
    }

    /**
     * @param UpdateTimeDownloadRequest $request
     * @return ApiResponse
     */
    public function updateTimeDownload(UpdateTimeDownloadRequest $request): ApiResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $user_work_job = UserWorkJob::where('id', $data['work_job_id'])
            ->where('user_id', $user->id)->first();
        if ($user_work_job) {
            $user_work_job->timer = $data['timer'];
            $user_work_job->save();
        }

        return new ApiResponse();
    }

    public function declineImage(DeclineImageRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $decline_img_id = $data['checked_images'];

        $image_job = ImageJob::where('id', $data['job_id'])
            ->where('user_id', $user->id)->first();

        if (!$image_job) {
            return new ApiErrorResponse('This work does not belong to the user', 401);
        }
        $image_job->user_work->status = WorkJobStatusEnum::$REOPENED;
        $image_job->user_work->save();

        WorkedImage::where('image_jobs_id', $image_job->id)
            ->whereIn('image_id', $decline_img_id)
            ->update(['status' => WorkedImagesStatusEnum::$DECLINED]);
        Image::whereIn('id', $decline_img_id)
            ->update(['decline' => 1, 'message' => $data['message']]);

        try {
            $text = 'Some images were returned back. Please check my comments: ' . $data['message'];
            $this->twilioService->sendMessage($user, "channel-" . $image_job->id, $text);
            $wi = WorkedImage::where('image_jobs_id', $image_job->id)
                ->whereIn('image_id', $decline_img_id)->first();
            $editor = User::find($wi->user_id);
            $link = getenv("LINK_APP").'/job/'.$image_job->id.'/chat';
            SendEmailJob::dispatch($editor->email, $text, $user->name, $link);
        } catch (\Exception $exception) {
            Log::info('ERROR '.$exception);
        }

        return new ApiResponse();
    }

    /**
     * @throws \App\Exceptions\ApiValidationException
     */
    public function approvalImage(Request $request)
    {
        $data = $request->only(['job_id', 'checked_images']);
        $user = $request->user();
        $checked_images_id = $data['checked_images'];

        $image_job = ImageJob::where('id', $data['job_id'])
            ->where('user_id', $user->id)->first();

        if (!$image_job) {
            return new ApiErrorResponse('This work does not belong to the user', 401);
        }
        $images = Image::find($checked_images_id);
        foreach ($images as $image) {
            $image->approval = 1;
            $image->save();
        }
        $images = Image::where('image_job_id', $image_job->id);
        $images_count = $images->count();
        $images_approval_count = $images->where('approval', 1)->count();

        if ($images_approval_count === $images_count) {
            $image_job->status = ImageJobStatusEnum::$FINISHED;
            $image_job->save();
            try {
                $payment_job = Payment::where('job_image_id', $image_job->id)->first();
                $user_work_job_id = UserWorkJob::where('image_jobs_id', $image_job->id)->first();
                $payment = $this->stripeService->confirmPaymentIntents($payment_job->payment_intent_id, $user);
                $payment_job->status = $payment->status;
                $payment_job->client_id = $user_work_job_id->user_id;
                $payment_job->save();
                TransferMoneyToEditor::dispatch($image_job->id, $payment_job, $user_work_job_id->user_id);
            } catch (\Exception $exception) {
                Log::info('ERROR approvalImage payment confirm finished ' . $exception);
            }

            $this->twilioService->deleteChat($image_job->id);
        }

        if ($request->get('review')) {
            ReviewsAboutEditor::create([
                'user_id' => $user->id,
                'user_editor_id' => $image_job->user_work->user_id,
                'review' => $request->get('review'),
            ]);
        }

        return new ApiResponse($image_job);
    }

    public function pastJob(Request $request): ApiResponse
    {
        $user = $request->user();
        if ($user->is_business()) {
            $past_jobs = ImageJob::where('user_id', $user->id)
                ->where('status', ImageJobStatusEnum::$FINISHED)
                ->with('images', 'images_decline','user_work.user','review')
                ->withSum('finished_worked_images', 'sum_timers')
                ->get();
        } else {
            $user_worker_jobs = UserWorkJob::where('user_id', $user->id)->get()->pluck('image_jobs_id')->toArray();
            $past_jobs = ImageJob::whereIn('id', $user_worker_jobs)
                ->where('status', ImageJobStatusEnum::$FINISHED)
                ->with('images', 'images_decline')
                ->withSum('finished_worked_images', 'sum_timers')
                ->get();
        }

        return new ApiResponse(compact('past_jobs', 'user'));
    }
}

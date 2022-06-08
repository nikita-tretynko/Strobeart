<?php

namespace App\Http\Controllers;

use App\Enums\MoneyTransferStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\TypeBalanceEnum;
use App\Enums\UserPlansStatusEnum;
use App\Exceptions\ApiValidationException;
use App\Http\Requests\AddStyleGuideRequest;
use App\Http\Requests\MembershipPlanRequest;
use App\Http\Requests\StripeCreatePaymentMethodRequest;
use App\Http\Requests\UpdateUserAvatarRequest;
use App\Http\Requests\UpdateUserBioRequest;
use App\Http\Requests\UpdateUserBusinessRequest;
use App\Http\Requests\UpdateUserLocationRequest;
use App\Http\Requests\UpdateUserNameRequest;
use App\Http\Requests\UploadFileRequest;
use App\Http\Requests\WithdrawMoneyRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiResponse;
use App\Models\Avatar;
use App\Models\ImageJob;
use App\Models\MoneyTransfer;
use App\Models\StripeConnect;
use App\Models\StyleGuide;
use App\Models\UserFile;
use App\Models\UserPlan;
use App\Services\ImageService;
use App\Services\MoneyTransferService;
use App\Services\PaymentService;
use App\Services\StripeService;
use App\Services\UserPlanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ProfileController extends Controller
{
    private ImageService $imageService;
    private StripeService $stripeService;
    private PaymentService $paymentService;
    private UserPlanService $userPlanService;
    private MoneyTransferService $moneyTransferService;

    /**
     *
     */
    public function __construct()
    {
        $this->imageService = new ImageService();
        $this->stripeService = new StripeService();
        $this->paymentService = new PaymentService();
        $this->userPlanService = new UserPlanService();
        $this->moneyTransferService = new MoneyTransferService();
    }

    /**
     * @param UpdateUserNameRequest $request
     * @return ApiResponse
     */
    public function updateUserName(UpdateUserNameRequest $request): ApiResponse
    {
        $user = $request->user();
        $user->load('avatar');
        $user->update(['first_name' => $request->get('first_name'), 'last_name' => $request->get('last_name')]);

        return new ApiResponse($user);
    }

    /**
     * @param UpdateUserBusinessRequest $request
     * @return ApiResponse
     */
    public function updateUserBusiness(UpdateUserBusinessRequest $request): ApiResponse
    {
        $user = $request->user();
        $user->load('avatar');
        $user->update(['business_name' => $request->get('business')]);

        return new ApiResponse($user);
    }

    /**
     * @param UpdateUserLocationRequest $request
     * @return ApiResponse
     */
    public function updateUserLocation(UpdateUserLocationRequest $request): ApiResponse
    {
        $user = $request->user();
        $user->load('avatar');
        $user->update(['location' => $request->get('location')]);

        return new ApiResponse($user);
    }

    /**
     * @param UpdateUserBioRequest $request
     * @return ApiResponse
     */
    public function updateUserBio(UpdateUserBioRequest $request): ApiResponse
    {
        $user = $request->user();
        $user->load('avatar');
        $user->update(['bio' => $request->get('bio')]);

        return new ApiResponse($user);
    }

    /**
     * @param UpdateUserAvatarRequest $request
     * @return ApiResponse
     */
    public function updateAvatar(UpdateUserAvatarRequest $request): ApiResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            try {
                $image = explode('storage/', $user->avatar->url)[1];
                Storage::disk('public')->delete($image);
            } catch (\Exception $exception) {
            }
            $avatar_model = $user->avatar;
        } else {
            $avatar_model = new Avatar();
            $avatar_model->user_id = $user->id;
        }
        if ($request->hasfile('avatar')) {
            $file = $request->file('avatar');
            $images_url_array = $this->imageService->saveImageWork($file, ['path' => 'avatar']);
            $avatar_model->url = $images_url_array['image_url'];
            $avatar_model->save();
        }
        $user->load('avatar');
        return new ApiResponse($user);
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function setting(Request $request): ApiResponse
    {
        $user = $request->user();
        $user->load('avatar', 'connect_id', 'login_instagram', 'shopify_connect', 'user_card', 'plan', 'payment_history.user_editor',
            'money_transfers_done.payment.user_business', 'money_transfers_done.payment_images');
        $account = StripeConnect::where('user_id', $user->id)->first();
        $short_info = $this->stripeService->getCardsStripeAccount($account);
        $user['short_info'] = $short_info ?? null;
        $user['balance'] = MoneyTransfer::balance($user->id);

        return new ApiResponse(compact('user'));
    }

    /**
     * @param AddStyleGuideRequest $request
     * @return ApiResponse
     */
    public function addStyleGuide(AddStyleGuideRequest $request): ApiResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $style_guide = StyleGuide::create($data);

        return new ApiResponse($style_guide);
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function getStyleGuide(Request $request): ApiResponse
    {
        $user = $request->user();
        $style_guide = StyleGuide::where('user_id', $user->id)->get();

        return new ApiResponse($style_guide);

    }

    public function getFileJob($job_id): ApiResponse
    {
        $image_jobs = ImageJob::with('file_logo','file_watermark','file_video_instructions','file_typography', 'file_color_palette')->find($job_id);
        $job_files[] = $image_jobs->file_logo;
        $job_files[] = $image_jobs->file_watermark;
        $job_files[] = $image_jobs->file_color_palette;
        $job_files[] = $image_jobs->file_video_instructions;
        $job_files[] = $image_jobs->file_typography;

        return new ApiResponse($job_files);
    }

    public function getStyleJob(Request $request): ApiResponse
    {
        $user = $request->user();
        $style_guides = StyleGuide::where('user_id', $user->id)->get();
        $user_files = UserFile::where('user_id', $user->id)->get();

        return new ApiResponse(compact('style_guides', 'user_files'));
    }

    /**
     * @throws \App\Exceptions\ApiValidationException
     */
    public function addPaymentMethod(StripeCreatePaymentMethodRequest $request): ApiResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $user_card = $this->stripeService->createPaymentMethod($data, $user);

        return new ApiResponse($user_card);
    }


    /**
     * @throws \App\Exceptions\ApiValidationException
     */
    public function addMembershipPlan(MembershipPlanRequest $request)
    {
        $user = $request->user();
        $plan = $request['plan'];
        if (!$user->user_card) {
            return new ApiErrorResponse('Non card');
        }
        $data['plan'] = $plan;
        $data['timezone'] = $request->get('timezone');
        $amount = UserPlan::gepPricePlan()[$plan];

        $old_plan = UserPlan::where('user_id', $user->id)
            ->where('status', UserPlansStatusEnum::$ACTIVE)->first();

        if ($old_plan) {
            $user_plan = $this->updatePlanUser($old_plan, $user, $plan, $amount, $data);
            return new ApiResponse($user_plan);
        }

        $payment_intent = $this->stripeService->paymentIntents($user, $amount, 'Payment MEMBERSHIP');
        $payment = $this->stripeService->confirmPaymentIntents($payment_intent->id, $user);

        $this->paymentService->createPayment($payment, $user, $amount, PaymentTypeEnum::$MEMBERSHIP);
        $user_plan = $this->userPlanService->createUserPlan($user, $plan, $data);

        return new ApiResponse($user_plan);
    }

    public function updatePlanUser($old_plan, $user, $plan, $amount)
    {
        try {
            $days_in_month = Carbon::now()->daysInMonth;
            $price = $amount;
            if ($old_plan->price < $amount) {
                $price_in_day = (int)($amount / $days_in_month);
                $finished_date = Carbon::parse(new Carbon($old_plan->finished_date));
                $date = new Carbon();
                $days = $date->diffInDays($finished_date);
                $price = $price_in_day * $days;

                $payment_intent = $this->stripeService->paymentIntents($user, $price, 'Payment MEMBERSHIP');
                $payment = $this->stripeService->confirmPaymentIntents($payment_intent->id, $user);
                $this->paymentService->createPayment($payment, $user, $price, PaymentTypeEnum::$MEMBERSHIP);
            }
            $user_plan = UserPlan::create([
                'user_id' => $user->id,
                'plan' => $plan,
                'continuation_plan' => 1,
                'price' => $price,
                'status' => UserPlansStatusEnum::$ACTIVE,
                'finished_date' => $old_plan->finished_date,
                'created' => $old_plan->created
            ]);
            $old_plan->status = UserPlansStatusEnum::$CANCELED;
            $old_plan->save();
        } catch (\Exception $exception) {
            Log::info($exception);
            return null;
        }
        return $user_plan;
    }

    public function withdrawMoney(WithdrawMoneyRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        $user->load('connect_id');
        if (!$this->moneyTransferService->isMoneyForUser($user, $data['amount'])) {
            return new ApiErrorResponse('Not enough money');
        }
        $connect_id = $user->connect_id['connect_id'] ?? null;
        $user_account = $this->stripeService->connectedAccount($connect_id);
        $platform_payments = $user_account['capabilities']['platform_payments'] ?? null;
        if ($user_account['verification']['disabled_reason'] || ($platform_payments != 'active')) {
            return new ApiErrorResponse('Your Stripe Connect account hasn’t been verified yet. Please open Stripe from “Manage” button in Earnings section to finish verification.');
        } else {
            try {
                $this->stripeService->payouts($data['amount'], $user_account['id'], $data['card_id']);
                $this->moneyTransferService->create($user->id, $data['amount'],
                    MoneyTransferStatusEnum::$PAYED, TypeBalanceEnum::$WITHDRAWN, $data['amount'],
                    null, 'Withdraw to bank account');
            } catch (\Exception $exception) {
                Log::info('Problem with transfer (MakeTransfersForUser):');
                Log::info($exception);
                return new ApiErrorResponse($exception->getMessage());
            }
        }
        $user->load('payment_history.user_editor', 'money_transfers_done.payment.user_business', 'money_transfers_done.payment_images');
        $user['balance'] = MoneyTransfer::balance($user->id);

        return new ApiResponse($user);
    }

    public function cancelMembershipPlan(Request $request)
    {
        $user = $request->user();
        $user->plan->continuation_plan = false;
        $user->plan->save();
    }

    public function uploadUserFile(UploadFileRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        try {
            if ($request->hasfile('file')) {
                $file = $request->file('file');
                $name_file = time() . $file->getClientOriginalName();
                $path = $file->storeAs(
                    'files/user_id' . $user->id, $name_file, ['disk' => 'public']
                );
                $image_url = URL::to('/') . '/storage/' . 'files/user_id' . $user->id . '/' . $name_file;
                $type_file = $file->getClientOriginalExtension();
                UserFile::create(['user_id' => $user->id, 'file_name' => $data['file_name'], 'file_patch' => $path, 'file_url' => $image_url, 'type' => $type_file]);
            }
        } catch (\Exception $exception) {
            return new ApiErrorResponse($exception);
        }

        return new ApiResponse();
    }
}

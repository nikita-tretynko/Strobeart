<?php

namespace App\Http\Controllers;

use App\Enums\UserSocialiteTypeEnum;
use App\Enums\WorkedImagesStatusEnum;
use App\Http\Requests\LoginInstagramRequest;
use App\Http\Requests\LoginShopifyRequest;
use App\Http\Requests\PublishingFilesRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiResponse;
use App\Jobs\PublishingImagesInstagramJob;
use App\Jobs\PublishingImagesShopifyJob;
use App\Libraries\CookieInstagram\InstagramService;
use App\Libraries\CookieInstagram\InstagramUserRegistrationService;
use App\Libraries\CookieInstagram\InstaLite\Exception;
use App\Libraries\Shopify\ShopifyService;
use App\Models\ImageJob;
use App\Models\InstagramConnects;
use App\Models\PublishingFiles;
use App\Models\SocialiteUser;
use App\Models\WorkedImage;
use App\Services\SocialiteUserService;
use App\Traits\DateTimeTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublishingFilesController extends Controller
{
    use DateTimeTrait;


    /**
     * @param LoginShopifyRequest $request
     * @return ApiErrorResponse|ApiResponse
     */
    public function loginShopify(LoginShopifyRequest $request)
    {
        $data = $request->validated();
        if (!(new ShopifyService())->isLogin($data)) {
            return new ApiErrorResponse('Error login Shopify');
        }
        return new ApiResponse();
    }

    public function loginInstagram(LoginInstagramRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        if (!(new InstagramUserRegistrationService())->checkUserData($data['login'], $data['password'])) {
            return new ApiErrorResponse('Error login Instagram');
        }
        $socialiteUserService = new SocialiteUserService();
        $socialite_user = $socialiteUserService->create($user, UserSocialiteTypeEnum::$INSTAGRAM, $data['login'], $data['password']);

        return new ApiResponse($socialite_user);
    }

    /**
     * @throws \Exception
     */
    public function postImages(PublishingFilesRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $image_job = ImageJob::where('id', $data['image_jobs_id'])->where('user_id', $user->id)->first();
        if (!$image_job) {
            return new ApiErrorResponse('Error image job');
        }

        $data['user_id'] = $user->id;
        $data['date_publication'] = $this->setTimezoneDateTime($data['date_publication'], $data['timezone']);


        $time = Carbon::parse($this->getIsoDateTime(new Carbon()));
        $time_from_publish = Carbon::parse($data['date_publication']);
        $start_published = $time->diffInSeconds($time_from_publish);

        switch ($data['platform']) {
            case 'instagram':
                $connect_instagram = InstagramConnects::where('user_id', $user->id)->first();
                $photos = WorkedImage::where('image_jobs_id', $image_job)
                    ->where('status', WorkedImagesStatusEnum::$FINISHED)->get();
                $data['count_posts'] = count(array_chunk($photos->toArray(), 10));
                $date_publication = Carbon::parse($data['date_publication'])->format('d');
                $count_posts = PublishingFiles::where('user_id',$user->id)->whereDay('date_publication', $date_publication)->sum('count_posts');
                if (($count_posts + $data['count_posts']) > 25){
                    return new ApiErrorResponse("Can't create more than 25 image-posts per day. Please schedule on another day.");
                }
                $publishing = PublishingFiles::create($data);
                PublishingImagesInstagramJob::dispatch($photos,$publishing, $connect_instagram)->delay(Carbon::now()->addSeconds($start_published));
                break;
            case 'shopify':
                $shopify_login['shop'] = $data['shop'];
                $publishing = PublishingFiles::create($data);
                PublishingImagesShopifyJob::dispatch($user, $publishing, $shopify_login, $data['product_id'])->delay(Carbon::now()->addSeconds($start_published));
                break;
        }

        return new ApiResponse($data['date_publication']);
    }


}

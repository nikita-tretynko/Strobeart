<?php

namespace App\Http\Controllers;

use App\Enums\ImageJobStatusEnum;
use App\Enums\UserSocialiteTypeEnum;
use App\Exceptions\ApiValidationException;
use App\Http\Requests\GetTokenFacebookRequest;
use App\Http\Requests\GetTokenShopify;
use App\Http\Requests\LoginShopify;
use App\Http\Requests\LoginShopifyRequest;
use App\Http\Requests\SendMessageRequest;
use App\Http\Requests\UserLoginSocialRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiResponse;
use App\Jobs\SendEmailJob;
use App\Models\ImageJob;
use App\Models\InstagramConnects;
use App\Models\KeysUser;
use App\Models\MoneyTransfer;
use App\Models\ShopifyConnects;
use App\Models\SocialiteUser;
use App\Models\StripeConnect;
use App\Models\UserWorkJob;
use App\Services\InstagramConnectService;
use App\Services\InstagramService;
use App\Services\ShopifyService;
use App\Services\SocialiteUserService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * @var StripeService
     */
    private StripeService $stripeService;
    /**
     * @var ShopifyService
     */
    private ShopifyService $shopifyService;
    /**
     * @var InstagramService
     */
    private InstagramService $instagramService;

    /**
     *
     */
    public function __construct()
    {
        $this->stripeService = new StripeService();
        $this->shopifyService = new ShopifyService();
        $this->instagramService = new InstagramService();
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function updateTypeUser(Request $request): ApiResponse
    {
        $type_user = $request->get('type_user');
        $user = $request->user();
        $user->update(['type_user' => $type_user]);
        $user->load('avatar');

        return new ApiResponse($user);
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function getUser(Request $request): ApiResponse
    {
        $user = $request->user();
        $user->load('avatar');

        return new ApiResponse($user);
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function getUserProfile(Request $request): ApiResponse
    {
        $user = $request->user();
        $user->load('avatar', 'style_guide');
        if ($user->is_business()) {
            $past_job = ImageJob::where('user_id', $user->id)
                ->where('status', ImageJobStatusEnum::$FINISHED)
                ->with('images')
                ->get();
        } else {
            $user_worker_jobs = UserWorkJob::where('user_id', $user->id)->get()->pluck('image_jobs_id')->toArray();
            $past_job = ImageJob::whereIn('id', $user_worker_jobs)
                ->where('status', ImageJobStatusEnum::$FINISHED)
                ->with('images','images_decline')
                ->withSum('finished_worked_images','sum_timers')
                ->get();
        }


        return new ApiResponse(compact('user', 'past_job'));
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function connectStripe(Request $request): ApiResponse
    {
        $user = $request->user();
        $this->stripeService->initStripe();
        $account_id = $user->connect_id['connect_id'] ?? null;
        if (!$account_id) {
            $account = $this->stripeService->createExpressAccount($user->email);
            StripeConnect::create(['user_id' => $user->id, 'connect_id' => $account['id']]);
            $account_id = $account['id'];
        }
        $link = $this->stripeService->createAccountLink($account_id);

        return new ApiResponse($link);
    }

    /**
     * @param Request $request
     * @return ApiErrorResponse|ApiResponse
     */
    public function createLoginLinkAccount(Request $request)
    {
        try {
            $user = $request->user();
            $account_id = $user->connect_id['connect_id'] ?? null;
            $account = $this->stripeService->connectedAccount($account_id);
            if ($account['verification']['disabled_reason']) {
                $link = $this->stripeService->createAccountLink($account_id);
            } else {
                $link = $this->stripeService->createLoginLinkAccount($account);
            }
        } catch (\Exception $e) {
            Log::info('loginLinkAccount: ' . $e);
            return new ApiErrorResponse('loginLinkAccount error' . $e);
        }

        return new ApiResponse($link);
    }

    /**
     * @param UserLoginSocialRequest $request
     * @return ApiResponse
     */
    public function getUserLoginSocial(UserLoginSocialRequest $request): ApiResponse
    {
        $user = $request->user();
        $social_user = SocialiteUser::where('user_id', $user->id)->where('type', $request['type'])->first();

        return new ApiResponse($social_user);
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function unlinkInstagram(Request $request): ApiResponse
    {
        $user = $request->user();
        InstagramConnects::where('user_id', $user->id)->delete();

        return new ApiResponse();
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function unlinkShopify(Request $request): ApiResponse
    {
        $user = $request->user();
        ShopifyConnects::where('user_id', $user->id)->delete();

        return new ApiResponse();
    }

    /**
     * @param SendMessageRequest $request
     * @return ApiResponse
     */
    public function sendEmail(SendMessageRequest $request): ApiResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $data['link'] = $request->get('link') ? getenv("LINK_APP") . $data['link'] : '';
        SendEmailJob::dispatch($data['email'], $data['text'], $user->name, $data['link']);

        return new ApiResponse();
    }

    /**
     * @param LoginShopify $request
     * @return ApiResponse
     */
    public function loginShopify(LoginShopify $request): ApiResponse
    {
        $data = $request->validated();
        $shop = $data['shop'];
        $link = $this->shopifyService->linkLoginShopify($shop);

        return new ApiResponse($link);
    }


    /**
     * @param GetTokenShopify $request
     * @return ApiResponse
     * @throws \App\Exceptions\ApiValidationException
     */
    public function accessTokenShopify(GetTokenShopify $request): ApiResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $token = $this->shopifyService->getTokenShopify($data['code'], $data['shop']);
        ShopifyConnects::create(['user_id' => $user->id, 'shop' => $data['shop'], 'token' => $token]);

        return new ApiResponse();
    }


    /**
     * @throws \App\Exceptions\ApiValidationException
     */
    public function connectInstagram(GetTokenFacebookRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        try {
            $connect_instagram = $this->instagramService->connectInstagram($user, $data['code']);
        } catch (ApiValidationException $e) {
            return new ApiErrorResponse($e);
        }

        return new ApiResponse($connect_instagram);
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function isInstagramConnect(Request $request): ApiResponse
    {
        $user = $request->user();
        $instagram_connect = InstagramConnects::where('user_id', $user->id)->first();
        if ($instagram_connect) {
            return new ApiResponse(true);
        }

        return new ApiResponse(false);
    }


    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function isShopifyConnect(Request $request): ApiResponse
    {
        $user = $request->user();
        $shopify_connect = ShopifyConnects::where('user_id', $user->id)->first();
        if ($shopify_connect) {
            return new ApiResponse(true);
        }

        return new ApiResponse(false);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\NewPasswordRequest;
use App\Http\Requests\RegisterUserRequest;

use App\Http\Requests\ResetPasswordInfoRequest;
use App\Http\Requests\VerifySmsRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiResponse;
use App\Jobs\ResetPasswordEmailJob;
use App\Models\Avatar;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\AuthService;
use App\Services\InstagramService;
use App\Services\TwilioService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use PHPUnit\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    private AuthService $auth_service;
    /**
     * @var TwilioService
     */
    private TwilioService $twilio_service;


    /**
     * @param AuthService $auth_service
     * @param TwilioService $twilio_service
     */
    public function __construct(AuthService $auth_service, TwilioService $twilio_service)
    {
        $this->twilio_service = $twilio_service;
        $this->auth_service = $auth_service;
    }

    public function login(LoginUserRequest $request)
    {
        $data = $request->only('email', 'password');

        if (auth()->attempt($data)) {
            $user = auth()->user();
            $token = JWTAuth::fromUser($user);
            $user->load('avatar');

            return new ApiResponse(compact('user', 'token'));
        }
        return new ApiErrorResponse('Incorrect email or password');
    }

    /**
     * @param VerifySmsRequest $request
     */
    public function verifySms(VerifySmsRequest $request)
    {
        try {
            $data = $request->validated();
            $this->auth_service->verifySms($data);

            return new ApiResponse();
        } catch (\Exception $exception) {
            Log::info($exception);
            return new ApiErrorResponse($exception->getMessage());
        }

    }

    /**
     * @param VerifySmsRequest $request
     */
    public function sendSms(VerifySmsRequest $request): void
    {
        $phone = $request->get('phone');
        $message = $request->get('code');
        $this->twilio_service->sendSMS($phone, $message);
    }

    /**
     * @param RegisterUserRequest $request
     * @return ApiResponse
     */
    public function register(RegisterUserRequest $request): ApiResponse
    {
        $data = $request->validated();
        $user = $this->auth_service->registerUser($data);

        return new ApiResponse($user);
    }
    public function redirectToFacebook(): ApiResponse
    {
        $url = Socialite::driver('facebook')
            ->stateless()
            ->scopes([
                'public_profile',
                'instagram_basic',
                'pages_show_list',
//                'instagram_manage_insights',
//                'instagram_manage_comments',
                'manage_pages',
                'ads_management',
                'business_management',
                'instagram_content_publish',
                'pages_read_engagement'
            ])
            ->redirect()->getTargetUrl();

        return new ApiResponse($url);
    }

    public function redirectToProvider($provider): ApiResponse
    {
        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

        return new ApiResponse($url);
    }

    public function handleProviderCallback($provider)
    {
        try {
            $user_google = Socialite::driver($provider)->stateless()->user();
            $find_user = User::where('provider_id', $user_google->id)->first();

            if ($find_user) {
                $token = JWTAuth::fromUser($find_user);
                $user = $find_user->load('avatar');
                $user = $user->toJson();
                return view('callback', [
                    'user' => $user,
                    'token' => $token,
                    'registration' => false
                ]);
            } else {
                $user = User::where('email', $user_google->email)->first();
                if ($user) {
                    $user->update(['provider_id' => $user_google->id, 'provider' => $provider]);
                    $token = JWTAuth::fromUser($user);
                    $user = $user->load('avatar');
                    $registration = false;
                } else {
                    $newUser = User::create([
                        'name' => $user_google->name,
                        'first_name' => $user_google->user['given_name'] ?? '',
                        'last_name' => $user_google->user['family_name'] ?? '',
                        'email' => $user_google->email,
                        'provider_id' => $user_google->id,
                        'provider' => $provider,
                        'password' => ''
                    ]);
                    if ($user_google->avatar ?? null) {
                        Avatar::create(['user_id' => $newUser->id, 'url' => $user_google->avatar]);
                    }
                    $token = JWTAuth::fromUser($newUser);
                    $user = $newUser->load('avatar');
                    $registration = true;
                }

                $user = $user->toJson();
                return view('callback', [
                    'user' => $user,
                    'token' => $token,
                    'registration' => $registration,
                ]);
            }

        } catch (Exception $e) {
            Log::info($e->getMessage());
            return new ApiErrorResponse($e->getMessage());
        }
    }

    public function handleAppleCallback()
    {
        try {
            $user_apple = Socialite::driver('apple')->stateless()->user();
            $find_user = User::where('provider_id', $user_apple->id)->first();

            if ($find_user) {
                $token = JWTAuth::fromUser($find_user);
                $user = $find_user->load('avatar');
                $user = $user->toJson();
                return view('callback', [
                    'user' => $user,
                    'token' => $token,
                    'registration' => false
                ]);
            } else {
                $user = User::where('email', $user_apple->email)->first();
                if ($user) {
                    $user->update(['provider_id' => $user_apple->id, 'provider' => 'apple']);
                    $token = JWTAuth::fromUser($user);
                    $user = $user->load('avatar');
                    $registration = false;
                } else {
                    $newUser = User::create([
                        'name' => $user_apple->nickname ?? '',
                        'first_name' => $user_apple->name ?? '',
                        'email' => $user_apple->email ?? '',
                        'provider_id' => $user_apple->id,
                        'provider' => 'apple',
                        'password' => ''
                    ]);
                    if ($user_apple->avatar ?? null) {
                        Avatar::create(['user_id' => $newUser->id, 'url' => $user_apple->avatar]);
                    }
                    $token = JWTAuth::fromUser($newUser);
                    $user = $newUser->load('avatar');
                    $registration = true;
                }

                $user = $user->toJson();
                return view('callback', [
                    'user' => $user,
                    'token' => $token,
                    'registration' => $registration
                ]);
            }

        } catch (Exception $e) {
            Log::info($e->getMessage());
            return new ApiErrorResponse($e->getMessage());
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request): ApiResponse
    {
        $email = $request->only('email');
        $this->auth_service->forgotPassword($email);

        return new ApiResponse(compact('email'));
    }

    /**
     * @throws \App\Exceptions\ApiValidationException
     */
    public function setNewPassword(NewPasswordRequest $request): ApiResponse
    {
        $data = $request->validated();
        $this->auth_service->setNewPassword($data);

        return new ApiResponse();
    }

    /**
     * @throws \App\Exceptions\ApiValidationException
     */
    public function resetPasswordInfo(ResetPasswordInfoRequest $request): ApiResponse
    {
        $user = $this->auth_service->resetPasswordInfo($request->token());

        return new ApiResponse(compact('user'));
    }

    /**
     * @return ApiErrorResponse|ApiResponse
     */
    public function refreshToken()
    {
        try {
            $token_old = JWTAuth::getToken();
            $token = JWTAuth::refresh($token_old);

            return new ApiResponse(compact('token'));
        } catch (\Exception $exception) {
            return new ApiErrorResponse($exception->getMessage());
        }
    }
}

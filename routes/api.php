<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublishingFilesController;
use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\JobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', [AuthController::class, 'login']);
Route::post('registration', [AuthController::class, 'register']);
Route::post('sms/verify', [AuthController::class, 'verifySms']);
Route::post('reset_password', [AuthController::class, 'forgotPassword']);
Route::post('set_new_password', [AuthController::class, 'setNewPassword']);
Route::post('reset-pass-info', [AuthController::class, 'resetPasswordInfo']);
Route::post('refresh_token', [AuthController::class, 'refreshToken']);

Route::get('auth/{provider}', [AuthController::class, 'redirectToProvider']);
Route::post('auth/apple/callback', [AuthController::class, 'handleAppleCallback']);
Route::get('auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);


Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('user', [UserController::class, 'getUser']);
    Route::get('connect/facebook', [AuthController::class, 'redirectToFacebook']);
    Route::post('update_type_user', [UserController::class, 'updateTypeUser']);
    Route::post('create_new_job', [JobController::class, 'createJob']);
    Route::match(['get', 'post'], 'get-user-owned-jobs', [JobController::class, 'getUserOwnedJobs']);
    Route::get('jobs', [JobController::class, 'getJobs']);
    Route::get('get-job/{id}', [JobController::class, 'getJobById']);
    Route::get('get-work-job/{id}', [JobController::class, 'getWorkJobById']);
    Route::post('accept-job', [JobController::class, 'acceptJob']);
    Route::post('create-work-image', [JobController::class, 'createWorkImage']);
    Route::post('update_time_work_image', [JobController::class, 'updateTimeWorkImage']);
    Route::post('upload-work-image', [JobController::class, 'uploadWorkImage']);
    Route::post('finish-work-image', [JobController::class, 'finishWorkImage']);

    Route::post('decline-job/{job_id}', [JobController::class, 'declineJob']);
    Route::post('update-time-download', [JobController::class, 'updateTimeDownload']);

    Route::post('set_time_job/{job_id}', [JobController::class, 'setTimeJob']);
    Route::post('decline-image', [JobController::class, 'declineImage']);
    Route::post('approval-image', [JobController::class, 'approvalImage']);

    Route::post('post-images', [PublishingFilesController::class, 'postImages']);
    Route::post('login-instagram', [PublishingFilesController::class, 'loginInstagram']);
    Route::post('search-product-shopify', [ShopifyController::class, 'searchProductShopify']);

    Route::post('access_token', [TwilioController::class, 'getToken']);
    Route::post('create-channel_chat', [TwilioController::class, 'createChannelChat']);
    Route::post('chat', [TwilioController::class, 'chat']);
    Route::post('chat/image', [TwilioController::class, 'saveChatImage']);
    Route::get('chats-list', [TwilioController::class, 'listChats']);
    Route::post('get-login-social', [UserController::class, 'getUserLoginSocial']);

    Route::match(['put', 'post'], 'approval-or-decline', [JobController::class, 'approvalOrDeclineImages']);
    Route::post('send-email', [UserController::class, 'sendEmail']);
    Route::post('login-shopify', [UserController::class, 'loginShopify']);
    Route::post('get-token-shopify', [UserController::class, 'accessTokenShopify']);
    Route::post('connect-instagram', [UserController::class, 'connectInstagram']);

    Route::get('shopify-connect', [UserController::class, 'isShopifyConnect']);
    Route::get('is-instagram-connect', [UserController::class, 'isInstagramConnect']);

    Route::get('past-job', [JobController::class, 'pastJob']);
    Route::post('create-review', [UserController::class, 'createReview']);


    Route::group(['prefix' => 'profile'], function () {
        Route::get('/user', [UserController::class, 'getUserProfile']);
        Route::post('/upload-file', [ProfileController::class, 'uploadUserFile']);
        Route::post('/add_payment_method', [ProfileController::class, 'addPaymentMethod']);
        Route::post('/add-style-guide', [ProfileController::class, 'addStyleGuide']);
        Route::get('/get-style_guide', [ProfileController::class, 'getStyleGuide']);
        Route::get('/get-file-job/{job_id}', [ProfileController::class, 'getFileJob']);
        Route::get('/get-styles-job', [ProfileController::class, 'getStyleJob']);
        Route::get('/setting', [ProfileController::class, 'setting']);
        Route::post('/update-avatar', [ProfileController::class, 'updateAvatar']);
        Route::post('/update-user_name', [ProfileController::class, 'updateUserName']);
        Route::post('/update-user_bio', [ProfileController::class, 'updateUserBio']);
        Route::post('/update-user_business', [ProfileController::class, 'updateUserBusiness']);
        Route::post('/update-user_location', [ProfileController::class, 'updateUserLocation']);
        Route::post('/add_membership_plan', [ProfileController::class, 'addMembershipPlan']);
        Route::post('/cancel-membership_plan', [ProfileController::class, 'cancelMembershipPlan']);
        Route::get('/connect-stripe', [UserController::class, 'connectStripe']);
        Route::get('/link-stripe', [UserController::class, 'createLoginLinkAccount']);
        Route::post('/withdraw-money', [ProfileController::class, 'withdrawMoney']);
        Route::post('/unlink-instagram', [UserController::class, 'unlinkInstagram']);
        Route::post('/unlink-shopify', [UserController::class, 'unlinkShopify']);
    });
});

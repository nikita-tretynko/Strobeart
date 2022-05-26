<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveChatImageRequest;
use App\Http\Responses\ApiResponse;
use App\Models\ChatImage;
use App\Models\ChatJob;
use App\Services\TwilioService;
use Illuminate\Http\Request;

class TwilioController extends Controller
{

    /**
     * @var TwilioService
     */
    private TwilioService $twilioService;

    /**
     *
     */
    public function __construct()
    {
        $this->twilioService = new TwilioService();
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function getToken(Request $request): ApiResponse
    {
        $identity = $request->get('identity');
        $token = $this->twilioService->getToken($identity);

        return new ApiResponse($token);
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function deleteChannel(Request $request): ApiResponse
    {
        $image_job_id = $request->get('job_id');
        $this->twilioService->deleteChannel($image_job_id);

        return new ApiResponse();
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function createChannelChat(Request $request): ApiResponse
    {
        $image_job_id = $request->get('job_id');
        $this->twilioService->createChannelChat($image_job_id);

        return new ApiResponse();
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function chat(Request $request): ApiResponse
    {
        $image_job_id = $request->get('job_id');
        $users = $this->twilioService->chat($image_job_id);

        return new ApiResponse($users);
    }

    /**
     * @param SaveChatImageRequest $request
     * @return ApiResponse
     */
    public function saveChatImage(SaveChatImageRequest $request): ApiResponse
    {
        $user = $request->user();
        $image = new ChatImage();
        $image->user_id = $user->id;
        $image->job_image_id = $request->get('job_id');
        $image->updateChatImage($request->image());
        $image->save();

        return new ApiResponse(compact('image'));
    }

    public function listChats(Request $request)
    {
        $user = $request->user();
        $chats = ChatJob::where('user_editor_id', $user->id)
            ->orWhere('user_business_id', $user->id)
            ->with('user_business.avatar','user_editor.avatar','job_image')
            ->orderByDesc('created_at')
            ->get();

        return new ApiResponse($chats);
    }
}

<?php

namespace App\Services;

use App\Exceptions\ApiValidationException;
use App\Libraries\CookieInstagram\InstaLite\Exception;
use App\Models\ChatImage;
use App\Models\ChatJob;
use App\Models\ImageJob;
use App\Models\User;
use App\Traits\DateTimeTrait;
use Illuminate\Support\Facades\Log;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;
use Twilio\Rest\Client;

class TwilioService
{
    use DateTimeTrait;

    private $accountSid, $apiKeySid, $apiKeySecret, $apiKeyToken, $accountChat;

    public function __construct()
    {

        $this->accountSid = config('services.twilio.sid');
        $this->apiKeySid = config('services.twilio.key');
        $this->apiKeySecret = config('services.twilio.secret');
        $this->apiKeyToken = config('services.twilio.token');
        $this->accountChat = config('services.twilio.chat');
    }

    /**
     * @param $identity
     * @return string
     */
    public function getToken($identity): string
    {
        $token = new AccessToken(
            $this->accountSid,
            $this->apiKeySid,
            $this->apiKeySecret,
            3600,
            $identity
        );
        $chat_grant = new ChatGrant();
        $chat_grant->setServiceSid($this->accountChat);
        $token->addGrant($chat_grant);

        return $token->toJWT();
    }

    /**
     * @param $image_job_id
     * @return bool
     */
    private function isChatJob($image_job_id): bool
    {
        $chat_job = ChatJob::where('job_image_id', $image_job_id)->first();
        if ($chat_job) {
            return true;
        }
        return false;
    }

    /**
     * @param $image_job_id
     * @return bool
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function createChannelChat($image_job_id): bool
    {
        try {
            if ($this->isChatJob($image_job_id)) {
                return true;
            }
            $image_job = ImageJob::with('user_work')->find($image_job_id);

            $channel_id = 'channel-' . $image_job_id;
            $twilio = new Client($this->accountSid, $this->apiKeyToken);
            $channel = $twilio->chat->v2->services($this->accountChat)
                ->channels
                ->create([
                    'friendlyName' => 'ImageJob' . $image_job_id,
                    'uniqueName' => $channel_id,
                    'type' => 'private',
                ]);

            ChatJob::create([
                'job_image_id' => $image_job_id,
                'channels_sid' => $channel->sid,
                'channels_name' => $channel_id,
                'user_editor_id' => $image_job->user_work->user_id ?? null,
                'user_business_id' => $image_job->user_id,
            ]);

            $users_id[] = $image_job->user_id;
            $users_id[] = $image_job->user_work->user_id;
            foreach ($users_id as $id) {
                try {
                    $twilio->chat->v2->services($this->accountChat)
                        ->channels($channel_id)
                        ->members($id)
                        ->fetch();

                } catch (\Twilio\Exceptions\RestException $e) {
                    $member = $twilio->chat->v2->services($this->accountChat)
                        ->channels($channel_id)
                        ->members
                        ->create($id);
                }
            }
            return true;
        } catch (Exception $exception) {
            Log::info('ERROR CREATE CHANNEL:' . $exception);
        }
        return false;
    }

    /**
     * @param $image_job_id
     * @return array
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function chat($image_job_id): array
    {
        $image_job = ImageJob::with('user_work')->find($image_job_id);
        if (!$image_job->user_work) {
            return [];
        }
        $channel_id = 'channel-' . $image_job_id;
        $twilio = new Client($this->accountSid, $this->apiKeyToken);
        $users = User::with('avatar')
            ->whereIn('id', [$image_job->user_id, $image_job->user_work->user_id])
            ->get();
        try {
            $twilio->chat->v2->services($this->accountChat)
                ->channels($channel_id)
                ->fetch();
        } catch (\Twilio\Exceptions\RestException $e) {
            Log::info('ERROR CHAT!!! ' . $e);
        }

        return $users->toArray();
    }


    /**
     * @param $image_job_id
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function deleteChannel($image_job_id)
    {
        try {
            $channel_id = 'channel-' . $image_job_id;
            $twilio = new Client($this->accountSid, $this->apiKeyToken);
            $twilio->chat->v2->services($this->accountChat)
                ->channels($channel_id)
                ->delete();
        } catch (Exception $exception) {
            Log::info('ERROR deleteChannel job-id:' . $image_job_id);
        }
    }

    public function deleteChat($image_job_id)
    {
        try {
            $chat_img = ChatImage::where('job_image_id', $image_job_id)->first();
            if ($chat_img) {
                $chat_img->delete();
            }
            $chat_job = ChatJob::where('job_image_id', $image_job_id)->first();
            if ($chat_job) {
                $chat_job->delete();
                $this->deleteChannel($image_job_id);
            }
        } catch (Exception $exception) {
            Log::info('ERRoR delete chat:' . $exception);
        }
    }


    /**
     * @param $phone
     * @param $message
     */
    public function sendSMS($phone, $message)
    {
        try {
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($phone, [
                'from' => $twilio_number,
                'body' => $message]);

        } catch (\Exception $e) {
            Log::info("Error: " . $e->getMessage());
        }

    }

    public function sendMessage($user, $channel, $message): string
    {
        $sid = getenv("TWILIO_ACCOUNT_SID");
        $token = getenv("TWILIO_ACCOUNT_TOKEN");
        $twilio = new Client($sid, $token);

        $message = $twilio->chat->v2->services(getenv("TWILIO_SERVICE_SID"))
            ->channels($channel)
            ->messages
            ->create(["body" => $message,"from"=>$user->id]);

        return $message->sid;
    }
}

<?php

/**
 * COOKIE INSTAGRAM SERVICE
 * @version 1.0.0
 */

namespace App\Libraries\CookieInstagram;

use App\Libraries\CookieInstagram\InstagramPhotoService;
use App\Libraries\CookieInstagram\InstaLite\Exception;
use App\Libraries\CookieInstagram\InstaLite\InstaLite;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\File;

class InstagramService
{
    /**
     * Client
     */
    private $client;

    /**
     * InstagramPhotoService
     */
    private $photoService;

    /**
     * Instagram service constructor
     *
     * @param string $username
     * @param string $password
    */
    public function __construct(string $username, string $password)
    {
        $this->client = new InstaLite($username, $password);
        $this->photoService = new InstagramPhotoService();
    }

    /**
     * Search users
     *
     * @param string $search_key
     * @param bool $flag
     *
     * @return array
     */
    public function searchUsers(string $search_key, bool $flag = false): array
    {
        if ($flag) {
            return $this->client->searchUser($search_key)->all();
        }

        return $this->client->searchUser($search_key)->id();
    }

    /**
     * Send a message to all users found by the search key or only to a user with a complete match
     *
     * @param string $message
     * @param string $search_key
     * @param bool $all
     *
     * @return bool|null
     */
    public function sendMessage(string $message, string $search_key, bool $all = false): ?bool
    {
        $users_array = null;

        if ($all) {
            $users_array = $this->client->searchUser($search_key)->id();
        } else {
            $users = $this->client->searchUser($search_key)->all();
            $user = collect($users)->where('username', $search_key)->first();
            if (!empty($user)) {
                $user_id = $user['pk'] ?? null;
                $users_array = $user_id ? [$user_id] : null;
            }
        }

        if ($users_array) {
            return $this->client->sendMessage($message, $users_array);
        }

        throw new Exception("User not found!");
    }

    /**
     * Posting a photo to Instagram from an external source without saving it on the server
     *
     * @param $photo
     * @param string $message
     *
     * @return string|null
     */
    public function publishedPhoto($photo, string $message = ''): ?string
    {
        $data = [
            'photo' => $photo,
            'message' => $message,
            'cleared' => true,
        ];

        return $this->photoService->publishedPhoto($this->client, $data);
    }

    public function publishPhoto($photo, string $message = '')
    {
        $data = [
            'photo' => $photo,
            'message' => $message,
            'cleared' => true,
        ];

       $this->client->uploadPhoto($data['photo'], $data['message']);

    }

    /**
     * Save and published photo to Instagram
     *
     * @param $photo
     * @param string $save_photo_path
     * @param string $message
     *
     * @return array
     */
    public function saveAndPublishedPhoto($photo, string $save_photo_path, string $message = ''): array
    {
        $data = [
            'photo' => $photo,
            'message' => $message,
            'image_save_path' => $save_photo_path,
        ];

        return $this->photoService->publishedPhoto($this->client, $data);
    }

    /**
     * Published saved photo to Instagram
     *
     * @param string $photo_path
     * @param string $message
     *
     * @return array
     */
    public function publishedSavedPhoto(string $photo_path, string $message = '')
    {
        $data = [
            'image_path' => $photo_path,
            'message' => $message,
        ];

        return $this->photoService->publishedPhoto($this->client, $data);
    }
}

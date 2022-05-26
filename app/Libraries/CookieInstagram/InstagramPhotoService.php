<?php

/**
 * COOKIE INSTAGRAM PHOTO SERVICE
 * @version 1.0.0
 */

namespace App\Libraries\CookieInstagram;

use App\Libraries\CookieInstagram\InstaLite\Exception;
use Illuminate\Support\Facades\Storage;

class InstagramPhotoService
{
    /**
     * Posting a photo to Instagram
     *
     * @param $client
     * @param array $data
     *
     * @return string|array|null
     */
    public function publishedPhoto($client, array $data)
    {
        $image_save_path = $data['image_save_path'] ?? 'temp/image';
        $message = $data['message'] ?? '';
        $cleared = !empty($data['cleared']) && (bool)$data['cleared'];

        if (!empty($data['photo'])) {
            $store_result = Storage::disk('public')->put($image_save_path, $data['photo']);
            $real_path = storage_path('app/public/') . $store_result;
        } else {
            if (empty($data['image_path'])) {
                throw new Exception('Picture not found!');
            }

            $real_path = str_replace(
                env('APP_URL') . '/storage',
                storage_path('app/public'),
                $data['image_path']
            );
        }

        try {
            $result = $client->uploadPhoto($real_path, $message);
        } catch (\Exception $e) {
            if ($cleared || empty($data['image_path'])) {
                $this->clearTmpFolder($real_path);
            }

            throw new Exception($e->getMessage());
        }

        if ($cleared) {
            $this->clearTmpFolder($real_path);

            return $result ?? null;
        }

        return [
            'photo_path' => !empty($store_result) ? env('APP_URL') . '/storage/' . $store_result : $data['image_path'],
            'media_id_instagram' => $result ?? null,
        ];
    }

    /**
     * Clear temp folder
     *
     * @param string $real_path
     *
     * @return void
     */
    private function clearTmpFolder(string $real_path)
    {
        if (file_exists($real_path)) {
            unlink($real_path);
        }
    }
}

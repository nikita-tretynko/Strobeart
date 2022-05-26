<?php

namespace App\Libraries\Shopify\core;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class ShopifyImageService
 * @package App\Libraries\Shopify\core
 */
class ShopifyImageService
{
    /**
     * Code image in base 64
     *
     * @param string $real_image_path
     * @param string|null $image_name
     *
     * @return array
     */
    public function codeImageIn64(string $real_image_path, string $image_name = null): array
    {
        $file = file_get_contents($real_image_path);
        $base64_code = base64_encode($file);

        $image_name = $image_name ?? collect(explode('/', $real_image_path))->last();

        return [
            'attachment' => $base64_code,
            'filename' => $image_name,
        ];
    }

    /**
     * Save image
     *
     * @param $image
     * @param string|null $save_path
     *
     * @return array
     * @throws \Exception
     */
    public function saveImage($image, string $save_path = null): array
    {
        $path = $save_path ?? 'temp/image';

        try {
            $store_result = Storage::disk('public')->put($path, $image);
            $real_path = storage_path('app/public/') . $store_result;
        } catch (\Exception $e) {
            throw new \Exception('No save file! ERROR: ' . $e->getMessage(), 500);
        }

        return [
            'url_path' => env('APP_URL') . '/storage/' . $store_result,
            'real_path' => $real_path,
        ];
    }

    /**
     * Image url path to real path
     *
     * @param string $url_path
     *
     * @return string|null
     */
    public function getRealPath(string $url_path): ?string
    {
        $real_path = str_replace(
            env('APP_URL') . '/storage',
            storage_path('app/public'),
            $url_path
        );

        if (!file_exists($real_path)) {
            return null;
        }

        return $real_path;
    }

    /**
     * Published save image
     *
     * @param $client
     * @param string $real_path
     * @param int $product_id
     *
     * @return mixed
     * @throws \Exception
     */
    public function publishedSaveImage($client, string $real_path, int $product_id)
    {
        try {
            $image_data_array = $this->codeImageIn64($real_path);
        } catch (\Exception $e) {
            throw new \Exception('No code image! ERROR: ' . $e->getMessage(), 500);
        }

        return $this->pushShopify($client, $product_id, $image_data_array);
    }

    public function postPhoto(array $data)
    {
        try {
           $this->publishedSaveImage($data['client'],$data['image'], $data['product_id']);
        } catch (\Exception $e) {
            Log::info('Error publish Shopify: ' . $e);
        }
    }

    /**
     * Published in Shopify and save or not save image
     *
     * @param array $data ['client', 'product_id', 'image', 'save_path' => string|null]
     *
     * @return mixed
     * @throws \Exception
     */
    public function publishedAndSaveOrNotSave(array $data)
    {
        if (empty($data['client'])) {
            throw new \Exception('Not valid CLIENT!', 400);
        }
        if (empty($data['product_id'])) {
            throw new \Exception('Not Product ID!', 400);
        }
        if (empty($data['image'])) {
            throw new \Exception('Not IMAGE!', 400);
        }

        try {
            $save_result = $this->saveImage($data['image'], $data['save_path'] ?? null);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        try {
            $result = $this->publishedSaveImage($data['client'], $save_result['real_path'], $data['product_id']);
        } catch (\Exception $e) {
            $this->deleteTempImage($save_result['real_path']);
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        if (!empty($data['save_path'])) {
            return [
                'url_path' => $save_result['url_path'],
                'result' => $result,
            ];
        }

        $this->deleteTempImage($save_result['real_path']);

        return $result;
    }

    /**
     * Publish Image to Shopify
     *
     * @param $client
     * @param int $product_id
     * @param array $image ['attachment', 'filename']
     *
     * @return mixed
     */
    public function pushShopify($client, int $product_id, array $image)
    {
        return $client('POST ' . ShopifyEnum::$API_URL . '/' . $product_id . '/images.json', [], ['image' => $image]);
    }

    /**
     * Delete tmp image
     *
     * @param string $real_path
     *
     * @return void
     */
    private function deleteTempImage(string $real_path)
    {
        if (file_exists($real_path)) {
            unlink($real_path);
        }
    }
}

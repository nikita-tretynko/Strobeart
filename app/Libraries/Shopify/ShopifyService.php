<?php

namespace App\Libraries\Shopify;

use App\Libraries\CookieInstagram\InstaLite\Exception;
use App\Libraries\Shopify\core\Shopify;
use App\Libraries\Shopify\core\ShopifyEnum;
use App\Libraries\Shopify\core\ShopifyImageService;
use Illuminate\Support\Facades\Log;

/**
 * Class ShopifyService
 * @package App\Libraries\Shopify
 */
class ShopifyService
{
    /**
     * @var Shopify
     */
    private $shopify;

    /**
     * @var ShopifyImageService
     */
    private $shopifyImageService;

    /**
     * Shopify service __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->shopify = new Shopify();
        $this->shopifyImageService = new ShopifyImageService();
    }

    /**
     * Client
     *
     * @param array $shopify_data ['shop', 'api_key', 'password']
     *
     * @return mixed
     * @throws \Exception
     */
    public function client(array $shopify_data)
    {
        if (empty($shopify_data['shop']) || empty($shopify_data['api_key']) || empty($shopify_data['password'])) {
            throw new \Exception('Not valid Shopify data!', 400);
        }

        return $this->shopify->client($shopify_data['shop'], $shopify_data['api_key'], $shopify_data['password']);
    }

    /**
     * Get product by ID
     *
     * @param array $shopify_data ['shop', 'api_key', 'password']
     * @param int $product_id
     *
     * @return mixed
     */
    public function getProductById(array $shopify_data, int $product_id)
    {
        $client = $this->client($shopify_data);

        return $client('GET ' . ShopifyEnum::$API_URL . '/' . $product_id . '.json');
    }

    /**
     * Get products list
     *
     * @param array $shopify_data ['shop', 'api_key', 'password']
     *
     * @return mixed
     */
    public function getProductsList(array $shopify_data)
    {
        $client = $this->client($shopify_data);

        return $client('GET ' . ShopifyEnum::$API_URL . '.json');
    }

    /**
     * Receive a list of all Product Images
     *
     * @param array $shopify_data ['shop', 'api_key', 'password']
     * @param int $product_id
     *
     * @return mixed
     */
    public function getProductImages(array $shopify_data, int $product_id)
    {
        $client = $this->client($shopify_data);

        return $client('GET ' . ShopifyEnum::$API_URL . '/' . $product_id . '/images.json');
    }

    /**
     * @param $image
     * @param array $shopify_data
     * @param int $product_id
     * @throws \Exception
     */
    public function postPhoto($image, array $shopify_data, int $product_id)
    {
        $client = $this->client($shopify_data);

        $data = [
            'client' => $client,
            'image' => $image,
            'product_id' => $product_id,
        ];

        $this->shopifyImageService->postPhoto($data);

    }

    /**
     * Posting image to Shopify from an external source without saving it on the server
     *
     * @param $image
     * @param array $shopify_data ['shop', 'api_key', 'password']
     * @param int $product_id
     *
     * @return array
     * @throws \Exception
     */
    public function publishedImage($image, array $shopify_data, int $product_id): array
    {
        $client = $this->client($shopify_data);

        $data = [
            'client' => $client,
            'image' => $image,
            'product_id' => $product_id,
        ];

        try {
            $result = $this->shopifyImageService->publishedAndSaveOrNotSave($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $result;
    }


    /**
     * @param array $shopify_data
     * @return bool
     * @throws \Exception
     */
    public function isLogin(array $shopify_data):bool
    {
        try {
            $this->getProductsList($shopify_data);
            return true;
        }catch (Exception $exception){
            return false;
        }
    }

    /**
     * Posting image to Shopify from an external source and saving it on the server
     *
     * @param $image
     * @param array $shopify_data ['shop', 'api_key', 'password']
     * @param int $product_id
     * @param string $save_path
     *
     * @return array
     * @throws \Exception
     */
    public function publishedAndSaveImage($image, array $shopify_data, int $product_id, string $save_path): array
    {
        $client = $this->client($shopify_data);

        $data = [
            'client' => $client,
            'product_id' => $product_id,
            'image' => $image,
            'save_path' => $save_path,
        ];

        try {
            $result = $this->shopifyImageService->publishedAndSaveOrNotSave($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    /**
     * Published saved photo to Shopify
     *
     * @param array $shopify_data ['shop', 'api_key', 'password']
     * @param int $product_id
     * @param string $image_url http://example.com/storage/.../image.jpg
     *
     * @return mixed
     * @throws \Exception
     */
    public function publishedSavedImage(array $shopify_data, int $product_id, string $image_url)
    {
        $client = $this->client($shopify_data);

        $real_path = $this->shopifyImageService->getRealPath($image_url);

        if (!$real_path) {
            throw new \Exception('Image not found in the specified path!', 400);
        }

        return $this->shopifyImageService->publishedSaveImage($client, $real_path, $product_id);
    }

    /**
     * Get filtered products list
     *
     * @param array $shopify_data ['shop', 'api_key', 'password']
     * @param array $filters ['id' => integer, 'title' => string, 'product_type' => string, 'status' => int (0 || 1), 'sku' => string]
     *
     * @return mixed
     */
    public function getFilteredProductList(array $shopify_data, array $filters = [])
    {
        $product_list = $this->getProductsList($shopify_data);

        if (empty($product_list)) {
            return [];
        }

        if (empty($filters)) {
            return $product_list;
        }

        if (isset($filters['status'])) {
            $status = ShopifyEnum::$STATUSES[$filters['status']];
            $product_list = collect($product_list)->where('status', $status);
        }

        if (!empty($filters['id'])) {
            $product_list = collect($product_list)->where('id', $filters['id']);
        }

        if (!empty($filters['sku'])) {
            $product_list = collect($product_list)->filter(function ($value) use ($filters) {
                if (!empty(collect($value['variants'])->where('sku', $filters['sku'])->first())) {
                    return $value;
                }
            });
        }

        if (!empty($filters['product_type'])) {
            $product_list = $this->filterLike($product_list, 'product_type', $filters['product_type']);
        }

        if (!empty($filters['title'])) {
            $product_list = $this->filterLike($product_list, 'title', $filters['title']);
        }

        return $product_list;
    }

    /**
     * Filter collection by like key
     *
     * @param $items
     * @param string $key
     * @param string $value
     *
     * @returns mixed
     */
    public function filterLike($items, string $key, string $value)
    {
//        $reg_exp = '%' . $value . '%';
//        return collect($items)->where($key, 'like', $reg_exp);

        return collect($items)->filter(function ($item) use ($key, $value) {
            if (stristr($item[$key], $value)) {
                return $item;
            }
        });
    }
}

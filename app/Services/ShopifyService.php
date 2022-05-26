<?php

namespace App\Services;

use App\Exceptions\ApiValidationException;
use App\Libraries\CookieInstagram\InstaLite\Exception;
use Illuminate\Support\Facades\Http;

class ShopifyService
{
    /**
     * @var string|mixed
     */
    private string $api_key;
    /**
     * @var string|mixed
     */
    private string $api_secret;
    /**
     * @var string|mixed
     */
    private string $scope;
    /**
     * @var string|mixed
     */
    private string $redirect_uri;

    /**
     *
     */
    public function __construct()
    {
        $this->api_key = env("SHOPIFY_API_KEY");
        $this->api_secret = env("SHOPIFY_API_SECRET");
        $this->redirect_uri = env("SHOPIFY_REDIRECT_URL");
        $this->scope = env("SHOPIFY_SCOPE", ' write_customers,read_product_listings,write_product_listings, read_products,write_products');
    }

    /**
     * @param $shop
     * @return string
     */
    public function linkLoginShopify($shop): string
    {
        return "https://" . $shop . ".myshopify.com/admin/oauth/authorize?client_id=" . $this->api_key . "&scope=" . $this->scope . "&redirect_uri=" . urlencode($this->redirect_uri);
    }

    /**
     * @param string $code
     * @param string $shop
     * @return string
     * @throws ApiValidationException
     */
    public function getTokenShopify(string $code, string $shop): string
    {
        try {
            $request = Http::acceptJson()
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post("https://" . $shop . "/admin/oauth/access_token", [
                        "client_id" => $this->api_key,
                        "client_secret" => $this->api_secret,
                        "code" => $code
                    ]
                );

            return $request['access_token'];

        } catch (Exception $exception) {
            throw new ApiValidationException('Error get token Shopify');
        }
    }

    /**
     * @param $shop
     * @param $token
     * @return array
     */
    public function getAllProducts($shop, $token): array
    {
        $request = Http::acceptJson()
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-Shopify-Access-Token' => $token,
            ])
            ->get("https://" . $shop . "/admin/api/2022-04/products.json");

        return $request['products'];
    }

    /**
     * @param $shop
     * @param $token
     * @param array $filters
     * @return array|\Illuminate\Support\Collection
     */
    public function searchProduct($shop, $token, array $filters = [])
    {
        $products = $this->getAllProducts($shop, $token);

        if (!empty($filters['title'])) {
            $products = $this->filterLike($products, 'title', $filters['title']);
        }

        return $products ?? [];
    }

    /**
     * @param $token
     * @param $shop
     * @param $product_id
     * @param $image
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     */
    public function addImagesToProduct($token, $shop, $product_id, $image)
    {
        return Http::acceptJson()
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-Shopify-Access-Token' => $token,
            ])
            ->post("https://" . $shop . "/admin/api/2022-01/products/" . $product_id . "/images.json", [
                "image" => collect($image),
            ]);
    }

    /**
     * @param $items
     * @param string $key
     * @param string $value
     * @return \Illuminate\Support\Collection
     */
    public function filterLike($items, string $key, string $value): \Illuminate\Support\Collection
    {
        return collect($items)->filter(function ($item) use ($key, $value) {
            if (stristr($item[$key], $value)) {
                return $item;
            }
        });
    }


}

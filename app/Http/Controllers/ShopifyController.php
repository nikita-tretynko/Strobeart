<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginShopifyRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiResponse;
use App\Services\ShopifyService;
use Illuminate\Http\Request;

class ShopifyController extends Controller
{
    public function searchProductShopify(Request $request)
    {
        $user = $request->user();
        $user->load('shopify_connect');
        $shopifyService = new ShopifyService();
        if (is_numeric($request->get('search'))) {
            $search = ['id' => $request->get('search')];
        } else {
            $search = ['title' => $request->get('search')];
        }
        $shop = $user->shopify_connect->shop ?? null;
        $token = $user->shopify_connect->token ?? null;
        if (!$shop||!$token) {
            return new ApiErrorResponse('Error connect to shopify');
        }
        $product_list = $shopifyService->searchProduct($shop, $token, $search);

        return new ApiResponse($product_list);
    }
}

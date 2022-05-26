<?php

namespace App\Libraries\Shopify\core;

/**
 * FINAL CLASS
 * @package App\Libraries\Shopify\core
 * Enum Like Constant Resolver
 */
final class ShopifyEnum
{
    /**
     * @var int Curl max errors
     */
    public static $CURL_MAX_ERRORS = 1;

    /**
     * @var int
     */
    public static $DELAY_BEFORE_RETRY = 1;

    /**
     * @var string
     */
    public static $API_URL = '/admin/api/2022-01/products';

    /**
     * @var array products statuses in Shopify
     */
    public static $STATUSES = [
        0 => 'draft',
        1 => 'active',
    ];

    /**
     *  PRIVATE CONSTRUCTOR
     */
    private function __construct()
    {
    }
}

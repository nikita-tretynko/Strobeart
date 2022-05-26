<?php

namespace App\Libraries\Shopify\core;

/**
 * Class CurlException
 *
 * @package App\Libraries\Shopify\core
 */
class CurlException extends ExceptionShopify
{
    public function __construct($message, $code, $request, $response = [], ?ExceptionShopify $previous = null)
    {
        parent::__construct($message, $code, $request, $response, $previous);
    }
}

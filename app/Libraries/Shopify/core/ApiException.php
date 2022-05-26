<?php

namespace App\Libraries\Shopify\core;

/**
 * Class ApiException
 *
 * @package App\Libraries\Shopify\core
 */
class ApiException extends ExceptionShopify
{
    /**
     * ApiException __construct
     *
     * @param string $message
     * @param string $code
     * @param $request
     * @param array $response
     * @param ExceptionShopify|null $previous
     *
     * @return void
     */
    public function __construct($message, $code, $request, $response = [], ?ExceptionShopify $previous = null)
    {
        if (is_string($code)) {
            $arr_code = explode(':', $code);
            $last_in_array = $arr_code[array_key_last($arr_code)];
            $code = trim($last_in_array, ':";');
        }

        $response_body_json = $response['body'] ?? '';
        if (is_array($response_body_json)) {
            $response = $response_body_json;
        } else {
            $response = json_decode($response_body_json, true);
        }

        $response_error = isset($response['errors']) ? ' '.var_export($response['errors'], true) : '';

        $this->message = $message.$response_error;

        parent::__construct($this->message, $code, $request, $response, $previous);
    }
}

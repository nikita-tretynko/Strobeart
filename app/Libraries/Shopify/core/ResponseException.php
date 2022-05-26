<?php

namespace App\Libraries\Shopify\core;

/**
 * Class ResponseException
 *
 * @package App\Libraries\Shopify\core
 */
class ResponseException extends ExceptionShopify
{
    /**
     * @var HttpShopify
     */
    private $httpShopify;

    /**
     * ResponseException __construct
     *
     * @param $message
     * @param $code
     * @param $request
     * @param array $response
     * @param ExceptionShopify|null $previous
     *
     * @return void
     */
    public function __construct($message, $code, $request, $response = [], ?ExceptionShopify $previous = null)
    {
        $this->httpShopify = new HttpShopify();

        $url = $this->httpShopify->_http_client_request_uri($request['uri'], $request['query']);
        $this->message = "${message} (${url})";
        $code = (int)$code;
        parent::__construct($this->message, $code, $request, $response, $previous);
    }
}

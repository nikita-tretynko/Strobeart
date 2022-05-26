<?php

namespace App\Libraries\Shopify\core;

/**
 * Class ExceptionShopify
 *
 * @package App\Libraries\Shopify\core
 */

class ExceptionShopify extends \Exception
{
    /**
     * Request
     */
    protected $request;

    /**
     * Response
     */
    protected $response;

    /**
     * ExceptionShopify __construct
     *
     * @param $message
     * @param $code
     * @param $request
     * @param $response
     * @param Exception|null $previous
     *
     * @return void
     */
    public function __construct($message, $code, $request, $response = [], ?Exception $previous = null)
    {
        $this->request = $request;
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    /**
     * To string
     */
    public function __toString()
    {
        $backtrace = $this->getTrace();
        return static::class . ": [{$this->code}] {$this->message} in {$backtrace[0]['file']} on line {$backtrace[0]['line']}";
    }

    /**
     * Get request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}

<?php

namespace App\Libraries\Shopify\core;

/**
 * Class Shopify
 * @package App\Libraries\Shopify\core
 */
class Shopify
{
    /**
     * @var HttpShopify
     */
    private $httpShopify;

    /**
     * Shopify __construct
     */
    public function __construct()
    {
        $this->httpShopify = new HttpShopify();
    }

    /**
     * Install URL
     *
     * @param string $shop
     * @param string $api_key
     *
     * @return string
     */
    public function install_url(string $shop, string $api_key): string
    {
        return "https://${shop}/admin/api/auth?api_key=${api_key}";
    }

    /**
     * Check request
     *
     * @param $query_params
     * @param $shared_secret
     *
     * @return bool
     */
    public function is_valid_request($query_params, $shared_secret): bool
    {
        if (! isset($query_params['timestamp'])) {
            return false;
        }

        $seconds_in_a_day = 24 * 60 * 60;
        $older_than_a_day = $query_params['timestamp'] < time() - $seconds_in_a_day;
        if ($older_than_a_day) {
            return false;
        }

        $signature = $query_params['signature'];
        unset($query_params['signature']);
        $params = [];
        foreach ($query_params as $key => $val) {
            $params[] = "${key}=${val}";
        }
        sort($params);

        return md5($shared_secret.implode('', $params)) === $signature;
    }

    /**
     * Authorization URL
     *
     * @param string $shop
     * @param string $api_key
     * @param array $scopes
     * @param string $redirect_uri
     *
     * @return string
     */
    public function authorization_url(string $shop, string $api_key, array $scopes = [], string $redirect_uri = ''): string
    {
        $scopes = empty($scopes) ? '' : '&scope='.implode(',', $scopes);
        $redirect_uri = empty($redirect_uri) ? '' : '&redirect_uri='.urlencode($redirect_uri);
        return "https://${shop}/admin/oauth/authorize?client_id=${api_key}${scopes}${redirect_uri}";
    }

    /**
     * Access token
     *
     * @param string $shop
     * @param string $api_key
     * @param string $shared_secret
     * @param $code
     *
     * @return string
     * @throws CurlException|ApiException
     */
    public function access_token(string $shop, string $api_key, string $shared_secret, $code): string
    {
        try {
            $response = $this->httpShopify->request("POST https://${shop}/admin/oauth/access_token", [], ['client_id' => $api_key, 'client_secret' => $shared_secret, 'code' => $code]);
        } catch (CurlException $e) {
            throw new CurlException($e->getMessage(), $e->getCode(), $e->getRequest());
        } catch (ResponseException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $e->getRequest(), $e->getResponse());
        }

        return $response['access_token'];
    }

    /**
     * Client
     *
     * @param string $shop
     * @param string $api_key
     * @param string $oauth_token
     * @param $private_app
     *
     * @return mixed
     */
    public function client(string $shop, string $api_key, string $oauth_token, $private_app = false)
    {
        $base_uri = $private_app ? $this->_private_app_base_url($shop, $api_key, $oauth_token) : "https://${shop}/";

        return function ($method_uri, $query = '', $payload = '', &$response_headers = [], $request_headers = [], $curl_opts = []) use ($base_uri, $oauth_token, $private_app) {
            if (! $private_app) {
                $request_headers['X-Shopify-Access-Token'] = $oauth_token;
            }
            $request_headers['content-type'] = 'application/json; charset=utf-8';
            $http_client = $this->httpShopify->client($base_uri, $request_headers);
            $response = null;
            $curl_error = false;
            $errors_count = 0;

            do {
                $curl_error = false;

                try {
                    $response = $http_client($method_uri, $query, $payload, $response_headers, $request_headers, $curl_opts);
                } catch (CurlException $e) {
                    $curl_error = true;
                    $errors_count++;
                    if ($errors_count >= ShopifyEnum::$CURL_MAX_ERRORS) {
                        throw new CurlException($e->getMessage(), $e->getCode(), $e->getRequest());
                    }
                    sleep(ShopifyEnum::$DELAY_BEFORE_RETRY);
                } catch (ResponseException $e) {
                    throw new ApiException($e->getMessage(), $e->getCode(), $e->getRequest(), $e->getResponse());
                }
            } while ($curl_error && ($errors_count < ShopifyEnum::$CURL_MAX_ERRORS));

            if (isset($response['errors'])) {
                [$method, $uri] = explode(' ', $method_uri, 2);
                $uri = rtrim($base_uri).'/'.ltrim($uri, '/');
                $headers = $request_headers;
                $request = compact('method', 'uri', 'query', 'headers', 'payload');
                $response = ['headers' => $response_headers, 'body' => $response];
                throw new ApiException($response_headers['http_status_message'].": ${uri}", $response_headers['http_status_code'], $request, $response);
            }

            return (is_array($response) and ! empty($response)) ? array_shift($response) : $response;
        };
    }

    /**
     * Private app base URL
     *
     * @param string $shop
     * @param string $api_key
     * @param string $password
     *
     * @return string
     */
    public function _private_app_base_url(string $shop, string $api_key, string $password): string
    {
        return "https://${api_key}:${password}@${shop}/";
    }

    /**
     * Calls made
     *
     * @param $response_headers
     *
     * @return int
     */
    public function calls_made($response_headers): int
    {
        return $this->_shop_api_call_limit_param(0, $response_headers);
    }

    /**
     * Call limit
     *
     * @param $response_headers
     *
     * @return int
     */
    public function call_limit($response_headers): int
    {
        return $this->_shop_api_call_limit_param(1, $response_headers);
    }

    /**
     * Calls left
     *
     * @param $response_headers
     *
     * @return int
     */
    public function calls_left($response_headers): int
    {
        return $this->call_limit($response_headers) - $this->calls_made($response_headers);
    }

    /**
     * Shop API call limit param
     *
     * @param $index
     * @param $response_headers
     *
     * @return int
     */
    public function _shop_api_call_limit_param($index, $response_headers): int
    {
        $params = explode('/', $response_headers['http_x_shopify_shop_api_call_limit']);
        return (int) $params[$index];
    }
}

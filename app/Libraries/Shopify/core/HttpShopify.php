<?php

namespace App\Libraries\Shopify\core;

/**
 * Class HttpShopify
 *
 * @package App\Libraries\Shopify\core
 */
class HttpShopify
{
    /**
     * Client
     *
     * @param string $base_uri
     * @param array $instance_request_headers
     * @param array $instance_curl_opts
     *
     * @return mixed
     */
    public function client($base_uri = '', $instance_request_headers = [], $instance_curl_opts = [])
    {
        return function ($method_uri, $query = '', $payload = '', &$response_headers = [], $request_headers_override = [], $curl_opts_override = []) use ($base_uri, $instance_request_headers, $instance_curl_opts) {
            [$method, $uri] = explode(' ', $method_uri, 2);
            $uri = $uri[0] === '/' ? rtrim($base_uri, '/').$uri : $uri;
            $request_headers = $request_headers_override + $instance_request_headers;
            $curl_opts = $curl_opts_override + $instance_curl_opts;
            return $this->request("${method} ${uri}", $query, $payload, $response_headers, $request_headers, $curl_opts);
        };
    }

    /**
     * Request
     *
     * @param string $method_uri
     * @param string|array|object $query
     * @param string|array|object $payload
     * @param array $response_headers
     * @param array $request_headers
     * @param array $curl_opts
     *
     * @return mixed
     */
    public function request($method_uri, $query = '', $payload = '', &$response_headers = [], $request_headers = [], $curl_opts = [])
    {
        [$method, $uri] = explode(' ', $method_uri, 2);
        $url = $this->_http_client_request_uri($uri, $query);
        $ch = curl_init($url);
        $this->_http_client_setopts($ch, $method, $payload, $request_headers, $curl_opts);
        $response = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        $headers = $request_headers;
        $request = compact('method', 'uri', 'query', 'headers', 'payload');

        if ($errno) {
            throw new CurlException($error, $errno, $request);
        }

        $header_size = $curl_info['header_size'];
        $msg_header = substr($response, 0, $header_size);
        $msg_body = substr($response, $header_size);
        $response_headers = $this->_http_client_response_headers($msg_header);
        $http_status_message = $response_headers['http_status_message'];
        $http_status_code = $response_headers['http_status_code'];
        $response = ['headers' => $response_headers, 'body' => $msg_body];

        if ($http_status_code >= 400) {
            $test_code = str_replace(['h3=":', '"'], '', $http_status_code);
            if ((int)$test_code != 443) {
                throw new ResponseException($http_status_message, $http_status_code, $request, $response);
            }
        }

        $msg_body = strpos($response_headers['content-type'], 'application/json') !== false ? json_decode($msg_body, true) : $msg_body;

        return $msg_body;
    }

    /**
     * Http client request uri
     *
     * @param $uri
     * @param $query
     *
     * @return string
     */
    public function _http_client_request_uri($uri, $query): string
    {
        if (empty($query)) {
            return $uri;
        }
        if (is_array($query)) {
            return "${uri}?".http_build_query($query);
        }

        return "${uri}?${query}";
    }

    /**
     * Http client setup options
     *
     * @param $ch
     * @param $method
     * @param $payload
     * @param $request_headers_assoc
     * @param $curl_opts
     *
     * @return void
     */
    public function _http_client_setopts($ch, $method, $payload, $request_headers_assoc, $curl_opts)
    {
        $default_curl_opts = [
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSLVERSION => 1,
        ];

        $request_headers = $request_headers_assoc_lower = [];
        foreach ($request_headers_assoc as $key => $val) {
            $request_headers_assoc_lower[strtolower($key)] = $val;
        }
        $request_headers_assoc = $request_headers_assoc_lower;

        if ($method === 'GET') {
            $default_curl_opts[CURLOPT_HTTPGET] = true;
        } else {
            $default_curl_opts[CURLOPT_CUSTOMREQUEST] = $method;

            if ($method === 'POST') {
                $request_headers[] = 'Expect:';
            }

            if (is_array($payload)) {
                if (isset($request_headers_assoc['content-type'])) {
                    if (strpos($request_headers_assoc['content-type'], 'application/x-www-form-urlencoded') !== false) {
                        $payload = http_build_query($payload);
                    } elseif (strpos($request_headers_assoc['content-type'], 'application/json') !== false) {
                        $payload = str_replace('\\/', '/', json_encode($payload));
                    }
                } else {
                    $payload = http_build_query($payload);
                    $request_headers[] = 'Content-Type: application/x-www-form-urlencoded';
                }
            }

            if (! empty($payload)) {
                $default_curl_opts[CURLOPT_POSTFIELDS] = $payload;
            }
        }

        foreach ($request_headers_assoc as $key => $val) {
            $request_headers[] = "${key}: ${val}";
        }
        if (! empty($request_headers)) {
            $default_curl_opts[CURLOPT_HTTPHEADER] = $request_headers;
        }

        $overriden_opts = $curl_opts + $default_curl_opts;
        foreach ($overriden_opts as $curl_opt => $value) {
            curl_setopt($ch, $curl_opt, $value);
        }
    }

    /**
     * Http client response headers
     *
     * @param $msg_header
     *
     * @return array
     */
    public function _http_client_response_headers($msg_header): array
    {
        $multiple_headers = preg_split("/\r\n\r\n|\n\n|\r\r|\r\n/", trim($msg_header));

        $last_response_header_lines = array_pop($multiple_headers);

        $response_headers = [];

        $header_lines = preg_split("/\r\n|\n|\r/", $last_response_header_lines);

        [, $response_headers['http_status_code'], $response_headers['http_status_message']] = explode(' ', trim(array_shift($header_lines)), 3);
        foreach ($header_lines as $header_line) {
            [$name, $value] = explode(':', $header_line, 2);
            $name = trim(strtolower($name));
            $value = trim($value);
            if (isset($response_headers[$name])) {
                $response_headers[$name] = [
                    $response_headers[$name],
                    $value,
                ];
            } else {
                $response_headers[$name] = $value;
            }
        }

        foreach ($multiple_headers as $line_header) {
            if (count(explode(':', $line_header, 2)) >= 2) {
                [$name, $value] = explode(':', $line_header, 2);
                $name = trim(strtolower($name));
                $value = trim($value);
                if (isset($response_headers[$name])) {
                    $response_headers[$name] = [
                        $response_headers[$name],
                        $value,
                    ];
                } else {
                    $response_headers[$name] = $value;
                }
            }
        }

        return $response_headers;
    }
}

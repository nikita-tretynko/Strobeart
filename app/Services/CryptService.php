<?php

namespace App\Services;

use App\Http\Responses\ApiErrorResponse;

/**
 * Class CryptService
 *
 * @package App\Services
 */
class CryptService
{
    /**
     * Encrypt user password
     *
     * @param string $password
     *
     * @return array
     */
    public function encryptUserPassword(string $password): array
    {
        $key = $this->createKey();
        $cipher="AES-128-CBC";

        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($password, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
        $password_hash = base64_encode( $iv.$hmac.$ciphertext_raw );

        return [
            'key' => $key,
            'password_hash' => $password_hash,
        ];
    }

    /**
     * Decrypt user password
     *
     * @param string $key
     * @param string $password_hash
     *
     * @return string
     */
    public function decryptUserPassword(string $key, string $password_hash): string
    {
        $sha2len = 32;
        $cipher="AES-128-CBC";
        $c = base64_decode($password_hash);
        $ivlen = openssl_cipher_iv_length($cipher);
        $offset = $ivlen + $sha2len;


        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len);
        $ciphertext_raw = substr($c, $offset);

        $password = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, true);

        if (hash_equals($hmac, $calcmac))
        {
            return $password;
        }

        return new ApiErrorResponse('Not walid decrypt password!');
    }

    /**
     * Create crypt key
     *
     * @return string
     */
    private function createKey(): string
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($permitted_chars), 0, 16);
    }
}

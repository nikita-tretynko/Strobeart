<?php

namespace App\Http\Requests\Api;

use App\Models\User;

/**
 * Class ApiRequestAuth
 * @package App\Http\Requests\Api
 */
class ApiRequestAuth extends ApiRequest
{
    /**
     * @param null $guard
     * @return User
     */
    public function user($guard = null): User
    {
        return parent::user($guard);
    }
}

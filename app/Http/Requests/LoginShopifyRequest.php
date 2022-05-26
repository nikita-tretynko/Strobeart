<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginShopifyRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'shop' => ['required','string'],
            'api_key' => ['required','string'],
            'password' => ['required','string'],
        ];
    }
}

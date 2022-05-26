<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetTokenFacebookRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'code' => ['required','string'],
        ];
    }
}

<?php

namespace App\Http\Requests;

class ResetPasswordInfoRequest extends ApiRequest
{
    /**
     * @return string
     */
    public function token(): string
    {
        return $this->get('token');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string']
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'token.*' => 'Link is no longer active. Please try again'
        ];
    }
}

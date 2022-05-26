<?php

namespace App\Http\Requests;


use App\Http\Requests\ApiRequest;

class RegisterUserRequest extends ApiRequest
{

    public function rules(): array
    {
        return [
            'last_name' => ['required', 'string', 'max:30'],
            'first_name' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'email', $this->regex_array['email'], 'unique:users'],
            'phone'=> ['required','max:13', 'unique:users' ],
            'type_user'=> ['required', 'string'],
            'password'=>['required','string','min:8']
        ];
    }
}

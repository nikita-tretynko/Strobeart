<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class PublishingFilesRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'image_jobs_id' => ['required'],
            'description' => ['required'],
            'password_instagram' => ['nullable'],
            'login_instagram' => ['nullable'],
            'sequence_pictures' => ['nullable'],
            'timezone'=> ['required'],
            'password' => ['nullable'],
            'api_key' => ['nullable'],
            'shop' => ['nullable'],
            'hashtags' => ['nullable'],
            'product_id' => ['nullable'],
            'platform' => ['required'],
            'date_publication' => ['required', 'date'],
        ];
    }
}

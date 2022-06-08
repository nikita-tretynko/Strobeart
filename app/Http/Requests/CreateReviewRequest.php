<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReviewRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'numeric'],
            'message' => ['nullable'],
            'job_image_id' => ['required', 'numeric'],
            'to_user_id' => ['required', 'numeric'],
        ];
    }
}

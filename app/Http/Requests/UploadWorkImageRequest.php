<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadWorkImageRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'work_image_id' => ['required'],
            'file' => ['required',$this->regex_array['image_mimes']],
        ];
    }
}

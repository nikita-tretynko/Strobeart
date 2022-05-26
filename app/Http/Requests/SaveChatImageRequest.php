<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveChatImageRequest extends ApiRequest
{

    /**
     * @return array|\Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile[]|null
     */
    public function image()
    {
        return $this->file('image');
    }
    /**
     * @return array
     */
    public function validationData(): array
    {
        $image = $this->image();
        return array_merge($this->all(), compact('image'));
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'image' => ['required', 'image'],
            'job_id' => ['required', 'numeric', 'exists:image_jobs,id'],
        ];
    }
}

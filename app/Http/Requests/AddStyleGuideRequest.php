<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddStyleGuideRequest extends ApiRequest
{
    /**
     * @return array
     */
    public function validationData(): array
    {

        $uploaded_logo = $this->file('uploaded_logo');
        $upload_watermark = $this->file('upload_watermark');
        $video_instructions = $this->file('video_instructions');
        $body = json_decode($this->get('body'), true);
        $body['uploaded_logo'] = $uploaded_logo;
        $body['upload_watermark'] = $upload_watermark;
        $body['video_instructions'] = $video_instructions;

        return $body;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'remove_background' => ['required', 'string'],
            'size_with' => ['required', 'string'],
            'size_height' => ['required', 'string'],
            'unit_measurement' => ['required', 'string'],
            'file_format' => ['required', 'string'],
            'color_profile' => ['required', 'string'],
            'resolution' => ['required', 'string'],
            'resolution_units' => ['required', 'string'],
            'other' => ['nullable', 'string'],
            'uploaded_logo' => ['nullable', 'image'],
            'upload_watermark' => ['nullable', 'image'],
            'video_instructions' => ['required', 'file', 'mimetypes:video/mp4']
        ];
    }
}

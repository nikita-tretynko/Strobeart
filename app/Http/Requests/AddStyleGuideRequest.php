<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddStyleGuideRequest extends ApiRequest
{

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
            'file_id_logo' => ['nullable', 'numeric'],
            'file_id_watermark' => ['nullable', 'numeric'],
            'file_id_video_instructions' => ['required', 'numeric'],
              'file_id_color_palette' => ['nullable', 'numeric'],
            'file_id_video_typography' => ['required', 'numeric']
        ];
    }
}

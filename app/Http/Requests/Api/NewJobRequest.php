<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\ApiRequest;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class NewJobRequest extends ApiRequest
{

    /**
     * @return array
     */
    public function validationData(): array
    {
        $image = $this->file('files');
        $body = json_decode($this->get('body'), true);
        $body['files'] = $image;

        return $body;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'images.*.src' => ['required', 'string'],
            'images.*.name' => ['required', 'string'],
            'files.*' => ['required'],
            'due_date' => ['required', 'date'],
            'editing_level' => ['required', 'string'],
            'style_guide' => ['sometimes', 'nullable', 'string'],
            'color_profile' => ['required', 'string'],
            'file_format' => ['required', 'string'],
            'other' => ['sometimes', 'nullable', 'string'],
            'add_logo' => ['nullable', 'numeric'],
            'add_watermark' => ['nullable', 'numeric'],
            'file_id_video_instruction' => ['nullable', 'numeric'],
            'color_palette' => ['nullable', 'numeric'],
            'typography' => ['nullable', 'numeric'],
            'white_background' => ['sometimes', 'boolean'],
            'red_eye' => ['sometimes', 'boolean'],
            'timezone'=> ['required'],
        ];
    }
}

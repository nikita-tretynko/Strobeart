<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkImageRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'work_job' => ['required'],
            'number_file' => ['required'],
            'image_id'=> ['required'],
            'image_jobs_id'=> ['required']
        ];
    }
}

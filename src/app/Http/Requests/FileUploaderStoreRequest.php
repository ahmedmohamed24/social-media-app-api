<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploaderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'files' => ['required', 'array', 'max:5'], //max to upload 5 files
            'files.*' => ['file', 'mimes:png,jpg,pdf,svg,md', 'max:1024'],
        ];
    }
}

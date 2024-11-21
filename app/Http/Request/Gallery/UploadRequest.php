<?php

namespace App\Http\Request\Gallery;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property file $image
 * @property string $title
 * @property ?string $description
 */
class UploadRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    /**
     *
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'image' => 'required|file|mimetypes:image/png,image/jpg,image/jpeg',
            'title' => 'required|string',
            'description' => 'string',
        ];
    }
}

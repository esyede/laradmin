<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SystemMediaRequest extends FormRequest
{
    /**
     * Max upload file size in Mega Bytes (MB).
     *
     * @var int
     */
    protected $maxUploadFileSize = 10;

    public function rules()
    {
        if ($this->isMethod('post')) {
            return [
                'file' => 'required|file|max:' . ($this->maxUploadFileSize * 1024),
            ];
        } elseif ($this->isMethod('put')) {
            return [
                'category_id' => 'exists:system_media_categories,id',
            ];
        } else {
            return [];
        }
    }

    public function attributes()
    {
        return [
            'file' => 'File',
            'category_id' => 'Category',
        ];
    }
}

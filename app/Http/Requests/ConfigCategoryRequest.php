<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class ConfigCategoryRequest extends FormRequest
{
    /**
     * Reserved config slug names
     *
     * @var array
     */
    protected $reservedSlugs = [
        // Avoid requesting /configs/test
        'test',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $cateId = (int) $this->route()->originalParameter('config_category');
        $rules = [
            'name' => 'required|string|max:50|unique:config_categories,name,'.$cateId,
            'slug' => 'required|string|max:50|not_in:'.implode(',', $this->reservedSlugs).'|unique:config_categories,slug,'.$cateId,
        ];
        if ($this->isMethod('put')) {
            $rules = Arr::only($rules, $this->keys());
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => 'Name',
            'slug' => 'Slug',
        ];
    }

    public function messages()
    {
        return [
            'slug.not_in' => ':attribute cannot be: '.implode(',', $this->reservedSlugs).', as it is reserved name.',
        ];
    }
}

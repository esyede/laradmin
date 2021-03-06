<?php

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class SystemMediaCategoryRequest extends FormRequest
{
    public function rules()
    {
        $cate = $this->route('system_media_category');
        $id = optional($cate)->id;

        $rules = [
            'name' => [
                'bail',
                'required',
                'max:20',
                Rule::unique('system_media_categories', 'name')
                    ->where(function (Builder $query) use ($cate) {
                        $parentId = $this->input('parent_id') ?? optional($cate)->parent_id;
                        return $query->where('parent_id', $parentId);
                    })
                    ->ignore($id),
            ],
            'parent_id' => 'exists:system_media_categories,id',
            'folder' => 'bail|nullable|regex:/^[a-z_\/\d]+$/i|max:50',
        ];

        if ($this->isMethod('put')) {
            $keys = $this->keys();
            if ($this->onlyUpdateParentId()) {
                $keys[] = 'name';
            }

            $rules = Arr::only($rules, $keys);
        }

        if ($this->input('parent_id') == 0) {
            $rules['parent_id'] = 'nullable';
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'parent_id' => 'Parent Category',
            'name' => 'Name',
            'folder' => 'Folder',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'Category :attribute already exists.',
            'folder.regex' => 'The :attribute format is invalid',
        ];
    }

    protected function onlyUpdateParentId()
    {
        return $this->isMethod('put') && ($this->has('parent_id') && ! $this->has('name'));
    }

    public function validationData()
    {
        $data = parent::validationData();

        if ($this->onlyUpdateParentId()) {
            $data['name'] = $this->route('system_media_category')->name;
        }

        return $data;
    }
}

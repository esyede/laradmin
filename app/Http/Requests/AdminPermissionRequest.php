<?php

namespace App\Http\Requests;

use App\Models\AdminPermission;
use App\Rules\AdminPermissionHttpPath;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class AdminPermissionRequest extends FormRequest
{
    public function rules()
    {
        $id = (int) optional($this->route('admin_permission'))->id;

        $rules = [
            'name' => 'required|unique:admin_permissions,name,' . $id,
            'slug' => 'required|unique:admin_permissions,slug,' . $id,
            'http_method' => 'nullable|array',
            'http_method.*' => Rule::in(AdminPermission::$httpMethods),
            'http_path' => ['nullable',  new AdminPermissionHttpPath()],
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
            'http_method' => 'Request Method',
            'http_method.*' => 'Request Method',
            'http_path' => 'Request URI',
        ];
    }
}

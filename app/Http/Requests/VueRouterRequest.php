<?php

namespace App\Http\Requests;

use Illuminate\Support\Arr;

class VueRouterRequest extends FormRequest
{
    public function rules()
    {
        if ($this->route()->getName() == 'admin.vue-routers.by-import') {
            return [
                'file' => 'required|file',
            ];
        }

        $rules = [
            'title' => 'required|max:50',
            'icon' => 'max:50',
            'path' => 'max:50',
            'order' => 'integer|between:-9999,9999',
            'cache' => 'boolean',
            'menu' => 'boolean',
            'roles' => 'array',
            'roles.*' => 'exists:admin_roles,id',
            'permission' => 'nullable|exists:admin_permissions,slug',
            'parent_id' => 'exists:vue_routers,id',
        ];

        if ($this->isMethod('put')) {
            $rules = Arr::only($rules, $this->keys());
        }

        if ($this->post('parent_id') == 0) {
            $rules['parent_id'] = 'nullable';
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'parent_id' => 'Parent Category',
            'title' => 'Title',
            'icon' => 'Icon',
            'path' => 'Path',
            'order' => 'Order',
            'cache' => 'Cache',
            'menu' => 'Menu',
            'roles' => 'Role',
            'roles.*' => 'Role',
            'permission' => 'Permission',
            'file' => 'File',
        ];
    }
}

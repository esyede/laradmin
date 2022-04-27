<?php

namespace App\Http\Requests;

use App\Models\AdminUser;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class AdminUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = $this->userResource();
        $id = (int) optional($user)->id;

        $rules = [
            'username' => 'required|max:100|unique:admin_users,username,'.$id,
            'name' => 'required|max:100',
            'avatar' => 'nullable|string|max:255',
            'password' => 'required|between:6,20|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:admin_roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:admin_permissions,id',
        ];

        if ($this->isMethod('put')) {
            $rules = Arr::only($rules, $this->keys());

            // If the password is not filled in when updating, no verification is required
            if (! $this->post('password')) {
                unset($rules['password']);
            }

            // When processing the update, if the image has not changed, no verification is required.
            if ($this->input('avatar') === Storage::disk('uploads')->url($user->avatar)) {
                unset($rules['avatar']);
            }
        }

        return $rules;
    }

    /**
     * @return AdminUser
     */
    public function userResource()
    {
        return $this->route('admin_user');
    }

    public function attributes()
    {
        return [
            'username' => 'Username',
            'name' => 'Name',
            'password' => 'Password',
            'roles' => 'Role',
            'roles.*' => 'Role',
            'permissions' => 'Permission',
            'permissions.*' => 'Permission',
            'avatar' => 'Avatar',
        ];
    }
}

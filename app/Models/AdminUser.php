<?php

namespace App\Models;

use App\Traits\ModelHelpers;
use App\Utils\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use HasPermissions;
    use Notifiable;
    use ModelHelpers;
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
        'name',
        'avatar',
    ];

    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_user_role', 'user_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_user_permission', 'user_id', 'permission_id');
    }

    /**
     * Add user from request data
     *
     * @param array $inputs
     * @param bool  $hashedPassword
     *
     * @return AdminUser|\Illuminate\Database\Eloquent\Model
     */
    public static function createUser($inputs, $hashedPassword = false)
    {
        if (! $hashedPassword) {
            $inputs['password'] = bcrypt($inputs['password']);
        }

        return static::create($inputs);
    }

    /**
     * Update user from request data
     *
     * @param array $inputs
     * @param bool  $hashedPassword
     *
     * @return bool
     */
    public function updateUser($inputs, $hashedPassword = false)
    {
        if (isset($inputs['password']) && ! $hashedPassword) {
            $inputs['password'] = bcrypt($inputs['password']);
        }

        return $this->update($inputs);
    }
}

<?php
/**
 * 来自 laravel-admin
 */

namespace App\Utils;

use Illuminate\Support\Collection;

trait HasPermissions
{
    public function allPermissions()
    {
        return $this
            ->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->merge($this->permissions)
            ->unique('id')
            ->values();
    }

    public function can($ability, $arguments = [])
    {
        if ($this->isAdministrator()) {
            return true;
        }

        if ($this->permissions->pluck('slug')->contains($ability)) {
            return true;
        }

        return $this->roles->pluck('permissions')->flatten()->pluck('slug')->contains($ability);
    }

    public function cannot($ability, $arguments = [])
    {
        return ! $this->can($ability);
    }

    public function isAdministrator()
    {
        return $this->isRole('administrator');
    }

    public function isRole(string $role)
    {
        return $this->roles->pluck('slug')->contains($role);
    }

    public function inRoles($roles = [])
    {
        return $this->roles->pluck('slug')->intersect($roles)->isNotEmpty();
    }

    public function visible($roles = [])
    {
        if (empty($roles)) {
            return true;
        }

        $roles = array_column($roles, 'slug');

        return $this->inRoles($roles) || $this->isAdministrator();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->roles()->detach();

            $model->permissions()->detach();
        });
    }
}

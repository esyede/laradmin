<?php

namespace App\Http\Filters\Traits;

use Illuminate\Database\Eloquent\Builder;

trait RolePermissionFilter
{
    protected function roleName($value)
    {
        $this->builder->whereHas('roles', function (Builder $query) use ($value) {
            $query->where('name', 'like', '%' . $value . '%');
        }, '>', 0);
    }

    protected function permissionName($value)
    {
        $this->builder->whereHas('permissions', function (Builder $query) use ($value) {
            $query->where('name', 'like', '%' . $value . '%');
        }, '>', 0);
    }
}

<?php
/**
 * 来自 laravel-admin
 */

namespace App\Utils;

use App\Models\AdminPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionChecker
{
    public static function check($permission)
    {
        if (Admin::isAdministrator()) {
            return true;
        }

        if (is_array($permission)) {
            collect($permission)->each(function ($permission) {
                static::check($permission);
            });
        } elseif (Admin::user()->can($permission)) {
            return true;
        } else {
            static::error();
        }
    }

    public static function allow($roles)
    {
        if (Admin::isAdministrator()) {
            return true;
        }

        if (! Admin::user()->inRoles($roles)) {
            static::error();
        }

        return true;
    }

    public static function free()
    {
        return true;
    }

    public static function deny($roles)
    {
        if (Admin::isAdministrator()) {
            return true;
        }

        if (Admin::user()->inRoles($roles)) {
            static::error();
        }

        return true;
    }

    public static function error()
    {
        abort(403, 'Unauthorized access');
    }

    public static function shouldPassThrough(AdminPermission $permission, Request $request)
    {
        if (empty($permission->http_method) && empty($permission->http_path)) {
            return true;
        }

        $method = $permission->http_method;
        $matches = array_map(function ($path) use ($method) {
            if (Str::contains($path, ':')) {
                list($method, $path) = explode(':', $path);
                $method = explode(',', $method);
            }

            $path = 'admin-api' . $path;

            return compact('method', 'path');
        }, $permission->http_path);

        foreach ($matches as $match) {
            if (static::matchRequest($match, $request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检测请求的方法和路径是否匹配特定值
     *
     * @param array $match
     * @param Request $request
     *
     * @return bool
     */
    protected static function matchRequest(array $match, Request $request)
    {
        if (! $request->is(trim($match['path'], '/'))) {
            return false;
        }

        $method = collect($match['method'])->filter()->map(function ($method) {
            return strtoupper($method);
        });

        return $method->isEmpty() || $method->contains($request->method());
    }
}

<?php

namespace App\Utils;

use Illuminate\Support\Str;

class Admin
{
    public static function user()
    {
        return static::guard()->user();
    }

    public static function isAdministrator()
    {
        return static::user() && static::user()->isAdministrator();
    }

    public static function url($path = '')
    {
        $prefix = 'admin-api';
        $path = trim($path, '/');
        $path = (is_null($path) || strlen($path) === 0) ? $prefix : $prefix . '/' . $path;

        return $path;
    }

    public static function guard()
    {
        return auth('admin');
    }

    public static function urlWithMethod(string $path = ''): string
    {
        if (Str::contains($path, ':')) {
            list($method, $path) = explode(':', $path);
            return $method . ':' . static::url($path);
        } else {
            return static::url($path);
        }
    }
}

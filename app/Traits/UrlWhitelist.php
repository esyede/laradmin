<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait UrlWhitelist
{
    protected function urlWhitelist(): array
    {
        if (property_exists($this, 'urlWhitelist')) {
            return $this->urlWhitelist;
        } else {
            return [];
        }
    }

    protected function shouldPassThrough($request)
    {
        foreach ($this->urlWhitelist() as $url) {
            $methods = [];

            if (Str::contains($url, ':')) {
                [$methods, $url] = explode(':', $url);
                $methods = explode(',', $methods);
            }

            if ($url !== '/') {
                $url = trim($url, '/');
            }

            if ($this->isAnyMethod($request, $methods) && ($request->fullUrlIs($url) || $request->is($url))) {
                return true;
            }
        }

        return false;
    }

    protected function isAnyMethod(Request $request, array $methods = []): bool
    {
        if (empty($methods)) {
            return true;
        }

        foreach ($methods as $method) {
            if ($request->isMethod($method)) {
                return true;
            }
        }

        return false;
    }
}

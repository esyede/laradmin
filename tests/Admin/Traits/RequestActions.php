<?php

namespace Tests\Admin\Traits;

use Illuminate\Testing\TestResponse;
use Illuminate\Support\Str;

trait RequestActions
{
    protected $specialPluralSingularMap = [
        'system-media' => 'system-media',
    ];

    protected function routePrefix(): string
    {
        if (isset($this->routePrefix)) {
            return $this->routePrefix.'.';
        } else {
            return '';
        }
    }

    protected function route(string $name, $parameters = [], $absolute = true): string
    {
        return route($this->routePrefix().$name, $parameters, $absolute);
    }

    protected function varName(string $name): string
    {
        if ($t = $this->specialPluralSingularMap[$name] ?? null) {
            $name = $t;
        } else {
            $name = Str::singular($name);
        }

        return str_replace('-', '_', $name);
    }

    protected function getResources(array $params = [], string $name = null): TestResponse
    {
        $name = $name ?: $this->resourceName;

        return $this->get($this->route("{$name}.index", $params));
    }

    protected function storeResource(array $data = [], string $name = null, array $routeParams = []): TestResponse
    {
        $name = $name ?: $this->resourceName;

        return $this->post($this->route("{$name}.store", $routeParams), $data);
    }

    protected function destroyResource(int $id, string $name = null): TestResponse
    {
        $name = $name ?: $this->resourceName;

        return $this->delete($this->route("{$name}.destroy", [$this->varName($name) => $id]));
    }

    protected function getResource(int $id, string $name = null): TestResponse
    {
        $name = $name ?: $this->resourceName;

        return $this->get($this->route("{$name}.show", [$this->varName($name) => $id]));
    }

    protected function updateResource(int $id, array $data = [], string $name = null): TestResponse
    {
        $name = $name ?: $this->resourceName;

        return $this->put($this->route("{$name}.update", [$this->varName($name) => $id]), $data);
    }

    protected function editResource(int $id, string $name = null): TestResponse
    {
        $name = $name ?: $this->resourceName;

        return $this->get($this->route("{$name}.edit", [$this->varName($name) => $id]));
    }

    protected function createResource(string $name = null): TestResponse
    {
        $name = $name ?: $this->resourceName;

        return $this->get($this->route("{$name}.create"));
    }
}

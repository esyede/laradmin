<?php

namespace App\Http\Controllers\Admin;

use App\Http\Filters\ConfigFilter;
use App\Http\Requests\UpdateConfigValuesRequest;
use App\Http\Requests\ConfigRequest;
use App\Http\Resources\ConfigResource;
use App\Models\Config;
use App\Models\ConfigCategory;
use App\Models\VueRouter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ConfigController extends Controller
{
    public function vueRouters(VueRouter $vueRouter)
    {
        $resource = $vueRouter->treeWithAuth()->toTree();

        return $this->ok($resource);
    }

    public function destroy(Config $config)
    {
        $config->delete();

        return $this->noContent();
    }

    public function edit(Request $request, Config $config)
    {
        $resource = ConfigResource::make($config)
            ->additional($this->formData());

        return $this->ok($resource);
    }

    public function update(ConfigRequest $request, Config $config)
    {
        $inputs = $request->validated();
        $config->update($inputs);
        $resource = ConfigResource::make($config);

        return $this->created($resource);
    }

    public function index(ConfigFilter $filter)
    {
        $configs = Config::query()
            ->with('category')
            ->filter($filter)
            ->orderByDesc('id')
            ->paginate();

        $resource = ConfigResource::collection($configs);

        return $this->ok($resource);
    }

    /**
     * Add and edit data required by the form
     *
     * @return array
     */
    protected function formData()
    {
        return [
            'types_map' => Config::$typeMap,
            'categories' => ConfigCategory::query()->orderByDesc('id')->get(),
        ];
    }

    public function create()
    {
        $resource = $this->formData();

        return $this->ok($resource);
    }

    public function store(ConfigRequest $request)
    {
        $inputs = $request->validated();
        $config = Config::create($inputs);
        $resource = ConfigResource::make($config);

        return $this->created($resource);
    }

    public function getByCategorySlug(string $categorySlug)
    {
        $resource = ConfigResource::collection(Config::getByCategorySlug($categorySlug));

        return $this->ok($resource);
    }

    public function updateValues(UpdateConfigValuesRequest $request)
    {
        $configs = $request->getConfigs();
        $configs = Config::updateValues($configs, $request->validated());

        return $this->created($configs);
    }

    public function getValuesByCategorySlug(string $categorySlug)
    {
        return $this->ok(config(Config::CONFIG_KEY.'.'.$categorySlug), []);
    }

    public function cache()
    {
        Artisan::call('admin:cache-config');

        return $this->noContent();
    }
}

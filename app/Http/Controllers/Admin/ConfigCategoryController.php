<?php

namespace App\Http\Controllers\Admin;

use App\Http\Filters\ConfigCategoryFilter;
use App\Http\Requests\ConfigCategoryRequest;
use App\Http\Resources\ConfigCategoryResource;
use App\Models\ConfigCategory;
use Illuminate\Http\Request;

class ConfigCategoryController extends Controller
{
    public function index(Request $request, ConfigCategoryFilter $filter)
    {
        $categories = ConfigCategory::query()
            ->filter($filter)
            ->orderByDesc('id');

        $categories = $request->input('all') ? $categories->get() : $categories->paginate();
        $resource = ConfigCategoryResource::collection($categories);

        return $this->ok($resource);
    }

    public function store(ConfigCategoryRequest $request)
    {
        $inputs = $request->validated();
        $category = ConfigCategory::create($inputs);
        $resource = ConfigCategoryResource::make($category);

        return $this->created($resource);
    }

    public function update(ConfigCategoryRequest $request, ConfigCategory $configCategory)
    {
        $inputs = $request->validated();
        $configCategory->update($inputs);
        $resource = ConfigCategoryResource::make($configCategory);

        return $this->created($resource);
    }

    public function destroy(ConfigCategory $configCategory)
    {
        $configCategory->delete();

        return $this->noContent();
    }
}

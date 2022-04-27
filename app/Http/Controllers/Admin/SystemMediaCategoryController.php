<?php

namespace App\Http\Controllers\Admin;

use App\Http\Filters\SystemMediaFilter;
use App\Http\Requests\SystemMediaCategoryRequest;
use App\Http\Requests\SystemMediaRequest;
use App\Http\Resources\SystemMediaResource;
use App\Models\SystemMediaCategory;
use Illuminate\Http\Request;

class SystemMediaCategoryController extends Controller
{
    public function store(SystemMediaCategoryRequest $request)
    {
        $inputs = $request->validated();
        $category = SystemMediaCategory::create($inputs);

        return $this->created($category);
    }

    public function update(SystemMediaCategoryRequest $request, SystemMediaCategory $systemMediaCategory)
    {
        $inputs = $request->validated();
        $systemMediaCategory->update($inputs);

        return $this->created($systemMediaCategory);
    }

    public function edit(SystemMediaCategory $systemMediaCategory)
    {
        return $this->ok($systemMediaCategory);
    }

    public function destroy(SystemMediaCategory $systemMediaCategory)
    {
        $systemMediaCategory->delete();

        return $this->noContent();
    }

    public function index(Request $request, SystemMediaCategory $model)
    {
        $except = (int) $request->input('except');
        $resource = $model->treeExcept($except)->toTree();

        return $this->ok($resource);
    }

    /**
     * Upload files to the specified category
     *
     * @param SystemMediaRequest  $request
     * @param SystemMediaCategory $systemMediaCategory
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSystemMedia(SystemMediaRequest $request, SystemMediaCategory $systemMediaCategory)
    {
        $files = $this->saveFiles($request, $systemMediaCategory->folder);
        $media = $systemMediaCategory->media()->create($files['file']);
        $resource = SystemMediaResource::make($media);

        return $this->created($resource);
    }

    /**
     * Get all files under category
     *
     * @param SystemMediaCategory $systemMediaCategory
     * @param SystemMediaFilter   $filter
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function systemMediaIndex(SystemMediaCategory $systemMediaCategory, SystemMediaFilter $filter)
    {
        $media = $systemMediaCategory->media()
            ->filter($filter)
            ->orderByDesc('id')
            ->paginate();

        $resource = SystemMediaResource::collection($media);

        return $this->ok($resource);
    }
}

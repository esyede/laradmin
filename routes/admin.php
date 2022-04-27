<?php

use App\Http\Controllers\Admin as Admins;
use Illuminate\Support\Facades\Route;

if (config('app.env') === 'local') {
    Route::any('/test', [Admins\TestSomethingController::class, 'index']);
    Route::any('/{path}/test', [Admins\TestSomethingController::class, 'index'])->where('path', '.*');
}

Route::get('/admin/log-viewer', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->middleware('admin');
Route::get('/admin-dev/log-viewer', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->middleware('admin');

Route::get('/admin/{path?}', [Admins\RedirectController::class, 'index'])->where('path', '.*')->name('redirect.index');
Route::get('/admin-dev/{path?}', [Admins\RedirectController::class, 'indexDev'])->where('path', '.*')->name('redirect.index-dev');

Route::prefix('admin-api')
    ->middleware('admin')
    ->as('admin.')
    ->group(function () {
        Route::post('auth/login', [Admins\Auth\LoginController::class, 'login'])->name('login');

        Route::middleware([
            'admin.auth',
            'admin.permission',
        ])->group(function () {
            Route::post('auth/logout', [Admins\Auth\LoginController::class, 'logout'])->name('logout');

            Route::get('user', [Admins\AdminUserController::class, 'user'])->name('user');
            Route::get('user/edit', [Admins\AdminUserController::class, 'editUser'])->name('user.edit');
            Route::put('user', [Admins\AdminUserController::class, 'updateUser'])->name('user.update');

            Route::resource('admin-users', Admins\AdminUserController::class);

            Route::post('vue-routers/by-import', [Admins\VueRouterController::class, 'importVueRouters'])->name('vue-routers.by-import');
            Route::put('vue-routers', [Admins\VueRouterController::class, 'batchUpdate'])->name('vue-routers.batch.update');
            Route::resource('vue-routers', Admins\VueRouterController::class)->except(['show']);

            Route::resource('admin-permissions', Admins\AdminPermissionController::class)->except(['show']);
            Route::resource('admin-roles', Admins\AdminRoleController::class)->except(['show']);

            Route::resource('config-categories', Admins\ConfigCategoryController::class)->except(['show', 'create']);

            Route::post('configs/cache', [Admins\ConfigController::class, 'cache'])->name('configs.cache');

            Route::get('configs/vue-routers', [Admins\ConfigController::class, 'vueRouters'])->name('configs.vue-routers');

            Route::resource('configs', Admins\ConfigController::class)->except(['show']);

            Route::get('configs/{category_slug}', [Admins\ConfigController::class, 'getByCategorySlug'])->name('configs.by-category-slug');
            Route::get('configs/{category_slug}/values', [Admins\ConfigController::class, 'getValuesByCategorySlug'])
                ->name('configs.values.by-category-slug');

            Route::put('configs/{category_slug}/values', [Admins\ConfigController::class, 'updateValues'])
                ->name('configs.update-values');

            Route::resource('system-media-categories', Admins\SystemMediaCategoryController::class)->except(['show', 'create']);

            Route::post(
                'system-media-categories/{system_media_category}/system-media',
                [Admins\SystemMediaCategoryController::class, 'storeSystemMedia']
            )->name('system-media-categories.system-media.store');
            Route::get(
                'system-media-categories/{system_media_category}/system-media',
                [Admins\SystemMediaCategoryController::class, 'systemMediaIndex']
            )->name('system-media-categories.system-media.index');

            Route::resource('system-media', Admins\SystemMediaController::class)
                ->parameters(['system-media' => 'system_media'])
                ->except(['store', 'show', 'create']);

            Route::put('system-media', [Admins\SystemMediaController::class, 'batchUpdate'])->name('system-media.batch.update');
            Route::delete('system-media', [Admins\SystemMediaController::class, 'batchDestroy'])->name('system-media.batch.destroy');
        });
    });

<?php

namespace Tests\Admin;

use App\Http\Middleware\AdminPermission;
use App\Models\AdminUser;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

abstract class AdminTestCase extends TestCase
{
    protected $token;
    protected $routePrefix = 'admin';
    protected $user;
    protected $storage;
    protected $filesystem = 'uploads';

    protected function login(AdminUser $user = null)
    {
        $user = $user ?: AdminUser::factory()->create(['username' => 'admin']);
        $this->actingAs($user, 'admin');

        $this->user = $user;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkPermission(false);

        $this->storage = Storage::disk($this->filesystem);
    }

    protected function checkPermission($check)
    {
        if ($check) {
            $ins = new MyDummyAdminCheckPermissionOne();
        } else {
            $ins = new MyDummyAdminCheckPermissionTwo();
        }

        $this->app->singleton(AdminPermission::class, function () use ($ins) {
            return $ins;
        });
    }

    protected function getLastInsertId(string $table = null)
    {
        if (is_null($table)) {
            return DB::getPdo()->lastInsertId();
        } else {
            return DB::table($table)->orderByDesc('id')->value('id');
        }
    }

    protected function reloadAdminConfig()
    {
        Config::clearConfigCache();
        Config::loadToConfig();
    }
}


class MyDummyAdminCheckPermissionOne extends AdminPermission
{
    protected $urlWhitelist = [
        '/test-resources/pass-through',
        'get:/test-resources/pass-through-get-put',
    ];
}

class MyDummyAdminCheckPermissionTwo extends AdminPermission
{
    public function handle(Request $request, \Closure $next, ...$args)
    {
        return $next($request);
    }
}

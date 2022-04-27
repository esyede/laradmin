<?php

namespace App\Console\Commands;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\AdminUser;
use App\Models\Config;
use App\Models\ConfigCategory;
use App\Models\VueRouter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AdminInstallCommand extends Command
{
    protected $signature = 'admin:install';
    protected $description = 'Install basic routing, accout and permission configuration';

    public static $initConfirmTip = 'The command will clear the routing, role and permission tables. Are you sure?';

    public function handle()
    {
        if ($this->confirm(static::$initConfirmTip)) {
            $this->createVueRouters();
            $this->createUserRolePermission();
            $this->createDefaultConfigs();
            $this->call(CacheConfig::class);
            $this->info('Done! login: admin, password: password');
            return 1;
        } else {
            return 0;
        }
    }

    protected function createVueRouters()
    {
        $inserts = [
            [1, 0, 'Dashboard', 'index', 0, null, 1],

            [2, 0, 'Routes', null, 1, null, 1],
            [3, 2, 'All routes', 'vue-routers', 2, null, 1],
            [4, 2, 'Add route', 'vue-routers/create', 3, null, 1],
            [5, 2, 'Edit route', 'vue-routers/:id(\\d+)/edit', 4, null, 0],

            [6, 0, 'Users', null, 5, null, 1],
            [7, 6, 'User list', 'admin-users', 6, null, 1],
            [8, 6, 'Add user', 'admin-users/create', 7, null, 1],
            [9, 6, 'Edit user', 'admin-users/:id(\\d+)/edit', 8, null, 0],

            [10, 0, 'Roles', null, 9, null, 1],
            [11, 10, 'Role list', 'admin-roles', 10, null, 1],
            [12, 10, 'Add role', 'admin-roles/create', 11, null, 1],
            [13, 10, 'Edit role', 'admin-roles/:id(\\d+)/edit', 12, null, 0],

            [14, 0, 'Permissions', null, 13, null, 1],
            [15, 14, 'Permission list', 'admin-permissions', 14, null, 1],
            [16, 14, 'Add permission', 'admin-permissions/create', 15, null, 1],
            [17, 14, 'Edit permission', 'admin-permissions/:id(\\d+)/edit', 16, null, 0],

            [18, 0, 'Files', 'system-media', 17, null, 1],

            [19, 0, 'Configs', null, 18, null, 1],
            [20, 19, 'Categories', 'config-categories', 19, null, 1],
            [21, 19, 'All config', 'configs', 20, null, 1],
            [22, 19, 'Add config', 'configs/create', 21, null, 1],
            [23, 19, 'Edit config', 'configs/:id(\\d+)/edit', 22, null, 0],

            [24, 0, 'Settings', '/configs/system_basic', 23, null, 1],
        ];

        $inserts = $this->combineInserts(
            ['id', 'parent_id', 'title', 'path', 'order', 'icon', 'menu'],
            $inserts,
            [
                'cache' => 0,
                'created_at' => now(),
                'updated_at' => now(),
                'permission' => null,
            ]
        );

        VueRouter::truncate();
        VueRouter::insert($inserts);
    }

    protected function createUserRolePermission()
    {
        AdminUser::truncate();
        AdminRole::truncate();
        AdminPermission::truncate();

        collect(['admin_role_permission', 'admin_user_permission', 'admin_user_role', 'vue_router_role'])
            ->each(function ($table) {
                DB::table($table)->truncate();
            });

        $user = AdminUser::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $user->roles()->create([
            'name' => 'Superadmin',
            'slug' => 'administrator',
        ]);

        AdminRole::first()
            ->permissions()
            ->create([
                'name' => 'All',
                'slug' => 'pass-all',
                'http_path' => '*',
            ]);
    }

    protected function createDefaultConfigs()
    {
        $categories = [
            [1, 'Settings', 'system_basic'],
        ];
        $categories = $this->combineInserts(
            ['id', 'name', 'slug'],
            $categories,
            ['created_at' => now(), 'updated_at' => now()]
        );
        $configs = [
            [1, Config::TYPE_INPUT, 'App Name', 'app_name', null, json_encode('Dashboard'), 'required|string|max:20'],
            [1, Config::TYPE_FILE, 'App Logo', 'app_logo', '{"max":1,"ext":"jpg,png,jpeg"}', null, 'nullable|string'],
            [1, Config::TYPE_OTHER, 'Home Route', 'home_route', null, json_encode('1'), 'required|exists:vue_routers,id'],
            [1, Config::TYPE_INPUT, 'CDN Domain', 'cdn_domain', null, json_encode('/'), 'required|string'],
            [
                1, Config::TYPE_SINGLE_SELECT, 'Captcha', 'admin_login_captcha',
                json_encode(['options' => "1=>On\n0=>Off", 'type' => 'input']),
                json_encode('1'), 'required|string',
            ],
        ];
        $configs = $this->combineInserts(
            ['category_id', 'type', 'name', 'slug', 'options', 'value', 'validation_rules'],
            $configs,
            ['created_at' => now(), 'updated_at' => now()]
        );

        ConfigCategory::truncate();
        ConfigCategory::insert($categories);

        Config::truncate();
        Config::insert($configs);
    }

    protected function combineInserts(array $fields, array $inserts, array $extra): array
    {
        return array_map(function ($item) use ($fields, $extra) {
            $item = array_combine($fields, $item);
            return array_merge($item, $extra);
        }, $inserts);
    }
}

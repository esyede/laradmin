<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;

class AdminUsersTableSeeder extends Seeder
{
    public function run()
    {
        AdminUser::factory(10)->create();
        $admin = null;

        if (! AdminUser::query()->where('username', 'admin')->exists()) {
            $admin = AdminUser::query()->first();
        }

        if ($admin) {
            $admin->update([
                'username' => 'admin',
                'name' => 'Superadmin',
            ]);
        }
    }
}

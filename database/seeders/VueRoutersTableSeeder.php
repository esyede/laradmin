<?php

namespace Database\Seeders;

use App\Models\VueRouter;
use Illuminate\Database\Seeder;

class VueRoutersTableSeeder extends Seeder
{
    public function run()
    {
        VueRouter::truncate();

        VueRouter::factory()->create([
            'path' => '/index',
            'title' => 'Dashboard',
            'order' => 0,
        ]);

        $vueRouters = VueRouter::factory(9)->make(['created_at' => now(), 'updated_at' => now()])->toArray();

        foreach ($vueRouters as $i => &$router) {
            $parentId = ($i < 3) ? 0 : ($i - 1);
            $router['parent_id'] = $parentId;
        }

        unset($router);

        VueRouter::insert($vueRouters);
    }
}

<?php

namespace Database\Seeders;

use App\Models\SystemMediaCategory;
use Illuminate\Database\Seeder;

class SystemMediaCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = SystemMediaCategory::factory(5)->create();
        $categories->each(function (SystemMediaCategory $item) {
            $item->children()->createMany(SystemMediaCategory::factory(2)->make()->toArray());
        });
    }
}

<?php

namespace Database\Seeders;

use App\Models\SystemMedia;
use App\Models\SystemMediaCategory;
use Faker\Generator;
use Illuminate\Database\Seeder;

class SystemMediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $media = SystemMedia::factory(20)->create();
        $categoryIds = SystemMediaCategory::pluck('id')->toArray();

        if (! empty($categoryIds)) {
            $faker = app(Generator::class);
            $media->each(function (SystemMedia $item) use ($categoryIds, $faker) {
                $item->update(['category_id' => $faker->randomElement($categoryIds)]);
            });
        }
    }
}

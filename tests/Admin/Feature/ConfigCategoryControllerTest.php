<?php

namespace Tests\Admin\Feature;

use App\Models\Config;
use App\Models\ConfigCategory;
use Tests\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\Traits\RequestActions;

class ConfigCategoryControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use RequestActions;

    protected $resourceName = 'config-categories';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    public function testStoreValidation()
    {
        $res = $this->storeResource(['name' => '']);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $res = $this->storeResource(['name' => []]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $res = $this->storeResource(['name' => str_repeat('a', 51)]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        ConfigCategory::factory()->create(['name' => 'name']);

        $res = $this->storeResource(['name' => 'name']);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $res = $this->storeResource(['slug' => 'test']);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    public function testStore()
    {
        $res = $this->storeResource(['name' => 'name', 'slug' => 'slug']);
        $res->assertStatus(201);
        $this->assertDatabaseHas('config_categories', [
            'id' => $this->getLastInsertId('config_categories'),
            'name' => 'name',
            'slug' => 'slug',
        ]);
    }

    public function testUpdate()
    {
        $id = ConfigCategory::factory()->create(['name' => 'name'])->id;
        $res = $this->updateResource($id, ['name' => 'name']);
        $res->assertStatus(201);

        $res = $this->updateResource($id, ['name' => 'new', 'slug' => 'new']);
        $res->assertStatus(201);
        $this->assertDatabaseHas('config_categories', [
            'id' => $id,
            'name' => 'new',
            'slug' => 'new',
        ]);
    }

    public function testDestroy()
    {
        $categoryId1 = ConfigCategory::factory()->create()->id;
        $categoryId2 = ConfigCategory::factory()->create()->id;
        $configId = Config::factory()->make(['category_id' => $categoryId2])->id;

        $res = $this->destroyResource($categoryId2);
        $res->assertStatus(204);
        $this->assertDatabaseMissing('config_categories', ['id' => $categoryId2]);
        $this->assertDatabaseMissing('configs', ['id' => $configId]);

        $res = $this->destroyResource($categoryId1);
        $res->assertStatus(204);
        $this->assertDatabaseMissing('config_categories', ['id' => $categoryId1]);
    }

    public function testIndex()
    {
        ConfigCategory::insert(ConfigCategory::factory(20)->make()->toArray());
        ConfigCategory::first()->update(['name' => 'test query name']);
        ConfigCategory::offset(1)->first()->update(['name' => 'test query name 2']);

        $res = $this->getResources(['all' => 1]);
        $res->assertStatus(200)
            ->assertJsonCount(20);

        $res = $this->getResources(['name' => 'query']);
        $res->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}

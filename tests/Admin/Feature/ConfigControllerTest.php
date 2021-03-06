<?php

namespace Tests\Admin\Feature;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\Config;
use App\Models\ConfigCategory;
use App\Models\VueRouter;
use Tests\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\Traits\RequestActions;

class ConfigControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use RequestActions;

    protected $resourceName = 'configs';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    protected function getConfig(string $config)
    {
        return $this->get(route("admin.configs.{$config}"));
    }

    protected function prepareVueRouters()
    {
        $ids = VueRouter::factory(5)->create()->pluck('id');
        VueRouter::find($ids[1])->children()->save(VueRouter::find($ids[2]));
        VueRouter::find($ids[3])->children()->save(VueRouter::find($ids[4]));

        return $ids;
    }

    public function testVueRoutersWithoutAuth()
    {
        $this->prepareVueRouters();

        $res = $this->getConfig('vue-routers');
        $res->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function testVueRoutersUserNoAuth()
    {
        $ids = $this->prepareVueRouters();

        VueRouter::find($ids[0])->roles()->create(
            AdminRole::factory()->create(['slug' => 'role-router-1'])->toArray()
        );

        VueRouter::find($ids[2])->update([
            'permission' => AdminPermission::factory()->create(['slug' => 'perm-router-3'])->slug,
        ]);

        $res = $this->getConfig('vue-routers');
        $res->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonMissing(['id' => $ids[2]]);
    }

    public function testVueRoutersUserHasAuth()
    {
        $this->prepareVueRouters();

        $this->user->roles()->attach(1);
        $this->user->permissions()->attach(1);

        $res = $this->getConfig('vue-routers');
        $res->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function testDestroy()
    {
        Config::factory()->create();

        $res = $this->destroyResource(1);
        $res->assertStatus(204);

        $this->assertDatabaseMissing('configs', ['id' => 1]);
    }

    public function testEdit()
    {
        $id = Config::factory()->create()->id;

        $res = $this->editResource($id);
        $res->assertStatus(200);
    }

    public function testUpdate()
    {
        $categoryId = ConfigCategory::factory()->create()->id;
        $configId = Config::factory()->create([
            'name' => 'name',
            'slug' => 'slug',
            'type' => Config::TYPE_INPUT,
        ])->id;

        $res = $this->updateResource($configId, ['category_id' => -1]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);

        $res = $this->updateResource($configId, ['name' => 'name1']);
        $res->assertStatus(201)
            ->assertJsonMissingValidationErrors(['name']);

        $inputs = [
            'name' => 'new name',
            'type' => Config::TYPE_TEXTAREA,
            'slug' => 'new_slug',
            'category_id' => $categoryId,
            'desc' => 'new desc',
            'value' => 'new value',
            'validation_rules' => 'required',
        ];

        $res = $this->updateResource($configId, $inputs);
        $res->assertStatus(201);

        $expectData = array_merge($inputs, [
            'type' => Config::TYPE_TEXTAREA,
            'slug' => 'new_slug',
            'value' => json_encode('new value'),
        ]);

        $this->assertDatabaseHas('configs', $expectData);
    }

    public function testIndex()
    {
        ConfigCategory::factory(2)->create()
            ->each(function (ConfigCategory $cate) {
                $cate->configs()->createMany(Config::factory(2)->make()->toArray());
            });

        $res = $this->getResources();
        $res->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }

    public function testStoreValidation()
    {
        ConfigCategory::factory()->create();
        Config::factory()->create([
            'name' => 'name',
            'slug' => 'slug',
        ]);

        $res = $this->storeResource(['desc' => [], 'validation_rules' => []]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['type', 'name', 'slug', 'desc', 'validation_rules', 'category_id']);

        $res = $this->storeResource([
            'type' => 'not in',
            'category_id' => '-999',
            'name' => [],
            'slug' => [],
            'desc' => str_repeat('a', 256),
            'validation_rules' => str_repeat('a', 256),
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['type', 'name', 'slug', 'desc', 'validation_rules']);

        $res = $this->storeResource([
            'type' => Config::TYPE_INPUT,
            'name' => str_repeat('a', 51),
            'slug' => str_repeat('a', 51),
            'validation_rules' => 'required|invalid_rule1|string|invalid_rule2',
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug', 'validation_rules'])
            ->assertSeeText('invalid_rule1, invalid_rule2');

        $res = $this->storeResource([
            'type' => Config::TYPE_INPUT,
            'name' => 'name',
            'slug' => 'slug',
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug']);
    }

    public function testConfigOptionsValidation()
    {
        $res = $this->storeResource(['options' => null]);
        $res->assertJsonMissingValidationErrors(['options']);
        $types = [Config::TYPE_INPUT, Config::TYPE_TEXTAREA, Config::TYPE_OTHER];

        foreach ($types as $type) {
            $res = $this->storeResource(['type' => $type, 'options' => 'not null']);
            $res->assertJsonValidationErrors(['options']);
        }

        $optionsInputs = [null, 'not number', '-1'];

        foreach ($optionsInputs as $max) {
            $res = $this->storeResource([
                'type' => Config::TYPE_FILE,
                'options' => ['max' => $max, 'ext' => null],
            ]);

            $res->assertStatus(422)
                ->assertJsonValidationErrors('options');
        }

        foreach ([null, [], "=>\n=>"] as $options) {
            $res = $this->storeResource([
                'type' => Config::TYPE_SINGLE_SELECT,
                'options' => ['options' => $options, 'type' => 'input'],
            ]);

            $res->assertStatus(422)
                ->assertJsonValidationErrors('options');
        }

        foreach ([null, 'not in'] as $type) {
            $res = $this->storeResource([
                'type' => Config::TYPE_SINGLE_SELECT,
                'options' => ['options' => '1=>value1', 'type' => $type],
            ]);

            $res->assertStatus(422)
                ->assertJsonValidationErrors('options');
        }
    }

    public function testStore()
    {
        $categoryId = ConfigCategory::factory()->create()->id;

        $inputs = Config::factory()->make()->toArray();
        $inputs['category_id'] = $categoryId;

        $res = $this->storeResource($inputs);
        $res->assertStatus(201);

        $this->assertDatabaseHas('configs', [
            'id' => $this->getLastInsertId('configs'),
            'category_id' => $categoryId,
        ]);
    }

    public function testGetByCategorySlug()
    {
        $category = ConfigCategory::factory()->create();
        Config::factory(2)->create(['category_id' => $category->id]);

        $res = $this->get(route('admin.configs.by-category-slug', ['category_slug' => $category->slug]));

        $res->assertStatus(200)
            ->assertJsonCount(2);

        $res = $this->get(route('admin.configs.by-category-slug', ['category_slug' => 'not exists slug']));
        $res->assertStatus(200)
            ->assertJsonCount(0);
    }

    public function testUpdateValues()
    {
        $category = ConfigCategory::factory()->create();
        Config::factory()->create([
            'slug' => 'field',
            'validation_rules' => 'required|max:20',
            'category_id' => $category->id,
        ]);

        $res = $this->put(route('admin.configs.update-values', ['category_slug' => $category->slug]), ['field' => null]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['field']);

        $res = $this->put(route('admin.configs.update-values', ['category_slug' => $category->slug]), ['field' => 'new value']);
        $res->assertStatus(201);
        $this->assertDatabaseHas('configs', [
            'slug' => 'field',
            'value' => json_encode('new value'),
        ]);
    }

    public function testGetValuesByCategorySlug()
    {
        $category = ConfigCategory::factory()->create(['slug' => 'slug']);
        Config::factory()->create([
            'slug' => 'field',
            'type' => Config::TYPE_FILE,
            'category_id' => $category->id,
            'value' => 'uploads/test/logo.png',
        ]);

        $this->reloadAdminConfig();

        $res = $this->get(route('admin.configs.values.by-category-slug', ['category_slug' => 'slug']));
        $res->assertStatus(200)
            ->assertJson(['field' => 'uploads/test/logo.png']);
    }

    public function testCreate()
    {
        ConfigCategory::factory()->create(['slug' => 'slug']);

        $res = $this->createResource();
        $res->assertStatus(200)
            ->assertJsonFragment(['slug' => 'slug', 'types_map' => Config::$typeMap]);
    }
}

<?php

namespace Tests\Admin\Feature;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\VueRouter;
use Database\Seeders\VueRoutersTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\AdminTestCase;
use Tests\Admin\Traits\RequestActions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VueRouterControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use WithFaker;
    use RequestActions;

    protected $resourceName = 'vue-routers';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    public function testStoreValidation()
    {
        $res = $this->storeResource([
            'title' => '',
            'order' => 15.1,
            'cache' => 'not bool',
            'menu' => 'not bool',
            'permission' => 'not exists',
            'roles' => [999],
        ]);

        $res->assertJsonValidationErrors(['title', 'order', 'cache', 'menu', 'permission', 'roles.0']);

        $res = $this->storeResource(['permission' => '']);
        $res->assertJsonMissingValidationErrors(['permission']);

        $res = $this->storeResource([
            'title' => str_repeat('a', 51),
            'icon' => str_repeat('a', 51),
            'path' => str_repeat('a', 51),
            'order' => 10000,
        ]);

        $res->assertJsonValidationErrors(['icon', 'path', 'title', 'order']);

        $res = $this->storeResource(['order' => -10000]);
        $res->assertJsonValidationErrors(['order']);

        VueRouter::factory()->create();

        $res = $this->storeResource(['parent_id' => 999]);
        $res->assertJsonValidationErrors('parent_id');
    }

    public function testStore()
    {
        $routerId1 = VueRouter::factory()->create()->id;
        $permissionId = AdminPermission::factory()->create(['slug' => 'slug'])->id;
        $roleId = AdminRole::factory()->create()->id;
        $inputs = VueRouter::factory()->make([
            'parent_id' => $routerId1,
            'path' => 'no/start/slash',
            'permission' => 'slug',
        ])->toArray();

        $res = $this->storeResource($inputs + ['roles' => [$roleId]]);
        $res->assertStatus(201);

        $routerId2 = $this->getLastInsertId('vue_routers');
        $this->assertDatabaseHas(
            'vue_routers',
            array_merge($inputs, [
                'id' => $routerId2,
                'parent_id' => $routerId1,
                'permission' => 'slug',
            ])
        );

        $this->assertDatabaseHas('vue_router_role', [
            'vue_router_id' => $routerId2,
            'role_id' => $roleId,
        ]);

        $inputs['parent_id'] = null;
        $res = $this->storeResource($inputs);
        $res->assertStatus(201);
        $this->assertDatabaseHas('vue_routers', [
            'id' => $this->getLastInsertId('vue_routers'),
            'parent_id' => 0,
        ]);
    }

    public function testUpdate()
    {
        $routerIds = VueRouter::factory(2)->create()->pluck('id');
        $roleIds = AdminRole::factory(2)->create()->pluck('id');
        VueRouter::find($routerIds[1])->roles()->attach($roleIds[0]);

        $inputs = [
            'parent_id' => $routerIds[1],
            'title' => 'new title',
            'icon' => 'new icon',
            'path' => 'new/path',
            'order' => 99,
        ];
        $res = $this->updateResource($routerIds[0], $inputs + ['roles' => [$roleIds[1]]]);
        $res->assertStatus(201);

        $this->assertDatabaseHas('vue_routers', ['id' => $routerIds[0]] + $inputs);
        $this->assertDatabaseHas('vue_router_role', [
            'vue_router_id' => $routerIds[0],
            'role_id' => $roleIds[1],
        ]);
        $this->assertDatabaseMissing('vue_router_role', [
            'vue_router_id' => $routerIds[0],
            'role_id' => $roleIds[0],
        ]);

        $res = $this->updateResource($routerIds[0], ['roles' => []]);
        $res->assertStatus(201);
        $this->assertDatabaseMissing('vue_router_role', [
            'vue_router_id' => $routerIds[0],
            'role_id' => $roleIds[1],
        ]);
    }

    public function testEdit()
    {
        $router = VueRouter::factory()->create()->toArray();
        $res = $this->editResource($router['id']);
        $res->assertStatus(200)
            ->assertJsonFragment($router);
    }

    public function testIndex()
    {
        app(VueRoutersTableSeeder::class)->run();

        $vueRouter = VueRouter::with([
            'children',
            'children.children',
            'children.children.children',
        ])->find(2);

        $vueRouter = $vueRouter->toArray();

        $res = $this->getResources();
        $res->assertStatus(200)
            ->assertJsonFragment($vueRouter);

        $except = $vueRouter['children'][0]['id'];
        $fragment = $vueRouter;

        unset($fragment['children']);

        $res = $this->getResources(['except' => $except]);
        $res->assertStatus(200)
            ->assertJsonFragment($fragment);
    }

    public function testDestroy()
    {
        $this->destroyResource(999)->assertStatus(404);

        app(VueRoutersTableSeeder::class)->run();
        $vueRouter = VueRouter::with(['children', 'children.children'])->find(2);

        $this->destroyResource($vueRouter->id)->assertStatus(204);

        $this->assertDatabaseMissing('vue_routers', ['id' => $vueRouter->id]);
        $this->assertDatabaseMissing('vue_routers', ['id' => $vueRouter['children'][0]['id']]);
        $this->assertDatabaseMissing('vue_routers', ['id' => $vueRouter['children'][0]['children'][0]['id']]);
    }

    public function testBatchUpdateOrder()
    {
        $ids = VueRouter::factory(3)->create()->pluck('id');

        $res = $this->put($this->route('vue-routers.batch.update'), [
            '_order' => [
                ['id' => $ids[2], 'children' => [['id' => $ids[0]]]],
                ['id' => $ids[1]],
            ],
        ]);

        $res->assertStatus(201);

        $this->assertDatabaseHas('vue_routers', [
            'id' => $ids[0],
            'parent_id' => $ids[2],
            'order' => 2,
        ]);
    }

    public function testCreate()
    {
        VueRouter::factory()->create(['title' => 'title'])->id;
        AdminPermission::factory()->create(['slug' => 'permission'])->id;
        AdminRole::factory()->create(['name' => 'role'])->id;

        $res = $this->createResource();
        $res->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'title',
                'slug' => 'permission',
                'name' => 'role',
            ]);
    }

    public function testImportVueRouters()
    {
        $url = $this->route('vue-routers.by-import');
        Storage::fake('local');
        $disk = Storage::disk('local');

        $res = $this->post($url);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['file']);

        $res = $this->post($url, ['file' => UploadedFile::fake()->create('tree.json', 10)]);
        $res->assertStatus(400);

        $item1 = VueRouter::factory()->make(['id' => 1]);
        $item2 = VueRouter::factory()->make([
            'id' => 2,
            'children' => [VueRouter::factory()->make(['id' => 3, 'parent_id' => 2])],
        ]);

        $content = json_encode([$item1, $item2]);
        $disk->put('tree.json', $content);

        UploadedFile::fake()->create('test');

        $file = new UploadedFile($disk->path('tree.json'), 'tree.json', null, null, true);
        $res = $this->post($url, ['file' => $file]);
        $res->assertStatus(201)
            ->assertJson(json_decode($content, true));
    }
}

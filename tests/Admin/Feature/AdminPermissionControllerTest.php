<?php

namespace Tests\Admin\Feature;

use App\Models\AdminPermission;
use Tests\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\Traits\RequestActions;

class AdminPermissionControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use WithFaker;
    use RequestActions;
    protected $resourceName = 'admin-permissions';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    public function testStoreValidation()
    {
        $res = $this->storeResource([
            'name' => '',
            'slug' => '',
            'http_method' => 'not array',
            'http_path' => "ERR:err/err",
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug', 'http_method', 'http_path']);

        AdminPermission::factory()->create(['slug' => 'slug']);
        AdminPermission::factory()->create(['name' => 'name']);

        $res = $this->storeResource([
            'name' => 'name',
            'slug' => 'slug',
            'http_method' => ['GET', 'TEST'],
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug', 'http_method.1']);
    }

    public function testStore()
    {
        $model = AdminPermission::factory()->make();
        $this->assertStore($model);

        $model = AdminPermission::factory()->make([
            'http_method' => null,
            'http_path' => null,
        ]);

        $this->assertStore($model);
    }

    protected function assertStore(AdminPermission $model)
    {
        $inputs = $model->toArray();
        $inputs['http_path'] = implode("\n", $inputs['http_path']);
        $res = $this->storeResource($inputs);
        $res->assertStatus(201);

        $this->assertDatabaseHas('admin_permissions', array_merge($model->getAttributes()));
    }

    public function testIndex()
    {
        AdminPermission::factory(20)->create();

        $res = $this->getResources();
        $res->assertStatus(200)
            ->assertJsonFragment(['total' => 20])
            ->assertJsonFragment(['last_page' => 2]);

        $id = AdminPermission::factory()->create([
            'http_path' => 'path/to/query',
            'slug' => 'slug query',
            'name' => 'name query',
        ])->id;

        $res = $this->getResources(['id' => $id]);
        $res->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => $id]);

        $res = $this->getResources([
            'id' => $id,
            'http_path' => 'to',
            'slug' => 'slug',
            'name' => 'name',
        ]);

        $res->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => $id]);

        $res = $this->getResources(['all' => 1, 'only' => ['id', 'name']]);
        $res->assertJsonCount(21);
    }

    public function testEdit()
    {
        $res = $this->editResource(99999);
        $res->assertStatus(404);

        $id = AdminPermission::factory()->create()->id;
        $res = $this->editResource($id);
        $res->assertStatus(200)->assertJsonFragment(['id' => $id]);
    }

    public function testUpdate()
    {
        $id1 = AdminPermission::factory()->create()->id;
        $id2 = AdminPermission::factory()->create([
            'name' => 'name',
            'slug' => 'slug',
        ])->id;

        $res = $this->updateResource($id1, [
            'name' => 'name',
            'slug' => 'slug',
        ]);

        $res->assertStatus(422)->assertJsonValidationErrors(['name', 'slug']);

        $inputs = [
            'slug' => 'new slug',
            'http_path' => null,
            'http_method' => null,
        ];

        $res = $this->updateResource($id2, $inputs);
        $res->assertStatus(201);

        $this->assertDatabaseHas('admin_permissions', $inputs + ['id' => $id2, 'name' => 'name']);
    }

    public function testDestroy()
    {
        $id = AdminPermission::factory()->create()->id;

        $res = $this->destroyResource($id);
        $res->assertStatus(204);

        $this->assertDatabaseMissing('admin_permissions', ['id' => $id]);
    }
}

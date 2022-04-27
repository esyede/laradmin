<?php

namespace Tests\Admin\Feature;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use Database\Seeders\AdminRolePermissionTableSeeder;
use Illuminate\Support\Arr;
use Tests\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\Traits\RequestActions;

class AdminRoleControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use RequestActions;
    use WithFaker;

    protected $resourceName = 'admin-roles';

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
            'permissions' => 'not array',
        ]);

        $res->assertJsonValidationErrors(['name', 'slug', 'permissions']);

        AdminRole::factory()->create(['name' => 'name', 'slug' => 'slug']);
        AdminPermission::factory()->create();

        $res = $this->storeResource([
            'name' => 'name',
            'slug' => 'slug',
            'permissions' => [99],
        ]);

        $res->assertJsonValidationErrors(['name', 'slug', 'permissions']);
    }

    public function testStore()
    {
        $inputs = [
            'name' => 'name',
            'slug' => 'slug',
        ];

        $res = $this->storeResource($inputs);
        $res->assertStatus(201);
        $this->assertDatabaseHas('admin_roles', $inputs);

        $inputs = [
            'name' => 'name1',
            'slug' => 'slug2',
        ];

        $permissionId = AdminPermission::factory()->create()->id;
        $res = $this->storeResource($inputs + ['permissions' => [$permissionId]]);
        $res->assertStatus(201);

        $this->assertDatabaseHas('admin_role_permission', [
            'role_id' => $this->getLastInsertId('admin_roles'),
            'permission_id' => $permissionId,
        ]);
    }

    protected function createRole()
    {
        $role = AdminRole::factory()->create();
        $permissionId = AdminPermission::factory()->create()->id;

        $role->permissions()->attach($permissionId);

        return [$role->id, $permissionId];
    }

    public function testEdit()
    {
        [$roleId, $_] = $this->createRole();

        $res = $this->editResource($roleId);
        $res->assertStatus(200)
            ->assertJsonFragment(AdminRole::first()->toArray())
            ->assertJsonCount(1, 'permissions');
    }

    public function testUpdate()
    {
        list($roleId1, $permissionId1) = $this->createRole();

        $inputs = AdminRole::first()->toArray();
        $res = $this->updateResource($roleId1, $inputs + ['permissions' => []]);
        $res->assertStatus(201);
        $this->assertDatabaseHas('admin_roles', $inputs);
        $this->assertDatabaseMissing('admin_role_permission', ['role_id' => $roleId1, 'permission_id' => $permissionId1]);

        list($roleId2, $permissionId2) = $this->createRole();

        $inputs = [
            'name' => 'new name',
            'slug' => 'new slug',
            'permissions' => [$permissionId1],
        ];

        $res = $this->updateResource($roleId2, $inputs);
        $res->assertStatus(201);
        $this->assertDatabaseHas('admin_roles', Arr::except($inputs, 'permissions'));
        $this->assertDatabaseHas('admin_role_permission', [
            'role_id' => $roleId2,
            'permission_id' => $permissionId1,
        ]);

        $this->assertDatabaseMissing('admin_role_permission', [
            'role_id' => $roleId2,
            'permission_id' => $permissionId2,
        ]);

        $res = $this->updateResource($roleId2, ['permissions' => []]);
        $res->assertStatus(201);
        $this->assertDatabaseMissing('admin_role_permission', ['role_id' => $roleId2]);
    }

    public function testDestroy()
    {
        list($roleId, $permissionId) = $this->createRole();
        $res = $this->destroyResource($roleId);
        $res->assertStatus(204);
        $this->assertDatabaseMissing('admin_roles', ['id' => $roleId]);
        $this->assertDatabaseMissing('admin_role_permission', ['role_id' => $roleId, 'permission_id' => $permissionId]);
    }

    public function testIndex()
    {
        AdminRole::factory(20)->create();
        AdminPermission::factory(5)->create();
        app(AdminRolePermissionTableSeeder::class)->run();
        $res = $this->getResources();
        $res->assertStatus(200)
            ->assertJsonCount(15, 'data')
            ->assertJsonCount(AdminRole::orderByDesc('id')->first()->permissions->count(), 'data.0.permissions');

        AdminRole::factory()
            ->create(['name' => 'role name query', 'slug' => 'role slug query'])
            ->permissions()
            ->create(AdminPermission::factory()->create(['name' => 'perm name query'])->toArray());

        $res = $this->getResources([
            'id' => $this->getLastInsertId('admin_roles'),
            'name' => 'role name',
            'slug' => 'role slug',
        ]);

        $res->assertJsonCount(1, 'data');

        $res = $this->getResources(['permission_name' => 'perm name']);
        $res->assertJsonCount(1, 'data');
        $res = $this->getResources(['permission_name' => 'nothing']);
        $res->assertJsonCount(0, 'data');
    }

    public function testCreate()
    {
        AdminPermission::factory()->create(['name' => 'name']);

        $res = $this->createResource();
        $res->assertStatus(200)
            ->assertJsonFragment(['name' => 'name']);
    }
}

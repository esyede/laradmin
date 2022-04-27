<?php

namespace Tests\Admin\Feature;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;
use Tests\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\Traits\RequestActions;

class AdminUserControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use RequestActions;

    protected $resourceName = 'admin-users';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    protected function attachAuthToUser(AdminUser $user = null)
    {
        $user = $user ?? $this->user;

        $permission1 = AdminPermission::factory()->create(['slug' => 'perm1'])->id;
        $permission2 = AdminPermission::factory()->create(['slug' => 'perm2'])->id;
        $role = AdminRole::factory()->create(['slug' => 'role']);
        $role->permissions()->attach($permission1);
        $user->roles()->attach($role->id);
        $user->permissions()->attach($permission2);

        return [$role->id, $permission1, $permission2];
    }

    public function testUser()
    {
        $this->attachAuthToUser();

        $res = $this->get(route('admin.user'));
        $res->assertStatus(200)
            ->assertJsonFragment(['id' => $this->user->id])
            ->assertJsonFragment(['roles' => ['role']])
            ->assertJsonFragment(['permissions' => ['perm1', 'perm2']]);
    }

    public function testEditUser()
    {
        $this->attachAuthToUser();

        $res = $this->get(route('admin.user.edit'));
        $res->assertStatus(200)
            ->assertJsonCount(1, 'roles')
            ->assertJsonCount(1, 'permissions');
    }

    public function testUpdateUser()
    {
        list($roleId, $perm1Id, $perm2Id) = $this->attachAuthToUser();

        $res = $this->put(route('admin.user.update'), [
            'name' => 'new name',
            'password' => '123456',
            'password_confirmation' => '123456',
            'username' => 'can not update',
            'roles' => [],
            'permissions' => [],
        ]);

        $res->assertStatus(201);

        $this->assertDatabaseHas('admin_users', [
            'name' => 'new name',
            'username' => $this->user->username,
        ]);

        $this->assertDatabaseHas('admin_user_permission', [
            'user_id' => $this->user->id,
            'permission_id' => $perm2Id,
        ]);

        $this->assertDatabaseHas('admin_user_role', [
            'user_id' => $this->user->id,
            'role_id' => $roleId,
        ]);

        $this->assertTrue(Hash::check('123456', $this->user->password));
    }

    public function testIndex()
    {
        AdminUser::factory(20)->create();
        $permissions = AdminPermission::factory(20)->create();
        $roles = AdminRole::factory(10)->create();

        $this->user->roles()->attach($roles->take(3)->pluck('id'));
        $this->user->permissions()->attach($permissions->take(3)->pluck('id'));

        $res = $this->getResources(['page' => 2]);
        $res->assertStatus(200)
            ->assertJsonCount(6, 'data')
            ->assertJsonCount(3, 'data.5.roles')
            ->assertJsonCount(3, 'data.5.permissions');

        $res = $this->getResources(['role_name' => 'nothing']);
        $res->assertStatus(200)
            ->assertJsonCount(0, 'data');

        $res = $this->getResources(['permission_name' => 'nothing']);
        $res->assertStatus(200)
            ->assertJsonCount(0, 'data');
        $res = $this->getResources([
            'role_name' => AdminRole::first()->value('name'),
            'permission_name' => AdminPermission::first()->value('name'),
        ]);

        $res->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function testStoreValidation()
    {
        $res = $this->storeResource([
            'roles' => [9999],
            'permissions' => [9999],
            'avatar' => str_repeat('a', 256),
        ]);

        $res->assertJsonValidationErrors([
            'username',
            'name',
            'password',
            'roles.0',
            'permissions.0',
            'avatar',
        ]);

        $res = $this->storeResource([
            'username' => str_repeat('e', 101),
            'name' => str_repeat('e', 101),
            'password' => str_repeat('e', 21),
        ]);

        $res->assertJsonValidationErrors(['username', 'name', 'password']);

        $res = $this->storeResource([
            'username' => 'admin',
            'password' => str_repeat('e', 5),
        ]);
        $res->assertJsonValidationErrors(['username', 'password']);

        $res = $this->storeResource([
            'password' => 'password',
            'password_confirmation' => 'not match',
        ]);

        $res->assertJsonValidationErrors(['password']);
    }

    public function testStore()
    {
        $roles = AdminRole::factory(5)->create();
        $permissions = AdminPermission::factory(5)->create();
        $pw = 'password';

        $userInputs = AdminUser::factory()->make(['password' => $pw])->toArray();

        $res = $this->storeResource($userInputs + [
            'password_confirmation' => $pw,
            'roles' => $roles->take(3)->pluck('id')->toArray(),
            'permissions' => $permissions->take(-2)->pluck('id')->toArray(),
        ]);

        $res->assertStatus(201);

        $userId = $this->getLastInsertId('admin_users');
        $this->assertDatabaseHas('admin_users', [
            'id' => $userId,
            'username' => $userInputs['username'],
            'name' => $userInputs['name'],
        ]);

        $this->assertTrue(Hash::check($pw, AdminUser::find($userId)->password));

        $this->assertDatabaseHas('admin_user_role', [
            'user_id' => $userId,
            'role_id' => $roles->take(3)->pluck('id')->first(),
        ]);

        $this->assertDatabaseHas('admin_user_permission', [
            'user_id' => $userId,
            'permission_id' => $permissions->take(-2)->pluck('id')->first(),
        ]);
    }

    public function testShow()
    {
        $this->user->roles()->attach(AdminRole::factory(3)->create()->pluck('id'));
        $this->user->permissions()->attach(AdminPermission::factory(3)->create()->pluck('id'));

        $res = $this->getResource($this->user->id);
        $res->assertStatus(200)
            ->assertJsonCount(3, 'roles')
            ->assertJsonCount(3, 'permissions');
    }

    public function testUpdate()
    {
        $this->storage
            ->getDriver()
            ->getConfig()
            ->set('url', 'http://domain.com');

        $this->user->avatar = '/path/to/avatar/jpg';
        $this->user->save();

        $this->user->roles()->createMany(AdminRole::factory(3)->make()->toArray());

        $oldRoleId = $this->getLastInsertId('admin_roles');
        $this->user->permissions()->createMany(AdminPermission::factory(3)->make()->toArray());

        $oldPermissionId = $this->getLastInsertId('admin_permissions');

        $newRoles = AdminRole::factory(3)->create()->pluck('id')->toArray();
        $newPerms = AdminPermission::factory(3)->create()->pluck('id')->toArray();

        $userId = $this->user->id;
        $pw = 'new password';
        $res = $this->updateResource($userId, [
            'username' => 'admin',
            'name' => 'new name',
            'roles' => $newRoles,
            'permissions' => $newPerms,
            'password' => $pw,
            'password_confirmation' => $pw,
            'avatar' => $this->storage->url($this->user->avatar),
        ]);
        $res->assertStatus(201);
        $this->assertTrue(Hash::check($pw, AdminUser::find($userId)->password));
        $this->assertDatabaseHas('admin_users', [
            'id' => $userId,
            'username' => 'admin',
            'name' => 'new name',
            'avatar' => $this->user->avatar,
        ]);

        $this->assertDatabaseHas('admin_user_role', [
            'user_id' => $userId,
            'role_id' => $newRoles[0],
        ]);

        $this->assertDatabaseMissing('admin_user_role', [
            'user_id' => $userId,
            'role_id' => $oldRoleId,
        ]);

        $this->assertDatabaseHas('admin_user_permission', [
            'user_id' => $userId,
            'permission_id' => $newPerms[0],
        ]);

        $this->assertDatabaseMissing('admin_user_permission', [
            'user_id' => $userId,
            'permission_id' => $oldPermissionId,
        ]);

        $res = $this->updateResource($userId, [
            'roles' => [],
            'permissions' => [],
        ]);

        $res->assertStatus(201);
        $this->assertDatabaseMissing('admin_user_role', ['user_id' => $userId]);

        $pw = AdminUser::find($userId)->password;
        $res = $this->updateResource($userId, ['password' => '']);
        $res->assertStatus(201);
        $this->assertTrue($pw === AdminUser::find($userId)->password);
    }

    public function testDestroy()
    {
        $this->user->roles()->createMany(AdminRole::factory(1)->make()->toArray());
        $this->user->permissions()->createMany(AdminPermission::factory(1)->make()->toArray());

        $userId = $this->user->id;
        $res = $this->destroyResource($userId);
        $res->assertStatus(204);

        $this->assertDatabaseMissing('admin_users', ['id' => $userId]);
        $this->assertDatabaseMissing('admin_user_role', ['user_id' => $userId]);
        $this->assertDatabaseMissing('admin_user_permission', ['user_id' => $userId]);
    }

    public function testEdit()
    {
        $this->user->roles()->attach($roleIds = AdminRole::factory(3)->create()->pluck('id'));
        $this->user->permissions()->attach(AdminPermission::factory(3)->create()->pluck('id'));

        $res = $this->editResource($this->user->id);
        $res->assertStatus(200)
            ->assertJsonFragment(['roles' => $roleIds]);
    }

    public function testCreate()
    {
        AdminRole::factory()->create(['name' => 'role']);
        AdminPermission::factory()->create(['slug' => 'permission']);

        $res = $this->createResource();
        $res->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'role',
                'slug' => 'permission',
            ]);
    }
}

<?php

namespace Tests\Admin\Feature;

use App\Http\Controllers\Admin\Controller;
use App\Models\SystemMedia;
use App\Models\SystemMediaCategory;
use Illuminate\Http\UploadedFile;
use Tests\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\Traits\RequestActions;

class SystemMediaCategoryControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use RequestActions;

    protected $resourceName = 'system-media-categories';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    protected function createNestedData()
    {
        SystemMediaCategory::insert([
            [
                'id' => 1,
                'parent_id' => 0,
                'name' => 'level 0-1',
            ],
            [
                'id' => 2,
                'parent_id' => 0,
                'name' => 'level 0-2',
            ],
            [
                'id' => 3,
                'parent_id' => 1,
                'name' => 'level 1-1',
            ],
            [
                'id' => 4,
                'parent_id' => 3,
                'name' => 'level 3-1',
            ],
        ]);
    }

    public function testStoreValidation()
    {
        $this->createNestedData();

        $res = $this->storeResource([
            'parent_id' => 111,
            'name' => str_repeat('a', 21),
            'folder' => null,
        ]);

        $res->assertJsonValidationErrors(['parent_id', 'name']);

        $res = $this->storeResource([
            'parent_id' => 3,
            'name' => 'level 3-1',
            'folder' => '1212/*/',
        ]);

        $res->assertJsonValidationErrors(['name', 'folder']);

        $res = $this->storeResource([
            'parent_id' => 0,
            'name' => 'level 3-1',
            'folder' => str_repeat('a', 51),
        ]);

        $res->assertJsonMissingValidationErrors(['parent_id', 'name'])
            ->assertJsonValidationErrors(['folder']);
    }

    public function testStore()
    {
        $res = $this->storeResource([
            'name' => 'level 0-1',
            'folder' => '///////////',
        ]);
        $res->assertStatus(201);
        $id1 = $this->getLastInsertId('system_media_categories');
        $this->assertDatabaseHas('system_media_categories', [
            'id' => $id1,
            'name' => 'level 0-1',
            'parent_id' => 0,
            'folder' => null,
        ]);

        $res = $this->storeResource([
            'parent_id' => $id1,
            'name' => 'level 1-1',
            'folder' => '//user////avatars//////',
        ]);

        $res->assertStatus(201);
        $id2 = $this->getLastInsertId('system_media_categories');
        $this->assertDatabaseHas('system_media_categories', [
            'id' => $id2,
            'parent_id' => $id1,
            'name' => 'level 1-1',
            'folder' => 'user/avatars',
        ]);
    }

    public function testUpdate()
    {
        $id1 = SystemMediaCategory::factory()->create(['name' => 'Category'])->id;
        $id2 = SystemMediaCategory::factory()->create(['name' => 'Category', 'parent_id' => $id1])->id;
        $id3 = SystemMediaCategory::factory()->create(['name' => 'Category2', 'parent_id' => $id1])->id;

        $res = $this->updateResource($id2, ['name' => 'Category2']);
        $res->assertJsonValidationErrors(['name']);

        $res = $this->updateResource($id2, ['parent_id' => 0]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $res = $this->updateResource($id2, [
            'parent_id' => $id3,
            'name' => 'Category3',
        ]);
        $res->assertStatus(201);

        $this->assertDatabaseHas('system_media_categories', [
            'id' => $id2,
            'parent_id' => $id3,
            'name' => 'Category3',
        ]);

        $res = $this->updateResource($id2);
        $res->assertStatus(201);
    }

    public function testEdit()
    {
        $id = SystemMediaCategory::factory()
            ->create([
                'name' => 'level 0-1',
                'folder' => 'user/avatars',
            ])->id;

        $res = $this->editResource($id);
        $res->assertStatus(200)
            ->assertJson([
                'id' => $id,
                'name' => 'level 0-1',
                'parent_id' => 0,
                'folder' => 'user/avatars',
            ]);
    }

    public function testDestroy()
    {
        $this->createNestedData();
        SystemMedia::factory()->create(['category_id' => 3]);

        $res = $this->destroyResource(1);
        $res->assertStatus(204);

        $this->assertDatabaseMissing('system_media_categories', ['id' => 1]);
        $this->assertDatabaseMissing('system_media_categories', ['id' => 3]);
        $this->assertDatabaseMissing('system_media_categories', ['id' => 4]);

        $this->assertDatabaseHas('system_media', [
            'id' => 1,
            'category_id' => 0,
        ]);

        $this->assertDatabaseHas('system_media_categories', ['id' => 2]);
    }

    public function testIndex()
    {
        $this->createNestedData();

        $res = $this->getResources();
        $res->assertStatus(200)
            ->assertJson([
                [
                    'id' => 1,
                    'parent_id' => 0,
                    'name' => 'level 0-1',
                    'children' => [
                        [
                            'id' => 3,
                            'parent_id' => 1,
                            'name' => 'level 1-1',
                            'children' => [
                                [
                                    'id' => 4,
                                    'parent_id' => 3,
                                    'name' => 'level 3-1',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 2,
                    'parent_id' => 0,
                    'name' => 'level 0-2',
                ],
            ]);

        $res = $this->getResources(['except' => 1]);
        $res->assertStatus(200)
            ->assertJson([
                [
                    'id' => 2,
                    'parent_id' => 0,
                    'name' => 'level 0-2',
                ],
            ]);
    }

    /**
     * @param int $cateId
     * @param array $data
     *
     * @return \Illuminate\Testing\TestResponse
     */
    protected function storeSystemMedia($cateId, $data = [])
    {
        return $this->storeResource(
            $data,
            $this->resourceName.'.system-media',
            ['system_media_category' => $cateId]
        );
    }

    public function testStoreSystemMedia()
    {
        $folder = 'users/avatars';
        $categoryId = SystemMediaCategory::factory()->create(['folder' => $folder])->id;

        $res = $this->storeSystemMedia($categoryId);
        $res->assertJsonValidationErrors(['file']);

        $res = $this->storeSystemMedia($categoryId, ['file' => 'not a file']);
        $res->assertJsonValidationErrors(['file']);

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $res = $this->storeSystemMedia($categoryId, ['file' => $file]);
        $res->assertStatus(201);

        $filename = md5_file($file).'.jpg';
        $path = Controller::UPLOAD_FOLDER_PREFIX.'/'.$folder.'/'.$filename;
        $this->assertDatabaseHas('system_media', [
            'id' => $this->getLastInsertId('system_media'),
            'category_id' => $categoryId,
            'filename' => $filename,
            'size' => $file->getSize(),
            'ext' => 'jpg',
            'mime_type' => $file->getMimeType(),
            'path' => $path,
            'path_key' => md5($path),
        ]);

        $this->storage->exists($path);
        $this->storage->delete($path);
    }

    protected function systemMediaIndex($params = [])
    {
        return $this->getResources($params, $this->resourceName.'.system-media');
    }

    public function testSystemMediaIndex()
    {
        SystemMediaCategory::factory()->create()->media()->createMany([
            SystemMedia::factory()->make(['filename' => 'avatar.jpg', 'ext' => 'jpg'])->toArray(),
            SystemMedia::factory()->make(['filename' => 'funny.gif', 'ext' => 'gif'])->toArray(),
        ]);

        $categoryId1 = $this->getLastInsertId('system_media_categories');

        SystemMediaCategory::factory()->create()->media()->createMany(
            SystemMedia::factory(2)->make(['ext' => 'jpg'])->toArray()
        );

        $res = $this->systemMediaIndex(['system_media_category' => $categoryId1, 'ext' => ['jpg']]);
        $res->assertStatus(200)
            ->assertJsonFragment(['ext' => 'jpg'])
            ->assertJsonMissing(['ext' => 'gif']);

        $res = $this->systemMediaIndex(['system_media_category' => $categoryId1, 'ext' => 'jpg,gif']);
        $res->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $res = $this->systemMediaIndex(['system_media_category' => $categoryId1, 'filename' => 'ny']);
        $res->assertStatus(200)
            ->assertJsonFragment(['filename' => 'funny.gif'])
            ->assertJsonMissing(['filename' => 'avatar.jpg']);
    }
}

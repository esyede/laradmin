<?php

namespace Tests\Admin\Unit;

use App\Models\Model;
use Illuminate\Http\Request;
use Tests\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BaseModelTest extends AdminTestCase
{
    public function testGetPerPage()
    {
        $model = app(Model::class);

        $this->mockGetPerPage('4');
        $this->assertEquals(4, $model->getPerPage());

        foreach (['4.4', 'not number', '-1', '0'] as $page) {
            $this->mockGetPerPage($page);
            $this->assertEquals(15, $model->getPerPage());
        }

        $model = new MyDummyTestAdminUnitModel();
        $maxPerPage = $model->getMaxPerPage();
        $this->mockGetPerPage((string) ($maxPerPage + 1));
        $this->assertEquals($maxPerPage, $model->getPerPage());
    }

    protected function mockGetPerPage($page)
    {
        $this->setUp();

        $request = new MyDummyTestAdminUnitRequest($page);

        $this->instance('request', $request);
    }
}


class MyDummyTestAdminUnitModel extends Model
{
    public function getMaxPerPage()
    {
        return $this->maxPerPage;
    }
}

class MyDummyTestAdminUnitRequest extends Request
{
    public $mockPage;

    public function __construct($page)
    {
        parent::__construct();
        $this->mockPage = $page;
    }

    public function get($key, $default = null)
    {
        return $this->mockPage;
    }
}

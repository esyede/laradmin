<?php

namespace Tests\Admin\Controllers;

use App\Http\Controllers\Admin\Controller;
use App\Utils\PermissionChecker;

class DummyAdminController extends Controller
{
    public function index()
    {
        return $this->ok();
    }

    public function store()
    {
        return $this->created();
    }

    public function check()
    {
        PermissionChecker::check('check');
        return $this->ok();
    }

    public function withArgs()
    {
        return $this->ok();
    }

    public function passThrough()
    {
        return $this->ok();
    }

    public function passThroughGet()
    {
        return $this->ok();
    }

    public function cannotPassThroughPut()
    {
        return $this->created();
    }
}

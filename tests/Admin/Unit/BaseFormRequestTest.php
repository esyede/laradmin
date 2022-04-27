<?php

namespace Tests\Admin\Unit;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\ValidationException;
use Tests\Admin\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BaseFormRequestTest extends AdminTestCase
{
    public function testMergeArrayError()
    {
        $request = $this->makeRequest(['array' => ['not integer']]);

        try {
            $request->validateResolved();
        } catch (ValidationException $e) {
            $this->assertTrue(array_key_exists('array', $e->validator->errors()->messages()));
        }
        $request = $this->makeRequest(['array' => ['not integer']]);
        $request->setMergeArrayError(false);
        try {
            $request->validateResolved();
        } catch (ValidationException $e) {
            $this->assertFalse(array_key_exists('array', $e->validator->errors()->messages()));
        }
    }

    protected function makeRequest($data = [])
    {
        $request = new DummyFormRequest($data);

        app()->call([$request, 'setContainer']);
        app()->call([$request, 'setRedirector']);

        return $request;
    }
}

class DummyFormRequest extends FormRequest
{
    public function rules()
    {
        return ['array.*' => 'integer'];
    }

    public function setMergeArrayError($merge)
    {
        $this->mergeArrayError = $merge;
    }
}

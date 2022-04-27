<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    protected $mergeArrayError = true;

    protected function failedValidation(Validator $validator)
    {
        $this->mergeArrayError($validator);
        parent::failedValidation($validator);
    }

    protected function mergeArrayError(Validator $validator)
    {
        if (!$this->mergeArrayError) {
            return;
        }
        $msgBag = $validator->errors();
        foreach ($msgBag->messages() as $field => $errors) {
            // http_method.0 --> http_method
            // some.0.nested --> some
            $dotPos = strpos($field, '.');

            if ($dotPos === false) {
                continue;
            }

            $targetField = substr($field, 0, $dotPos);

            foreach ($errors as $i) {
                $msgBag->add($targetField, $i);
            }
        }
    }
}

<?php

namespace App\Rules;

use App\Models\Config;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ConfigSelectTypeOptions implements Rule
{
    protected $errorMessage;

    public function passes($attribute, $value)
    {
        if (! $value) {
            return true;
        }

        if (! is_string($value)) {
            return false;
        }

        $pairs = explode("\n", $value);

        foreach ($pairs as $pair) {
            $p = explode('=>', $pair);

            if (count($p) < 2) {
                return false;
            }

            $label = $p[1];

            if ($label) {
                return true;
            }
        }

        return false;
    }

    public function message()
    {
        return 'The option setting is invalid.';
    }
}

class ConfigOptions implements Rule
{
    protected $type;
    protected $errorMessage;

    /**
     * Create a new rule instance.
     *
     * @param string $type
     *
     * @return void
     */
    public function __construct(string $type = null)
    {
        $this->type = $type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (! isset(Config::$typeMap[$this->type])) {
            return true;
        }

        switch ($this->type) {
            case Config::TYPE_INPUT:
            case Config::TYPE_TEXTAREA:
            case Config::TYPE_OTHER:
                if (! is_null($value)) {
                    $this->errorMessage = 'Option must be empty';
                }
                break;
            case Config::TYPE_FILE:
                if (! is_array($value)) {
                    $this->errorMessage = 'Option is should be an array';
                    break;
                }

                $validator = Validator::make($value, [
                    'max' => 'required|integer|between:1,99',
                    'ext' => 'nullable',
                ], [], [
                    'max' => 'Max Uploads',
                    'ext' => 'Extension',
                ]);
                break;
            case Config::TYPE_SINGLE_SELECT:
            case Config::TYPE_MULTIPLE_SELECT:
                if (! is_array($value)) {
                    $this->errorMessage = 'Option is should be an array';
                    break;
                }

                $validator = Validator::make($value, [
                    'options' => ['required', new ConfigSelectTypeOptions()],
                    'type' => 'required|in:input,select',
                ], [], [
                    'options' => 'Option Setting',
                    'type' => 'Select Form',
                ]);
                break;
        }

        if (isset($validator) && $validator->fails()) {
            $this->errorMessage = $validator->getMessageBag()->first();
        }

        return !$this->errorMessage;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }
}

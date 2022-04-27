<?php

namespace App\Http\Requests;

use App\Models\Config;
use App\Rules\ConfigOptions;
use App\Rules\ValidRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class ConfigRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $configId = $this->route()->originalParameter('config');

        $rules = [
            'type' => 'required|in:'.implode(',', array_keys(Config::$typeMap)),
            'category_id' => 'required|exists:config_categories,id',
            'name' => 'required|string|max:50|unique:configs,name,' . intval($configId),
            'slug' => 'required|regex:/^[a-z][a-z_\d]*$/|max:50|unique:configs,slug,' . intval($configId),
            'desc' => 'nullable|string|max:255',
            'options' => new ConfigOptions($this->input('type')),
            'value' => 'nullable',
            'validation_rules' => ['nullable', 'string', 'max:255', new ValidRules()],
        ];

        if ($this->isMethod('put')) {
            $rules = Arr::only($rules, $this->keys());
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'type' => 'Input Type',
            'category_id' => 'Category',
            'name' => 'Name',
            'slug' => 'Slug',
            'desc' => 'Description',
            'options' => 'Options',
            'value' => 'Values',
            'validation_rules' => 'Validations',
        ];
    }

    public function messages()
    {
        return [
            'slug.regex' => ':attribute can only contain numbers, lowercase letters and underscores, and must start with a letter.',
        ];
    }
}

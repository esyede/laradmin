<?php

namespace App\Http\Requests;

use App\Models\Config;
use App\Models\ConfigCategory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigValuesRequest extends FormRequest
{
    /**
     * @var Config[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $configs;

    /**
     * @var ConfigCategory
     */
    protected $category;
    protected $validationRules = [];
    protected $validationAttributes = [];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->gatherRulesAndAttributes();

        return $this->validationRules;
    }

    public function getConfigs()
    {
        $category = $this->getCategory();

        if (! $this->configs) {
            $this->configs = $category
                ->configs()
                ->whereIn('slug', $this->keys())
                ->get();
        }

        return $this->configs;
    }

    protected function gatherRulesAndAttributes()
    {
        $configs = $this->getConfigs();

        $rules = [];
        $attributes = [];

        foreach ($configs as $config) {
            $rules[$config->slug] = $config->validation_rules ?: 'nullable';
            $attributes[$config->slug] = $config->name;
        }

        $this->validationRules = $rules;
        $this->validationAttributes = $attributes;
    }

    public function attributes()
    {
        return $this->validationAttributes;
    }

    public function getCategory()
    {
        if (! $this->category) {
            $this->category = ConfigCategory::query()
                ->where('slug', $this->route('category_slug'))
                ->firstOrFail();
        }

        return $this->category;
    }
}

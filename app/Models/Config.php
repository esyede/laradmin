<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Throwable;

class Config extends Model
{
    const TYPE_INPUT = 'input';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_FILE = 'file';
    const TYPE_SINGLE_SELECT = 'single_select';
    const TYPE_MULTIPLE_SELECT = 'multiple_select';
    const TYPE_OTHER = 'other';
    const CACHE_KEY = 'admin_config';
    const CONFIG_KEY = 'admin';

    public static $typeMap = [
        self::TYPE_INPUT => 'Input',
        self::TYPE_TEXTAREA => 'Textarea',
        self::TYPE_FILE => 'File',
        self::TYPE_SINGLE_SELECT => 'Radio',
        self::TYPE_MULTIPLE_SELECT => 'Choice',
        self::TYPE_OTHER => 'Other',
    ];

    protected $fillable = [
        'category_id',
        'type',
        'name',
        'slug',
        'desc',
        'options',
        'value',
        'validation_rules',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'options' => Json::class,
        'value' => Json::class,
    ];

    public function getTypeTextAttribute()
    {
        return static::$typeMap[$this->type] ?? '';
    }

    public function category()
    {
        return $this->belongsTo(ConfigCategory::class);
    }

    public static function getByCategorySlug(string $categorySlug, bool $onlyValues = false)
    {
        $configs = static::whereHas('category', function (Builder $query) use ($categorySlug) {
            $query->where('slug', $categorySlug);
        })->get();

        return $onlyValues ? $configs->pluck('value', 'slug') : $configs;
    }

    public static function updateValues($configs, $inputs)
    {
        $configs->each(function (Config $config) use ($inputs) {
            if (array_key_exists($config->slug, $inputs)) {
                $config->update(['value' => $inputs[$config->slug]]);
            }
        });

        return $configs->pluck('value', 'slug');
    }

    public static function getConfigValue(string $slug, $default = null)
    {
        return static::query()->where('slug', $slug)->value('value') ?? $default;
    }

    protected static function getConfigGroupsFromDB(): array
    {
        try {
            $groups = ConfigCategory::query()
                ->select(['id', 'slug'])
                ->with('configs:category_id,slug,value')
                ->get()
                ->map(function (ConfigCategory $category) {
                    return [
                        'slug' => $category->slug,
                        'configs' => $category->configs->pluck('value', 'slug')->toArray(),
                    ];
                })
                ->pluck('configs', 'slug')
                ->toArray();
        } catch (Throwable $e) {
            report($e);
            $groups = [];
        }

        return $groups;
    }

    protected static function getConfigGroupsFromFile(): array
    {
        $configFilePath = app('path.config').'/'.static::CONFIG_KEY.'.php';
        $config = [];

        if (is_file($configFilePath) && ! is_array($config = include $configFilePath)) {
            $config = [];
        }

        return $config;
    }

    public static function getDottedConfigFromCache(): array
    {
        return Cache::rememberForever(static::CACHE_KEY, function () {
            return array_merge(
                static::dotConfigs(static::getConfigGroupsFromFile()),
                static::dotConfigs(static::getConfigGroupsFromDB())
            );
        });
    }

    protected static function dotConfigs(array $configs)
    {
        return Arr::dot($configs, static::CONFIG_KEY.'.');
    }

    public static function loadToConfig()
    {
        config(static::getDottedConfigFromCache());
    }

    public static function clearConfigCache()
    {
        Cache::forget(static::CACHE_KEY);
    }
}

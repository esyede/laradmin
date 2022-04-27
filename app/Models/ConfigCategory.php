<?php

namespace App\Models;

class ConfigCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function configs()
    {
        return $this->hasMany(Config::class, 'category_id');
    }

    public function delete()
    {
        $deleted = parent::delete();
        $this->configs->each->delete();

        return $deleted;
    }
}

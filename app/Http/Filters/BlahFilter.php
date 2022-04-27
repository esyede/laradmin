<?php

namespace App\Http\Filters;

class BlahFilter extends Filter
{
    protected $simpleFilters = [
        'id',
    ];

    protected $filters = [
        'field_name',
    ];

    protected function fieldName($value)
    {
        $this->builder->where('field_name', $value);
    }
}

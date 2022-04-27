<?php

namespace App\Traits;

use App\Http\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

trait ModelHelpers
{
    protected $maxPerPage = 200;

    public function getPerPage()
    {
        $perPage = Request::get('per_page');
        $intPerPage = (int) $perPage;

        if ($intPerPage > 0 && ((string) $intPerPage === $perPage)) {
            return min($intPerPage, $this->maxPerPage);
        } else {
            return $this->perPage;
        }
    }

    public function scopeFilter(Builder $builder, Filter $filter)
    {
        return $filter->apply($builder);
    }
}

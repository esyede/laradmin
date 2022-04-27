<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class Filter
{
    protected $request;

    /**
     * @var Builder
     */
    protected $builder;
    protected $filters = [];
    protected $simpleFilters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->formatSimpleFilters();
    }

    protected function formatSimpleFilters()
    {
        $filters = [];

        foreach ($this->simpleFilters as $field => $operator) {
            if (is_int($field)) {
                $filters[$operator] = 'equal';
            } else {
                $filters[$field] = $operator;
            }
        }

        $this->simpleFilters = $filters;
    }

    /**
     * Apply filter
     *
     * @param $builder
     *
     * @return mixed
     */
    public function apply($builder)
    {
        $this->builder = $builder;
        $filters = $this->getFilters();

        foreach ($filters as $filter => $value) {
            if (is_null($value)) {
                continue;
            }

            if ($op = $this->simpleFilters[$filter] ?? null) { // simple filter
                $this->applySimpleFilter($filter, $op, $value);
            } else { // other (custom) filters
                $method = Str::camel($filter);

                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
        }

        return $this->builder;
    }

    protected function applySimpleFilter($filter, $operators, $value)
    {
        if (is_array($operators)) {
            $args = array_slice($operators, 1);
            $operators = $operators[0];
        }

        switch ($operators) {
            case 'equal':
                $this->builder->where($filter, $value);
                break;

            case 'like':
                $this->builder->where($filter, 'like', str_replace('?', $value, $args[0]));
                break;

            case 'in':
                if (is_string($value)) {
                    $value = explode(',', $value);
                }

                $this->builder->whereIn($filter, $value);
                break;
        }
    }

    public function getFilters()
    {
        return $this->request->only(array_merge($this->filters, array_keys($this->simpleFilters)));
    }

    /**
     * Keep only specific filter fields
     *
     * @param array|string $only
     *
     * @return $this
     */
    public function only($only)
    {
        if (is_string($only)) {
            $only = [$only];
        }

        $this->filters = array_intersect($this->filters, $only);
        $this->simpleFilters = array_intersect_key($this->simpleFilters, array_flip($only));

        return $this;
    }
}

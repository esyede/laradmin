<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait ModelTree
{
    protected $except = 0;
    protected static $branchOrder = [];

    protected function parentColumn()
    {
        return 'parent_id';
    }

    protected function orderColumn()
    {
        return 'order';
    }

    protected function idColumn()
    {
        return 'id';
    }

    public function treeExcept(int $id)
    {
        $this->except = $id;
        return $this;
    }

    public function toTree(): array
    {
        return $this->buildNestedArray();
    }

    protected function buildNestedArray(array $nodes = [], $parentId = 0): array
    {
        $branch = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        static $parentIds;

        $parentIds = $parentIds ?: array_flip(array_column($nodes, $this->parentColumn()));

        foreach ($nodes as $node) {
            if ($this->ignoreTreeNode($node)) {
                continue;
            }

            if ($node[$this->parentColumn()] == $parentId) {
                $children = $this->buildNestedArray($nodes, $node[$this->idColumn()]);
                $node['children'] = $children;
                $branch[] = $node;
            }
        }

        return $branch;
    }

    protected function allNodes(): array
    {
        return $this->allNodesQuery()->get()->toArray();
    }

    protected function allNodesQuery(): Builder
    {
        return static::query()
            ->when($this->except, function (Builder $query) {
                $query->where($this->idColumn(), '<>', $this->except)
                    ->where($this->parentColumn(), '<>', $this->except);
            })
            ->orderBy($this->orderColumn());
    }

    public function children()
    {
        return $this->hasMany(static::class, $this->parentColumn(), $this->idColumn());
    }

    public function parent()
    {
        return $this->belongsTo(static::class, $this->parentColumn(), $this->idColumn());
    }

    public function delete()
    {
        $this->children->each->delete();

        return parent::delete();
    }

    protected function ignoreTreeNode(array $node): bool
    {
        return false;
    }

    protected function setBranchOrder(array $order)
    {
        static::$branchOrder = array_flip(Arr::flatten($order));
        static::$branchOrder = array_map(function ($item) {
            return ++$item;
        }, static::$branchOrder);
    }

    public function saveOrder($tree = [], $parentId = 0)
    {
        if (empty(static::$branchOrder)) {
            $this->setBranchOrder($tree);
        }

        foreach ($tree as $branch) {
            $node = static::find($branch[$this->idColumn()]);
            $node->{$node->parentColumn()} = $parentId;
            $node->{$node->orderColumn()} = static::$branchOrder[$branch[$this->idColumn()]];
            $node->save();

            if (isset($branch['children'])) {
                static::saveOrder($branch['children'], $branch[$this->idColumn()]);
            }
        }
    }

    public function flatten(array $tree): array
    {
        $flatten = [];

        foreach ($tree as $item) {
            $children = Arr::pull($item, 'children', []);
            $flatten[] = $item;
            $flatten = array_merge($flatten, $this->flatten($children));
        }

        return $flatten;
    }
}

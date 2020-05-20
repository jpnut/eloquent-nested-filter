<?php

declare(strict_types=1);

namespace JPNut\EloquentNestedFilter;

use Illuminate\Database\Eloquent\Builder;
use JPNut\EloquentNestedFilter\Contracts\Filterable;

class ResourceFilterObject implements Filterable
{
    /**
     * @var \JPNut\EloquentNestedFilter\AbstractFilter[]
     */
    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function filter(string $name, Builder $query): Builder
    {
        return $query->whereHas($name, fn (Builder $sub_query) => $this->combineFilters($sub_query));
    }

    private function combineFilters(Builder $query): Builder
    {
        foreach ($this->data as $filter) {
            $query->where(fn (Builder $sub_query) => $filter->filter($sub_query));
        }

        return $query;
    }
}

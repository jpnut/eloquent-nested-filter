<?php

namespace JPNut\EloquentNestedFilter\Tests\Objects;

use Illuminate\Database\Eloquent\Builder;
use JPNut\EloquentNestedFilter\Contracts\Filterable;

class InvalidFilterObject implements Filterable
{
    public function filter(string $name, Builder $query): Builder
    {
        return $query;
    }
}

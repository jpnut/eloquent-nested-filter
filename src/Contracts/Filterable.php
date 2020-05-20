<?php

namespace JPNut\EloquentNestedFilter\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Filterable
{
    public function filter(string $name, Builder $query): Builder;
}

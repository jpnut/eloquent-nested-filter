<?php

namespace JPNut\EloquentNestedFilter\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface FilterObject extends Filterable
{
    public function filter(string $name, Builder $query): Builder;

    public static function fromArray(array $properties = []): self;
}

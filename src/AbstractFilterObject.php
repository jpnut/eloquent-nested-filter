<?php

declare(strict_types=1);

namespace JPNut\EloquentNestedFilter;

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use JPNut\EloquentNestedFilter\Contracts\Filterable;

abstract class AbstractFilterObject implements Filterable
{
    abstract public function filter(string $name, Builder $query): Builder;

    abstract public static function fromArray(array $properties = []): self;

    public static function invalidOperator(Operator $operator): InvalidArgumentException
    {
        return new InvalidArgumentException("Invalid Operator: {$operator->getValue()}.");
    }

    public static function missingOperator(): InvalidArgumentException
    {
        return new InvalidArgumentException("No operator provided.");
    }

    protected static function operatorFromProperties(array $properties = []): Operator
    {
        if (isset($properties['operator'])) {
            return $properties['operator'] instanceof Operator
                ? $properties['operator']
                : new Operator($properties['operator']);
        }

        throw static::missingOperator();
    }
}

<?php

declare(strict_types=1);

namespace JPNut\EloquentNestedFilter;

use Illuminate\Database\Eloquent\Builder;

class BooleanFilterObject extends AbstractFilterObject
{
    public ?bool $value;

    public Operator $operator;

    public function __construct(?bool $value, Operator $operator)
    {
        $this->value = $value;
        $this->operator = $operator;
    }

    public static function fromArray(array $properties = []): self
    {
        return new self(
            is_null($properties['value'] ?? null) ? null : boolval($properties['value']),
            static::operatorFromProperties($properties)
        );
    }

    public function filter(string $name, Builder $query): Builder
    {
        if ($this->operator->equals(Operator::IS())) {
            return $query->where($name, $this->value);
        }

        if ($this->operator->equals(Operator::IS_NOT())) {
            return $query->where($name, '<>', $this->value);
        }

        if ($this->operator->equals(Operator::NULL())) {
            return $query->whereNull($name);
        }

        if ($this->operator->equals(Operator::NOT_NULL())) {
            return $query->whereNotNull($name);
        }

        throw static::invalidOperator($this->operator);
    }
}

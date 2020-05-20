<?php

declare(strict_types=1);

namespace JPNut\EloquentNestedFilter;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DateFilterObject extends AbstractFilterObject
{
    public ?Carbon $value;

    public Operator $operator;

    public function __construct(?Carbon $value, Operator $operator)
    {
        $this->value = $value;
        $this->operator = $operator;
    }

    public static function fromArray(array $properties = []): self
    {
        return new static(
            is_null($properties['value'] ?? null) ? null : Carbon::parse($properties['value']),
            static::operatorFromProperties($properties)
        );
    }

    public function filter(string $name, Builder $query): Builder
    {
        if ($this->operator->equals(Operator::IS())) {
            return $query->whereDate($name, '=', $this->value);
        }

        if ($this->operator->equals(Operator::IS_NOT())) {
            return $query->whereDate($name, '<>', $this->value);
        }

        if ($this->operator->equals(Operator::LESS_THAN())) {
            return $query->where($name, '<', $this->value);
        }

        if ($this->operator->equals(Operator::LESS_THAN_OR_EQUAL_TO())) {
            return $query->where($name, '<=', $this->value);
        }

        if ($this->operator->equals(Operator::GREATER_THAN())) {
            return $query->where($name, '>', $this->value);
        }

        if ($this->operator->equals(Operator::GREATER_THAN_OR_EQUAL_TO())) {
            return $query->where($name, '>=', $this->value);
        }

        if ($this->operator->equals(Operator::NULL())) {
            return $query->whereNull($name);
        }

        if ($this->operator->equals(Operator::NOT_NULL())) {
            return $query->whereNotNull($name);
        }

        throw $this->invalidOperator($this->operator);
    }
}

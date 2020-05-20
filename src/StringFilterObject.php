<?php

declare(strict_types=1);

namespace JPNut\EloquentNestedFilter;

use Illuminate\Database\Eloquent\Builder;

class StringFilterObject extends AbstractFilterObject
{
    public ?string $value;

    public Operator $operator;

    public function __construct(?string $value, Operator $operator)
    {
        $this->value = $value;
        $this->operator = $operator;
    }

    public static function fromArray(array $properties = []): self
    {
        return new static(
            is_null($properties['value'] ?? null) ? null : strval($properties['value']),
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

        if ($this->operator->equals(Operator::CONTAINS())) {
            return $query->where($name, 'LIKE', "%{$this->value}%");
        }

        if ($this->operator->equals(Operator::DOES_NOT_CONTAIN())) {
            return $query->where($name, 'NOT LIKE', "%{$this->value}%");
        }

        if ($this->operator->equals(Operator::BEGINS())) {
            return $query->where($name, 'LIKE', "{$this->value}%");
        }

        if ($this->operator->equals(Operator::DOES_NOT_BEGIN())) {
            return $query->where($name, 'NOT LIKE', "{$this->value}%");
        }

        if ($this->operator->equals(Operator::ENDS())) {
            return $query->where($name, 'LIKE', "%{$this->value}");
        }

        if ($this->operator->equals(Operator::DOES_NOT_END())) {
            return $query->where($name, 'NOT LIKE', "%{$this->value}");
        }

        if ($this->operator->equals(Operator::LIKE())) {
            return $query->where($name, 'LIKE', $this->value);
        }

        if ($this->operator->equals(Operator::NOT_LIKE())) {
            return $query->where($name, 'LIKE', $this->value);
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

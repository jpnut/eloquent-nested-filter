<?php

declare(strict_types=1);

namespace JPNut\EloquentNestedFilter;

use ReflectionClass;
use ReflectionProperty;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;
use JPNut\EloquentNestedFilter\Contracts\Filterable;

class AbstractFilter
{
    /** @var static[]|null */
    public ?array $and = null;

    /** @var static[]|null */
    public ?array $or = null;

    protected ?AbstractFilter $parent;

    protected ?int $max_depth = 10;

    protected int $depth = 0;

    protected ?int $max_filters = 100;

    protected int $filters = 0;

    public function __construct(array $parameters = [], ?AbstractFilter $parent = null)
    {
        $this->parent = $parent;

        $this->setDepth($parent);

        foreach ($this->filterProperties() as $reflectionProperty) {
            $field = $reflectionProperty->getName();

            $this->{$field} = isset($parameters[$field])
                ? $this->resolveFilterField($reflectionProperty, $parameters[$field])
                : null;
        }
    }

    protected function setDepth(?AbstractFilter $parent = null): void
    {
        if (is_null($parent)) {
            return;
        }

        $this->max_depth = $parent->getMaxDepth();

        $this->depth = $parent->getDepth() + 1;

        if (!is_null($this->max_depth) && $this->depth > $this->max_depth) {
            throw new InvalidArgumentException("Max filter depth exceeded.");
        }
    }

    public function getMaxDepth(): ?int
    {
        return $this->max_depth;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @return \JPNut\EloquentNestedFilter\Contracts\Filterable[]
     */
    public function filters(): array
    {
        $filters = [];

        foreach ($this->filterProperties() as $reflectionProperty) {
            $filters[$reflectionProperty->getName()] = $this->getFilterValue($reflectionProperty);
        }

        return array_filter($filters);
    }

    public function filter(Builder $builder): Builder
    {
        foreach ($this->filters() as $name => $filter) {
            $builder = $filter->filter($name, $builder);
        }

        return $builder;
    }

    protected function getFilterValue(ReflectionProperty $property): ?Filterable
    {
        if (($value = $property->getValue($this)) === null) {
            return null;
        }

        if ($property->getName() === 'and') {
            return new AndFilterObject($value);
        }

        if ($property->getName() === 'or') {
            return new OrFilterObject($value);
        }

        if (is_subclass_of($this->resolvePropertyType($property), AbstractFilter::class)) {
            return new ResourceFilterObject($value);
        }

        return $value;
    }

    /**
     * @return \ReflectionProperty[]
     */
    protected function filterProperties(): array
    {
        $class = new ReflectionClass(static::class);

        return array_filter(
            $class->getProperties(ReflectionProperty::IS_PUBLIC),
            fn (ReflectionProperty $reflectionProperty) => ! $reflectionProperty->isStatic()
                && $this->isFilterableProperty($reflectionProperty)
        );
    }

    protected function isFilterableProperty(ReflectionProperty $property): bool
    {
        $name = $property->getName();
        $type = $this->resolvePropertyType($property);

        return $name === 'and'
            || $name === 'or'
            || class_exists($type) && (
                $this->classImplements($type, Filterable::class)
                || is_subclass_of($type, AbstractFilter::class)
            );
    }

    protected function resolvePropertyType(ReflectionProperty $property): string
    {
        if (is_null($type = $property->getType()) || $type->getName() === 'array') {
            return $this->resolvePropertyTypeFromDocBlock($property);
        }

        return $type->getName();
    }

    /**
     * @param  \ReflectionProperty  $property
     * @param  mixed $value
     * @return \JPNut\EloquentNestedFilter\AbstractFilterObject|\JPNut\EloquentNestedFilter\AbstractFilterObject[]
     */
    protected function resolveFilterField(ReflectionProperty $property, $value)
    {
        $type = $this->resolvePropertyType($property);

        if ($this->propertyIsArrayType($property)) {
            if (!is_array($value)) {
                throw new InvalidArgumentException("Expected value to be array, ".gettype($value)." received");
            }

            return array_map(
                /**
                 * @param  mixed $props
                 * @return mixed
                 */
                fn ($props) => $this->resolveFilterClass(
                    $type,
                    $props,
                    $property->getName(),
                ),
                $value
            );
        }

        return $this->resolveFilterClass($type, $value, $property->getName());
    }

    protected function resolvePropertyTypeFromDocBlock(ReflectionProperty $property): string
    {
        $resolver = TypeResolver::fromProperty($property, $this->reflectionClass());

        if (count($resolver->allowedTypes) !== 1) {
            return 'mixed';
        }

        return count($resolver->allowedArrayTypes) === 1
            ? $resolver->allowedArrayTypes[0]
            : $resolver->allowedTypes[0];
    }

    protected function propertyIsArrayType(ReflectionProperty $property): bool
    {
        if (! is_null($type = $property->getType()) && $type->getName() === 'array') {
            return true;
        }

        return count(TypeResolver::fromProperty($property, $this->reflectionClass())->allowedArrayTypes) > 0;
    }

    /**
     * @param  string  $class
     * @param  mixed $value
     * @param  string  $name
     * @return mixed
     */
    protected function resolveFilterClass(string $class, $value, string $name)
    {
        $this->incrementFilters();

        /**
         * If the passed value is already an instance of the class, return that instance.
         */
        if ($value instanceof $class) {
            return $value;
        }

        if (! is_array($value)) {
            throw new InvalidArgumentException(
                "Expected value of '{$name}' to be instance of '{$class}' or array."
            );
        }

        if (is_subclass_of($class, AbstractFilter::class)) {
            return new $class($value, $this);
        }

        if (is_subclass_of($class, AbstractFilterObject::class)) {
            return $class::fromArray($value);
        }

        throw new InvalidArgumentException(
            "Could not construct filter object for property '{$name}' of class '{$class}'"
        );
    }

    /**
     * @param  string  $class
     * @param  string  $contract
     * @return bool
     */
    protected function classImplements(string $class, string $contract): bool
    {
        return isset(class_implements($class)[$contract]);
    }

    protected function reflectionClass(): ReflectionClass
    {
        return new ReflectionClass(static::class);
    }

    public function incrementFilters(): void
    {
        if (!is_null($this->parent)) {
            $this->parent->incrementFilters();

            return;
        }

        $this->filters++;

        if (!is_null($this->max_filters) && $this->filters > $this->max_filters) {
            throw new InvalidArgumentException(
                "Maximum allowed filter amount ({$this->max_filters}) exceeded."
            );
        }
    }
}

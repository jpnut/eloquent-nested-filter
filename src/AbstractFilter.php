<?php

declare(strict_types=1);

namespace JPNut\EloquentNestedFilter;

use ReflectionClass;
use ReflectionProperty;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;
use JPNut\EloquentNestedFilter\Contracts\Filterable;
use JPNut\EloquentNestedFilter\Contracts\ResourceFilter;

class AbstractFilter
{
    /**
     * @var static[]|null
     */
    public ?array $and;

    /**
     * @var static[]|null
     */
    public ?array $or;

    public function __construct(array $parameters = [])
    {
        foreach ($this->filterProperties() as $reflectionProperty) {
            $field = $reflectionProperty->getName();

            $this->{$field} = isset($parameters[$field])
                ? $this->resolveFilterField($reflectionProperty, $parameters[$field])
                : null;
        }
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

        if ($this->classImplements($this->resolvePropertyType($property), ResourceFilter::class)) {
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
                && ! is_null($reflectionProperty->getType())
                && $this->isFilterableProperty($reflectionProperty)
        );
    }

    private function isFilterableProperty(ReflectionProperty $property): bool
    {
        $name = $property->getName();
        $type = $this->resolvePropertyType($property);

        return $name === 'and'
            || $name === 'or'
            || $this->classImplements($type, Filterable::class)
            || $this->classImplements($type, ResourceFilter::class);
    }

    private function resolvePropertyType(ReflectionProperty $property): string
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
    private function resolveFilterField(ReflectionProperty $property, $value)
    {
        $type = $this->resolvePropertyType($property);

        if ($this->propertyIsArrayType($property)) {
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

    private function resolvePropertyTypeFromDocBlock(ReflectionProperty $property): string
    {
        $resolver = TypeResolver::fromProperty($property, $this->reflectionClass());

        $allowedTypes = array_filter($resolver->allowedTypes, fn (string $type) => $type !== 'null');

        if (count($allowedTypes) !== 1) {
            return 'mixed';
        }

        return count($resolver->allowedArrayTypes) === 1
            ? $resolver->allowedArrayTypes[0]
            : $allowedTypes[0];
    }

    private function propertyIsArrayType(ReflectionProperty $property): bool
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
    private function resolveFilterClass(string $class, $value, string $name)
    {
        /**
         * If the passed value is already an instance of the class, return that instance.
         */
        if ($value instanceof $class) {
            return $value;
        }

        if (! is_array($value)) {
            throw new InvalidArgumentException(
                "Expected value of {$name} to be instance of {$class} or array."
            );
        }

        /**
         * If the class is an implementation of ResourceFilter, create a new instance using the passed value.
         */
        if ($this->classImplements($class, ResourceFilter::class)) {
            return new $class($value);
        }

        /**
         * If the class is a subclass of AbstractFilterObject, create a new instance from the passed value.
         */
        if (is_subclass_of($class, AbstractFilterObject::class)) {
            return $class::fromArray($value);
        }

        throw new InvalidArgumentException(
            "Could not construct filter object for property '{$name}' of class {$class}"
        );
    }

    /**
     * @psalm-assert class-string $class
     * @param  string  $class
     * @param  string  $contract
     * @return bool
     */
    private function classImplements(string $class, string $contract): bool
    {
        return isset(class_implements($class)[$contract]);
    }

    private function reflectionClass(): ReflectionClass
    {
        return new ReflectionClass(static::class);
    }
}

<?php

namespace JPNut\EloquentNestedFilter;

use ReflectionClass;
use ReflectionProperty;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class TypeResolver
{
    private static ?DocBlockFactory $docBlockReader;

    private ReflectionProperty $property;

    private ReflectionClass $class;

    /** @var string[] */
    public array $allowedTypes;

    /** @var string[] */
    public array $allowedArrayTypes;

    public function __construct(string $definition, ReflectionProperty $property, ReflectionClass $class)
    {
        $this->property = $property;
        $this->class = $class;
        $this->allowedTypes = $this->resolveAllowedTypes($definition);
        $this->allowedArrayTypes = $this->resolveAllowedArrayTypes($definition);
    }

    public static function fromProperty(ReflectionProperty $property, ReflectionClass $class): self
    {
        /**
         * If there is no doc comment, or the doc comment does not contain the appropriate tag,
         * fallback to the type of the property.
         */
        if ($property->getDocComment() === false
            || empty($varTags = static::docBlockReader()->create($property->getDocComment())->getTagsByName('var'))
            || ! (($tag = $varTags[0]) instanceof Var_)) {
            return new static(
                is_null($type = $property->getType())
                    ? ''
                    : $type->getName(),
                $property,
                $class
            );
        }

        return new static((string) $tag->getType(), $property, $class);
    }

    private static function docBlockReader(): DocBlockFactory
    {
        return static::$docBlockReader ??= DocBlockFactory::createInstance();
    }

    /**
     * @param  string  $definition
     * @return string[]
     */
    protected function resolveAllowedTypes(string $definition): array
    {
        return $this->normaliseTypes(...explode('|', $definition));
    }

    /**
     * @param  string  $definition
     * @return string[]
     */
    protected function resolveAllowedArrayTypes(string $definition): array
    {
        return $this->normaliseTypes(...array_map(
            function (string $type) {
                if (! $type) {
                    return;
                }

                if (strpos($type, '[]') !== false) {
                    return str_replace('[]', '', $type);
                }

                if (strpos($type, 'iterable<') !== false) {
                    return str_replace(['iterable<', '>'], ['', ''], $type);
                }
            },
            explode('|', $definition)
        ));
    }

    private function normaliseType(?string $type): ?string
    {
        if ($type === 'self') {
            return $this->property->getDeclaringClass()->getName();
        }

        if ($type === 'static') {
            return $this->class->getName();
        }

        return $type;
    }

    private function normaliseTypes(?string ...$types): array
    {
        return array_filter(
            array_map(
                /**
                 * @param  string|null $type
                 * @return string|null
                 */
                fn (?string $type) => $this->normaliseType($type),
                $types
            )
        );
    }
}

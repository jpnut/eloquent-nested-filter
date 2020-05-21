<?php

namespace JPNut\EloquentNestedFilter\Tests;

use JPNut\EloquentNestedFilter\TypeResolver;
use JPNut\EloquentNestedFilter\IDFilterObject;
use JPNut\EloquentNestedFilter\DateFilterObject;
use JPNut\EloquentNestedFilter\NumberFilterObject;
use JPNut\EloquentNestedFilter\StringFilterObject;
use JPNut\EloquentNestedFilter\BooleanFilterObject;
use JPNut\EloquentNestedFilter\Tests\Filters\ProductFilter;
use JPNut\EloquentNestedFilter\Tests\Filters\CategoryFilter;
use JPNut\EloquentNestedFilter\Tests\Objects\InvalidFilterObject;

class TypeResolverTest extends TestCase
{
    /**
     * @dataProvider filterablePropertiesProvider
     * @test
     *
     * @param string $class
     * @param string $property
     * @param array $allowed_types
     * @param array $allowed_array_types
     */
    public function it_can_parse_allowed_types(string $class, string $property, array $allowed_types, array $allowed_array_types)
    {
        $reflectionClass = new \ReflectionClass($class);
        $reflectionProperty = new \ReflectionProperty($class, $property);

        $type = TypeResolver::fromProperty($reflectionProperty, $reflectionClass);

        $this->assertEquals(
            $allowed_types,
            $type->allowedTypes
        );

        $this->assertEquals(
            $allowed_array_types,
            $type->allowedArrayTypes
        );
    }

    public function filterablePropertiesProvider(): array
    {
        return [
            [
                ProductFilter::class,
                'and',
                ['static[]'],
                [ProductFilter::class],
            ],
            [
                ProductFilter::class,
                'or',
                ['static[]'],
                [ProductFilter::class],
            ],
            [
                ProductFilter::class,
                'id',
                [IDFilterObject::class],
                [],
            ],
            [
                ProductFilter::class,
                'name',
                [StringFilterObject::class],
                [],
            ],
            [
                ProductFilter::class,
                'amount',
                [NumberFilterObject::class],
                [],
            ],
            [
                ProductFilter::class,
                'in_stock',
                [BooleanFilterObject::class],
                [],
            ],
            [
                ProductFilter::class,
                'created_at',
                [DateFilterObject::class],
                [],
            ],
            [
                ProductFilter::class,
                'category',
                ['\\'.CategoryFilter::class.'[]'],
                ['\\'.CategoryFilter::class],
            ],
            [
                ProductFilter::class,
                'invalid_property',
                [InvalidFilterObject::class],
                [],
            ],
            [
                ProductFilter::class,
                'iterable_property',
                ['iterable<\\'.IDFilterObject::class.'>'],
                ['\\'.IDFilterObject::class],
            ],
            [
                ProductFilter::class,
                'self_property',
                [ProductFilter::class],
                [],
            ],
            [
                ProductFilter::class,
                'ignored_property',
                [],
                [],
            ],
        ];
    }
}

<?php

namespace JPNut\EloquentNestedFilter\Tests;

use JPNut\EloquentNestedFilter\Operator;
use JPNut\EloquentNestedFilter\StringFilterObject;
use JPNut\EloquentNestedFilter\Tests\Models\Product;

class StringFilterObjectTest extends TestCase
{
    /**
     * @param  array  $args
     * @param  string  $query
     * @param  array  $bindings
     *
     * @test
     * @dataProvider queryProvider
     */
    public function it_creates_correct_query_for_given_args(array $args, string $query, array $bindings)
    {
        $this->assertQueryEquals(
            $query,
            $bindings,
            StringFilterObject::fromArray($args)->filter('name', Product::query())
        );
    }

    public function queryProvider(): array
    {
        return [
            [
                ['value' => 'Foo', 'operator' => Operator::IS(), 'case_sensitive' => false],
                'select * from `products` where LOWER(name) = ?',
                ['foo']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::IS(), 'case_sensitive' => true],
                'select * from `products` where `name` = ?',
                ['Foo']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::IS_NOT(), 'case_sensitive' => false],
                'select * from `products` where LOWER(name) <> ?',
                ['foo']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::IS_NOT(), 'case_sensitive' => true],
                'select * from `products` where `name` <> ?',
                ['Foo']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::CONTAINS(), 'case_sensitive' => false],
                'select * from `products` where LOWER(name) LIKE ?',
                ['%foo%']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::CONTAINS(), 'case_sensitive' => true],
                'select * from `products` where `name` LIKE ?',
                ['%Foo%']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::DOES_NOT_CONTAIN(), 'case_sensitive' => false],
                'select * from `products` where LOWER(name) NOT LIKE ?',
                ['%foo%']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::DOES_NOT_CONTAIN(), 'case_sensitive' => true],
                'select * from `products` where `name` NOT LIKE ?',
                ['%Foo%']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::BEGINS(), 'case_sensitive' => false],
                'select * from `products` where LOWER(name) LIKE ?',
                ['foo%']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::BEGINS(), 'case_sensitive' => true],
                'select * from `products` where `name` LIKE ?',
                ['Foo%']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::DOES_NOT_BEGIN(), 'case_sensitive' => false],
                'select * from `products` where LOWER(name) NOT LIKE ?',
                ['foo%']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::DOES_NOT_BEGIN(), 'case_sensitive' => true],
                'select * from `products` where `name` NOT LIKE ?',
                ['Foo%']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::ENDS(), 'case_sensitive' => false],
                'select * from `products` where LOWER(name) LIKE ?',
                ['%foo']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::ENDS(), 'case_sensitive' => true],
                'select * from `products` where `name` LIKE ?',
                ['%Foo']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::DOES_NOT_END(), 'case_sensitive' => false],
                'select * from `products` where LOWER(name) NOT LIKE ?',
                ['%foo']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::DOES_NOT_END(), 'case_sensitive' => true],
                'select * from `products` where `name` NOT LIKE ?',
                ['%Foo']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::LIKE(), 'case_sensitive' => false],
                'select * from `products` where LOWER(name) LIKE ?',
                ['foo']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::LIKE(), 'case_sensitive' => true],
                'select * from `products` where `name` LIKE ?',
                ['Foo']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::NOT_LIKE(), 'case_sensitive' => false],
                'select * from `products` where LOWER(name) NOT LIKE ?',
                ['foo']
            ],
            [
                ['value' => 'Foo', 'operator' => Operator::NOT_LIKE(), 'case_sensitive' => true],
                'select * from `products` where `name` NOT LIKE ?',
                ['Foo']
            ],
            [
                ['value' => null, 'operator' => Operator::NULL()],
                'select * from `products` where `name` is null',
                []
            ],
            [
                ['value' => null, 'operator' => Operator::NOT_NULL()],
                'select * from `products` where `name` is not null',
                []
            ],
        ];
    }

    /** @test */
    public function it_throws_for_invalid_operator()
    {
        $this->expectExceptionMessage('Invalid Operator: LESS_THAN.');

        $filter = new StringFilterObject(null, Operator::LESS_THAN());

        $filter->filter('name', Product::query());
    }
}

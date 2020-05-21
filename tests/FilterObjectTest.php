<?php

namespace JPNut\EloquentNestedFilter\Tests;

use JPNut\EloquentNestedFilter\IDFilterObject;
use JPNut\EloquentNestedFilter\Operator;
use JPNut\EloquentNestedFilter\Tests\Models\Product;

class FilterObjectTest extends TestCase
{
    /** @test */
    public function it_parses_valid_operator()
    {
        $filter = IDFilterObject::fromArray(['value' => 1, 'operator' => 'IS']);

        $this->assertQueryEquals(
            'select * from `products` where `products`.`id` = ?',
            [1],
            $filter->filter('id', Product::query())
        );
    }

    /** @test */
    public function it_allows_instance_of_operator()
    {
        $filter = IDFilterObject::fromArray(['value' => 1, 'operator' => Operator::IS()]);

        $this->assertQueryEquals(
            'select * from `products` where `products`.`id` = ?',
            [1],
            $filter->filter('id', Product::query())
        );
    }

    /** @test */
    public function it_throws_if_invalid_operator_is_used()
    {
        $object = new IDFilterObject(1, Operator::BEGINS());

        $this->expectExceptionMessage("Invalid Operator: BEGINS.");

        $object->filter('id', Product::query());
    }

    /** @test */
    public function it_throws_if_no_operator_is_provided()
    {
        $this->expectExceptionMessage("No operator provided.");

        IDFilterObject::fromArray(['value' => 1]);
    }

    /** @test */
    public function it_throws_if_unrecognised_operator_is_provided()
    {
        $this->expectExceptionMessage("Value 'foo' is not part of the enum JPNut\EloquentNestedFilter\Operator");

        IDFilterObject::fromArray(['value' => 1, 'operator' => 'foo']);
    }
}

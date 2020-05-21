<?php

namespace JPNut\EloquentNestedFilter\Tests;

use JPNut\EloquentNestedFilter\Operator;
use JPNut\EloquentNestedFilter\IDFilterObject;
use JPNut\EloquentNestedFilter\Tests\Models\Product;

class IDFilterObjectTest extends TestCase
{
    /** @test */
    public function it_creates_correct_query_for_is_operator()
    {
        $filter = new IDFilterObject(1, Operator::IS());

        $query = $filter->filter('id', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `products`.`id` = ?',
            [1],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_is_not_operator()
    {
        $filter = new IDFilterObject(1, Operator::IS_NOT());

        $query = $filter->filter('id', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `products`.`id` != ?',
            [1],
            $query
        );
    }

    /** @test */
    public function it_throws_for_invalid_operator()
    {
        $this->expectExceptionMessage('Invalid Operator: BEGINS.');

        $filter = new IDFilterObject(1, Operator::BEGINS());

        $filter->filter('id', Product::query());
    }
}

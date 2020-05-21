<?php

namespace JPNut\EloquentNestedFilter\Tests;

use JPNut\EloquentNestedFilter\BooleanFilterObject;
use JPNut\EloquentNestedFilter\Operator;
use JPNut\EloquentNestedFilter\Tests\Models\Product;

class BooleanFilterObjectTest extends TestCase
{
    /** @test */
    public function it_creates_correct_query_for_is_operator()
    {
        $filter = new BooleanFilterObject(true, Operator::IS());

        $query = $filter->filter('in_stock', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `in_stock` = ?',
            [true],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_is_not_operator()
    {
        $filter = new BooleanFilterObject(true, Operator::IS_NOT());

        $query = $filter->filter('in_stock', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `in_stock` <> ?',
            [true],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_is_null_operator()
    {
        $filter = new BooleanFilterObject(null, Operator::NULL());

        $query = $filter->filter('in_stock', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `in_stock` is null',
            [],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_is_not_null_operator()
    {
        $filter = new BooleanFilterObject(null, Operator::NOT_NULL());

        $query = $filter->filter('in_stock', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `in_stock` is not null',
            [],
            $query
        );
    }

    /** @test */
    public function it_throws_for_invalid_operator()
    {
        $this->expectExceptionMessage("Invalid Operator: BEGINS.");

        $filter = new BooleanFilterObject(true, Operator::BEGINS());

        $filter->filter('in_stock', Product::query());
    }
}

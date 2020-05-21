<?php

namespace JPNut\EloquentNestedFilter\Tests;

use JPNut\EloquentNestedFilter\Operator;
use JPNut\EloquentNestedFilter\NumberFilterObject;
use JPNut\EloquentNestedFilter\Tests\Models\Product;

class NumberFilterObjectTest extends TestCase
{
    /** @test */
    public function it_creates_correct_query_for_is_operator()
    {
        $filter = new NumberFilterObject(1, Operator::IS());

        $query = $filter->filter('amount', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `amount` = ?',
            [1],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_is_not_operator()
    {
        $filter = new NumberFilterObject(1, Operator::IS_NOT());

        $query = $filter->filter('amount', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `amount` <> ?',
            [1],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_less_than_operator()
    {
        $filter = new NumberFilterObject(1, Operator::LESS_THAN());

        $query = $filter->filter('amount', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `amount` < ?',
            [1],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_less_than_or_equal_to_operator()
    {
        $filter = new NumberFilterObject(1, Operator::LESS_THAN_OR_EQUAL_TO());

        $query = $filter->filter('amount', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `amount` <= ?',
            [1],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_greater_than_operator()
    {
        $filter = new NumberFilterObject(1, Operator::GREATER_THAN());

        $query = $filter->filter('amount', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `amount` > ?',
            [1],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_greater_than_or_equal_to_operator()
    {
        $filter = new NumberFilterObject(1, Operator::GREATER_THAN_OR_EQUAL_TO());

        $query = $filter->filter('amount', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `amount` >= ?',
            [1],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_null_operator()
    {
        $filter = new NumberFilterObject(null, Operator::NULL());

        $query = $filter->filter('amount', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `amount` is null',
            [],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_not_null_operator()
    {
        $filter = new NumberFilterObject(null, Operator::NOT_NULL());

        $query = $filter->filter('amount', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `amount` is not null',
            [],
            $query
        );
    }

    /** @test */
    public function it_throws_for_invalid_operator()
    {
        $this->expectExceptionMessage('Invalid Operator: BEGINS.');

        $filter = new NumberFilterObject(null, Operator::BEGINS());

        $filter->filter('amount', Product::query());
    }
}

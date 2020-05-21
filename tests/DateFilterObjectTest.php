<?php

namespace JPNut\EloquentNestedFilter\Tests;

use Carbon\Carbon;
use JPNut\EloquentNestedFilter\Operator;
use JPNut\EloquentNestedFilter\DateFilterObject;
use JPNut\EloquentNestedFilter\Tests\Models\Product;

class DateFilterObjectTest extends TestCase
{
    /** @test */
    public function it_creates_correct_query_for_is_operator()
    {
        $filter = new DateFilterObject(Carbon::parse('2020-01-01'), Operator::IS());

        $query = $filter->filter('created_at', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where date(`created_at`) = ?',
            ['2020-01-01'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_is_not_operator()
    {
        $filter = new DateFilterObject(Carbon::parse('2020-01-01'), Operator::IS_NOT());

        $query = $filter->filter('created_at', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where date(`created_at`) <> ?',
            ['2020-01-01'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_less_than_operator()
    {
        $filter = new DateFilterObject(Carbon::parse('2020-01-01'), Operator::LESS_THAN());

        $query = $filter->filter('created_at', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `created_at` < ?',
            ['2020-01-01T00:00:00.000000Z'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_less_than_or_equal_to_operator()
    {
        $filter = new DateFilterObject(Carbon::parse('2020-01-01'), Operator::LESS_THAN_OR_EQUAL_TO());

        $query = $filter->filter('created_at', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `created_at` <= ?',
            ['2020-01-01T00:00:00.000000Z'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_greater_than_operator()
    {
        $filter = new DateFilterObject(Carbon::parse('2020-01-01'), Operator::GREATER_THAN());

        $query = $filter->filter('created_at', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `created_at` > ?',
            ['2020-01-01T00:00:00.000000Z'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_greater_than_or_equal_to_operator()
    {
        $filter = new DateFilterObject(Carbon::parse('2020-01-01'), Operator::GREATER_THAN_OR_EQUAL_TO());

        $query = $filter->filter('created_at', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `created_at` >= ?',
            ['2020-01-01T00:00:00.000000Z'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_null_operator()
    {
        $filter = new DateFilterObject(null, Operator::NULL());

        $query = $filter->filter('created_at', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `created_at` is null',
            [],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_not_null_operator()
    {
        $filter = new DateFilterObject(null, Operator::NOT_NULL());

        $query = $filter->filter('created_at', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `created_at` is not null',
            [],
            $query
        );
    }

    /** @test */
    public function it_throws_for_invalid_operator()
    {
        $this->expectExceptionMessage('Invalid Operator: BEGINS.');

        $filter = new DateFilterObject(null, Operator::BEGINS());

        $filter->filter('created_at', Product::query());
    }
}

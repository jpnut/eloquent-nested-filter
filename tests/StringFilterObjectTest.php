<?php

namespace JPNut\EloquentNestedFilter\Tests;

use JPNut\EloquentNestedFilter\Operator;
use JPNut\EloquentNestedFilter\StringFilterObject;
use JPNut\EloquentNestedFilter\Tests\Models\Product;

class StringFilterObjectTest extends TestCase
{
    /** @test */
    public function it_creates_correct_query_for_is_operator()
    {
        $filter = new StringFilterObject('foo', Operator::IS());

        $query = $filter->filter('name', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `name` = ?',
            ['foo'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_is_not_operator()
    {
        $filter = new StringFilterObject('foo', Operator::IS_NOT());

        $query = $filter->filter('name', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `name` <> ?',
            ['foo'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_contains_operator()
    {
        $filter = new StringFilterObject('foo', Operator::CONTAINS());

        $query = $filter->filter('name', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `name` LIKE ?',
            ['%foo%'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_does_not_contain_operator()
    {
        $filter = new StringFilterObject('foo', Operator::DOES_NOT_CONTAIN());

        $query = $filter->filter('name', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `name` NOT LIKE ?',
            ['%foo%'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_begins_operator()
    {
        $filter = new StringFilterObject('foo', Operator::BEGINS());

        $query = $filter->filter('name', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `name` LIKE ?',
            ['foo%'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_does_not_begin_operator()
    {
        $filter = new StringFilterObject('foo', Operator::DOES_NOT_BEGIN());

        $query = $filter->filter('name', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `name` NOT LIKE ?',
            ['foo%'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_ends_operator()
    {
        $filter = new StringFilterObject('foo', Operator::ENDS());

        $query = $filter->filter('name', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `name` LIKE ?',
            ['%foo'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_does_not_end_operator()
    {
        $filter = new StringFilterObject('foo', Operator::DOES_NOT_END());

        $query = $filter->filter('name', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `name` NOT LIKE ?',
            ['%foo'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_like_operator()
    {
        $filter = new StringFilterObject('foo', Operator::LIKE());

        $query = $filter->filter('name', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `name` LIKE ?',
            ['foo'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_not_like_operator()
    {
        $filter = new StringFilterObject('foo', Operator::NOT_LIKE());

        $query = $filter->filter('name', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `name` NOT LIKE ?',
            ['foo'],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_null_operator()
    {
        $filter = new StringFilterObject(null, Operator::NULL());

        $query = $filter->filter('name', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `name` is null',
            [],
            $query
        );
    }

    /** @test */
    public function it_creates_correct_query_for_not_null_operator()
    {
        $filter = new StringFilterObject(null, Operator::NOT_NULL());

        $query = $filter->filter('name', Product::query());

        $this->assertQueryEquals(
            'select * from `products` where `name` is not null',
            [],
            $query
        );
    }

    /** @test */
    public function it_throws_for_invalid_operator()
    {
        $this->expectExceptionMessage('Invalid Operator: LESS_THAN.');

        $filter = new StringFilterObject(null, Operator::LESS_THAN());

        $filter->filter('name', Product::query());
    }
}

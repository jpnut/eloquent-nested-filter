<?php

namespace JPNut\EloquentNestedFilter\Tests;

use Carbon\Carbon;
use JPNut\EloquentNestedFilter\BooleanFilterObject;
use JPNut\EloquentNestedFilter\DateFilterObject;
use JPNut\EloquentNestedFilter\IDFilterObject;
use JPNut\EloquentNestedFilter\NumberFilterObject;
use JPNut\EloquentNestedFilter\Operator;
use JPNut\EloquentNestedFilter\StringFilterObject;
use JPNut\EloquentNestedFilter\Tests\Filters\CategoryFilter;
use JPNut\EloquentNestedFilter\Tests\Models\Product;
use JPNut\EloquentNestedFilter\Tests\Filters\ProductFilter;
use JPNut\EloquentNestedFilter\Tests\Objects\InvalidFilterObject;

class FilterTest extends TestCase
{
    /** @test */
    public function it_can_build_filter_from_array()
    {
        $filter = new ProductFilter([
            'and' => [
                [
                    'id' => [
                        'value' => 1,
                        'operator' => 'IS',
                    ],
                ],
                [
                    'name' => [
                        'value' => 'foo',
                        'operator' => 'CONTAINS',
                    ],
                ],
                [
                    'or' => [
                        [
                            'amount' => [
                                'value' => 10,
                                'operator' => 'GREATER_THAN_OR_EQUAL_TO',
                            ],
                        ],
                        [
                            'amount' => [
                                'operator' => 'NULL',
                            ],
                        ],
                        [
                            'in_stock' => [
                                'value' => true,
                                'operator' => 'IS',
                            ],
                        ],
                        [
                            'created_at' => [
                                'value' => '2020-01-01',
                                'operator' => 'LESS_THAN',
                            ],
                        ],
                    ],
                ],
                [
                    'category' => [
                        [
                            'name' => [
                                'value' => 'bar',
                                'operator' => 'BEGINS'
                            ]
                        ]
                    ]
                ]
            ],
        ]);

        $query = $filter->filter(Product::query());

        $this->assertEquals(
            'select * from `products` where ((`products`.`id` = ?) and (`name` LIKE ?) and (((`amount` >= ?) or (`amount` is null) or (`in_stock` = ?) or (`created_at` < ?))) and (exists (select * from `categories` where `products`.`category_id` = `categories`.`id` and (`name` LIKE ?))))',
            $query->toSql()
        );

        $this->assertEquals(
            ['1', '%foo%', 10, true, '2020-01-01T00:00:00.000000Z', 'bar%'],
            json_decode(json_encode($query->getBindings())),
        );
    }

    /** @test */
    public function it_can_build_filter_from_objects()
    {
        $filter = new ProductFilter([
            'and' => [
                new ProductFilter([
                    'id' => new IDFilterObject(1, Operator::IS()),
                ]),
                new ProductFilter([
                    'name' => new StringFilterObject('foo', Operator::CONTAINS()),
                ]),
                new ProductFilter([
                    'or' => [
                        new ProductFilter([
                            'amount' => new NumberFilterObject(10, Operator::GREATER_THAN_OR_EQUAL_TO()),
                        ]),
                        new ProductFilter([
                            'amount' => new NumberFilterObject(null, Operator::NULL()),
                        ]),
                        new ProductFilter([
                            'in_stock' => new BooleanFilterObject(true, Operator::IS()),
                        ]),
                        new ProductFilter([
                            'created_at' => new DateFilterObject(Carbon::parse('2020-01-01'), Operator::LESS_THAN()),
                        ]),
                    ],
                ]),
                new ProductFilter([
                    'category' => [
                        new CategoryFilter([
                            'name' => new StringFilterObject('bar', Operator::BEGINS()),
                        ])
                    ]
                ])
            ],
        ]);

        $query = $filter->filter(Product::query());

        $this->assertEquals(
            'select * from `products` where ((`products`.`id` = ?) and (`name` LIKE ?) and (((`amount` >= ?) or (`amount` is null) or (`in_stock` = ?) or (`created_at` < ?))) and (exists (select * from `categories` where `products`.`category_id` = `categories`.`id` and (`name` LIKE ?))))',
            $query->toSql()
        );

        $this->assertEquals(
            ['1', '%foo%', 10, true, '2020-01-01T00:00:00.000000Z', 'bar%'],
            json_decode(json_encode($query->getBindings())),
        );
    }

    /** @test */
    public function it_ignores_non_filter_properties()
    {
        $filter = new ProductFilter([
            'ignored_property' => true,
        ]);

        $query = $filter->filter(Product::query());

        $this->assertEquals(
            'select * from `products`',
            $query->toSql()
        );
    }

    /** @test */
    public function it_throws_exception_if_invalid_filter_value_passed()
    {
        $this->expectExceptionMessage("Expected value of 'id' to be instance of '".IDFilterObject::class."' or array.");

        new ProductFilter([
            'id' => true
        ]);
    }

    /** @test */
    public function it_throws_exception_if_unknown_filter_object_used()
    {
        $this->expectExceptionMessage("Could not construct filter object for property 'invalid_property' of class '".InvalidFilterObject::class."'");

        new ProductFilter([
            'invalid_property' => [],
        ]);
    }
}

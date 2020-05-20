<?php

namespace JPNut\EloquentNestedFilter\Tests;

use JPNut\EloquentNestedFilter\Tests\Filters\ProductFilter;
use JPNut\EloquentNestedFilter\Tests\Models\Product;

class AbstractFilterTest extends TestCase
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
                    ]
                ],
                [
                    'name' => [
                        'value' => 'foo',
                        'operator' => 'CONTAINS'
                    ]
                ],
                [
                    'or' => [
                        [
                            'amount' => [
                                'value' => 10,
                                'operator' => 'GREATER_THAN_OR_EQUAL_TO'
                            ]
                        ],
                        [
                            'amount' => [
                                'operator' => 'NULL'
                            ]
                        ],
                        [
                            'in_stock' => [
                                'value' => true,
                                'operator' => 'IS'
                            ]
                        ],
                        [
                            'created_at' => [
                                'value' => '2020-01-01',
                                'operator' => 'LESS_THAN'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $query = $filter->filter(Product::query());

        $this->assertEquals(
            'select * from `products` where ((`products`.`id` = ?) and (`name` LIKE ?) and (((`amount` >= ?) or (`amount` is null) or (`in_stock` = ?) or (`created_at` < ?))))',
            $query->toSql()
        );

        $this->assertEquals(
            ['1', '%foo%', 10, true, '2020-01-01T00:00:00.000000Z'],
            json_decode(json_encode($query->getBindings())),
        );
    }
}

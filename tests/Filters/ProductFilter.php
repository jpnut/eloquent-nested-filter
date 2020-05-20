<?php

namespace JPNut\EloquentNestedFilter\Tests\Filters;

use JPNut\EloquentNestedFilter\AbstractFilter;
use JPNut\EloquentNestedFilter\IDFilterObject;
use JPNut\EloquentNestedFilter\DateFilterObject;
use JPNut\EloquentNestedFilter\NumberFilterObject;
use JPNut\EloquentNestedFilter\StringFilterObject;
use JPNut\EloquentNestedFilter\BooleanFilterObject;
use JPNut\EloquentNestedFilter\Contracts\ResourceFilter;

class ProductFilter extends AbstractFilter implements ResourceFilter
{
    public ?IDFilterObject $id;

    public ?StringFilterObject $name;

    public ?NumberFilterObject $amount;

    public ?BooleanFilterObject $in_stock;

    public ?DateFilterObject $created_at;
}

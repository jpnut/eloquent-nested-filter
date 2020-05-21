<?php

namespace JPNut\EloquentNestedFilter\Tests\Filters;

use JPNut\EloquentNestedFilter\AbstractFilter;
use JPNut\EloquentNestedFilter\IDFilterObject;
use JPNut\EloquentNestedFilter\DateFilterObject;
use JPNut\EloquentNestedFilter\NumberFilterObject;
use JPNut\EloquentNestedFilter\StringFilterObject;
use JPNut\EloquentNestedFilter\BooleanFilterObject;
use JPNut\EloquentNestedFilter\Tests\Objects\InvalidFilterObject;

class ProductFilter extends AbstractFilter
{
    public ?IDFilterObject $id = null;

    public ?StringFilterObject $name = null;

    public ?NumberFilterObject $amount = null;

    public ?BooleanFilterObject $in_stock = null;

    public ?DateFilterObject $created_at = null;

    /** @var \JPNut\EloquentNestedFilter\Tests\Filters\CategoryFilter[]|null */
    public ?array $category;

    public ?InvalidFilterObject $invalid_property;

    /** @var iterable<\JPNut\EloquentNestedFilter\IDFilterObject>|null */
    public $iterable_property;

    /** @var self|null */
    public $self_property;

    public $ignored_property;
}

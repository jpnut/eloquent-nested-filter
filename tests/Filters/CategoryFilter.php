<?php

namespace JPNut\EloquentNestedFilter\Tests\Filters;

use JPNut\EloquentNestedFilter\AbstractFilter;
use JPNut\EloquentNestedFilter\IDFilterObject;
use JPNut\EloquentNestedFilter\DateFilterObject;
use JPNut\EloquentNestedFilter\StringFilterObject;

class CategoryFilter extends AbstractFilter
{
    public ?IDFilterObject $id = null;

    public ?StringFilterObject $name = null;

    public ?DateFilterObject $created_at = null;
}

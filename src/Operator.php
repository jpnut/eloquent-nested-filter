<?php

declare(strict_types=1);

namespace JPNut\EloquentNestedFilter;

use MyCLabs\Enum\Enum;

/**
 * @method static Operator IS()
 * @method static Operator IS_NOT()
 * @method static Operator LIKE()
 * @method static Operator NOT_LIKE()
 * @method static Operator CONTAINS()
 * @method static Operator DOES_NOT_CONTAIN()
 * @method static Operator BEGINS()
 * @method static Operator DOES_NOT_BEGIN()
 * @method static Operator ENDS()
 * @method static Operator DOES_NOT_END()
 * @method static Operator LESS_THAN()
 * @method static Operator LESS_THAN_OR_EQUAL_TO()
 * @method static Operator GREATER_THAN()
 * @method static Operator GREATER_THAN_OR_EQUAL_TO()
 * @method static Operator NULL()
 * @method static Operator NOT_NULL()
 *
 * @psalm-immutable
 */
class Operator extends Enum
{
    private const IS = 'IS';
    private const IS_NOT = 'IS_NOT';
    private const LIKE = 'LIKE';
    private const NOT_LIKE = 'NOT_LIKE';
    private const CONTAINS = 'CONTAINS';
    private const DOES_NOT_CONTAIN = 'DOES_NOT_CONTAIN';
    private const BEGINS = 'BEGINS';
    private const DOES_NOT_BEGIN = 'DOES_NOT_BEGIN';
    private const ENDS = 'ENDS';
    private const DOES_NOT_END = 'DOES_NOT_END';
    private const LESS_THAN = 'LESS_THAN';
    private const LESS_THAN_OR_EQUAL_TO = 'LESS_THAN_OR_EQUAL_TO';
    private const GREATER_THAN = 'GREATER_THAN';
    private const GREATER_THAN_OR_EQUAL_TO = 'GREATER_THAN_OR_EQUAL_TO';
    private const NULL = 'NULL';
    private const NOT_NULL = 'NOT_NULL';
}

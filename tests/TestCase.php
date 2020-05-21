<?php

namespace JPNut\EloquentNestedFilter\Tests;

use Illuminate\Database\Eloquent\Builder;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    //

    protected function assertQueryEquals(string $sql, array $bindings, Builder $query)
    {
        $this->assertEquals(
            $sql,
            $query->toSql()
        );

        $this->assertEquals(
            $bindings,
            json_decode(json_encode($query->getBindings()))
        );
    }
}

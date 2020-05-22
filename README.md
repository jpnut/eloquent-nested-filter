# Eloquent Nested Filter

[![Version](https://img.shields.io/packagist/v/jpnut/eloquent-nested-filter?style=flat-square)](https://packagist.org/packages/jpnut/eloquent-nested-filter)
[![StyleCI](https://github.styleci.io/repos/265613845/shield?branch=master)](https://styleci.io/repos/265613845)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/jpnut/eloquent-nested-filter/run-tests?style=flat-square)
![License](https://img.shields.io/github/license/jpnut/eloquent-nested-filter?style=flat-square)

This package provides a way to quickly and succinctly define a nested filter structure for Eloquent models. This makes it easier to filter data in more complex ways based on user input.

For example, given a filter object defined as follows:

```php
...

class ProductFilter extends AbstractFilter
{
    public ?StringFilterObject $name;
}
```

we can use this to filter our `Product` query:

```php
    ...

    $filter = new ProductFilter([
        'or' => [
            [
                'name' => [
                    'value' => 'foo',
                    'operator' => 'BEGINS',
                ]
            ],
            [
                'name' => [
                    'value' => 'foo',
                    'operator' => 'ENDS',
                ]
            ],
        ]
    ]);

    $query = $filter->filter(Product::query());

    ...
```

which produces an sql query:

```sql
SELECT * FROM `products` WHERE (`name` LIKE 'foo%' or `name` LIKE '%foo')
```

## Installation

You can install the package via composer:

```shell script
composer require jpnut/eloquent-nested-filter
```

## Usage

To get started, you'll need to create a filter class for each model you wish to filter. This class should extend `JPNut\EloquentNestedFilter\AbstractFilter`. In this class, you should define all the filterable properties. This package ships with built in filters defined for primary keys, strings, numbers, booleans, and datetimes; though you are free to create and use any additional filters. It is also possible to filter relationships by including a property which refers to the filter class of a related model. Let's take a look at an example filter class:

```php
// app/ProductFilter.php

namespace App;

...

class ProductFilter extends AbstractFilter
{
    public ?IDFilterObject $id = null;

    public ?StringFilterObject $name = null;

    public ?NumberFilterObject $amount = null;

    public ?BooleanFilterObject $in_stock = null;

    public ?DateFilterObject $created_at = null;

    /** @var \App\CategoryFilter[]|null */
    public ?array $category = null;
}

```

First of all, note that we are defining the filter type of each property through it's type declaration. For array filters such as the `category` property, we must specify the filter type in the doc block instead since php does not currently support generics. Every instance of `AbstractFilter` also has pre-defined `and` and `or` properties which expect an array of the parent class. These properties allow us to group filters together in the expected way.

Since manually constructing these objects would be tedious, the constructor takes a single associative array and will attempt to cast the filter properties based on the type information. Of course, if an instance of the correct object is passed, that object will be used. However, in the case that an array is passed, the class will instead attempt to build the correct array/object.

Let's re-use the example we defined at the start of this readme:

```php
...

$filter = new ProductFilter([
    'or' => [
        [
            'name' => [
                'value' => 'foo',
                'operator' => 'BEGINS',
            ]
        ],
        [
            'name' => [
                'value' => 'foo',
                'operator' => 'ENDS',
            ]
        ],
    ]
]);

...
```

The `or` property is composed of an array of `ProductFilter` instances. In this case, we are passing through two associative arrays - these will automatically be casted to instances of `ProductFilter` by passing their value to the constructor of `ProductFilter`. The `name` filters are constructed in a similar way.

### Typical Request Workflow

The main use case for this library is from user-input. Typically this means that the `filter` associative array will be supplied as a JSON encoded string. The filter can then be constructed by passing in the decoded value:

```php
...

$filter = $request->has('filter')
    ? new ProductFilter(json_decode($request->query('filter'), true))
    : null;

...
```

Instances of `AbstractFilter` expose a `filter` method which expect a single argument - the base eloquent query. Typically this would be `Product::query()`, though it is of course possible to scope or manipulate the filter should you wish:

```php
...

$results = $filter->query(Product::query()->withTrashed())->get();

...
```

### Custom Filter Objects

You may wish to add new filter objects. To do so, simply create a new class which extends `JPNut\EloquentNestedFilter\AbstractFilterObject`. Your class _must_ define two methods:

```php
abstract public function filter(string $name, Builder $query): Builder;

abstract public static function fromArray(array $properties = []): self;
```

The `filter` method takes two arguments - the name of the field being filtered, and the query instance - and should return the modified query instance. Typically the filter method should handle all valid `Operator` values (e.g. `IS`, `IS_NOT` etc.), and throw an exception if an invalid operator is used. For example:

```php
    public function filter(string $name, Builder $query): Builder
    {
        if ($this->operator->equals(Operator::IS())) {
            return $query->where($name, $this->value);
        }

        if ($this->operator->equals(Operator::IS_NOT())) {
            return $query->where($name, '<>', $this->value);
        }

        ...

        throw $this->invalidOperator($this->operator);
    }
```

The `fromArray` method takes a single argument - the array of properties - and should return a new instance of the filter object. This method is called when creating the filter object from an associative array. This is a good way to validate or cast the filter value. For example, let's take a look at the `fromArray` function from the `NumberFilterObject`:

```php
    public static function fromArray(array $properties = []): self
    {
        return new static(
            is_null($properties['value'] ?? null) ? null : floatval($properties['value']),
            static::operatorFromProperties($properties)
        );
    }
```

Here you can see we permit null values, and otherwise attempt to cast the value to a float. We also get an instance of the `Operator` enum based on the supplied `operator` key-value pair.

## Validation / Query Complexity

Without validating or otherwise limiting the supplied query, it is possible for users to build very expensive queries, even without malicious intent. Presently, this library does **not** attempt to validate or calculate the complexity of queries. As an alternative, it is possible to set a maximum filter depth, and a maximum filter total. The depth refers to nested instances of `AbstractFilter`: for example, each nested `and` or `or` filter increments the depth by 1. The total number of filters is determined by the number of instances of `AbstractFitler` and `AbstractFilterObject`.

Returning to the example at the top of this file, we have a depth of 1 (since there is only 1 nested `AbstractFilter` instance, generated by the `or` statement) and a total filter number of 3 (1 for the `or` statement, and 1 for each of the `name` constraints). By default, the maximum depth is set to 10, and the maximum permitted filters to 100, though this can be modified by changing the default property values:

```php
...

class ProductFilter extends AbstractFilter
{
    protected ?int $max_depth = 5;

    protected ?int $max_filters = 50;
}

...
```

In order to disable these limits, simply set the property value to null.

## Testing

```shell script
vendor/bin/phpunit
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
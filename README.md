# Ginq

### Array handling in PHP? Be happy with Ginq!

**Ginq** is a **DSL** that can handle arrays and iterators of PHP unified.

**Ginq** is inspired by **Linq to Object**, but is not a clone.

Many functions in **Ginq** are evaluated in lazy, and no actions are taken until that time. This features bring you many benefits.

# Install

composer.json:

```json
{
    "require": {
        "ginq/ginq": "~0.2.3"
    }
}
```

see: https://packagist.org/packages/ginq/ginq

# Usage

```php
$xs = Ginq::from(array(1,2,3,4,5,6,7,8,9,10))
        ->where(function($x) { return $x % 2 != 0; })
        ->select(function($x) { return $x * $x; });
        ;
```
You pass **Ginq** data and build a query with it. In this example above, you order **Ginq** to choose even numbers and square each of them.

But **Ginq** do nothing, **Ginq** knows only you want a result of chosen and squared numbers.

Let's execute `foreach` loop with **Ginq** to get the result.

```php
foreach ($xs as $x) { echo "$x "; }
```
The result is 
```
1 9 25 49 81
```
You got the expected result!

Next, you can get an array with `toList`.

```php
$xs->toList();
```
```
array(1,9,25,49,81);
```

**Ginq** has functions, well-known in SQL, such as `join()`, `orderBy()`, and `groupBy()` other than `select()`, `where()` listed above.

##Selector and Predicate
Most of methods in **Ginq** receive a closure as a argument.

You may not be familiar with closures, but it is very simple things.
There are just three types of closures in **Ginq**, you can remember simply.
These are predicate, selector, and connection selector.

###Predicate
A closure that passed to a method that do select, such as `where()` is called **predicate**.

Predicate is a closure that receive a pair of key and values in the elements and return boolean value.

```php
function ($v, [$k]) { return $v % 2 == 0; }
```
You get even numbers when you pass this closure to `where()`.
You can skip second argument when you don't need it in the process. 

###Selector
A closure that passed to a method that do projection, such as `select()` is called **selector**.

Selector is a closure that receive a pair of key and value in the elements and create a new value or key, and then return it.

```php
function ($v, [$k]) { return $v * $v ; }
```

You get squared numbers of original when you pass this closure to `select()`.

This function is used to specify the key of grouping with `groupBy()`, the key of sorting with `groupBy()`. 

###Connection Selector
**Connection Selector** is one of the selector that combine two elements into one, is used with `join()`, `zip()`.

```php
function ($v0, $v1, [$k0, $k1]) { return array($v0, $v1) ; }
```

This function receive 4 arguments, two values and two keys, and then create new value or key and return it.
You can skip arguments when you don't need it in the process. 

These are `zip()` example that combine each elements from two arrays.

```php
$foods  = array("meat", "pasta", "salada");
$spices = array("time", "basil", "dill");

$xs = Ginq::from($foods)
        ->zip($spices, function($f, $s) {
            return "$f with $s!";
        })
        ;

foreach ($xs as $x) { echo "$x\n"; }
```

```
meat with time!
pasta with basil!
salada with dill!
```

## Shortcuts of predicate and selector

**Selector** can receive a character string instead of a closure.

They return the value of the field when the element is an object, or return the value of the key when it is an array.

So,

```php
Ginq::from($xs)->select('[key].property');
```

The example above is same as two examples below.

```php
Ginq::from($xs)->select(
    function ($v, $k) { return $v['key'].property; }
);
```

see: Property Access (Symfony)
http://symfony.com/doc/current/components/property_access/index.html

## More complex examples

## References


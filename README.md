# Ginq

### Array handling in PHP? Be happy with Ginq!

**Ginq** is a **DSL** that can handle array and iterator of PHP unified.

Many functions in **Ginq** are evaluated in lazy, and no actions are taken until that time. This features brings you many benefits.

**Ginq** is inspired by **Linq to Object**, but is not a clone.

Do you need more functions? Don't worry, **Ginq** supports plug-ins, you can add features you need.
Feel free to let me know if you request new functions into **Ginq**.

# Usage

```php
require_once('Ginq.php');
```

```php
$xs = Ginq::from([1,2,3,4,5,6,7,8,9,10])
        ->where(function($x) { return $x % 2 != 0; })
        ->select(function($x) { return $x * $x; });

// $xs is lazy sequence.
foreach ($xs in $x) { echo "$x "; }
```

```
1 9 25 49 81
```

# Shortcut

# Reference

Ginq::zero()
--------
`Ginq::zero()` is empty sequence.


Ginq::range($start, $stop = null, $step = 1)
--------
`Ginq::range($start, $stop, $step)` is sequence of numbers with common difference.

```php
// finite sequence
Ginq::range(1,10)->toArray();
=> array(1,2,3,4,5,6,7,8,9,10)

// finite sequence with step
Ginq::range(1,10, 2)->toArray(); 
=> array(1,3,5,7,9)

// finite sequence with negative step
Ginq::range(0,-9, -1)->toArray(); 
=> array(0,-1,-2,-3,-4,-5,-6,-7,-8,-9)

// infinite sequence
Ginq::range(1)->take(10)->toArray(); 
=> array(1,2,3,4,5,6,7,8,9,10)

// infinite sequence with step
Ginq::range(10, null, 5)->take(5)->toArray(); 
=> array(10,15,20,25,30)
```

Ginq::repeat($x)
--------
`Ginq::repeat($x)` is an infinite sequence, with `$x` the value of every element.

```php
$xs = Ginq::repeat("foo")->take(3)->toArray();
=> array("foo","foo","foo")
```

Ginq::cycle($xs)
--------

```php
$data = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
Ginq::cycle($data)->take(10)->toArray();
=> array('Mon','Tue','Wed','Thu','Fri','Sat','Sun','Mon','Tue','Wed')
```

Is's useful to using with **zip**.


Ginq::from($xs)
--------
`Ginq::from($xs)` is lazy sequence of `Iteratot` or `IteratotAggregate` or `array`. `Ginq` is an `IteratorAggregate` too.

```php
// array
$arr = Ginq::from(array(1,2,3,4,5))->toArray();
=> array(1,2,3,4,5)
        
// Iterator
$arr = Ginq::from(new ArrayIterator(array(1,2,3,4,5)))->toArray();
=> array(1,2,3,4,5)
        
// IteratorAggregate
$arr = Ginq::from(new ArrayObject(array(1,2,3,4,5)))->toArray();
=> array(1,2,3,4,5)

// Ginq object (IteratorAggregate)
$arr = Ginq::from(Ginq::from(array(1,2,3,4,5)))->toArray();
=> array(1,2,3,4,5)
```

select($selector)
--------

```php
// selector function
Ginq::from(array(1,2,3,4,5))
      ->select(function($x) { return $x * $x; })
      ->toArray();
=> array(1,4,9,16,25)

// key selector string
$data = array(
     array('id' => 1, 'name' => 'Taro',    'city' => 'Takatsuki')
    ,array('id' => 2, 'name' => 'Atsushi', 'city' => 'Ibaraki')
    ,array('id' => 3, 'name' => 'Junko',   'city' => 'Sakai')
);
Ginq::from($data)->select("name")->toArray();
=> array('Taro','Atsushi','Junko')

// field selector string
$data = array(
     new Person(1, 'Taro',    'Takatsuki')
    ,new Person(2, 'Atsushi', 'Ibaraki')
    ,new Person(3, 'Junko',   'Sakai')
);
Ginq::from($data)->select("name")->toArray();
=> array('Taro','Atsushi','Junko')
```

where($predicate)
--------

```php
$xs = Ginq::from(array(1,2,3,4,5,6,7,8,9,10))
        ->where(function($x) { return ($x % 2) == 0; })
        ->toArray();
=> array(2,4,6,8,10)
```

take($n)
--------

```php

```

drop($n)
--------

```php
```

takeWhile($predicate)
--------

```php
```

dropWhile($predicate)
--------

```php
```

concat($rhs)
--------

```php
```

selectMany($manySelector, $joinSelector = null)
--------

```php
```

join($outerKeySelector, $innerKeySelector, $joinSelector)
--------

```php
$persons = array(
     array('id' => 1, 'name' => 'Taro')
    ,array('id' => 2, 'name' => 'Atsushi')
    ,array('id' => 3, 'name' => 'Junko')
);

$phones = array(
     array('id' => 1, 'owner' => 1, 'phone' => '03-1234-5678')
    ,array('id' => 2, 'owner' => 1, 'phone' => '090-8421-9061')
    ,array('id' => 3, 'owner' => 2, 'phone' => '050-1198-4458')
    ,array('id' => 4, 'owner' => 3, 'phone' => '06-1111-3333')
    ,array('id' => 5, 'owner' => 3, 'phone' => '090-9898-1314')
    ,array('id' => 6, 'owner' => 3, 'phone' => '050-6667-2231')
);

$xs = Ginq::from($persons)->join($phones,
    'id', 'owner',
    function($outer, $inner) {
        return array($outer['name'], $inner['phone']);
    }
)->toArray();

=> array(
     array('Taro', '03-1234-5678')
    ,array('Taro', '090-8421-9061')
    ,array('Atsushi', '050-1198-4458')
    ,array('Junko', '06-1111-3333')
    ,array('Junko', '090-9898-1314')
    ,array('Junko', '050-6667-2231')
)
```

zip($rhs, $joinSelector)
--------

```php
```

groupBy($keySelector, $elementSelector = null)
--------

```php
```

toArray()
--------

```php
```

toArrayRec()
--------

```php
```

any($predicate)
--------

```php
```

all($predicate)
--------

```php
```

first($default = null)
--------

```php
```

find($predicate, $default = null)
--------

```php
```

fold($accumulator, $operator)
--------

```php
// sum of 1 .. 10
Ginq::range(1, 10)->fold(0, function($acc, $x) {
    return $acc + $x
});
=> 55;
```

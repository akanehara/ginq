# Ginq

### Array handling in PHP? Be happy with Ginq!

**Ginq** is a **DSL** that can handle arrays and iterators of PHP unified.

**Ginq** is inspired by **Linq to Object**, but is not a clone.

Many functions in **Ginq** are evaluated in lazy, and no actions are taken until that time. This features bring you many benefits.

# Usage

```php
require_once('Ginq.php');
```
Import **Ginq**.

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
foreach ($xs in $x) { echo "$x "; }
```
The result is 
```
1 9 25 49 81
```
You got the expected result!

Next, you can an array with `toList`.

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
These are predicate, selector, and connect selector.

###Predicate
A closure that passed to a method that do select, such as `where()` is called **predicate**.

Predicate is a closure that receive a pair of key and values in the elements and return boolean value.

```php
function ($v, [$k]) { return $v % 2 == 0; }
```
You can even numbers when you pass this closure to `where()`.
You can skip second argument when you don't need it in the process. 

###Selector


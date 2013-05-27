# Ginq

### Array handling in PHP? Be happy with Ginq!

**Ginq** is a **DSL** that can handle arrays and iterators of PHP unified.

Many functions in **Ginq** are evaluated in lazy, and no actions are taken until that time. This features bring you many benefits.

**Ginq** is inspired by **Linq to Object**, but is not a clone.

# Usage

```php
require_once('Ginq.php');
```
Copy wherever you want to, and import Ginq.php simply.

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

```php
$xs->toList();
```
```
array(1,9,25,49,81);
```


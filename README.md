Ginq

Ginq (Generator INtegrated Query), provides integrative interface for `array`, `Iterator`, `IteratorAggregate`, by generator on php5.5 or later.

```php
require_once('Ginq.php');
```

from / select / where
--------

```php:Example
$xs = Ginq::from([1,2,3,4,5,6,7,8,9,10])
        ->where(function($x) { return $x % 2 != 0; })
        ->select(function($x) { return $x * $x; });

foreach ($xs in $x) { echo "$x "; }
```

```:Output
1 9 25 49 81
```

join
--------

join($inner, $outerKeySelector, $innderKeySelector, $selector)

$inner

`outerKeySelector` and `innderKeySelector` are field/key string, or callable.

`selector` is function, has two parameters.
`function ($outer, $inner)`

```php:Example
$items = [
      ['title' => '吾輩は猫である', 'genre' => 3]
    , ['title' => 'パーフェクトPHP', 'genre' => 1]
    , ['title' => '男はつらいよ', 'genre' => 2]
    , ['title' => 'ダークナイト', 'genre' => 2]
    , ['title' => '星の王子さま', 'genre' => 3]
    , ['title' => 'MISS TAKE ～僕はミス・テイク～', 'genre' => 4]
    , ['title' => 'すごいHaskell楽しく学ぼう', 'genre' => 1]
    , ['title' => 'Scalaスケーラブルプログラミング', 'genre' => 1]
    , ['title' => '変身', 'genre' => 3]
    , ['title' => 'キングコング２', 'genre' => 2]
    , ['title' => 'エリーゼのために', 'genre' => 4]
    , ['title' => 'チョコレート・ファイター', 'genre' => 2]
    ];

$genres = [
      ['id' => 1, 'name' => '専門書']
    , ['id' => 2, 'name' => '映画']
    , ['id' => 3, 'name' => '小説']
    , ['id' => 4, 'name' => '音楽']
    , ['id' => 5, 'name' => 'ゲーム']
    ];

$rs = Ginq::from($genres)->join($items,
        'id', 'genre',
        function($genre, $item) {
          return "{$genre['name']}, {$item['title']}";
        });

echo "\n"; foreach ($rs as $r) { echo $r . "\n"; } echo "\n";
```

```:Output
専門書, パーフェクトPHP
専門書, すごいHaskell楽しく学ぼう
専門書, Scalaスケーラブルプログラミング
映画, 男はつらいよ
映画, ダークナイト
映画, キングコング２
映画, チョコレート・ファイター
小説, 吾輩は猫である
小説, 星の王子さま
小説, 変身
音楽, MISS TAKE ～僕はミス・テイク～
音楽, エリーゼのために
```

range
--------

range($start [, $stop [, $step]])

```php:Example
$xs = Ginq::range(1, 10, 2);

foreach ($xs in $x) { echo "$x\n"; }
```

```php:Output
1 3 5 7 9
```

Infinite sequence

```php:
$xs = Ginq::range(1);
```

Is's useful to using with **zip**.

```php:
$lines = ["", "", ""];

$xs = Ginq::range(1)->zip($lines,
        function($num, $line) {
          return "$num : $line";
        }
      );
```

```php:
$xs = Ginq::range(1)->zip($lines,
        function($num, $line) {
          return "$num : $line";
        }
      );
```

selectMany
--------

selectMany(manySelector [, selector])

`manySelector ` is Collection (array, Iterator, IteratorAggregate, Ginq object) or function.

`selector` is function, has two parameters.
`function ($outer, $inner)`

```php:Example

```


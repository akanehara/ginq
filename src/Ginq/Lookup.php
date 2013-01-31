<?php
class Lookup implements IteratorAggregate
{
    private $table = null;

    protected function __construct()
    {
        $this->table = [];
    }
    
    public static function from($xs, $keySelector)
    {
        $lookup = new Lookup();
        foreach ($xs as $x) {
            $lookup->put($keySelector($x), $x);
        }
        return $lookup;
    }

    public function getIterator()
    {
        return $this->_gen_iter();
    }

    protected function _gen_iter() {
        foreach ($this->table as $x) {
            yield $x;
        }
    }

    public function get($key)
    {
        @$v = $this->table[$key];
        if (is_array($v)) {
            return $v;
        }
        return [];
    }

    public function put($key, $value)
    {
        @$v = &$this->table[$key];
        if (is_array($v)) {
            array_push($v, $value);
        } else {
            $this->table[$key] = [$value];
        }
    }
}


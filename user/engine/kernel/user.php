<?php

class User extends Genome {

    private $lot = null;

    public function __construct($id, $lot = [], $NS = []) {
        $input = File::exist(USER . DS . $id . '.page', null);
        $this->lot = new Page($input, $lot, array_replace([1 => __c2f__(static::class, '_', '\\')], $NS));
        if (!$this->lot->key) {
            $this->lot->key = $id;
        }
    }

    public function __call($key, $lot = []) {
        if (!self::_($key)) {
            $value = $this->lot->{$key};
            $s = array_shift($lot) ?: null;
            if ($s instanceof \Closure) {
                return call_user_func($s, $value, $this);
            }
            return $value !== null ? $value : $s;
        }
        return parent::__call($key, $lot);
    }

    public function __set($key, $value = null) {
        return $this->lot->{$key} = $value;
    }

    public function __get($key) {
        return $this->lot->{$key};
    }

    // Fix case for `isset($user->key)` or `!empty($user->key)`
    public function __isset($key) {
        return !!$this->lot->{$key};
    }

    public function __unset($key) {
        $this->lot->{$key} = null;
    }

    public function __toString() {
        return $this->lot->author('@' . $this->lot->key) . "";
    }

}
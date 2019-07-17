<?php

class User extends Page {

    // Set pre-defined user property
    public static $data = [];

    public function __construct(string $path = null, array $lot = [], array $prefix = []) {
        $n = $path ? Path::N($path) : "";
        parent::__construct($path, array_replace_recursive([
            'url' => $n ? $GLOBALS['URL']['$'] . '/' . state('user')['/'] . '/' . $n : null
        ], static::$data, $lot), $prefix);
    }

    public function __toString() {
        if (is_string($v = $this->offsetGet('$'))) {
            return $v;
        }
        return (string) $this->user;
    }

    public function key() {
        return $this->name;
    }

    public function pass() {
        return File::open(Path::F($this->path) . DS . 'pass.data')->get(0);
    }

    public function user() {
        return $this->exist ? '@' . Path::N($this->path) : null;
    }

}
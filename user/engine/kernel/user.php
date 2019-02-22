<?php

class User extends Page {

    // Set pre-defined user property
    public static $data = [];

    public function __construct(string $path = null, array $lot = [], array $prefix = []) {
        $n = $path ? Path::N($path) : "";
        parent::__construct($path, extend([
            'url' => $n ? $GLOBALS['URL']['$'] . '/' . Extend::state('user', 'path') . '/' . $n : null
        ], static::$data, $lot), $prefix);
    }

    public function __toString() {
        if (!$this->__call('$')) {
            return $this->key() ?: "";
        }
        return parent::__toString();
    }

    public function key() {
        return $this->path ? '@' . Path::N($this->path) : null;
    }

    public function pass() {
        return File::open(Path::F($this->path) . DS . 'pass.data')->get(0);
    }

}
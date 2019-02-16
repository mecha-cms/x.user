<?php

class User extends Page {

    public function __construct(string $path = null, array $lot = [], $NS = []) {
        $n = $path ? Path::N($path) : "";
        parent::__construct($path, extend([
            'url' => $n ? $GLOBALS['URL']['$'] . '/' . Extend::state('user', 'path') . '/' . $n : null
        ], $lot, false), $NS);
    }

    public function key() {
        return $this->path ? '@' . Path::N($this->path) : null;
    }

    public function pass() {
        return File::open(Path::F($this->path) . DS . 'pass.data')->get(0);
    }

    public function __toString() {
        if (!$this->__call('$')) {
            return $this->__call('key') ?: "";
        }
        return parent::__toString();
    }

}
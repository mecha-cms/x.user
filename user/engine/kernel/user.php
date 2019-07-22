<?php

class User extends Page {

    public function __construct(string $path = null, array $lot = [], array $prefix = []) {
        global $url;
        $n = $path ? Path::N($path) : "";
        parent::__construct($path, array_replace_recursive([
            'url' => $n ? $url . '/' . state('user')['/'] . '/' . $n : null
        ], $lot), $prefix);
    }

    public function __toString() {
        if (is_string($v = $this->offsetGet('author'))) {
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
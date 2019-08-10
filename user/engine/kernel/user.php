<?php

class User extends Page {

    public function __construct(string $path = null, array $lot = []) {
        parent::__construct($path, $lot);
    }

    public function __toString() {
        if (is_string($v = $this->offsetGet('author'))) {
            return $v;
        }
        return (string) $this->user;
    }

    public function URL() {
        $n = $this->exist ? $this->name : null;
        return $n ? $GLOBALS['url'] . '/' . state('user')['/'] . '/' . $n : null;
    }

    public function user() {
        return $this->exist ? '@' . $this->name : null;
    }

}
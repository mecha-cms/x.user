<?php

class User extends Page {

    public function __construct(...$lot) {
        parent::__construct(...$lot);
    }

    public function __toString() {
        if (is_string($v = parent::author())) {
            return $v;
        }
        return (string) $this->user();
    }

    public function URL(...$lot) {
        extract($GLOBALS, EXTR_SKIP);
        $n = $this->exist() ? parent::name() : null;
        return $n ? $url . ($state->x->user->path ?? '/user') . '/' . $n : null;
    }

    public function user() {
        return $this->exist() ? '@' . parent::name() : null;
    }

}
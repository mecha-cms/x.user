<?php

class User extends Page {

    public function __construct(...$lot) {
        parent::__construct(...$lot);
    }

    public function __toString(): string {
        if (is_string($v = parent::author())) {
            return $v;
        }
        return (string) $this->user();
    }

    public function URL(...$lot) {
        extract(lot(), EXTR_SKIP);
        $n = $this->_exist() ? parent::name() : null;
        return $n ? $url . '/' . trim($state->x->user->route ?? 'user', '/') . '/' . $n : null;
    }

    public function user() {
        return $this->_exist() ? '@' . parent::name() : null;
    }

}
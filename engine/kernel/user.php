<?php

class User extends Page {

    public function __toString(): string {
        if (is_string($v = parent::author())) {
            return $v;
        }
        return (string) $this->user();
    }

    public function route(...$lot) {
        if (0 === strpos($this->path ?? P, LOT . D . 'user' . D)) {
            extract(lot(), EXTR_SKIP);
            return '/' . trim($state->x->user->route ?? 'user', '/') . '/' . $this->name;
        }
        return parent::route(...$lot);
    }

    public function user() {
        return $this->_exist() ? '@' . parent::name() : null;
    }

}
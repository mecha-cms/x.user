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
            if (!is_string($name = $this->name())) {
                return null;
            }
            extract(lot(), EXTR_SKIP);
            return '/' . strtr(rawurlencode(trim($state->x->user->route ?? 'user', '/') . '/' . $name), ['%2F' => '/']);
        }
        return parent::route(...$lot);
    }

    public function user() {
        return $this->file->exist() ? '@' . parent::name() : null;
    }

}
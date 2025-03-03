<?php

class User extends Page {

    // With the `__construct()` method defined, the `user()` method is now a normal method.
    // <https://wiki.php.net/rfc/remove_php4_constructors>
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

    // This should not emit `E_DEPRECATED` in PHP 7 because the `__construct()` method is already defined.
    public function user() {
        return $this->_exist() ? '@' . parent::name() : null;
    }

}
<?php

class User extends Page {

    public function __construct(string $path = null, array $lot = []) {
        $c = c2f(self::class);
        parent::__construct($path, array_replace_recursive((array) State::get('x.' . $c . '.page', true), $lot));
        $this->hook[] = $c;
    }

    public function __toString() {
        if (is_string($v = parent::get('author'))) {
            return $v;
        }
        return (string) $this->user();
    }

    public function URL(...$lot) {
        extract($GLOBALS, EXTR_SKIP);
        $n = $this->exist ? parent::name() : null;
        return $n ? $url . ($state->x->user->path ?? '/user') . '/' . $n : null;
    }

    public function user() {
        return $this->exist ? '@' . parent::name() : null;
    }

}
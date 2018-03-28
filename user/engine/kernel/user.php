<?php

class User extends Page {

    public function __construct($input = [], $lot = [], $NS = []) {
        global $url;
        parent::__construct($input, array_replace([
            'key' => is_string($input) ? '@' . ($n = Path::N($input)) : null,
            'url' => isset($n) ? $url . '/' . Extend::state('user', 'path') . '/' . $n : null
        ], $lot), $NS);
    }

    public function __toString() {
        if (!isset($this->lot['$'])) {
            return $this->__call('key');
        }
        return parent::__toString();
    }

}
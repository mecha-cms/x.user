<?php

class User extends Page {

    public function __construct($input = [], $lot = [], $NS = []) {
        parent::__construct($input, array_replace([
            'key' => is_string($input) ? '@' . ($n = Path::N($input)) : null,
            'url' => isset($n) ? $GLOBALS['URL']['$'] . '/' . Extend::state('user', 'path') . '/' . $n : null
        ], $lot), $NS);
    }

    public function __toString() {
        if (!$this->__call('$')) {
            return $this->__call('key') ?: "";
        }
        return parent::__toString();
    }

}
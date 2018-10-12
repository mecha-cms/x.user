<?php

class User extends Page {

    public function __construct($path = null, $lot = [], $NS = []) {
        parent::__construct($path, array_replace([
            'key' => is_string($path) ? '@' . ($n = Path::N($path)) : null,
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
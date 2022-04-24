<?php

class Users extends Pages {

    public function page(string $path = null, array $lot = []) {
        return new User($path, $lot);
    }

    public static function from(...$lot) {
        if (is_array($v = reset($lot))) {
            return parent::from($v);
        }
        $lot[0] = $lot[0] ?? LOT . D . 'user';
        return parent::from(...$lot);
    }

}
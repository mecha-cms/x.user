<?php

class Users extends Pages {

    public function page(string $path) {
        return new User($path);
    }

    public static function from(...$lot) {
        $lot[0] = $lot[0] ?? LOT . DS . 'user';
        return parent::from(...$lot);
    }

}
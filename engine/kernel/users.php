<?php

class Users extends Pages {

    public function page(...$lot) {
        return User::from(...$lot);
    }

    public static function from(...$lot) {
        if (is_array($v = reset($lot))) {
            return parent::from($v);
        }
        $lot[0] = $lot[0] ?? LOT . D . 'user';
        return parent::from(...$lot);
    }

}
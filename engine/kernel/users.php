<?php

class Users extends Pages {

    public function page(...$lot) {
        return new User(...$lot);
    }

    public static function from(...$lot) {
        $lot[0] = $lot[0] ?? LOT . D . 'user';
        return parent::from(...$lot);
    }

}
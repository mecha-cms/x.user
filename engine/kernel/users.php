<?php

class Users extends Pages {

    public function page(...$lot) {
        if (($v = $lot[0] ?? 0) instanceof User) {
            return $v;
        }
        if (is_array($v)) {
            unset($v[P]);
            $lot[0] = $v;
        }
        return new User(...$lot);
    }

    public static function from(...$lot) {
        $lot[0] = $lot[0] ?? LOT . D . 'user';
        return parent::from(...$lot);
    }

}
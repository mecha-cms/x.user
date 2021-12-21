<?php

$key = cookie('user.key');
$a = cookie('user.token');
$b = content(LOT . D . 'user' . D . $key . D . 'token.data');
$user = $a && $b && $a === $b ? '@' . $key : false;

Is::_('user', function($key = null) use($user) {
    if (is_string($key)) {
        $key = ltrim($key, '@');
        return $user && '@' . $key === $user ? $user : false;
    }
    if (is_int($key) && false !== $user) {
        $user = ltrim($user, '@');
        $user = new User(LOT . D . 'user' . D . $user . '.page');
        return $user->exist && $key === $user->status;
    }
    return false !== $user ? $user : false;
});

function user(...$lot) {
    return User::from(...$lot);
}
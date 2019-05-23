<?php

$key = Cookie::get('user.key') ?? Session::get('user.key') ?? "";
$a = File::open(USER . DS . $key . DS . 'token.data')->get(0) ?? "";
$b = Cookie::get('user.token') ?? Session::get('user.token') ?? "";

$user = $a && $b && $a === $b ? '@' . $key : false;

Is::_('user', function(string $key = null) use($user) {
    if ($key) {
        return $user && (strpos($key, '@') === 0 ? $key === $user : '@' . $key === $user) ? $user : false;
    }
    return $user !== false ? $user : false;
});
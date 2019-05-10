<?php

$key = substr($user = Cookie::get('user.key') ?? Session::get('user.key') ?? "", 1);
$a = File::open(USER . DS . $key . DS . 'token.data')->get(0) ?? "";
$b = Cookie::get('user.token') ?? Session::get('user.token') ?? "";

$user = $a && $b && $a === $b ? $user : false;

Is::_('user', function(string $key = null) use($user) {
    if ($key) {
        return $user && (strpos($key, '@') === 0 ? $key === $user : '@' . $key === $user) ? $user : false;
    }
    return $user !== false ? $user : false;
});
<?php

$key = substr($user = Cookie::get('url.user', Session::get('url.user')), 1);
$a = File::open(USER . DS . $key . DS . 'token.data')->get(0, "");
$b = Cookie::get('url.token', Session::get('url.token', ""));

$user = $a && $b && $a === $b ? $user : false;

Is::_('user', function($key = null, $fail = false) use($user) {
    if ($key) {
        return $user && (strpos($key, '@') === 0 ? $key === $user : '@' . $key === $user) ? $user : $fail;
    }
    return $user !== false ? $user : $fail;
});
<?php

$session = URL::session . '.';
$key = substr($user = Cookie::get($session . 'user', Session::get($session . 'user')), 1);
$a = File::open(USER . DS . $key . DS . 'token.data')->get(0, "");
$b = Cookie::get($session . 'token', Session::get($session . 'token', ""));

$user = $a && $b && $a === $b ? $user : false;

Is::_('user', function(string $key = null, $fail = false) use($user) {
    if ($key) {
        return $user && (strpos($key, '@') === 0 ? $key === $user : '@' . $key === $user) ? $user : $fail;
    }
    return $user !== false ? $user : $fail;
});
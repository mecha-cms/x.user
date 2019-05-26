<?php

$key = Cookie::get('user.key');
$a = Cookie::get('user.token');
$b = content(USER . DS . $key . DS . 'token.data');

$user = $a && $b && $a === $b ? '@' . $key : false;

Is::_('user', function(string $key = null) use($user) {
    if ($key) {
        return $user && (strpos($key, '@') === 0 ? $key === $user : '@' . $key === $user) ? $user : false;
    }
    return $user !== false ? $user : false;
});
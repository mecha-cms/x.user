<?php

$key = Cookie::get('user.key');
$a = Cookie::get('user.token');
$b = content(LOT . DS . 'user' . DS . $key . DS . 'token.data');

$user = $a && $b && $a === $b ? '@' . $key : false;

Is::_('user', function(string $key = null) use($user) {
    if ($key) {
        return $user && (0 === strpos($key, '@') ? $key === $user : '@' . $key === $user) ? $user : false;
    }
    return false !== $user ? $user : false;
});
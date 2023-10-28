<?php

$folder = LOT . D . 'user';
$name = cookie('user.name');
$token = cookie('user.token');

$current = content($folder . D . $name . D . 'token.data');
$key = $current && $token && $current === $token ? '@' . $name : false;

Is::_('user', static function ($of = null) use ($folder, $key) {
    if (false === $key) {
        return false;
    }
    if (is_int($of)) {
        $key = ltrim($key, '@');
        $user = new User($folder . D . $key . '.page');
        return $user->exist && $key === $user->status ? '@' . $key : false;
    }
    if (is_string($of)) {
        $key = ltrim($key, '@');
        if ($key !== ltrim($of, '@')) {
            return false;
        }
        return is_file($folder . D . $key . '.page') ? '@' . $key : false;
    }
    return $key;
});
<?php

// Store comment state to registry…
$state = Extend::state('user');
if (!empty($state['user'])) {
    // Prioritize default state
    Config::alt($state);
    User::$data = array_replace_recursive(Page::$data, (array) Config::get('user', true));
}

Config::set('is.enter', $user = Is::user());

$GLOBALS['user'] = new User($user ? USER . DS . \substr($user, 1) . '.page' : null);
$GLOBALS['users'] = Get::users()->map(function($v) {
    return new User($v);
});
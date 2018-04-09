<?php

// Create a `user` folder in `lot` if it is not there
$f = LOT . DS . 'user';
if (!Folder::exist($f)) {
    Folder::set($f, 0755);
    Guardian::kick($url->current);
}

// Require the plug manually…
r(__DIR__ . DS . 'engine' . DS . 'plug', [
    'get.php',
    'is.php'
], null, Lot::get(null, []));

// Store user state to registry…
$state = Extend::state('user');
if (!empty($state['user'])) {
    Config::alt(['user' => $state['user']]);
}

function fn_user($author) {
    if (is_string($author) && strpos($author, '@') === 0) {
        return new User(USER . DS . substr($author, 1) . '.page');
    }
    return $author;
}

Hook::set('*.author', 'fn_user', 1);

Config::set('is.enter', $user = Is::user());

Lot::set([
    'user' => new User,
    'users' => []
]);

Hook::set('on.ready', function() use($user) {
    $users = [];
    if ($files = Get::users()) {
        foreach ($files as $file) {
            $users[] = new User($file['path']);
        }
    }
    Lot::set([
        'user' => new User($user ? USER . DS . substr($user, 1) . '.page' : []),
        'users' => $users
    ]);
}, 0);

// Apply route(s) only if we have at least one user
if (g(USER, 'page')) {
    include __DIR__ . DS . 'lot' . DS . 'worker' . DS . 'worker' . DS . 'route.php';
}
<?php namespace fn\user;

// Require the plug manually…
\r(['get', 'is'], __DIR__ . DS . 'engine' . DS . 'plug', \Lot::get(null, []));

// Store user state to registry…
$state = \Extend::state('user');
if (!empty($state['user'])) {
    \Config::alt(['user' => $state['user']]);
}

function author($author = "") {
    if (is_string($author) && strpos($author, '@') === 0) {
        return new \User(USER . DS . substr($author, 1) . '.page');
    }
    return $author;
}

\Hook::set('*.author', __NAMESPACE__ . "\\author", 1);

\Config::set('is.enter', $user = \Is::user());

\Lot::set([
    'user' => new \User,
    'users' => new \Anemon
]);

\Hook::set('on.ready', function() use($user) {
    $users = [];
    \Lot::set([
        'user' => new \User($user ? USER . DS . substr($user, 1) . '.page' : null),
        'users' => \Get::users()->map(function($v) {
            return new \User($v['path']);
        })
    ]);
}, 0);

// Apply route(s) only if we have at least one user
if (\g(USER, 'page')) {
    include __DIR__ . DS . 'lot' . DS . 'worker' . DS . 'worker' . DS . 'route.php';
}
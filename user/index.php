<?php namespace fn\user;

// Require the plug manually…
\r(['get', 'is'], __DIR__ . DS . 'engine' . DS . 'plug', \Lot::get(null, []));

// Store user state to registry…
$state = \Extend::state('user');
if (!empty($state['user'])) {
    \Config::alt(['user' => $state['user']]);
}

function a($a) {
    if ($a && is_string($a) && strpos($a, '@') !== false) {
        $out = "";
        $parts = preg_split('#(<[!/]?[a-zA-Z\d:.-]+[\s\S]*?>)#', $a, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        foreach ($parts as $v) {
            if (strpos($v, '<') === 0 && substr($v, -1) === '>') {
                $out .= $v; // HTML tag
            } else {
                $out .= strpos($v, '@') !== false ? preg_replace_callback('#@[a-z\d-]+#', function($m) {
                    if ($f = \File::exist(USER . DS . substr($m[0], 1) . '.page')) {
                        $f = new \User($f);
                        return \HTML::a($f . "", $f->url, true, ['title' => $f->key]);
                    }
                    return $m[0];
                }, $v) : $v; // Plain text
            }
        }
        return $out;
    }
    return $a;
}

function author($author = "") {
    if ($author && is_string($author) && strpos($author, '@') === 0) {
        return new \User(USER . DS . substr($author, 1) . '.page');
    }
    return $author;
}

function avatar($avatar, array $lot = []) {
    if ($avatar) {
        return $avatar;
    }
    $w = array_shift($lot) ?? 72;
    $h = array_shift($lot) ?? $w;
    $d = array_shift($lot) ?? 'monsterid';
    return $GLOBALS['URL']['protocol'] . 'www.gravatar.com/avatar/' . md5($this->email) . '?s=' . $w . '&amp;d=' . $d;
}

\Hook::set([
    '*.content',
    '*.description',
    '*.excerpt', // `excerpt` plugin
    '*.title'
], __NAMESPACE__ . "\\a", 2);
\Hook::set('*.author', __NAMESPACE__ . "\\author", 2);
\Hook::set('*.avatar', __NAMESPACE__ . "\\avatar", 0);

\Config::set('is.enter', $user = \Is::user());

\Lot::set([
    'user' => new \User($user ? USER . DS . substr($user, 1) . '.page' : null),
    'users' => new \Anemon
]);

// Apply route(s) only if we have at least one user
if (\g(USER, 'page')) {
    \Hook::set('on.ready', function() {
        \Lot::set('users', \Get::users()->map(function($v) {
            return new \User($v);
        }));
    }, 0);
    include __DIR__ . DS . 'lot' . DS . 'worker' . DS . 'worker' . DS . 'route.php';
}
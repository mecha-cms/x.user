<?php namespace fn\user;

// Store user state to registry…
$state = \Extend::state('user');
if (!empty($state['user'])) {
    \Config::alt(['user' => $state['user']]);
}

// Require the plug manually…
require __DIR__ . DS . 'engine' . DS . 'plug' . DS . 'get.php';
require __DIR__ . DS . 'engine' . DS . 'plug' . DS . 'is.php';
require __DIR__ . DS . 'engine' . DS . 'plug' . DS . 'user.php';

function a($a) {
    if ($a && \is_string($a) && \strpos($a, '@') !== false) {
        $out = "";
        $parts = \preg_split('#(<pre(?:\s[^>]*)?>[\s\S]*?</pre>|<code(?:\s[^>]*)?>[\s\S]*?</code>|<kbd(?:\s[^>]*)?>[\s\S]*?</kbd>|<script(?:\s[^>]*)?>[\s\S]*?</script>|<style(?:\s[^>]*)?>[\s\S]*?</style>|<textarea(?:\s[^>]*)?>[\s\S]*?</textarea>|<[^>]+>)#i', $a, null, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE);
        foreach ($parts as $v) {
            if (\strpos($v, '<') === 0 && \substr($v, -1) === '>') {
                $out .= $v; // Is a HTML tag
            } else {
                $out .= \strpos($v, '@') !== false ? \preg_replace_callback('#@[a-z\d-]+#', function($m) {
                    if ($f = \File::exist(USER . DS . \substr($m[0], 1) . '.page')) {
                        $f = new \User($f);
                        return '<a href="' . $f->url . '" target="_blank" title="' . $f->key . '">' . $f . '</a>';
                    }
                    return $m[0];
                }, $v) : $v; // Is a plain text
            }
        }
        return $out;
    }
    return $a;
}

function author($author = "") {
    if ($author && \is_string($author) && \strpos($author, '@') === 0) {
        return new \User(USER . DS . \substr($author, 1) . '.page');
    }
    return $author;
}

function avatar($avatar, array $lot = []) {
    if ($avatar) {
        return $avatar;
    }
    $w = \array_shift($lot) ?? 72;
    $h = \array_shift($lot) ?? $w;
    $d = \array_shift($lot) ?? 'monsterid';
    return $GLOBALS['URL']['scheme'] . '://www.gravatar.com/avatar/' . \md5($this->email) . '?s=' . $w . '&d=' . $d;
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
    'user' => new \User($user ? USER . DS . \substr($user, 1) . '.page' : null),
    'users' => new \Anemon
]);

// Apply route(s) only if we have at least one user
if (\y(\g(USER, 'page'))) {
    \Hook::set('start', function() {
        \Lot::set('users', \Get::users()->map(function($v) {
            return new \User($v);
        }));
    }, 0);
    include __DIR__ . DS . 'lot' . DS . 'worker' . DS . 'worker' . DS . 'route.php';
}
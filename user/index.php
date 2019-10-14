<?php

require __DIR__ . DS . 'engine' . DS . 'plug' . DS . 'is.php';

require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'content.php';
require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'hook.php';
require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'language.php';
require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'state.php';
require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'user.php';

// Apply route(s) only if we have at least one user
if (q(g(USER, 'page')) > 0) {
    require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'route.php';
} else {
    require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'route' . DS . 'set.php';
}
<?php

require __DIR__ . DS . 'engine' . DS . 'plug' . DS . 'is.php';

require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'hook.php';
require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'layout.php';
require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'state.php';
require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'user.php';

// Apply route(s) only if we have at least one user
if (q(g(LOT . DS . 'user', 'page')) > 0) {
    require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'route.php';
// Else, prompt author to create an user account
} else {
    require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'route' . DS . 'set.php';
}
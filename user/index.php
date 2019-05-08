<?php

require __DIR__ . DS . 'engine' . DS . 'plug' . DS . 'get.php';
require __DIR__ . DS . 'engine' . DS . 'plug' . DS . 'is.php';

require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'config.php';
require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'content.php';
require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'hook.php';
require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'language.php';

// Apply route(s) only if we have at least one user
if (y(g(USER, 'page'))) {
    require __DIR__ . DS . 'engine' . DS . 'r' . DS . 'route.php';
}
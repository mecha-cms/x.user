<?php

require __DIR__ . D . 'engine' . D . 'fire.php';

// Apply route(s) only if we have at least one user
if (q(g(LOT . D . 'user', 'page')) > 0) {
    require __DIR__ . D . 'engine' . D . 'r' . D . 'route.php';
// Else, prompt author to create an user account
} else {
    require __DIR__ . D . 'engine' . D . 'r' . D . 'route' . D . 'set.php';
}
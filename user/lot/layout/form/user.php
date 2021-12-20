<?php

// No user(s) yet?
if (!is_dir($folder = LOT . D . 'user') || 0 === q(g($folder, 'page'))) {
    require __DIR__ . D . 'user' . D . 'set.php';
} else {
    require __DIR__ . D . 'user' . D . (Is::user() ? 'exit' : 'enter') . '.php';
}
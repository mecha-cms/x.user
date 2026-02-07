<?php

if (!is_dir($folder = LOT . D . 'user') || 0 === q(g($folder, x\page\x()))) {
    require __DIR__ . D . 'user' . D . 'start.php'; // No user(s) yet?
} else {
    require __DIR__ . D . 'user' . D . (isset($user) && $user->exist ? 'exit' : 'enter') . '.php';
}
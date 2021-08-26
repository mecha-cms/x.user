<?php

if (!is_dir($folder = LOT . DS . 'user') || 0 === q(g($folder, 'page'))) {
    Layout::set('form/user', __DIR__ . DS . 'layout' . DS . 'form' . DS . 'set.php');
} else {
    Layout::set('form/user', __DIR__ . DS . 'layout' . DS . 'form' . DS . (Is::user() ? 'exit' : 'enter') . '.php');
}
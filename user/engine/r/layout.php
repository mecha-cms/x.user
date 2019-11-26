<?php

if (!is_dir($d = LOT . DS . 'user') || 0 === q(g($d, 'page'))) {
    Layout::set('form/user', __DIR__ . DS . 'layout' . DS . 'set.php');
} else {
    Layout::set('form/user', __DIR__ . DS . 'layout' . DS . (Is::user() ? 'exit' : 'form') . '.php');
}
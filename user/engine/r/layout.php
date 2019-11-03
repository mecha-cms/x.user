<?php

if (!defined('USER') || 0 === q(g(USER, 'page'))) {
    Layout::set('form/user', __DIR__ . DS . 'layout' . DS . 'set.php');
} else {
    Layout::set('form/user', __DIR__ . DS . 'layout' . DS . (Is::user() ? 'exit' : 'form') . '.php');
}
<?php

if (!defined('USER') || q(g(USER, 'page')) === 0) {
    Content::set('form/user', __DIR__ . DS . 'content' . DS . 'set.php');
} else {
    Content::set('form/user', __DIR__ . DS . 'content' . DS . (Is::user() ? 'exit' : 'form') . '.php');
}
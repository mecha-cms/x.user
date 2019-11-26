<?php

State::set('is.enter', $user = Is::user());

$d = LOT . DS . 'user';
$GLOBALS['user'] = $user = new User($user ? $d . DS . \substr($user, 1) . '.page' : null);
$GLOBALS['users'] = $users = Users::from($d);
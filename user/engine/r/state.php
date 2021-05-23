<?php

State::set('is.enter', $user = Is::user());

$folder = LOT . DS . 'user';

$GLOBALS['user'] = $user = new User($user ? $folder . DS . \substr($user, 1) . '.page' : null);
$GLOBALS['users'] = $users = Users::from($folder);

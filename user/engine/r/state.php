<?php

State::set('is.enter', $user = Is::user());

$GLOBALS['user'] = $user = new User($user ? USER . DS . \substr($user, 1) . '.page' : null);
$GLOBALS['users'] = $users = Users::from(USER);
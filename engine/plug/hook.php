<?php

$GLOBALS['user'] = $user = new User(($n = cookie('user.name')) ? LOT . D . 'user' . D . $n . '.page' : null);
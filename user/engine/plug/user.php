<?php

// Based on `.\lot\extend\page\engine\plug\page.php`
User::_('time', "fn\\page\\time");
User::_('update', "fn\\page\\update");

User::$data = extend([], (array) Config::get('page', true), (array) Config::get('user', true));
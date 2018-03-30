<?php

// Based on `lot\extend\page\engine\plug\get.php`

function fn_get_users($folder = USER, $state = 'page', $sort = [1, 'slug'], $key = null) {
    return fn_get_pages($folder, $state, $sort, $key);
}

Get::_('users', 'fn_get_users');
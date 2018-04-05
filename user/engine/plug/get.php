<?php

// Based on `lot\extend\page\engine\plug\get.php`
Get::_('users', ['fn_get_pages', [USER, 'page', [1, 'slug'], null]]);
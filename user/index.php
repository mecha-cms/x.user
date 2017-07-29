<?php

// Create a `user` folder in `lot` if it is not there
$f = LOT . DS . 'user';
if (!Folder::exist($f)) {
    Folder::set($f, 0755);
    Guardian::kick($url->current);
}

function fn_user($author) {
    if (is_string($author) && strpos($author, '@') === 0) {
        return new User(str_replace('@', "", $author));
    }
    return $author;
}

Hook::set('page.author', 'fn_user', 1);
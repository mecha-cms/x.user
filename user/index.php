<?php

// Create a `user` folder in `lot` if it is not there
$f = LOT . DS . 'user';
if (!Folder::exist($f)) {
    Folder::set($f, 0755);
    File::write('deny from all')->saveTo($f . DS . '.htaccess', 0600);
    Guardian::kick($url->current);
}

function fn_user($author) {
    if (is_string($author) && strpos($author, User::ID) === 0) {
        return new User(substr($author, 1));
    }
    return $author;
}

Hook::set('page.author', 'fn_user', 1);
<?php

function fn_user($author) {
    if (is_string($author) && strpos($author, User::ID) === 0) {
        return new User(substr($author, 1));
    }
    return $author;
}

Hook::set('page.author', 'fn_user', 1);
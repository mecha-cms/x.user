<?php

// Create a `user` folder in `lot` if it is not there
$f = LOT . DS . 'user';
if (!Folder::exist($f)) {
    Folder::set($f, 0755);
    Guardian::kick($url->current);
// Self destruct!
} else {
    unlink(__FILE__);
}
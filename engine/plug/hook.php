<?php

// By default, extension(s) are loaded in alphabetical order. The most obvious example is the loading order of Panel and
// User extension. Panel extension will be loaded before User extension even though Panel depends on User. It is
// necessary to make sure that User extension is loaded before Panel extension. However, because of the extension’s
// default loading order, it is currently not possible to load User extension before Panel extension. Plug file(s) can
// be used as a dirty solution for now to load other extension requirement(s) early, so that they can be loaded before
// the target extension’s `index.php` file is loaded.
//
// There is no particular reason why I chose this `hook.php` file, except that it (the `Hook` class) is the most ideal
// class to watch its initial loading event because most extension(s) will call it in their `index.php` file, so it will
// immediately trigger the `$user` variable below to be declared. There are several other class(es) that are loaded much
// earlier than the `Hook` class, such as the `State` and the `URL` class(es), but they are loaded before the class
// and plug file(s) from other extension(s) are loaded, so there will be more potential for unpredictable error(s) due
// to declaring the `User` class too early.

if ($f = exist(LOT . D . 'user' . D . basename(cookie('user.name') ?? P) . '.{' . x\page\x() . '}', 1)) {
    $user = new User($f);
    if (($user->token ?? 0) !== (cookie('user.token') ?? 1)) {
        $user = new User; // Invalid token!
    }
    lot('user', $user);
}

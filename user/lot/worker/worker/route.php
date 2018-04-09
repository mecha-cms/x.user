<?php

$path = Extend::state('user', 'path');

Route::set($path, function() use($path) {
    extract(Lot::get(null, []));
    $is_enter = $site->is('enter');
    Config::set('trace', new Anemon([$language->{$is_enter ? 'exit' : 'enter'}, $site->title], ' &#x00B7; '));
    if ($r = HTTP::post()) {
        $key = isset($r['key']) ? $r['key'] : null;
        $pass = isset($r['pass']) ? $r['pass'] : null;
        $token = isset($r['token']) ? $r['token'] : null;
        // Has only 1 user!
        if (count($users) === 1) {
            // Set the `key` value to that user automatically
            $key = $users[0]->key;
        }
        // Remove the `@` prefix!
        if (strpos($key, '@') === 0) {
            $key = substr($key, 1);
        }
        // Log out!
        if ($is_enter) {
            // Check token…
            if (Is::void($token) || !Guardian::check($token)) {
                Message::error('token');
            } else if (!isset($r['x']) || Is::void($r['x'])) {
                Message::error('void_field', $language->user, true);
            } else {
                File::open(USER . DS . $r['x'] . DS . 'token.data')->delete();
                Session::reset('url.user');
                Session::reset('url.pass');
                Session::reset('url.token');
                Message::success('user_exit');
                // Trigger the hook!
                Hook::fire('on.user.exit', [USER . DS . $key . '.page', null]);
                // Redirect to the log in page by default!
                Guardian::kick((isset($r['kick']) ? $r['kick'] : $path) . HTTP::query(['kick' => false]));
            }
        // Log in!
        } else {
            // Check token…
            if (Is::void($token) || !Guardian::check($token)) {
                Message::error('token');
            // Check user name…
            } else if (Is::void($key)) {
                Message::error('void_field', $language->user, true);
            // Check user pass…
            } else if (Is::void($pass)) {
                Message::error('void_field', $language->pass, true);
            // No error(s), go to the next step(s)…
            } else {
                // Check if user already registered…
                if (file_exists($u = USER . DS . $key . '.page')) {
                    // Reset password by deleting `pass.data` manually, then log in!
                    if (!file_exists($f = USER . DS . $key . DS . 'pass.data')) {
                        File::set(X . password_hash($pass . ' ' . $key, PASSWORD_DEFAULT))->saveTo($f, 0600);
                        // Message::success('create', [$language->pass, '<em>' . $pass . '</em>']);
                    }
                    $enter = false;
                    $secret = File::open($f)->get(0, "");
                    // Validate password hash!
                    if (strpos($secret, X) === 0) {
                        $enter = password_verify($pass . ' ' . $key, substr($secret, 1));
                    // Validate password text!
                    } else {
                        $enter = $pass === $secret;
                    }
                    // Is valid, then…
                    if ($enter) {
                        // Save the token!
                        File::set($token)->saveTo(Path::F($u) . DS . 'token.data', 0600);
                        // Set `$GLOBALS['url']['user']` value!
                        Session::set('url.user', '@' . $key);
                        // Set `$GLOBALS['url']['pass']` value!
                        Session::set('url.pass', $pass);
                        // Set `$_SESSION['url']['token']` value!
                        Session::set('url.token', $token);
                        // Trigger the hook!
                        Hook::fire('on.user.enter', [$u, $u]);
                        // Show success message!
                        Message::success('user_enter');
                        // Redirect to the home page by default!
                        Guardian::kick((isset($r['kick']) ? $r['kick'] : "") . HTTP::query(['kick' => false]));
                    } else {
                        Message::error('user_or_pass');
                    }
                } else {
                    Message::error('user_or_pass');
                }
            }
        }
        if (Message::$x) {
            HTTP::save('post');
            HTTP::delete('post', 'pass');
        }
        Guardian::kick($path . HTTP::query());
    }
    Config::set('is', [
        'error' => false,
        'page' => true,
        'user' => true
    ]);
    Shield::attach(__DIR__ . DS . '..' . DS . 'user.php');
}, 20);

Route::set($path . '/%s%', function($id) use($path, $site) {
    if (!$file = File::exist([
        USER . DS . $id . '.page',
        USER . DS . $id . '.archive'
    ])) {
        Config::set('is.error', 404);
        Shield::abort('404/' . $path . '/' . $id);
    }
    $user = new User($file);
    if ($title = $user->{'$'}) {
        $user->author = $user->title = $title;
    }
    Config::set('trace', new Anemon([$user->key . ' (' . $title . ')', $site->title], ' &#x00B7; '));
    Lot::set('page', $user);
    Config::set('is', [
        'active' => Is::user($user->key),
        'error' => false,
        'page' => $user->path,
        'pages' => false,
        'user' => $user->key
    ]);
    Shield::attach('page/' . $path . '/' . $id);
}, 20);
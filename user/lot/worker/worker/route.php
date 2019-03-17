<?php

$state = Extend::state('user');
$max = $state['try'] ?? 5;
$path = $state['path'];
$path_secret = $state['_path'] ?? $path;

foreach (g(__DIR__ . DS . '..', 'php') as $v) {
    Shield::set(Path::N($v), $v);
}

Route::set(x($path_secret), function() use($max, $path, $path_secret) {
    extract(Lot::get(), EXTR_SKIP);
    $is_enter = $config->is('enter');
    Config::set('trace', new Anemon([$language->{$is_enter ? 'exit' : 'enter'}, $config->title], ' &#x00B7; '));
    if ($r = HTTP::post(null, false)) {
        $key = $r['key'] ?? null;
        $pass = $r['pass'] ?? null;
        $token = $r['token'] ?? null;
        // Has only 1 user!
        if ($users->count() === 1) {
            // Set the `key` value to that user automatically
            $key = $users[0]->key;
        }
        // Remove the `@` prefix!
        if (strpos($key, '@') === 0) {
            $key = substr($key, 1);
        }
        $u = USER . DS . $key . '.page';
        $try = USER . DS . $key . DS . 'try.data';
        $try_data = (array) e(content($try));
        $ip = Get::IP();
        if (!isset($try_data[$ip])) {
            $try_data[$ip] = 1;
        } else {
            ++$try_data[$ip];
        }
        $errors = 0;
        // Log out!
        if ($is_enter) {
            // Check token…
            if (Is::void($token) || !Guard::check($token, 'user')) {
                Message::error('token');
                ++$errors;
            } else if (!isset($r['x']) || Is::void($r['x'])) {
                Message::error('void_field', $language->user, true);
                ++$errors;
            } else {
                File::open(USER . DS . $r['x'] . DS . 'token.data')->delete();
                Cookie::reset('user.key');
                Cookie::reset('user.pass');
                Cookie::reset('user.token');
                Session::reset('user.key');
                Session::reset('user.pass');
                Session::reset('user.token');
                Message::success('user_exit');
                // Trigger the hook!
                Hook::fire('on.user.exit', [$u, null], $user);
                // Remove log-in attempt log
                File::open($try)->delete();
                // Redirect to the log in page by default!
                Guard::kick(($r['kick'] ?? $path_secret) . $url->query('&', ['kick' => false]));
            }
        // Log in!
        } else {
            // Check token…
            if (Is::void($token) || !Guard::check($token, 'user')) {
                Message::error('token');
                ++$errors;
            // Check user name…
            } else if (Is::void($key)) {
                Message::error('void_field', $language->user, true);
                ++$errors;
            // Check user pass…
            } else if (Is::void($pass)) {
                Message::error('void_field', $language->pass, true);
                ++$errors;
            // No error(s), go to the next step(s)…
            } else {
                if ($try_data[$ip] > $max) {
                    Guard::abort('Please delete the <code>' . str_replace(ROOT, '.', Path::D($try, 2)) . DS . $key[0] . str_repeat('&#x2022;', strlen($key) - 1) . DS . 'try.data</code> file to sign in.');
                } else {
                    Message::info('user_enter_try', $max - $try_data[$ip]);
                    ++$errors;
                }
                // Check if user already registered…
                if (is_file($u)) {
                    // Record log-in attempt
                    File::put(json_encode($try_data))->saveTo($try, 0600);
                    // Reset password by deleting `pass.data` manually, then log in!
                    if (!is_file($f = Path::F($u) . DS . 'pass.data')) {
                        File::put(X . password_hash($pass . ' ' . $key, PASSWORD_DEFAULT))->saveTo($f, 0600);
                        Message::info('is', [$language->pass, '<em>' . $pass . '</em>']);
                    }
                    $enter = false;
                    $secret = content($f);
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
                        File::put($token)->saveTo(Path::F($u) . DS . 'token.data', 0600);
                        Session::set('user.key', '@' . $key);
                        // Session::set('user.pass', $pass);
                        Session::set('user.token', $token);
                        // Duplicate session to cookie for 7 day(s)
                        Cookie::set('user.key', '@' . $key, '7 days');
                        // Cookie::set('user.pass', $pass, '7 days');
                        Cookie::set('user.token', $token, '7 days');
                        // Show success message!
                        Message::reset();
                        Message::success('user_enter');
                        // Trigger the hook!
                        Hook::fire('on.user.enter', [$u, $u], $user);
                        // Remove log-in attempt log
                        File::open($try)->delete();
                        // Redirect to the home page by default!
                        Guard::kick(($r['kick'] ?? "") . $url->query('&', ['kick' => false]));
                    } else {
                        Message::error('user_or_pass');
                        ++$errors;
                    }
                } else {
                    Message::error('user_or_pass');
                    ++$errors;
                }
            }
        }
        if ($errors > 0) {
            unset($r['pass']);
            Session::set('form', $r);
        }
        Guard::kick($path_secret . $url->query);
    }
    Config::set('is', [
        'error' => false,
        'page' => true,
        'user' => true
    ]);
    return Shield::attach('user');
}, 20);

Route::set(x($path) . '/([^/]+)', function($id) use($config, $path) {
    if (!$file = File::exist([
        USER . DS . $id . '.page',
        USER . DS . $id . '.archive'
    ])) {
        Config::set('is.error', 404);
        return Shield::abort('404/' . $path . '/' . $id);
    }
    $user = new User($file, [], [3 => 'page']);
    if ($title = $user->{'$'}) {
        $user->author = $user->title = $title;
    }
    Config::set('trace', new Anemon([$user->key . ' (' . $title . ')', $config->title], ' &#x00B7; '));
    Lot::set('page', $user);
    Config::set('is', [
        'active' => Is::user($user->key),
        'error' => false,
        'page' => $user->path,
        'pages' => false,
        'user' => $user->key
    ]);
    // Force to disable comment in user page
    Shield::reset('comments');
    return Shield::attach('page/' . $path . '/' . $id);
}, 20);
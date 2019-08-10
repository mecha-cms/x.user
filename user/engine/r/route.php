<?php

$state = state('user');
$max = state('user:guard')['try'] ?? 5;
$path = $state['/'];
$secret = $state['//'] ?? $path;

Route::set($secret, 200, function($form, $k) use($config, $language, $max, $path, $state, $secret, $url, $user, $users) {
    $is_enter = Config::is('enter');
    $GLOBALS['t'][] = $language->{'do' . ($is_enter ? 'Exit' : 'Enter')};
    if ($k === 'post') {
        $key = $form['key'] ?? null;
        $pass = $form['pass'] ?? null;
        $token = $form['token'] ?? null;
        // Has only 1 user!
        if (count($users) === 1) {
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
        $error = $form['_error'] ?? 0;
        // Log out!
        if ($is_enter) {
            // Check token…
            if (Is::void($token) || !Guard::check($token, 'user')) {
                Alert::error('token');
                ++$error;
            } else if (!isset($form['x']) || Is::void($form['x'])) {
                Alert::error('void-field', $language->user, true);
                ++$error;
            } else {
                (new File(USER . DS . $form['x'] . DS . 'token.data'))->let();
                Cookie::let(['user.key', 'user.pass', 'user.token']);
                Session::let(['user.key', 'user.pass', 'user.token']);
                Alert::success('user-exit');
                // Trigger the hook!
                Hook::fire('on.user.exit', [new File($u), null], $user);
                // Remove log-in attempt log
                (new File($try))->let();
                // Redirect to the log in page by default!
                Guard::kick(($form['kick'] ?? $secret) . $url->query('&', ['kick' => false]));
            }
        // Log in!
        } else {
            // Check token…
            if (Is::void($token) || !Guard::check($token, 'user')) {
                Alert::error('token');
                ++$error;
            // Check user key…
            } else if (Is::void($key)) {
                Alert::error('void-field', $language->user, true);
                ++$error;
            // Check user pass…
            } else if (Is::void($pass)) {
                Alert::error('void-field', $language->pass, true);
                ++$error;
            // No error(s), go to the next step(s)…
            } else {
                // Check if user already registered…
                if (is_file($u)) {
                    // Reset password by deleting `pass.data` manually, then log in!
                    if (!is_file($f = Path::F($u) . DS . 'pass.data')) {
                        $file = new File($f);
                        $file->set(P . password_hash($pass . '@' . $key, PASSWORD_DEFAULT));
                        $file->save(0600);
                        Alert::info('is', [$language->pass, '<em>' . $pass . '</em>']);
                    }
                    // Validate password hash!
                    if (strpos($h = content($f), P) === 0) {
                        $enter = password_verify($pass . '@' . $key, substr($h, 1));
                    // Validate password text!
                    } else {
                        $enter = $pass === $h;
                    }
                    // Is valid, then…
                    if (!empty($enter)) {
                        // Save the token!
                        $file = new File(Path::F($u) . DS . 'token.data');
                        $file->set($token);
                        $file->save(0600);
                        Cookie::set('user.key', $key, '7 days');
                        // Cookie::set('user.pass', $pass, '7 days');
                        Cookie::set('user.token', $token, '7 days');
                        // Remove try again message
                        Alert::let();
                        // Show success message!
                        Alert::success('user-enter');
                        // Trigger the hook!
                        Hook::fire('on.user.enter', [new File($u), null], $user);
                        // Remove log-in attempt log
                        (new File($try))->let();
                        // Redirect to the home page by default!
                        Guard::kick(($form['kick'] ?? "") . $url->query('&', ['kick' => false]));
                    } else {
                        Alert::error('user-or-pass');
                        ++$error;
                    }
                } else {
                    Alert::error('user-or-pass');
                    ++$error;
                }
            }
        }
        if ($error > 0) {
            // Store form data to session but `pass`
            unset($form['pass']);
            Session::set('form', $form);
            // Check for log-in attempt quota
            if ($try_data[$ip] > $max - 1) {
                Guard::abort('Please delete the <code>' . str_replace(ROOT, '.', Path::D($try, 2)) . DS . $key[0] . str_repeat('&#x2022;', strlen($key) - 1) . DS . 'try.data</code> file to enter.');
            }
            if (is_file($u)) {
                // Show remaining log-in attempt quota
                Alert::info('user-enter-try', $max - $try_data[$ip]);
                // Record log-in attempt
                $file = new File($try);
                $file->set(json_encode($try_data));
                $file->save(0600);
            }
        }
        Guard::kick($secret . $url->query);
    }
    Config::set('is', [
        'error' => false,
        'page' => true,
        'user' => true
    ]);
    $this->content(__DIR__ . DS . 'content' . DS . 'page.php');
});

Route::set($path . '/:name', function() use($config, $language, $path) {
    $id = $this->name;
    if (!$f = File::exist([
        USER . DS . $id . '.page',
        USER . DS . $id . '.archive'
    ])) {
        Config::set('is.error', 404);
        $GLOBALS['t'][] = $language->isError;
        $this->status(404);
        $this->content('404/' . $path . '/' . $id);
    }
    $user = new User($f);
    if ($t = (string) $user) {
        $user->author = $user->title = $t;
    }
    $GLOBALS['t'][] = $user->user . ' (' . $t . ')';
    $GLOBALS['page'] = $user;
    Config::set('is', [
        'active' => !!Is::user($user->user),
        'error' => false,
        'page' => true,
        'pages' => false,
        'user' => true
    ]);
    // Force to disable comment in user page
    Content::let('comments');
    $this->content('page/' . $path . '/' . $id);
});
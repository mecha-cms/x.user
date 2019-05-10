<?php

$state = Extend::state('user');
$max = $state['try'] ?? 5;
$path = $state['path'];
$secret = $state['_path'] ?? $path;

Route::set($secret, function($form, $k) use($config, $language, $max, $path, $secret, $url, $user, $users) {
    $is_enter = Config::is('enter');
    $this->trace([$language->{'do' . ($is_enter ? 'Exit' : 'Enter')}, $config->title]);
    if ($k === 'POST') {
        $key = $form['user']['key'] ?? null;
        $pass = $form['user']['pass'] ?? null;
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
        $errors = 0;
        // Log out!
        if ($is_enter) {
            // Check token…
            if (Is::void($token) || !$this->check($token, 'user')) {
                Message::error('token');
                ++$errors;
            } else if (!isset($form['x']) || Is::void($form['x'])) {
                Message::error('void-field', $language->user, true);
                ++$errors;
            } else {
                File::open(USER . DS . $form['x'] . DS . 'token.data')->let();
                Cookie::let('user.key');
                Cookie::let('user.pass');
                Cookie::let('user.token');
                Session::let('user.key');
                Session::let('user.pass');
                Session::let('user.token');
                Message::success('user-exit');
                // Trigger the hook!
                Hook::fire('on.user.exit', [new File($u), null], $user);
                // Remove log-in attempt log
                File::open($try)->let();
                // Redirect to the log in page by default!
                $this->kick(($form['kick'] ?? $secret) . $url->query('&', ['kick' => false]));
            }
        // Log in!
        } else {
            // Check token…
            if (Is::void($token) || !$this->check($token, 'user')) {
                Message::error('token');
                ++$errors;
            // Check user key…
            } else if (Is::void($key)) {
                Message::error('void-field', $language->user, true);
                ++$errors;
            // Check user pass…
            } else if (Is::void($pass)) {
                Message::error('void-field', $language->pass, true);
                ++$errors;
            // No error(s), go to the next step(s)…
            } else {
                if ($try_data[$ip] > $max) {
                    Guard::abort('Please delete the <code>' . str_replace(ROOT, '.', Path::D($try, 2)) . DS . $key[0] . str_repeat('&#x2022;', strlen($key) - 1) . DS . 'try.data</code> file to sign in.');
                } else {
                    Message::info('user-enter-try', $max - $try_data[$ip]);
                    ++$errors;
                }
                // Check if user already registered…
                if (is_file($u)) {
                    // Record log-in attempt
                    File::set(json_encode($try_data))->saveTo($try, 0600);
                    // Reset password by deleting `pass.data` manually, then log in!
                    if (!is_file($f = Path::F($u) . DS . 'pass.data')) {
                        File::set(X . password_hash($pass . ' ' . $key, PASSWORD_DEFAULT))->saveTo($f, 0600);
                        Message::info('is', [$language->pass, '<em>' . $pass . '</em>']);
                    }
                    // Validate password hash!
                    if (strpos($h = content($f), X) === 0) {
                        $enter = password_verify($pass . ' ' . $key, substr($h, 1));
                    // Validate password text!
                    } else {
                        $enter = $pass === $h;
                    }
                    // Is valid, then…
                    if (!empty($enter)) {
                        // Save the token!
                        File::set($token)->saveTo(Path::F($u) . DS . 'token.data', 0600);
                        Session::set('user.key', '@' . $key);
                        // Session::set('user.pass', $pass);
                        Session::set('user.token', $token);
                        // Duplicate session to cookie for 7 day(s)
                        Cookie::set('user.key', '@' . $key, '7 days');
                        // Cookie::set('user.pass', $pass, '7 days');
                        Cookie::set('user.token', $token, '7 days');
                        // Show success message!
                        Message::let();
                        Message::success('user-enter');
                        // Trigger the hook!
                        Hook::fire('on.user.enter', [new File($u), null], $user);
                        // Remove log-in attempt log
                        File::open($try)->let();
                        // Redirect to the home page by default!
                        $this->kick(($form['kick'] ?? "") . $url->query('&', ['kick' => false]));
                    } else {
                        Message::error('user-or-pass');
                        ++$errors;
                    }
                } else {
                    Message::error('user-or-pass');
                    ++$errors;
                }
            }
        }
        if ($errors > 0) {
            unset($form['user']['pass']);
            Session::set('form', $form);
        }
        $this->kick($secret . $url->query);
    }
    Config::set('is', [
        'error' => false,
        'page' => true,
        'user' => true
    ]);
    $this->view('user');
});

Route::set($path . '/:slug', function() use($config, $path) {
    $id = $this->slug;
    if (!$f = File::exist([
        USER . DS . $id . '.page',
        USER . DS . $id . '.archive'
    ])) {
        Config::set('is.error', 404);
        $this->view('404/' . $path . '/' . $id);
    }
    $GLOBALS['page'] = $user = new User($f, [], [3 => 'page']);
    if ($title = $user . "") {
        $user->author = $user->title = $title;
    }
    $this->trace([$user->user . ' (' . $title . ')', $config->title]);
    Config::set('is', [
        'active' => !!Is::user($user->user),
        'error' => false,
        'page' => true,
        'pages' => false,
        'user' => true
    ]);
    // Force to disable comment in user page
    Content::let('comments');
    $this->status(200);
    $this->view('page/' . $path . '/' . $id);
});
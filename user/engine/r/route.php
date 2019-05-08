<?php

$state = Extend::state('user');
$max = $state['try'] ?? 5;
$path = $state['path'];
$secret = $state['_path'] ?? $path;

Route::set($secret, function($r, $k) use($max, $path, $secret) {
    $is_enter = $this->config::is('enter');
    $this->title([$this->language->{'do' . ($is_enter ? 'Exit' : 'Enter')}, $this->config->title]);
    if ($k === 'POST') {
        $key = $r['key'] ?? null;
        $pass = $r['pass'] ?? null;
        $token = $r['token'] ?? null;
        // Has only 1 user!
        if (count($this->users) === 1) {
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
                $this->message::error('token');
                ++$errors;
            } else if (!isset($r['x']) || Is::void($r['x'])) {
                $this->message::error('void-field', $this->language->user, true);
                ++$errors;
            } else {
                File::open(USER . DS . $r['x'] . DS . 'token.data')->delete();
                Cookie::let('user.user');
                Cookie::let('user.pass');
                Cookie::let('user.token');
                Session::let('user.user');
                Session::let('user.pass');
                Session::let('user.token');
                $this->message::success('user-exit');
                // Trigger the hook!
                Hook::fire('on.user.exit', [$u, null], $user);
                // Remove log-in attempt log
                File::open($try)->delete();
                // Redirect to the log in page by default!
                $this->kick(($r['kick'] ?? $secret) . $this->url->query('&', ['kick' => false]));
            }
        // Log in!
        } else {
            // Check token…
            if (Is::void($token) || !Guard::check($token, 'user')) {
                $this->message::error('token');
                ++$errors;
            // Check user name…
            } else if (Is::void($key)) {
                $this->message::error('void-field', $this->language->user, true);
                ++$errors;
            // Check user pass…
            } else if (Is::void($pass)) {
                $this->message::error('void-field', $rhis->language->pass, true);
                ++$errors;
            // No error(s), go to the next step(s)…
            } else {
                if ($try_data[$ip] > $max) {
                    Guard::abort('Please delete the <code>' . str_replace(ROOT, '.', Path::D($try, 2)) . DS . $key[0] . str_repeat('&#x2022;', strlen($key) - 1) . DS . 'try.data</code> file to sign in.');
                } else {
                    $this->message::info('user-enter-try', $max - $try_data[$ip]);
                    ++$errors;
                }
                // Check if user already registered…
                if (is_file($u)) {
                    // Record log-in attempt
                    File::put(json_encode($try_data))->saveTo($try, 0600);
                    // Reset password by deleting `pass.data` manually, then log in!
                    if (!is_file($f = Path::F($u) . DS . 'pass.data')) {
                        File::put(X . password_hash($pass . ' ' . $key, PASSWORD_DEFAULT))->saveTo($f, 0600);
                        $this->message::info('is', [$this->language->pass, '<em>' . $pass . '</em>']);
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
                        Session::set('user.user', '@' . $key);
                        // Session::set('user.pass', $pass);
                        Session::set('user.token', $token);
                        // Duplicate session to cookie for 7 day(s)
                        Cookie::set('user.user', '@' . $key, '7 days');
                        // Cookie::set('user.pass', $pass, '7 days');
                        Cookie::set('user.token', $token, '7 days');
                        // Show success message!
                        $this->message::let();
                        $this->message::success('user-enter');
                        // Trigger the hook!
                        Hook::fire('on.user.enter', [$u, $u], $user);
                        // Remove log-in attempt log
                        File::open($try)->delete();
                        // Redirect to the home page by default!
                        $this->kick(($r['kick'] ?? "") . $this->url->query('&', ['kick' => false]));
                    } else {
                        $this->message::error('user-or-pass');
                        ++$errors;
                    }
                } else {
                    $this->message::error('user-or-pass');
                    ++$errors;
                }
            }
        }
        if ($errors > 0) {
            unset($r['pass']);
            Session::set('form', $r);
        }
        $this->kick($secret . $this->url->query);
    }
    $this->config::set('is', [
        'error' => false,
        'page' => true,
        'user' => true
    ]);
    $this->view('user');
});

Route::set($path . '/:user', function() use($config, $path) {
    $id = $this->user;
    if (!$file = File::exist([
        USER . DS . $id . '.page',
        USER . DS . $id . '.archive'
    ])) {
        $this->config::set('is.error', 404);
        $this->view('404/' . $path . '/' . $id);
    }
    $GLOBALS['page'] = $user = new User($file, [], [3 => 'page']);
    if ($title = $user->{'$'}) {
        $user->author = $user->title = $title;
    }
    $this->title([$user->user . ' (' . $title . ')', $this->config->title]);
    $this->config::set('is', [
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
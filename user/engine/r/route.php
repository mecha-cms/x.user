<?php

namespace _\lot\x\user {
    function route($name) {
        extract($GLOBALS, \EXTR_SKIP);
        $state = \State::get('x.user', true);
        $path = \trim($state['path'] ?? "", '/');
        $secret = \trim($state['guard']['path'] ?? $path, '/');
        $exit = \Get::get('exit');
        $token = $user['token'];
        // Force log out with `http://127.0.0.1/user/name?exit=b4d455`
        if (\Request::is('Get') && $exit && $token && $exit === $token) {
            (new \File(LOT . DS . 'user' . DS . $name . DS . 'token.data'))->let();
            (new \File(LOT . DS . 'user' . DS . $name . DS . 'try.data'))->let();
            \Cookie::let(['user.key', 'user.pass', 'user.token']);
            \Session::let(['user.key', 'user.pass', 'user.token']);
            \Alert::success('Logged out.');
            // Trigger the hook!
            \Hook::fire('on.user.exit', [$user->path]);
            // Redirect to the log in page by default!
            \Guard::kick(\Get::get('kick') ?? $secret . $url->query('&', [
                'exit' => false,
                'kick' => false
            ]) . $url->hash);
        }
        if (!$f = \File::exist([
            \LOT . \DS . 'user' . \DS . $name . '.page',
            \LOT . \DS . 'user' . \DS . $name . '.archive'
        ])) {
            \State::set('is.error', 404);
            $GLOBALS['t'][] = \i('Error');
            $this->status(404);
            $this->view('404/' . $path . '/' . $name);
        }
        $user = new \User($f);
        $GLOBALS['t'][] = $user->user . ' (' . ($user->title = $user . "") . ')';
        $GLOBALS['page'] = $user;
        \State::set('is', [
            'active' => !!\Is::user($user->user),
            'error' => false,
            'page' => true,
            'pages' => false,
            'user' => true
        ]);
        $this->view('page/' . $path . '/' . $name);
    }
    $state = \State::get('x.user', true);
    \Route::set(\trim($state['guard']['path'] ?? $state['path'], '/') . '/:name', 200, __NAMESPACE__ . "\\route");
}

namespace _\lot\x\user\route {
    function enter() {
        extract($GLOBALS, \EXTR_SKIP);
        $GLOBALS['t'][] = \i(\Is::user() ? 'Exit' : 'Enter');
        $state = \State::get('x.user', true);
        $path = \trim($state['path'] ?? "", '/');
        $secret = \trim($state['guard']['path'] ?? $path, '/');
        if (\Request::is('Post')) {
            $key = \Post::get('user');
            $pass = \Post::get('pass');
            $token = \Post::get('token');
            // Has only 1 user!
            if (isset($users) && 1 === \count($users)) {
                // Set the `key` value to that user automatically
                $key = $users[0]->name;
            }
            // Remove the `@` prefix!
            if (0 === \strpos($key, '@')) {
                $key = \substr($key, 1);
            }
            $u = \LOT . \DS . 'user' . \DS . $key . '.page';
            $try = \LOT . \DS . 'user' . \DS . $key . \DS . 'try.data';
            $try_data = (array) \e(\content($try));
            $ip = \Client::IP();
            $max = $state['guard']['try'] ?? 5;
            if (!isset($try_data[$ip])) {
                $try_data[$ip] = 1;
            } else {
                ++$try_data[$ip];
            }
            $error = 0;
            // Check token…
            if (\Is::void($token) || !\Guard::check($token, 'user')) {
                \Alert::error('Invalid token.');
                ++$error;
            // Check user key…
            } else if (\Is::void($key)) {
                \Alert::error('Please fill out the %s field.', 'User');
                ++$error;
            // Check user pass…
            } else if (\Is::void($pass)) {
                \Alert::error('Please fill out the %s field.', 'Pass');
                ++$error;
            // No error(s), go to the next step(s)…
            } else {
                // Check if user already registered…
                if (\is_file($u)) {
                    // Reset password by deleting `pass.data` manually, then log in!
                    if (!\is_file($f = \Path::F($u) . \DS . 'pass.data')) {
                        $file = new \File($f);
                        $file->set(\P . \password_hash($pass . '@' . $key, \PASSWORD_DEFAULT))->save(0600);
                        \Alert::info('Your %s is %s.', ['pass', '<em>' . $pass . '</em>']);
                    }
                    // Validate password hash!
                    if (0 === \strpos($h = \content($f), \P)) {
                        $enter = \password_verify($pass . '@' . $key, \substr($h, 1));
                    // Validate password text!
                    } else {
                        $enter = $pass === $h;
                    }
                    // Is valid, then…
                    if (!empty($enter)) {
                        // Save the token!
                        $file = new \File(\Path::F($u) . \DS . 'token.data');
                        $file->set($token)->save(0600);
                        \Cookie::set('user.key', $key, '7 days');
                        \Cookie::set('user.token', $token, '7 days');
                        // Remove try again message
                        \Alert::let();
                        // Show success message!
                        \Alert::success('Logged in.');
                        // Trigger the hook!
                        \Hook::fire('on.user.enter', [$u]);
                        // Remove log-in attempt log
                        (new \File($try))->let();
                        // Redirect to the home page by default!
                        \Guard::kick(\Post::get('kick') ?? $url->query('&', ['kick' => false]) . $url->hash);
                    } else {
                        \Alert::error('Invalid user or pass.');
                        ++$error;
                    }
                } else {
                    \Alert::error('Invalid user or pass.');
                    ++$error;
                }
            }
            if ($error > 0) {
                // Store form data to session but `pass`
                $lot = \Post::get();
                unset($lot['pass'], $lot['token']);
                \Session::set('form', $lot);
                // Check for log-in attempt quota
                if ($try_data[$ip] > $max - 1) {
                    \Guard::abort(\i('Please delete the %s file to enter.', '<code>' . \str_replace(\ROOT, '.', \Path::D($try, 2)) . \DS . $key[0] . \str_repeat('&#x2022;', \strlen($key) - 1) . \DS . 'try.data</code>'));
                }
                if (\is_file($u)) {
                    // Show remaining log-in attempt quota
                    \Alert::info('Try again for %d more times.', $max - $try_data[$ip]);
                    // Record log-in attempt
                    $file = new \File($try);
                    $file->set(\json_encode($try_data))->save(0600);
                }
            }
            \Guard::kick($secret . $url->query . $url->hash);
        }
        \State::set('is', [
            'error' => false,
            'page' => true,
            'user' => true
        ]);
        $this->view(__DIR__ . \DS . 'layout' . \DS . 'page.php');
    }
    $state = \State::get('x.user', true);
    \Route::set(\trim($state['guard']['path'] ?? $state['path'], '/'), 200, __NAMESPACE__ . "\\enter");
}

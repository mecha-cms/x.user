<?php

namespace _\lot\x\user {
    function route($lot, $type) {
        extract($GLOBALS, \EXTR_SKIP);
        $name = $this->name;
        $path = \trim(\State::get('x.user.path'), '/');
        $secret = \trim(\State::get('x.user._path'), '/');
        // Force log out with `http://127.0.0.1/user/name?exit=b4d455`
        if ($type === 'Get' && !empty($lot['exit']) && $lot['exit'] === $user['token']) {
            (new \File(USER . DS . $name . DS . 'token.data'))->let();
            (new \File(USER . DS . $name . DS . 'try.data'))->let();
            \Cookie::let(['user.key', 'user.pass', 'user.token']);
            \Session::let(['user.key', 'user.pass', 'user.token']);
            \Alert::success('user-exit');
            // Trigger the hook!
            \Hook::fire('on.user.exit', [new \File($user->path), null], $user);
            // Redirect to the log in page by default!
            \Guard::kick(($lot['kick'] ?? $secret) . $url->query('&', [
                'exit' => false,
                'kick' => false
            ]));
        }
        if (!$f = \File::exist([
            \USER . \DS . $name . '.page',
            \USER . \DS . $name . '.archive'
        ])) {
            \State::set('is.error', 404);
            $GLOBALS['t'][] = $language->isError;
            $this->status(404);
            $this->content('404/' . $path . '/' . $name);
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
        $this->content('page/' . $path . '/' . $name);
    }
    \Route::set(\trim(\State::get('x.user.path'), '/') . '/:name', 200, __NAMESPACE__ . "\\route");
}

namespace _\lot\x\user\route {
    function enter($lot, $type) {
        extract($GLOBALS, \EXTR_SKIP);
        $GLOBALS['t'][] = $language->{'do' . (\Is::user() ? 'Exit' : 'Enter')};
        $state = \State::get('x.user', true);
        $secret = \trim($state['guard']['path'] ?? "", '/');
        if ($type === 'Post') {
            $key = $lot['user'] ?? null;
            $pass = $lot['pass'] ?? null;
            $token = $lot['token'] ?? null;
            // Has only 1 user!
            if (isset($users) && \count($users) === 1) {
                // Set the `key` value to that user automatically
                $key = $users[0]->name;
            }
            // Remove the `@` prefix!
            if (\strpos($key, '@') === 0) {
                $key = \substr($key, 1);
            }
            $u = \USER . \DS . $key . '.page';
            $try = \USER . \DS . $key . \DS . 'try.data';
            $try_data = (array) \e(\content($try));
            $ip = \Get::IP();
            $max = $state['guard']['try'] ?? 5;
            if (!isset($try_data[$ip])) {
                $try_data[$ip] = 1;
            } else {
                ++$try_data[$ip];
            }
            $error = $lot['_error'] ?? 0;
            // Check token…
            if (\Is::void($token) || !\Guard::check($token, 'user')) {
                \Alert::error('token');
                ++$error;
            }
            // Check user key…
            if (\Is::void($key)) {
                \Alert::error('void-field', $language->user, true);
                ++$error;
            // Check user pass…
            } else if (\Is::void($pass)) {
                \Alert::error('void-field', $language->pass, true);
                ++$error;
            // No error(s), go to the next step(s)…
            } else {
                // Check if user already registered…
                if (\is_file($u)) {
                    // Reset password by deleting `pass.data` manually, then log in!
                    if (!\is_file($f = \Path::F($u) . \DS . 'pass.data')) {
                        $file = new \File($f);
                        $file->set(\P . \password_hash($pass . '@' . $key, \PASSWORD_DEFAULT))->save(0600);
                        \Alert::info('is', [$language->pass, '<em>' . $pass . '</em>']);
                    }
                    // Validate password hash!
                    if (\strpos($h = \content($f), \P) === 0) {
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
                        // \Cookie::set('user.pass', $pass, '7 days');
                        \Cookie::set('user.token', $token, '7 days');
                        // Remove try again message
                        \Alert::let();
                        // Show success message!
                        \Alert::success('user-enter');
                        // Trigger the hook!
                        \Hook::fire('on.user.enter', [new \File($u), null], $user);
                        // Remove log-in attempt log
                        (new \File($try))->let();
                        // Redirect to the home page by default!
                        \Guard::kick(($lot['kick'] ?? "") . $url->query('&', ['kick' => false]));
                    } else {
                        \Alert::error('user-or-pass');
                        ++$error;
                    }
                } else {
                    \Alert::error('user-or-pass');
                    ++$error;
                }
            }
            if ($error > 0) {
                // Store form data to session but `pass`
                unset($lot['pass']);
                \Session::set('form', $lot);
                // Check for log-in attempt quota
                if ($try_data[$ip] > $max - 1) {
                    \Guard::abort('Please delete the <code>' . \str_replace(\ROOT, '.', \Path::D($try, 2)) . \DS . $key[0] . \str_repeat('&#x2022;', \strlen($key) - 1) . \DS . 'try.data</code> file to enter.');
                }
                if (\is_file($u)) {
                    // Show remaining log-in attempt quota
                    \Alert::info('user-enter-try', $max - $try_data[$ip]);
                    // Record log-in attempt
                    $file = new \File($try);
                    $file->set(\json_encode($try_data))->save(0600);
                }
            }
            \Guard::kick($secret . $url->query);
        }
        \State::set('is', [
            'error' => false,
            'page' => true,
            'user' => true
        ]);
        $this->content(__DIR__ . \DS . 'content' . \DS . 'page.php');
    }
    $state = \State::get('x.user', true);
    \Route::set(\trim($state['guard']['path'] ?? $state['path'], '/'), 200, __NAMESPACE__ . "\\enter");
}
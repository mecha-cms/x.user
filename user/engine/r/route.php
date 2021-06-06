<?php

namespace x\user {
    function route($name) {
        extract($GLOBALS, \EXTR_SKIP);
        $path = \trim($state->x->user->path ?? '/user', '/');
        $secret = \trim($state->x->user->guard->path ?? $path, '/');
        $exit = \Get::get('exit');
        $kick = \Get::get('kick');
        $token = $user['token'];
        // Force log out with `http://127.0.0.1/user/name?exit=b4d455`
        if (\Request::is('Get') && $exit && $token && $exit === $token) {
            \unlink(\LOT . \DS . 'user' . \DS . $name . \DS . 'token.data');
            \unlink(\LOT . \DS . 'user' . \DS . $name . \DS . 'try.data');
            \Cookie::let(['user.key', 'user.pass', 'user.token']);
            \Session::let(['user.key', 'user.pass', 'user.token']);
            \Alert::success('Logged out.');
            // Trigger the hook!
            \Hook::fire('on.user.exit', [$user->path]);
            // Redirect to the log in page by default!
            \Guard::kick($kick ?? ($url . '/' . $secret . $url->query('&', [
                'exit' => false,
                'kick' => false
            ]) . $url->hash));
        }
        if (!$f = \File::exist([
            \LOT . \DS . 'user' . \DS . $name . '.archive',
            \LOT . \DS . 'user' . \DS . $name . '.page'
        ])) {
            \State::set('is.error', 404);
            $GLOBALS['t'][] = \i('Error');
            $this->layout('404/' . $path . '/' . $name);
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
        $this->layout('page/' . $path . '/' . $name);
    }
    \Route::set(\trim($state->x->user->path ?? '/user', '/') . '/:user', 200, __NAMESPACE__ . "\\route");
}

namespace x\user\route {
    function enter() {
        extract($GLOBALS, \EXTR_SKIP);
        $GLOBALS['t'][] = \i(\Is::user() ? 'Exit' : 'Enter');
        $path = \trim($state->x->user->path ?? '/user', '/');
        $secret = \trim($state->x->user->guard->path ?? $path, '/');
        if (\Request::is('Post')) {
            extract((array) \Post::get('user'), \EXTR_SKIP);
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
            $try_data = (array) \e(\file_get_contents($try));
            $ip = \Client::IP();
            $max = $state->x->user->guard->try ?? 5;
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
                        \file_put_contents($f, \P . \password_hash($pass . '@' . $key, \PASSWORD_DEFAULT));
                        \chmod($f, 0600);
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
                        // Use the stored token value from another device if any
                        // e.g. the user has not decided to log out on that device
                        if (\is_file($t = \Path::F($u) . \DS . 'token.data')) {
                            $token = \file_get_contents($t);
                        // Create the token file!
                        } else {
                            \file_put_contents($t, $token);
                            \chmod($t, 0600);
                        }
                        \Cookie::set('user.key', $key, '7 days');
                        \Cookie::set('user.token', $token, '7 days');
                        // Remove try again message
                        \Alert::let();
                        // Show success message!
                        \Alert::success('Logged in.');
                        // Trigger the hook!
                        \Hook::fire('on.user.enter', [$u]);
                        // Remove log-in attempt log
                        \unlink($try);
                        // Redirect to the home page by default!
                        \Guard::kick($kick ?? $url->query('&', ['kick' => false]) . $url->hash);
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
                $lot = (array) \Post::get('user');
                unset($lot['pass'], $lot['token']);
                \Session::set('form', ['user' => $lot]);
                // Check for log-in attempt quota
                if ($try_data[$ip] > $max - 1) {
                    \Guard::abort(\i('Please delete the %s file to enter.', '<code>' . \str_replace(\ROOT, '.', \Path::D($try, 2)) . \DS . $key[0] . \str_repeat('&#x2022;', \strlen($key) - 1) . \DS . 'try.data</code>'));
                }
                if (\is_file($u)) {
                    // Show remaining log-in attempt quota
                    \Alert::info('Try again for %d more times.', $max - $try_data[$ip]);
                    // Record log-in attempt
                    \file_put_contents($try, \json_encode($try_data));
                    \chmod($try, 0600);
                }
            }
            \Guard::kick($secret . $url->query . $url->hash);
        }
        \State::set('is', [
            'error' => false,
            'page' => true,
            'user' => true
        ]);
        $z = \defined("\\DEBUG") && \DEBUG ? '.' : '.min.';
        \Asset::set(__DIR__ . \DS . '..' . \DS . '..' . \DS . 'lot' . \DS . 'asset' . \DS . 'css' . \DS . 'index' . $z . 'css', 20.1);
        $this->layout(__DIR__ . \DS . 'layout' . \DS . 'page.php');
    }
    \Route::set(\trim($state->x->user->guard->path ?? $state->x->user->path ?? '/user', '/'), 200, __NAMESPACE__ . "\\enter");
}

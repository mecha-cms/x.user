<?php

namespace x\user {
    function route($name) {
        extract($GLOBALS, \EXTR_SKIP);
        $route = \trim($state->x->user->route ?? 'user', '/');
        $secret = \trim($state->x->user->guard->route ?? $route, '/');
        $exit = $_GET['exit'] ?? null;
        $kick = i$_GET['kick'] ?? null;
        $token = $user->token;
        // Force log out with `http://127.0.0.1/user/name?exit=b4d455`
        if ('GET' === $_SERVER['REQUEST_METHOD'] && $exit && $token && $exit === $token) {
            \delete(\LOT . \D. 'user' . \D . $name . \D . 'token.data');
            \delete(\LOT . \D . 'user' . \D . $name . \D . 'try.data');
            \cookie('user.key', null);
            \cookie('user.pass', null);
            \cookie('user.token', null);
            \Alert::success('Logged out.');
            // Trigger the hook!
            \Hook::fire('on.user.exit', [$user->path]);
            // Redirect to the log-in page by default!
            \kick($kick ?? ('/' . $secret . $url->query([
                'exit' => false,
                'kick' => false
            ]) . $url->hash));
        }
        if (!$file = \exist([
            \LOT . \D . 'user' . \D . $name . '.archive',
            \LOT . \D . 'user' . \D . $name . '.page'
        ], 1)) {
            \status(404);
            \State::set('is', ['error' => 404]);
            $GLOBALS['t'][] = \i('Error');
            \Hook::fire('layout', ['error/' . $route . '/' . $name]);
        }
        $user = new \User($file);
        $GLOBALS['page'] = $user;
        $GLOBALS['t'][] = $user->user . ' (' . ($user->title = $user . "") . ')';
        \State::set('is', [
            'active' => !!\Is::user($user->user),
            'error' => false,
            'page' => true,
            'pages' => false,
            'user' => true
        ]);
        \Hook::fire('layout', ['page/' . $route . '/' . $name]);
    }
    $route = \trim($state->x->user->route ?? 'user', '/');
    if (0 === \strpos($url->path ?? "", '/' . $route . '/')) {
        \Hook::set('route', function($path, $query, $hash) {
            $chops = \explode('/', $path);
            \Hook::fire('route.user', [\array_pop($chops), \implode('/', $path), $query, $hash]);
        }, 10);
        \Hook::set('route.user', __NAMESPACE__ . "\\route", 20);
    }
}

namespace x\user\route {
    function enter($path) {
        extract($GLOBALS, \EXTR_SKIP);
        $GLOBALS['t'][] = \i(\Is::user() ? 'Exit' : 'Enter');
        $path = \trim($path ?? "", '/');
        $route = \trim($state->x->user->path ?? 'route', '/');
        $secret = \trim($state->x->user->guard->path ?? $route, '/');
        if ($path !== $secret) {
            return;
        }
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            extract((array) ($_POST['user'] ?? []), \EXTR_SKIP);
            // Has only 1 user!
            if (isset($users) && 1 === \count($users)) {
                // Set the `key` value to that user automatically
                $key = $users[0]->name;
            }
            // Remove the `@` prefix!
            if (0 === \strpos($key, '@')) {
                $key = \substr($key, 1);
            }
            $file = \LOT . \D . 'user' . \D . $key . '.page';
            $try = \LOT . \D . 'user' . \D . $key . \D . 'try.data';
            $try_data = \json_decode(\is_file($try) ? \file_get_contents($try) : '[]', true);
            $ip = \getenv('HTTP_CLIENT_IP') ?: \getenv('HTTP_X_FORWARDED_FOR') ?: \getenv('HTTP_X_FORWARDED') ?: \getenv('HTTP_FORWARDED_FOR') ?: \getenv('HTTP_FORWARDED') ?: \getenv('REMOTE_ADDR');
            $max = $state->x->user->guard->try ?? 5;
            if (!isset($try_data[$ip])) {
                $try_data[$ip] = 1;
            } else {
                ++$try_data[$ip];
            }
            $error = 0;
            // Check token…
            if (\Is::void($token) || !\check($token, 'user')) {
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
                if (\is_file($file)) {
                    // Reset password by deleting `pass.data` manually, then log in!
                    if (!\is_file($f = \dirname($file) . \D . \pathinfo($file, \PATHINFO_FILENAME) . \D . 'pass.data')) {
                        \file_put_contents($f, \P . \password_hash($pass . '@' . $key, \PASSWORD_DEFAULT));
                        \chmod($f, 0600);
                        \Alert::info('Your %s is %s.', ['pass', '<em>' . $pass . '</em>']);
                    }
                    // Validate password hash!
                    if (0 === \strpos($h = \file_get_contents($f), \P)) {
                        $enter = \password_verify($pass . '@' . $key, \substr($h, 1));
                    // Validate password text!
                    } else {
                        $enter = $pass === $h;
                    }
                    // Is valid, then…
                    if (!empty($enter)) {
                        // Use the stored token value from another device if exists
                        // e.g. the user has not decided to log out on that device yet
                        if (\is_file($f = \dirname($file) . \D . \pathinfo($file, \PATHINFO_FILENAME) . \D . 'token.data')) {
                            $token = \file_get_contents($f);
                        // Create the token file!
                        } else {
                            \file_put_contents($f, $token);
                            \chmod($f, 0600);
                        }
                        \cookie('user.key', $key, '7 days');
                        \cookie('user.token', $token, '7 days');
                        // Remove try again message
                        \Alert::let();
                        // Show success message!
                        \Alert::success('Logged in.');
                        // Trigger the hook!
                        \Hook::fire('on.user.enter', [$file]);
                        // Remove log-in attempt log
                        \delete($try);
                        // Redirect to the home page by default!
                        \kick($kick ?? '/' . $url->query([
                            'kick' => false
                        ]) . $url->hash);
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
                // Store form data to session but `pass` and `token`
                $lot = (array) ($_POST['user'] ?? []);
                unset($lot['pass'], $lot['token']);
                $_SESSION['form']['user'] = $lot;
                // Check for log-in attempt quota
                if ($try_data[$ip] > $max - 1) {
                    \abort(\i('Please delete the %s file to enter.', '<code>' . \str_replace(\ROOT, '.', \dirname($try, 2)) . \D . $key[0] . \str_repeat('&#x2022;', \strlen($key) - 1) . \D . 'try.data</code>'));
                }
                if (\is_file($file)) {
                    // Show remaining log-in attempt quota
                    \Alert::info('Try again for %d more times.', $max - $try_data[$ip]);
                    // Record log-in attempt
                    \file_put_contents($try, \json_encode($try_data));
                    \chmod($try, 0600);
                }
            }
            \kick('/' . $secret . $url->query . $url->hash);
        }
        \State::set('is', [
            'error' => false,
            'page' => true,
            'user' => true
        ]);
        $z = \defined("\\TEST") && \TEST ? '.' : '.min.';
        \Asset::set(__DIR__ . \D . '..' . \D . '..' . \D . 'lot' . \D . 'asset' . \D . 'css' . \D . 'index' . $z . 'css', 20.1);
        \Hook::fire('layout', ['user']);
    }
    \Hook::set('route', __NAMESPACE__ . "\\enter", 10);
}
<?php

namespace x\user {
    function route($name) {
        \extract($GLOBALS, \EXTR_SKIP);
        $folder = \LOT . \D. 'user' . \D . $name;
        $route = \trim($state->x->user->route ?? 'user', '/');
        $route_secret = \trim($state->x->user->guard->route ?? $route, '/');
        $exit = $_GET['exit'] ?? null;
        $kick = $_GET['kick'] ?? null;
        $token = $user->token;
        $x_alert = isset($state->x->alert);
        // Force log out with `http://127.0.0.1/user/name?exit=b4d455`
        if ('GET' === $_SERVER['REQUEST_METHOD'] && $exit) {
            if ($token && $exit === $token) {
                \is_file($f = $folder . \D . 'token.data') && \unlink($f);
                \is_file($f = $folder . \D . 'try.data') && \unlink($f);
                \cookie('user.key', "", -1);
                \cookie('user.pass', "", -1);
                \cookie('user.token', "", -1);
                $x_alert && \Alert::success('Logged out.');
                // Trigger the hook!
                \Hook::fire('on.user.exit', [$user->path]);
            } else {
                $x_alert && \Alert::error('Invalid token.');
            }
            // Redirect to the log-in page by default!
            \kick($kick ?? ('/' . $route_secret . $url->query([
                'exit' => false,
                'kick' => false
            ]) . $url->hash));
        }
        if (!$file = \exist([
            $folder . '.archive',
            $folder . '.page'
        ], 1)) {
            \State::set('is', ['error' => 404]);
            $GLOBALS['t'][] = \i('Error');
            \status(404);
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
        \status(200);
        \Hook::fire('layout', ['page/' . $route . '/' . $name]);
    }
    $route = \trim($state->x->user->route ?? 'user', '/');
    if (0 === \strpos($url->path ?? "", '/' . $route . '/')) {
        \Hook::set('route', function($path, $query, $hash) {
            $chops = \explode('/', $path);
            \Hook::fire('route.user', [\array_pop($chops), \implode('/', $chops), $query, $hash]);
        }, 10);
        \Hook::set('route.user', __NAMESPACE__ . "\\route", 20);
    }
}

namespace x\user\route {
    function enter($path) {
        \extract($GLOBALS, \EXTR_SKIP);
        $path = \trim($path ?? "", '/');
        $route = \trim($state->x->user->path ?? 'user', '/');
        $route_secret = \trim($state->x->user->guard->path ?? $route, '/');
        if ($path !== $route_secret) {
            return;
        }
        $x_alert = isset($state->x->alert);
        $GLOBALS['t'][] = \i(\Is::user() ? 'Exit' : 'Enter');
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $key = $_POST['user']['key'] ?? null;
            $pass = $_POST['user']['pass'] ?? null;
            $token = $_POST['user']['token'] ?? null;
            // Has only 1 user!
            if (isset($users) && 1 === \count($users)) {
                // Set the `key` value to that user automatically
                $key = $users[0]->name;
            }
            // Remove the `@` prefix!
            if (0 === \strpos($key, '@')) {
                $key = \substr($key, 1);
            }
            $folder = \LOT . \D . 'user' . \D . $key;
            $file = $folder . '.page';
            $try = $folder . \D . 'try.data';
            $try_data = \json_decode(\is_file($try) ? \file_get_contents($try) : '[]', true);
            $try_limit = $state->x->user->guard->try ?? 5;
            $try_user = \getenv('HTTP_CLIENT_IP') ?: \getenv('HTTP_X_FORWARDED_FOR') ?: \getenv('HTTP_X_FORWARDED') ?: \getenv('HTTP_FORWARDED_FOR') ?: \getenv('HTTP_FORWARDED') ?: \getenv('REMOTE_ADDR');
            $try_data[$try_user] = ($try_data[$try_user] ?? 0) + 1;
            $error = 0;
            // Check token…
            if (\Is::void($token) || !\check($token, 'user')) {
                $x_alert && \Alert::error('Invalid token.');
                ++$error;
            // Check user key…
            } else if (\Is::void($key)) {
                $x_alert && \Alert::error('Please fill out the %s field.', 'User');
                ++$error;
            // Check user pass…
            } else if (\Is::void($pass)) {
                $x_alert && \Alert::error('Please fill out the %s field.', 'Pass');
                ++$error;
            // No error(s), go to the next step(s)…
            } else {
                // Check if user already registered…
                if (\is_file($file)) {
                    // Reset password by deleting `pass.data` manually, then log in!
                    if (!\is_file($f = \dirname($file) . \D . \pathinfo($file, \PATHINFO_FILENAME) . \D . 'pass.data')) {
                        \file_put_contents($f, \P . \password_hash($pass . '@' . $key, \PASSWORD_DEFAULT));
                        \chmod($f, 0600);
                        $x_alert && \Alert::info('Your %s is %s.', ['pass', '<em>' . $pass . '</em>']);
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
                        $x_alert && \Alert::let();
                        // Show success message!
                        $x_alert && \Alert::success('Logged in.');
                        // Trigger the hook!
                        \Hook::fire('on.user.enter', [$file]);
                        // Remove log-in attempt log
                        \is_file($try) && \unlink($try);
                        // Redirect to the home page by default!
                        \kick($kick ?? '/' . $url->query([
                            'kick' => false
                        ]) . $url->hash);
                    } else {
                        $x_alert && \Alert::error('Invalid user or pass.');
                        ++$error;
                    }
                } else {
                    $x_alert && \Alert::error('Invalid user or pass.');
                    ++$error;
                }
            }
            if ($error > 0) {
                // Store form data to session but `pass` and `token`
                $data = (array) ($_POST['user'] ?? []);
                unset($data['pass'], $data['token']);
                $_SESSION['form']['user'] = $data;
                // Check for log-in attempt quota
                if ($try_data[$try_user] > $try_limit - 1) {
                    \abort(\i('Please delete the %s file to enter.', '<code>' . \str_replace(\PATH, '.', \dirname($try, 2)) . \D . $key[0] . \str_repeat('&#x2022;', \strlen($key) - 1) . \D . 'try.data</code>'));
                }
                if (\is_file($file)) {
                    // Show remaining log-in attempt quota
                    $x_alert && \Alert::info('Try again for %d more times.', $try_limit - $try_data[$try_user]);
                    // Record log-in attempt
                    \file_put_contents($try, \json_encode($try_data));
                    \chmod($try, 0600);
                }
            }
            \kick('/' . $route_secret . $url->query . $url->hash);
        }
        \State::set('is', [
            'error' => false,
            'page' => true,
            'user' => true
        ]);
        $z = \defined("\\TEST") && \TEST ? '.' : '.min.';
        \status(200);
        \Asset::set(__DIR__ . \D . '..' . \D . '..' . \D . 'lot' . \D . 'asset' . \D . 'index' . $z . 'css', 20.1);
        \Hook::fire('layout', ['user']);
    }
    \Hook::set('route', __NAMESPACE__ . "\\enter", 10);
}
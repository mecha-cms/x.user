<?php

namespace {
    function user(...$lot) {
        return new \User(...$lot);
    }
    function users(...$lot) {
        return new \Users(...$lot);
    }
    if (\class_exists("\\Layout")) {
        !\Layout::path('form/user') && \Layout::set('form/user', __DIR__ . \D . 'engine' . \D . 'y' . \D . 'form' . \D . 'user.php');
        !\Layout::path('user') && \Layout::set('user', __DIR__ . \D . 'engine' . \D . 'y' . \D . 'user.php');
    }
    \State::set('[x].count.user', \State::get('[x].count.user') ?? \q(\g(\LOT . \D . 'user', 'page')));
    \State::set('has.user', \State::get('has.user') ?? !!\Is::user());
}

namespace x\user {
    function page__author($author) {
        if ($author && \is_string($author) && 0 === \strpos($author, '@') && \is_file($file = \LOT . \D . 'user' . \D . \substr($author, 1) . '.page')) {
            return new \User($file);
        }
        return $author;
    }
    function route($content, $path, $query, $hash) {
        if (null !== $content) {
            return $content;
        }
        \extract(\lot(), \EXTR_SKIP);
        $path = \trim($path ?? "", '/');
        if ($part = \x\page\n($path)) {
            $path = \substr($path, 0, -\strlen('/' . $part));
        }
        $route = \trim($state->x->user->route ?? 'user', '/');
        if ("" !== ($v = \substr($path, \strlen($route) + 1))) {
            \State::set('[x].query.user', $v);
        }
        return \Hook::fire('route.user', [$content, '/' . $path . ($part ? '/' . $part : ""), $query, $hash]);
    }
    function route__user($content, $path, $query, $hash) {
        if (null !== $content) {
            return $content;
        }
        \extract(\lot(), \EXTR_SKIP);
        $can_alert = \class_exists("\\Alert");
        $folder = \LOT . \D. 'user' . \D . ($name = \State::get('[x].query.user') ?? "");
        $path = \trim($path ?? "", '/');
        if ($part = \x\page\n($path)) {
            $path = \substr($path, 0, -\strlen('/' . $part));
        }
        $route = \trim($state->x->user->route ?? 'user', '/');
        $route_x = \trim($state->x->user->guard->route ?? $route, '/');
        $exit = $_GET['exit'] ?? null;
        $kick = $_GET['kick'] ?? null;
        $token = $user->token;
        // Force log-out with `http://127.0.0.1/user/:name?exit=:token`
        if ('GET' === $_SERVER['REQUEST_METHOD'] && $exit && 0 === \strpos(\trim($path, '/') . '/', $route_x . '/')) {
            if ($token && $exit === $token) {
                \is_file($f = $folder . \D . 'token.data') && \unlink($f);
                \is_file($f = $folder . \D . 'try.data') && \unlink($f);
                \cookie('user.name', "", -1);
                \cookie('user.token', "", -1);
                $can_alert && \Alert::success('Logged out.');
                // Trigger the hook
                \Hook::fire('on.user.exit', [$user->path], $user);
            } else {
                $can_alert && \Alert::error('Invalid token.');
            }
            // Redirect to the log-in page (by default)
            \kick($kick ?? ('/' . $route_x . $url->query([
                'exit' => false,
                'kick' => false
            ]) . $url->hash));
        }
        \State::set([
            'has' => [
                'next' => false,
                'page' => false,
                'pages' => false,
                'parent' => false,
                'part' => $part > 0,
                'prev' => false
            ],
            'is' => [
                'error' => 404,
                'page' => true,
                'pages' => false,
                'user' => true,
                'users' => false
            ]
        ]);
        if (!$file = \exist([
            $folder . '.archive',
            $folder . '.page'
        ], 1)) {
            \lot('t')[] = \i('Error');
            return ['page/user', [], 404];
        }
        \lot('page', $user = new \User($file));
        \lot('t')[] = \i('User');
        \lot('t')[] = $user->title = $user . "";
        \State::set('is', [
            'active' => !!\Is::user($user->user),
            'error' => false
        ]);
        return ['page/user/' . $user->name, [], 200];
    }
    \Hook::set('page.author', __NAMESPACE__ . "\\page__author", 2);
    $path = \trim($url->path ?? $state->route ?? 'index', '/');
    $route = \trim($state->x->user->route ?? 'user', '/');
    $route_x = \trim($state->x->user->guard->route ?? $route, '/');
    if (0 === \strpos($path . '/', $route_x . '/') || 0 === \strpos($path . '/', $route . '/')) {
        \Hook::set('route', __NAMESPACE__ . "\\route", 90);
        \Hook::set('route.user', __NAMESPACE__ . "\\route__user", 100);
    }
}

namespace x\user\route {
    function enter($content, $path) {
        if (null !== $content) {
            return $content;
        }
        \extract(\lot(), \EXTR_SKIP);
        $can_alert = \class_exists("\\Alert");
        $path = \trim($path ?? $state->route ?? 'index', '/');
        if ($part = \x\page\n($path)) {
            $path = \substr($path, 0, -\strlen('/' . $part));
        }
        $route = \trim($state->x->user->route ?? 'user', '/');
        $route_x = \trim($state->x->user->guard->route ?? $route, '/');
        if ($path !== $route_x) {
            return $content;
        }
        \lot('t')[] = \i(\Is::user() ? 'Exit' : 'Enter');
        if ('POST' !== $_SERVER['REQUEST_METHOD']) {
            \State::set([
                'has' => [
                    'next' => false,
                    'page' => false,
                    'pages' => false,
                    'parent' => false,
                    'part' => $part > 0,
                    'prev' => false
                ],
                'is' => [
                    'error' => false,
                    'page' => true,
                    'pages' => false,
                    'user' => true,
                    'users' => false
                ]
            ]);
            $z = \defined("\\TEST") && \TEST ? '.' : '.min.';
            \class_exists("\\Asset") && \Asset::set(__DIR__ . \D . 'index' . $z . 'css', 20.1);
            return ['user', [], 200];
        }
        $key = $_POST['key'] ?? "";
        $kick = $_POST['kick'] ?? null;
        $pass = $_POST['pass'] ?? "";
        $token = $_POST['token'] ?? null;
        // Has only 1 user
        if (1 === \q($it = \g($folder = \LOT . \D . 'user', 'page'))) {
            // To get the first element of a `RecursiveIteratorIterator` instance, a rewind is needed, somehow.
            $it->rewind(); // This should execute the `RecursiveIteratorIterator::beginIteration()` method.
            // Set the `key` value to the only user, automatically!
            $key = \basename($it->key(), '.page');
        }
        $key = \ltrim($key, '@'); // Remove the `@` prefix
        $file = ($folder .= \D . $key) . '.page';
        $try = $folder . \D . 'try.data';
        $try_data = \json_decode(\is_file($try) ? \file_get_contents($try) : '{}', true);
        $try_data[$try_user = \ip()] = ($try_data[$try_user] ?? 0) + 1;
        $try_max = ($state->x->user->guard->try ?? 5) + 1;
        $enter = $error = 0;
        // Check token…
        if (\Is::void($token) || !\check($token, 'user')) {
            $can_alert && \Alert::error('Invalid token.');
            ++$error;
        // Check user key…
        } else if (\Is::void($key)) {
            $can_alert && \Alert::error('Please fill out the %s field.', 'Key');
            ++$error;
        // Check user pass…
        } else if (\Is::void($pass)) {
            $can_alert && \Alert::error('Please fill out the %s field.', 'Pass');
            ++$error;
        // No error(s), go to the next step(s)…
        } else {
            // Check if user already exists…
            if (\is_file($file)) {
                // Reset pass by deleting `pass.data` manually, then log-in with the new pass!
                if (!\is_file($f = $folder . \D . 'pass.data')) {
                    \file_put_contents($f, \P . \password_hash($pass . '@' . $key, \PASSWORD_DEFAULT));
                    \chmod($f, 0600);
                    $can_alert && \Alert::info('Your %s is %s.', ['pass', '<em>' . $pass . '</em>']);
                }
                // Validate pass hash
                if (0 === \strpos($h = \file_get_contents($f), \P)) {
                    $enter = \password_verify($pass . '@' . $key, \substr($h, 1));
                // Validate pass text
                } else {
                    $enter = $pass === $h;
                }
                // Is valid, then…
                if ($enter) {
                    // Use the stored token value from another device if exists
                    // e.g. the user has not decided to log-out on that device yet
                    if (\is_file($f = $folder . \D . 'token.data')) {
                        $token = \file_get_contents($f);
                    // Create the token file
                    } else {
                        \file_put_contents($f, $token);
                        \chmod($f, 0600);
                    }
                    \cookie('user.name', $key, '+7 days');
                    \cookie('user.token', $token, '+7 days');
                    // Remove “try again” message
                    $can_alert && \Alert::let();
                    // Show success message
                    $can_alert && \Alert::success('Logged in.');
                    // Trigger the hook
                    \Hook::fire('on.user.enter', [$file], new \User($file));
                    // Remove the log-in attempt log
                    // TODO: Remove only the specified IP from the log-on attempt log
                    \is_file($try) && \unlink($try);
                    // Redirect to the home page (by default)
                    \kick($kick ?? '/' . $url->query([
                        'kick' => false
                    ]) . $url->hash);
                }
            }
            $can_alert && \Alert::error('Invalid user or pass.');
            ++$error;
        }
        if ($error > 0) {
            // Store form `key` to session
            $_SESSION['form']['key'] = $key;
            // Check for log-in attempt quota
            if ($try_data[$try_user] > $try_max - 1) {
                if (\defined("\\TEST") && \TEST) {
                    $path = \strtr(\dirname($try, 2), [\PATH . \D => '.' . \D]) . \D . $key[0] . \str_repeat('&#x2022;', \strlen($key) - 1) . \D . 'try.data';
                    if (\function_exists("\\abort")) {
                        \abort(\i('Please delete the %s file to enter.', '<code>' . $path . '</code>'));
                    }
                    \kick('/');
                }
                if ($can_alert) {
                    \Alert::let(); // Clear all previous alert(s)
                    \Alert::error('Too many failed attempts.');
                }
                \kick('/' . $route_x . $url->query . $url->hash);
            }
            if (\is_file($file)) {
                // Show remaining log-in attempt quota
                $can_alert && \Alert::info('Try again for %d more time' . (1 === ($v = $try_max - $try_data[$try_user]) ? "" : 's') . '.', $v);
                // Record log-in attempt
                \file_put_contents($try, \json_encode($try_data));
                \chmod($try, 0600);
            }
        }
        \kick('/' . $route_x . $url->query . $url->hash);
    }
    function start($content, $path) {
        if (null !== $content) {
            return $content;
        }
        \extract(\lot(), EXTR_SKIP);
        $can_alert = \class_exists("\\Alert");
        $path = \trim($path ?? $state->route ?? 'index', '/');
        if ($part = \x\page\n($path)) {
            $path = \substr($path, 0, -\strlen('/' . $part));
        }
        $route = \trim($state->x->user->route ?? 'user', '/');
        $route_x = \trim($state->x->user->guard->route ?? $route, '/');
        if ($path !== $route_x) {
            return $content;
        }
        \lot('t')[] = \i('User');
        if ('POST' !== $_SERVER['REQUEST_METHOD']) {
            \State::set([
                'has' => [
                    'next' => false,
                    'page' => false,
                    'pages' => false,
                    'parent' => false,
                    'part' => $part > 0,
                    'prev' => false
                ],
                'is' => [
                    'error' => false,
                    'page' => true,
                    'pages' => false,
                    'user' => true,
                    'users' => false
                ]
            ]);
            $z = \defined("\\TEST") && \TEST ? '.' : '.min.';
            \class_exists("\\Asset") && \Asset::set(__DIR__ . \D . 'index' . $z . 'css', 20.1);
            return ['user', [], 200];
        }
        $key = $_POST['key'] ?? null;
        $kick = $_POST['kick'] ?? null;
        $pass = $_POST['pass'] ?? null;
        $token = $_POST['token'] ?? null;
        $key = \ltrim($key, '@'); // Remove the `@` prefix
        $key = \To::kebab($key); // Force `foo-bar-baz` format
        $error = 0;
        // Check token…
        if (\Is::void($token) || !\check($token, 'user')) {
            $can_alert && \Alert::error('invalid token.');
            ++$error;
        // Check user key…
        } else if (\Is::void($key)) {
            $can_alert && \Alert::error('Please fill out the %s field.', 'User');
            ++$error;
        // Check user pass…
        } else if (\Is::void($pass)) {
            $can_alert && \Alert::error('Please fill out the %s field.', 'Pass');
            ++$error;
        // No error(s), go to the next step(s)…
        } else {
            $can_alert && \Alert::info('Your %s is %s.', ['pass', '<em>' . $pass . '</em>']);
            $pass = \P . \password_hash($pass . '@' . $key, \PASSWORD_DEFAULT);
            if (!\is_dir($folder = \LOT . \D . 'user' . \D . $key)) {
                \mkdir($folder, 0775, true);
            }
            \file_put_contents($file = $folder . \D . 'pass.data', $pass);
            \chmod($file, 0600);
            \file_put_contents($file = $folder . \D . 'time.data', \date('Y-m-d H:i:s'));
            \chmod($file, 0600);
            \file_put_contents($file = $folder . \D . 'token.data', $token);
            \chmod($file, 0600);
            \file_put_contents($file = $folder . '.page', \To::page([
                'content' => "",
                'status' => 1
            ]));
            \chmod($file, 0600);
            \cookie('user.name', $key, '+7 days');
            \cookie('user.token', $token, '+7 days');
            // Show success message
            $can_alert && \Alert::success('Logged in.');
            // Trigger the hook
            \Hook::fire('on.user.start', [$file], new \User($file));
            // Redirect to the user page (by default)
            \kick($kick ?? ('/' . $route_x . $url->query([
                'kick' => false
            ]) . $url->hash));
        }
        if ($error > 0) {
            // Store form `key` to session
            $_SESSION['form']['key'] = $key;
        }
        \kick('/' . $route_x . $url->query . $url->hash);
    }
    \Hook::set('route', __NAMESPACE__ . "\\" . (\State::get('[x].count.user') > 0 ? 'enter' : 'start'), 90);
}
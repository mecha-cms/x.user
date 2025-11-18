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
    \State::set('with.user', isset($user) && $user->exist ? $user->user : false);
}

namespace x\user {
    function page__author($author) {
        if ($author && \is_string($author) && 0 === \strpos($author, '@') && \is_file($file = \LOT . \D . 'user' . \D . \substr($author, 1) . '.page')) {
            return new \User($file);
        }
        return $author;
    }
    \Hook::set('page.author', __NAMESPACE__ . "\\page__author", 2);
    if ($part = \x\page\part($path = \trim($url->path ?? "", '/'))) {
        $path = \substr($path, 0, -\strlen('/' . $part));
    }
    $part = ($part ?? 0) - 1;
    $route = \trim($state->x->user->route ?? 'user', '/');
    $route_x = \trim($state->x->user->guard->route ?? $route, '/');
    // For `/user/…`
    if ('GET' === $_SERVER['REQUEST_METHOD'] && $route !== $route_x && \substr_count($path, '/') > 0 && !\array_key_exists('exit', $_GET)) {
        $route_x = $route;
    }
    if ($part < 0 && 0 === \strpos($path . '/', $route_x . '/')) {
        \Hook::set('route', __NAMESPACE__ . "\\route", 90);
        \Hook::set('route.user', __NAMESPACE__ . "\\route__user", 100);
        \State::set('is', [
            'secret' => $route !== $route_x,
            'user' => true
        ]);
        \State::set('q.user', $path !== $route_x ? \substr(\strstr($path, '/'), 1) : null);
    }
    function route($content, $path, $query, $hash) {
        if (null !== $content) {
            return $content;
        }
        return \Hook::fire('route.user', [$content, null, $query, $hash]);
    }
    function route__user($content, $path, $query, $hash) {
        if (null !== $content) {
            return $content;
        }
        \extract(\lot(), \EXTR_SKIP);
        $route = \trim($state->x->user->route ?? 'user', '/');
        $route_x = \trim($state->x->user->guard->route ?? $route, '/');
        $with_alert = isset($state->x->alert);
        // For `/user/…`
        if ($name = $state->q->user ?? "") {
            $folder = \LOT . \D . 'user' . \D . $name;
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
            $token = $user->token ?? "";
            // Force log-out with `/user/:name?exit=:token`
            if ('GET' === $_SERVER['REQUEST_METHOD'] && ($exit = $_GET['exit'] ?? 0)) {
                $kick = $_GET['kick'] ?? null;
                if ($token && $exit === $token) {
                    \cookie('user.name', "", -1);
                    \cookie('user.token', "", -1);
                    \delete($folder . \D . '.try' . \D . \md5(\ip()));
                    \delete($folder . \D . 'token.data');
                    $with_alert && \Alert::success('Logged out.');
                    // Trigger the hook
                    \Hook::fire('on.user.exit', [$user->path], $user);
                } else {
                    $with_alert && \Alert::error('Invalid token.');
                }
                // Redirect to the log-in page (by default)
                \kick($kick ?? ('/' . $route_x . $url->query([
                    'exit' => false,
                    'kick' => false
                ]) . $url->hash));
            }
            \State::set('is.active', $name === \cookie('user.name'));
            return ['page/user/' . $name, [], 200];
        }
        // For `/user`
        $count = \q($it = \g($folder = \LOT . \D . 'user', 'page'));
        if ('POST' !== $_SERVER['REQUEST_METHOD']) {
            if (isset($state->x->asset)) {
                \Asset::set(__DIR__ . \D . 'index' . (\defined("\\TEST") && \TEST ? '.' : '.min.') . 'css', 20.1);
            }
            \lot('t')[] = \i(0 === $count ? 'Create' : (isset($user) && $user->exist ? 'Exit' : 'Enter'));
            return ['user', [], 200];
        }
        $error = $valid = 0;
        $key = $_POST['key'] ?? "";
        $kick = $_POST['kick'] ?? null;
        $pass = $_POST['pass'] ?? "";
        $token = $_POST['token'] ?? null;
        // Has only 1 user
        if ("" === $key && 1 === $count) {
            // Set the `key` value to the only user, automatically!
            $key = \basename($it->key(), '.page');
        }
        // Check token…
        if (\Is::void($token) || !\check($token, 'user')) {
            $with_alert && \Alert::error('Invalid token.');
            ++$error;
        // Check user key…
        } else if (\Is::void($key)) {
            $with_alert && \Alert::error('Please fill out the %s field.', 'Key');
            ++$error;
        // Check user pass…
        } else if (\Is::void($pass)) {
            $with_alert && \Alert::error('Please fill out the %s field.', 'Pass');
            ++$error;
        }
        // No user(s) yet
        if (0 === $count) {
            if (0 === $error) {
                $with_alert && \Alert::info('Your %s is %s.', ['pass', '<em>' . $pass . '</em>']);
                $name = \To::kebab(\ltrim($key, '@')); // Force `foo-bar-baz` format
                $pass = \P . \password_hash($pass . '@' . $name, \PASSWORD_DEFAULT);
                if (!\is_dir($folder .= \D . $name)) {
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
                \cookie('user.name', $name, '+7 days');
                \cookie('user.token', $token, '+7 days');
                // Show success message
                $with_alert && \Alert::success('Logged in.');
                // Trigger the hook
                \Hook::fire('on.user.start', [$file], new \User($file));
                // Redirect to the user page (by default)
                \kick($kick ?? ('/' . $route_x . $url->query([
                    'kick' => false
                ]) . $url->hash));
            }
            // Store form `key` to session
            $_SESSION['form']['key'] = $key;
            \kick('/' . $route_x . $url->query . $url->hash);
        }
        $file = ($folder .= \D . ($name = \ltrim($key, '@'))) . '.page';
        $try_max = ($state->x->user->guard->try ?? 5) + 1;
        $try_now = ((int) (\content($try_file = $folder . \D . '.try' . \D . \md5(\ip())) ?? 0)) + 1;
        if (0 === $error) {
            // Check if user already exists…
            if (\is_file($file)) {
                // Reset pass by deleting `pass.data` manually, then log-in with the new pass!
                if (!\is_file($f = $folder . \D . 'pass.data')) {
                    \file_put_contents($f, \P . \password_hash($pass . '@' . $name, \PASSWORD_DEFAULT));
                    \chmod($f, 0600);
                    $with_alert && \Alert::info('Your %s is %s.', ['pass', '<em>' . $pass . '</em>']);
                }
                // Validate pass hash
                if (0 === \strpos($h = \file_get_contents($f), \P)) {
                    $valid = \password_verify($pass . '@' . $name, \substr($h, 1));
                // Validate pass text
                } else {
                    $valid = $pass === $h;
                }
                // Is valid, then…
                if ($valid) {
                    // Use the stored token value from another device if exists
                    // e.g. the user has not decided to log-out on that device yet
                    if (\is_file($f = $folder . \D . 'token.data')) {
                        $token = \file_get_contents($f);
                    // Create the token file
                    } else {
                        \file_put_contents($f, $token);
                        \chmod($f, 0600);
                    }
                    \cookie('user.name', $name, '+7 days');
                    \cookie('user.token', $token, '+7 days');
                    // Remove “try again” message
                    $with_alert && \Alert::let();
                    // Show success message
                    $with_alert && \Alert::success('Logged in.');
                    // Trigger the hook
                    \Hook::fire('on.user.enter', [$file], new \User($file));
                    // Remove the log-in attempt log
                    \delete($try_file);
                    // Redirect to the home page (by default)
                    \kick($kick ?? '/' . $url->query([
                        'kick' => false
                    ]) . $url->hash);
                }
            }
            $with_alert && \Alert::error('Invalid key or pass.');
            ++$error;
        }
        if ($error > 0) {
            // Store form `key` to session
            $_SESSION['form']['key'] = $key;
            // Check for log-in attempt quota
            if ($try_now > $try_max - 1) {
                $with_alert && \Alert::let(); // Clear all previous alert(s)
                if (\defined("\\TEST") && \TEST) {
                    $path = \strtr(\dirname($try_file, 3), [\PATH . \D => '.' . \D]) . \D . $name[0] . \str_repeat('&#x2022;', \strlen($name) - 1) . \D . '.try' . \D . \basename($try_file);
                    if (\function_exists("\\abort")) {
                        \abort(\i('Please delete the %s file to enter.', '<code>' . $path . '</code>'));
                    }
                    \kick('/');
                }
                $with_alert && \Alert::error('Too many failed attempts.');
                \kick('/' . $route_x . $url->query . $url->hash);
            }
            if (\is_file($file)) {
                // Show remaining log-in attempt quota
                $with_alert && \Alert::info('Try again for %d more time' . (1 === ($v = $try_max - $try_now) ? "" : 's') . '.', $v);
                // Record log-in attempt
                \content($try_file, (string) $try_now, 0600);
                // Trigger the hook
                \Hook::fire('on.user.try', [$file], new \User($file));
            }
        }
        \kick('/' . $route_x . $url->query . $url->hash);
    }
}
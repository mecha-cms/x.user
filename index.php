<?php

namespace {
    function user(...$lot) {
        return \User::from(...$lot);
    }
    function users(...$lot) {
        return \Users::from(...$lot);
    }
    $folder = \LOT . \D . 'user';
    $key = \cookie('user.key');
    $a = \cookie('user.token');
    $b = \content($folder . \D . $key . \D . 'token.data');
    $user = $a && $b && $a === $b ? '@' . $key : false;
    \Is::_('user', function ($key = null) use ($folder, $user) {
        if (\is_string($key)) {
            $key = \ltrim($key, '@');
            return $user && '@' . $key === $user ? $user : false;
        }
        if (\is_int($key) && false !== $user) {
            $user = \ltrim($user, '@');
            $user = new \User($folder . \D . $user . '.page');
            return $user->_exist && $key === $user->status;
        }
        return false !== $user ? $user : false;
    });
    \State::set('has.grant', $user = \Is::user());
    $GLOBALS['user'] = $user = \User::from($user ? $folder . \D . $key . '.page' : null);
    if (\class_exists("\\Layout")) {
        !\Layout::path('form/user') && \Layout::set('form/user', __DIR__ . \D . 'engine' . \D . 'y' . \D . 'form' . \D . 'user.php');
        !\Layout::path('user') && \Layout::set('user', __DIR__ . \D . 'engine' . \D . 'y' . \D . 'user.php');
    }
}

namespace x\user {
    function route($content, $path, $query, $hash) {
        if (null !== $content) {
            return $content;
        }
        \extract($GLOBALS, \EXTR_SKIP);
        $name = \From::query($query)['name'] ?? "";
        $folder = \LOT . \D. 'user' . \D . $name;
        $route = \trim($state->x->user->route ?? 'user', '/');
        $route_secret = \trim($state->x->user->guard->route ?? $route, '/');
        $exit = $_GET['exit'] ?? null;
        $kick = $_GET['kick'] ?? null;
        $token = $user->token;
        // Force log out with `http://127.0.0.1/user/name?exit=b4d455`
        if ('GET' === $_SERVER['REQUEST_METHOD'] && $exit && 0 === \strpos(\trim($path, '/') . '/', $route_secret . '/')) {
            if ($token && $exit === $token) {
                \is_file($f = $folder . \D . 'token.data') && \unlink($f);
                \is_file($f = $folder . \D . 'try.data') && \unlink($f);
                \cookie('user.key', "", -1);
                \cookie('user.pass', "", -1);
                \cookie('user.token', "", -1);
                \class_exists("\\Alert") && \Alert::success('Logged out.');
                // Trigger the hook!
                \Hook::fire('on.user.exit', [$user->path], $user);
            } else {
                \class_exists("\\Alert") && \Alert::error('Invalid token.');
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
            return ['page', [], 404];
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
        return ['page', [], 200];
    }
    $path = \trim($url->path ?? "", '/');
    $route = \trim($state->x->user->route ?? 'user', '/');
    $route_secret = \trim($state->x->user->guard->route ?? $route, '/');
    if (0 === \strpos($path, $route_secret . '/') || 0 === \strpos($path, $route . '/')) {
        \Hook::set('route', function ($content, $path, $query, $hash) {
            $chops = \explode('/', $path);
            $query = \To::query(\array_replace(\From::query($query), ['name' => \array_pop($chops)]));
            return \Hook::fire('route.user', [$content, $path, $query, $hash]);
        }, 90);
        \Hook::set('route.user', __NAMESPACE__ . "\\route", 100);
    }
}

namespace x\user\hook {
    function author($author) {
        if ($author && \is_string($author) && 0 === \strpos($author, '@')) {
            return new \User(\LOT . \D . 'user' . \D . \substr($author, 1) . '.page');
        }
        return $author;
    }
    \Hook::set('page.author', __NAMESPACE__ . "\\author", 2);
}

namespace x\user\route {
    function enter($content, $path) {
        \extract($GLOBALS, \EXTR_SKIP);
        $path = \trim($path ?? "", '/');
        $route = \trim($state->x->user->route ?? 'user', '/');
        $route_secret = \trim($state->x->user->guard->route ?? $route, '/');
        if ($path !== $route_secret) {
            return;
        }
        $GLOBALS['t'][] = \i(\Is::user() ? 'Exit' : 'Enter');
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $key = $_POST['user']['key'] ?? "";
            $kick = $_POST['user']['kick'] ?? null;
            $pass = $_POST['user']['pass'] ?? "";
            $token = $_POST['user']['token'] ?? null;
            // Has only 1 user!
            if (1 === \q(\g(\LOT . \D . 'user', 'page'))) {
                // Set the `key` value to that user automatically
                $key = \basename(\g(\LOT . \D . 'user', 'page')->key(), '.page');
            }
            // Remove the `@` prefix!
            if (0 === \strpos($key, '@')) {
                $key = \substr($key, 1);
            }
            $folder = \LOT . \D . 'user' . \D . $key;
            $file = $folder . '.page';
            $try = $folder . \D . 'try.data';
            $try_data = \json_decode(\is_file($try) ? \file_get_contents($try) : '[]', true);
            $try_limit = ($state->x->user->guard->try ?? 5) + 1;
            $try_data[$try_user = \ip()] = ($try_data[$try_user] ?? 0) + 1;
            $error = 0;
            // Check token…
            if (\Is::void($token) || !\check($token, 'user')) {
                \class_exists("\\Alert") && \Alert::error('Invalid token.');
                ++$error;
            // Check user key…
            } else if (\Is::void($key)) {
                \class_exists("\\Alert") && \Alert::error('Please fill out the %s field.', 'User');
                ++$error;
            // Check user pass…
            } else if (\Is::void($pass)) {
                \class_exists("\\Alert") && \Alert::error('Please fill out the %s field.', 'Pass');
                ++$error;
            // No error(s), go to the next step(s)…
            } else {
                // Check if user already registered…
                if (\is_file($file)) {
                    // Reset password by deleting `pass.data` manually, then log in!
                    if (!\is_file($f = \dirname($file) . \D . \pathinfo($file, \PATHINFO_FILENAME) . \D . 'pass.data')) {
                        \file_put_contents($f, \P . \password_hash($pass . '@' . $key, \PASSWORD_DEFAULT));
                        \chmod($f, 0600);
                        \class_exists("\\Alert") && \Alert::info('Your %s is %s.', ['pass', '<em>' . $pass . '</em>']);
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
                        \cookie('user.key', $key, '+7 days');
                        \cookie('user.token', $token, '+7 days');
                        // Remove try again message
                        \class_exists("\\Alert") && \Alert::let();
                        // Show success message!
                        \class_exists("\\Alert") && \Alert::success('Logged in.');
                        // Trigger the hook!
                        \Hook::fire('on.user.enter', [$file], new \User($file));
                        // Remove log-in attempt log
                        \is_file($try) && \unlink($try);
                        // Redirect to the home page by default!
                        \kick($kick ?? '/' . $url->query([
                            'kick' => false
                        ]) . $url->hash);
                    }
                }
                \class_exists("\\Alert") && \Alert::error('Invalid user or pass.');
                ++$error;
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
                    $i = $try_limit - $try_data[$try_user];
                    \class_exists("\\Alert") && \Alert::info('Try again for %d more time' . (1 === $i ? "" : 's') . '.', $i);
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
        \class_exists("\\Asset") && \Asset::set(__DIR__ . \D . 'index' . $z . 'css', 20.1);
        return ['user', [], 200];
    }
    function start($content, $path) {
        if (null !== $content) {
            return $content;
        }
        \extract($GLOBALS, EXTR_SKIP);
        $route = \trim($state->x->user->route ?? "", '/');
        $route_secret = \trim($state->x->user->guard->route ?? $route, '/');
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $key = $_POST['user']['key'] ?? null;
            $kick = $_POST['user']['kick'] ?? null;
            $pass = $_POST['user']['pass'] ?? null;
            $token = $_POST['user']['token'] ?? null;
            // Remove the `@` prefix!
            if (0 === \strpos($key, '@')) {
                $key = \substr($key, 1);
            }
            $key = \To::kebab($key); // Force `foo-bar-baz` format
            $error = 0;
            // Check token…
            if (\Is::void($token) || !\check($token, 'user')) {
                \class_exists("\\Alert") && \Alert::error('invalid token.');
                ++$error;
            // Check user key…
            } else if (\Is::void($key)) {
                \class_exists("\\Alert") && \Alert::error('Please fill out the %s field.', 'User');
                ++$error;
            // Check user pass…
            } else if (\Is::void($pass)) {
                \class_exists("\\Alert") && \Alert::error('Please fill out the %s field.', 'Pass');
                ++$error;
            // No error(s), go to the next step(s)…
            } else {
                \class_exists("\\Alert") && \Alert::info('Your %s is %s.', ['pass', '<em>' . $pass . '</em>']);
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
                \file_put_contents($file = $folder . '.page', \To::page(['status' => 1]));
                \chmod($file, 0600);
                \cookie('user.key', $key, '+7 days');
                \cookie('user.token', $token, '+7 days');
                // Show success message!
                \class_exists("\\Alert") && \Alert::success('Logged in.');
                // Trigger the hook!
                \Hook::fire('on.user.start', [$file], new \User($file));
                // Redirect to the user page by default!
                \kick($kick ?? ('/' . $route_secret . $url->query([
                    'kick' => false
                ]) . $url->hash));
            }
            if ($error > 0) {
                // Store form data to session but `pass` and `token`
                $data = (array) ($_POST['user'] ?? []);
                unset($data['pass'], $data['token']);
                $_SESSION['form']['user'] = $data;
            }
            \kick('/' . $route_secret . $url->query . $url->hash);
        }
        $GLOBALS['t'][] = \i('User');
        $z = \defined("\\TEST") && \TEST ? '.' : '.min.';
        \class_exists("\\Asset") && \Asset::set(__DIR__ . \D . 'index' . $z . 'css', 20.1);
        return ['user', [], 200];
    }
    $has_users = \q(\g(\LOT . \D . 'user', 'page')) > 0;
    \Hook::set('route', __NAMESPACE__ . "\\" . ($has_users ? 'enter' : 'start'), 90);
}
<?php namespace x\user\route;

function set($path) {
    \extract($GLOBALS, EXTR_SKIP);
    $route = \trim($state->x->user->route ?? "", '/');
    $route_secret = \trim($state->x->user->guard->route ?? $route, '/');
    $x_alert = isset($state->x->alert);
    if ('POST' === $_SERVER['REQUEST_METHOD']) {
        $key = $_POST['user']['key'] ?? null;
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
            $x_alert && \Alert::info('Your %s is %s.', ['pass', '<em>' . $pass . '</em>']);
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
            \cookie('user.key', $key, '7 days');
            \cookie('user.token', $token, '7 days');
            // Show success message!
            $x_alert && \Alert::success('Logged in.');
            // Trigger the hook!
            \Hook::fire('on.user.enter', [new \File($file), null], new \User($file));
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
    $GLOBALS['t'][] = i('User');
    $z = \defined("\\TEST") && \TEST ? '.' : '.min.';
    \status(200);
    \Asset::set(__DIR__ . \D . '..' . \D . '..' . \D . '..' . \D . 'lot' . \D . 'asset' . \D . 'index' . $z . 'css', 20.1);
    \Hook::fire('layout', ['user']);
}

\Hook::set('route', __NAMESPACE__ . "\\set", 0);
<?php

Route::set($url->path, function() {
    extract($GLOBALS, EXTR_SKIP);
    $state = State::get('x.user', true);
    $path = trim($state['path'] ?? "", '/');
    $secret = trim($state['guard']['path'] ?? $path, '/');
    if (Request::is('Post')) {
        extract(array_replace([
            'key' => null,
            'pass' => null,
            'token' => null
        ], (array) Post::get('user')), EXTR_SKIP);
        // Remove the `@` prefix!
        if (0 === strpos($key, '@')) {
            $key = substr($key, 1);
        }
        $key = To::kebab($key); // Force user name format
        $error = 0;
        // Check token…
        if (Is::void($token) || !Guard::check($token, 'user')) {
            Alert::error('Invalid token.');
            ++$error;
        // Check user key…
        } else if (Is::void($key)) {
            Alert::error('Please fill out the %s field.', 'User');
            ++$error;
        // Check user pass…
        } else if (Is::void($pass)) {
            Alert::error('Please fill out the %s field.', 'Pass');
            ++$error;
        // No error(s), go to the next step(s)…
        } else {
            Alert::info('Your %s is %s.', ['pass', '<em>' . $pass . '</em>']);
            $pass = P . password_hash($pass . '@' . $key, PASSWORD_DEFAULT);
            if (!is_dir($d = LOT . DS . 'user' . DS . $key)) {
                mkdir($d, 0775, true); // Force folder creation
            }
            ($user = new User($u = $d . '.page'))->set([
                'status' => 1
            ])->save(0600);
            (new File($d . DS . 'pass.data'))->set($pass)->save(0600);
            (new File($d . DS . 'time.data'))->set(date('Y-m-d H:i:s'))->save(0600);
            (new File($d . DS . 'token.data'))->set($token)->save(0600);
            Cookie::set('user.key', $key, '7 days');
            Cookie::set('user.token', $token, '7 days');
            // Show success message!
            Alert::success('Logged in.');
            // Trigger the hook!
            Hook::fire('on.user.enter', [new File($u), null], $user);
            // Redirect to the user page by default!
            Guard::kick($kick ?? ($url . '/' . $secret . $url->query('&', [
                'kick' => false
            ]) . $url->hash));
        }
        if ($error > 0) {
            // Store form data to session but `pass`
            $lot = (array) Post::get('user');
            unset($lot['pass'], $lot['token']);
            Session::set('form', ['user' => $lot]);
        }
        Guard::kick($url . '/' . $secret . $url->query . $url->hash);
    }
    $GLOBALS['t'][] = i('User');
    $z = defined('DEBUG') && DEBUG ? '.' : '.min.';
    Asset::set(__DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . 'lot' . DS . 'asset' . DS . 'css' . DS . 'index' . $z . 'css', 20.1);
    $this->layout(__DIR__ . DS . '..' . DS . 'layout' . DS . 'page.php');
});
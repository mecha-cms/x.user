<?php

Language::set([
    'alert-error-user-or-pass' => 'Invalid user or pass value.',
    'alert-error-token' => 'Invalid token.',
    'alert-error-void-field' => 'Please fill out the %s field.',
    'alert-info-user-enter-try' => 'Try again for %d more times.',
    'alert-success-user-enter' => 'Logged in.',
    'alert-success-user-exit' => 'Logged out.',
    'anon' => 'Anonymous',
    'guest' => ['Guest', 'Guest', 'Guests'],
    'pass' => ['Pass', 'Pass', 'Passes'],
    'profile' => ['Profile', 'Profile', 'Profiles'],
    'user' => ['User', 'User', 'Users'],
    'user-count' => function(int $i) {
        return $i . ' User' . ($i === 1 ? "" : 's');
    }
]);
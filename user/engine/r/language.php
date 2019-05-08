<?php

Language::set([
    'anon' => 'Anonymous',
    'guest' => ['Guest', 'Guest', 'Guests'],
    'message-error-user-or-pass' => 'Invalid user or pass value.',
    'message-error-void-field' => 'Please fill out the %s field.',
    'message-error-token' => 'Invalid token.',
    'message-info-user-enter-try' => 'Try again: %d',
    'message-success-user-enter' => 'Logged in.',
    'message-success-user-exit' => 'Logged out.',
    'pass' => ['Pass', 'Pass', 'Passes'],
    'profile' => ['Profile', 'Profile', 'Profiles'],
    'user' => ['User', 'User', 'Users'],
    'user-count' => function(int $i) {
        return $i . ' User' . ($i === 1 ? "" : 's');
    }
]);
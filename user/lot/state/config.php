<?php

return [
    '/' => 'user',
    '//' => null, // Secret log-in path
    'user' => [
        'status' => 0 // Default to pending
    ],
    'try' => 5 // Maximum log-in attempt
];
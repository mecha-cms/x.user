<?php

return [
    'path' => '/user',
    'avatar' => 'https://gravatar.com/avatar/%1$s?s=%2$d&d=mp',
    'guard' => [
        'path' => null, // Secret log-in path
        'try' => 5 // Maximum log-in attempt
    ]
];
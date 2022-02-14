<?php

return [
    'avatar' => 'https://gravatar.com/avatar/%1$s?s=%2$d&d=mp',
    'guard' => [
        'choke' => 50, // Maximum request(s) allowed in second
        'route' => null, // Secret log-in path
        'try' => 5 // Maximum log-in attempt
    ],
    'route' => '/user'
];
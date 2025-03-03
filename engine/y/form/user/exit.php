<?php

$route = trim($state->x->user->route ?? 'user', '/');
$route_x = trim($state->x->user->guard->route ?? $route, '/');

echo new HTML(Hook::fire('y.form.user', [[
    0 => 'form',
    1 => [
        'alert' => self::alert(),
        'tasks' => [
            0 => 'p',
            1 => [
                0 => [
                    0 => 'label',
                    1 => "" // Intentionally left blank as a selector to center the task button(s)
                ],
                1 => [
                    0 => 'br',
                    1 => false
                ],
                2 => [
                    0 => 'span',
                    1 => [
                        'user' => [
                            0 => 'a',
                            1 => i('Profile'),
                            2 => [
                                'href' => $url . '/' . $route . '/' . $user->name,
                                'role' => 'button'
                            ]
                        ],
                        'exit' => [
                            0 => 'a',
                            1 => i('Exit'),
                            2 => [
                                'href' => $url . '/' . $route_x . '/' . $user->name . $url->query([
                                    'exit' => $user->token
                                ]),
                                'role' => 'button',
                                'title' => $user->user
                            ]
                        ]
                    ],
                    2 => [
                        'role' => 'group'
                    ]
                ]
            ]
        ],
        'token' => [
            0 => 'input',
            1 => false,
            2 => [
                'name' => 'token',
                'type' => 'hidden',
                'value' => token('user')
            ]
        ],
        'kick' => [
            0 => 'input',
            1 => false,
            2 => [
                'name' => 'kick',
                'type' => 'hidden',
                'value' => $kick ?? null
            ]
        ]
    ],
    2 => [
        'action' => $url . '/' . $route_x . $url->query,
        'method' => 'post',
        'name' => 'user'
    ]
]], $page), true);
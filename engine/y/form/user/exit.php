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
                            0 => 'button',
                            1 => i('Exit'),
                            2 => [
                                'name' => 'exit',
                                'title' => $user->user,
                                'type' => 'submit',
                                'value' => $user->token
                            ]
                        ]
                    ],
                    2 => [
                        'role' => 'group'
                    ]
                ]
            ]
        ]
    ],
    2 => [
        'action' => $url . '/' . $route_x . '/' . $user->name,
        'method' => 'get',
        'name' => 'user'
    ]
]], $page), true);
<?php

// Check if we have more than one user to hide the user key field if we have only one user!
$has_users = State::get('[x].count.user') > 1;

// First form submit, but fail?
$first = empty($_SESSION['form']['key']);

echo new HTML(Hook::fire('y.form.user', [[
    0 => 'form',
    1 => [
        'alert' => self::alert(),
        'user' => $has_users ? [
            0 => 'p',
            1 => [
                0 => [
                    0 => 'label',
                    1 => i('Key'),
                    2 => [
                        'for' =>  $id = 'f:' . substr(uniqid(), 6)
                    ]
                ],
                1 => [
                    0 => 'br',
                    1 => false
                ],
                2 => [
                    0 => 'span',
                    1 => [
                        0 => [
                            0 => 'input',
                            1 => false,
                            2 => [
                                'autofocus' => $first || !isset($state->x->form),
                                'id' => $id,
                                'name' => 'key',
                                'pattern' => "[a-z\\d]+(-[a-z\\d]+)*",
                                'type' => 'text'
                            ]
                        ]
                    ]
                ]
            ]
        ] : null,
        'pass' => [
            0 => 'p',
            1 => [
                0 => [
                    0 => 'label',
                    1 => i('Pass'),
                    2 => [
                        'for' =>  $id = 'f:' . substr(uniqid(), 6)
                    ]
                ],
                1 => [
                    0 => 'br',
                    1 => false
                ],
                2 => [
                    0 => 'span',
                    1 => [
                        0 => [
                            0 => 'input',
                            1 => false,
                            2 => [
                                'autofocus' => $has_users && $first ? null : true,
                                'id' => $id,
                                'name' => 'pass',
                                'type' => 'password'
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'tasks' => [
            0 => 'p',
            1 => [
                0 => [
                    0 => 'label',
                    1 => i('Tasks')
                ],
                1 => [
                    0 => 'br',
                    1 => false
                ],
                2 => [
                    0 => 'span',
                    1 => [
                        'enter' => [
                            0 => 'button',
                            1 => i('Enter'),
                            2 => [
                                'name' => 'task',
                                'type' => 'submit',
                                'value' => 'enter'
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
        'action' => $url . '/' . trim($state->x->user->guard->route ?? $state->x->user->route ?? 'user', '/') . $url->query,
        'method' => 'post',
        'name' => 'user'
    ]
]], $page), true);
<form class="form-user form-user:exit" target="_top">
  <?php

  $tasks = [
      'alert' => $alert,
      'tasks' => [
          0 => 'p',
          1 => x\user\hook('user-form-tasks', [[
              'profile' => [
                  0 => 'a',
                  1 => i('Profile'),
                  2 => [
                      'class' => 'button',
                      'href' => $url . ($state->x->user->path ?? '/user') . '/' . $user->name,
                      'target' => '_top'
                  ]
              ],
              'exit' => [
                  0 => 'a',
                  1 => i('Exit'),
                  2 => [
                      'class' => 'button',
                      'href' => $url . ($state->x->user->path ?? '/user') . '/' . $user->name . $url->query('&', [
                          'exit' => $user->token
                      ]),
                      'title' => $user->user
                  ]
              ]
          ]], ' '),
          2 => []
      ]
  ];

  $tasks['token'] = '<input name="user[token]" type="hidden" value="' . Guard::token('user') . '">';

  if ($kick = Get::get('kick')) {
      $tasks['kick'] = '<input name="user[kick]" type="hidden" value="' . htmlspecialchars($kick) . '">';
  }

  ?>
  <?= x\user\hook('user-form', [$tasks]); ?>
</form>

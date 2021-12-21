<form class="form-user form-user:exit" target="_top">
  <?php

  $tasks = [
      'alert' => self::alert(),
      'tasks' => [
          0 => 'p',
          1 => x\user\hook('user-form-tasks', [[
              'profile' => [
                  0 => 'a',
                  1 => i('Profile'),
                  2 => [
                      'class' => 'button',
                      'href' => $url . '/' . trim($state->x->user->route ?? 'user', '/') . '/' . $user->name,
                      'target' => '_top'
                  ]
              ],
              'exit' => [
                  0 => 'a',
                  1 => i('Exit'),
                  2 => [
                      'class' => 'button',
                      'href' => $url . '/' . trim($state->x->user->route ?? 'user', '/') . '/' . $user->name . $url->query([
                          'exit' => $user->token
                      ]),
                      'target' => '_top',
                      'title' => $user->user
                  ]
              ]
          ]], ' '),
          2 => []
      ]
  ];

  $tasks['token'] = '<input name="user[token]" type="hidden" value="' . token('user') . '">';

  if ($kick = $_GET['kick'] ?? null) {
      $tasks['kick'] = '<input name="user[kick]" type="hidden" value="' . htmlspecialchars($kick) . '">';
  }

  ?>
  <?= x\user\hook('user-form', [$tasks]); ?>
</form>
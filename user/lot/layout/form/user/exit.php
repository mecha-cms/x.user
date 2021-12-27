<form action="<?= $url . '/' . trim($state->x->user->guard->route ?? $state->x->user->route ?? 'user', '/') . htmlspecialchars($url->query); ?>" name="user" method="post" target="_top">
  <?php

  $tasks = [
      'alert' => self::alert(),
      'tasks' => [
          0 => 'p',
          1 => (new HTML([
              0 => 'label',
              1 => "" // Intentionally left blank as a selector to center the action button(s)
          ])) . '<br><span role="group">' . x\user\hook('user-form-tasks', [[
              'profile' => [
                  0 => 'a',
                  1 => i('Profile'),
                  2 => [
                      'href' => $url . '/' . trim($state->x->user->route ?? 'user', '/') . '/' . $user->name,
                      'role' => 'button',
                      'target' => '_top'
                  ]
              ],
              'exit' => [
                  0 => 'a',
                  1 => i('Exit'),
                  2 => [
                      'href' => $url . '/' . trim($state->x->user->route ?? 'user', '/') . '/' . $user->name . $url->query([
                          'exit' => $user->token
                      ]),
                      'role' => 'button',
                      'target' => '_top',
                      'title' => $user->user
                  ]
              ]
          ]], ' ') . '</span>'
      ]
  ];

  $tasks['token'] = '<input name="user[token]" type="hidden" value="' . token('user') . '">';

  if ($kick = $_GET['kick'] ?? null) {
      $tasks['kick'] = '<input name="user[kick]" type="hidden" value="' . htmlspecialchars($kick) . '">';
  }

  ?>
  <?= x\user\hook('user-form', [$tasks]); ?>
</form>
<form action="<?= $url . '/' . trim($state->x->user->guard->route ?? $state->x->user->route ?? 'user', '/') . From::HTML($url->query ?? ""); ?>" method="post" name="user" target="_top">
  <?php

  $tasks = [
      'alert' => self::alert(),
      'user' => [
          0 => 'p',
          1 => (new HTML([
              0 => 'label',
              1 => i('Key'),
              2 => [
                  'for' => $id = 'f:' . substr(uniqid(), 6)
              ]
          ])) . '<br><span>' . (new HTML([
              0 => 'input',
              1 => false,
              2 => [
                  'autofocus' => true,
                  'id' => $id,
                  'name' => 'user[key]',
                  'placeholder' => i('New user %s', ['key']),
                  'type' => 'text'
              ]
          ])) . '</span>'
      ],
      'pass' => [
          0 => 'p',
          1 => (new HTML([
              0 => 'label',
              1 => i('Pass'),
              2 => [
                  'for' => $id = 'f:' . substr(uniqid(), 6)
              ]
          ])) . '<br><span>' . (new HTML([
              0 => 'input',
              1 => false,
              2 => [
                  'id' => $id,
                  'name' => 'user[pass]',
                  'placeholder' => i('New user %s', ['pass']),
                  'type' => 'password'
              ]
          ])) . '</span>'
      ]
  ];

  $tasks['tasks'] = [
      0 => 'p',
      1 => (new HTML([
          0 => 'label',
          1 => i('Actions'),
          2 => [
              'for' => $id = 'f:' . substr(uniqid(), 6)
          ]
      ])) . '<br><span role="group">' . x\user\hook('user-form-tasks', [[
          'enter' => [
              0 => 'button',
              1 => i('Create'),
              2 => [
                  'name' => 'user[task]',
                  'type' => 'submit',
                  'value' => 'start'
              ]
          ]
      ]], ' ') . '</span>'
  ];

  $tasks['token'] = '<input name="user[token]" type="hidden" value="' . token('user') . '">';

  if (!empty($kick)) {
      $tasks['kick'] = '<input name="user[kick]" type="hidden" value="' . htmlspecialchars($kick) . '">';
  }

  ?>
  <?= x\user\hook('user-form', [$tasks]); ?>
</form>
<form class="form-user form-user:exit" target="_top">
  <?= $alert; ?>
  <p>
    <a class="button" href="<?= $url . ($state->x->user->path ?? '/user') . '/' . $user->name; ?>">
      <?= i('Profile'); ?>
    </a> <a class="button" href="<?= $url . ($state->x->user->path ?? '/user') . '/' . $user->name . $url->query('&amp;', ['exit' => $user->token]); ?>" target="_top" title="<?= $user->user; ?>">
      <?= i('Exit'); ?>
    </a>
  </p>
</form>

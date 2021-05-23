<form action="<?= $url . ($state->x->user->guard->path ?? $state->x->user->path ?? '/user') . $url->query('&amp;'); ?>" class="form-user form-user:enter" method="post" name="user" target="_top">
  <?= $alert; ?>
  <?php if ($some = $users->count() > 1): ?>
    <p title="<?= i('User'); ?>">
      <input autofocus class="input width" name="user" placeholder="<?= i('User'); ?>" type="text">
    </p>
  <?php endif; ?>
  <p title="<?= i('Pass'); ?>">
    <input<?= $some ? "" : ' autofocus'; ?> class="input width" name="pass" placeholder="<?= i('Pass'); ?>" type="password">
  </p>
  <p>
    <button class="button" name="v" type="submit" value="1">
      <?= i('Enter'); ?>
    </button>
  </p>
  <input name="token" type="hidden" value="<?= Guard::token('user'); ?>">
  <?php if ($kick = Get::get('kick')): ?>
    <input name="kick" type="hidden" value="<?= strtr((string) $kick, ['&' => '&amp;']); ?>">
  <?php endif; ?>
</form>

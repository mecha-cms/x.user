<?php

$_state = State::get('x.user', true);
$_path = $_state['guard']['path'] ?? $_state['path'];

?>
<form action="<?= $url . $_path . $url->query('&amp;'); ?>" class="form-user form-user:<?= ($_enter = Is::user()) ? 'exit' : 'enter'; ?>" method="post" name="user" target="_top">
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
    <button class="button" name="v" type="submit" value="1"><?= i('Enter'); ?></button>
  </p>
  <input name="token" type="hidden" value="<?= Guard::token('user'); ?>">
  <?php if ("" !== ($_kick = strtr(Get::get('kick') ?? "", ['&' => '&amp;']))): ?>
  <input name="kick" type="hidden" value="<?= $_kick; ?>">
  <?php endif; ?>
</form>

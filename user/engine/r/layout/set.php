<?php

$_state = State::get('x.user', true);
$_path = $_state['guard']['path'] ?? $_state['path'];

?>
<form action="<?= $url . $_path . $url->query('&amp;'); ?>" class="form-user form-user:set" method="post" name="user" target="_top">
  <?= $alert; ?>
  <p title="<?= i('User'); ?>">
    <input autofocus class="input width" name="user" placeholder="<?= i('New %s', 'user'); ?>" type="text">
  </p>
  <p title="<?= i('Pass'); ?>">
    <input class="input width" name="pass" placeholder="<?= i('New %s', 'pass'); ?>" type="password">
  </p>
  <p>
    <button class="button" name="v" type="submit" value="1"><?= i('Create'); ?></button>
  </p>
  <input name="token" type="hidden" value="<?= Guard::token('user'); ?>">
  <?php if ("" !== ($_kick = strtr(Get::get('kick') ?? "", ['&' => '&amp;']))): ?>
  <input name="kick" type="hidden" value="<?= $_kick; ?>">
  <?php endif; ?>
</form>

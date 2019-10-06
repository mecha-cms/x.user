<?php

$_state = State::get('x.user', true);
$_path = $_state['guard']['path'] ?? $_state['path'];

?>
<form action="<?= $url . $_path . $url->query('&amp;'); ?>" class="form-user form-user:<?= ($_enter = Is::user()) ? 'exit' : 'enter'; ?>" method="post" name="user">
  <?= $alert; ?>
  <?php if ($users->count() > 1): ?>
  <p title="<?= $language->user; ?>">
    <input autofocus class="input width" name="user" placeholder="<?= $language->user; ?>" type="text">
  </p>
  <?php endif; ?>
  <p title="<?= $language->pass; ?>">
    <input class="input width" name="pass" placeholder="<?= $language->pass; ?>" type="password">
  </p>
  <p>
    <button class="button" name="v" type="submit" value="1"><?= $language->doEnter; ?></button>
  </p>
  <input name="token" type="hidden" value="<?= Guard::token('user'); ?>">
  <?php if ("" !== ($_kick = strtr(Get::get('kick') ?? "", ['&' => '&amp;']))): ?>
  <input name="kick" type="hidden" value="<?= $_kick; ?>">
  <?php endif; ?>
</form>
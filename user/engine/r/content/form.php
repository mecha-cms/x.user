<?php

$_state = State::get('x.user', true);
$_path = $_state['_path'] ?? $_state['path'];

?>
<form action="<?php echo $url . $_path . $url->query('&amp;'); ?>" class="form-user form-user:<?php echo ($_enter = Is::user()) ? 'exit' : 'enter'; ?>" method="post" name="user">
  <?php echo $alert; ?>
  <?php if ($users->count() > 1): ?>
  <p title="<?php echo $language->user; ?>">
    <input autofocus class="input width" name="user" placeholder="<?php echo $language->user; ?>" type="text">
  </p>
  <?php endif; ?>
  <p title="<?php echo $language->pass; ?>">
    <input class="input width" name="pass" placeholder="<?php echo $language->pass; ?>" type="password">
  </p>
  <p>
    <button class="button" name="v" type="submit" value="1"><?php echo $language->doEnter; ?></button>
  </p>
  <input name="token" type="hidden" value="<?php echo Guard::token('user'); ?>">
  <?php if ("" !== ($_kick = strtr(Get::get('kick') ?? "", ['&' => '&amp;']))): ?>
  <input name="kick" type="hidden" value="<?php echo $_kick; ?>">
  <?php endif; ?>
</form>
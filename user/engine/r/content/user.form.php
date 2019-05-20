<?php

$_state = extend('user');
$_path = $_state['_path'] ?? $_state['path'];

?>
<form action="<?php echo $url . '/' . $_path . $url->query('&amp;'); ?>" class="form-user form-user:<?php echo ($_user = Is::user()) ? 'exit' : 'enter'; ?>" method="post" name="user" style="display:block;max-width:15em;margin-right:auto;margin-left:auto;">
  <?php echo $message; ?>
  <?php if (!$_user): ?>
  <?php if (count($users) > 1): ?>
  <p title="<?php echo $language->user; ?>">
    <input autofocus class="input width" name="user[key]" placeholder="<?php echo $language->user; ?>" type="text">
  </p>
  <?php endif; ?>
  <p title="<?php echo $language->pass; ?>">
    <input class="input width" name="user[pass]" placeholder="<?php echo $language->pass; ?>" type="password">
  </p>
  <p>
    <button class="button" name="v" type="submit" value="1"><?php echo $language->doEnter; ?></button>
  </p>
  <?php else: ?>
  <p style="text-align:center;">
    <a class="button" href="<?php echo $url . '/' . $_state['path'] . '/' . substr($_user, 1); ?>"><?php echo $language->profile; ?></a> <button class="button" name="x" title="<?php echo $_user; ?>" type="submit" value="<?php echo $_user; ?>"><?php echo $language->doExit; ?></button>
  </p>
  <?php endif; ?>
  <input name="token" type="hidden" value="<?php echo token('user'); ?>">
  <input name="kick" type="hidden" value="<?php echo strtr(HTTP::get('kick'), ['&' => '&amp;']); ?>">
</form>
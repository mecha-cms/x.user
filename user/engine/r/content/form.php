<?php

$_state = state('user');
$_path = $_state['//'] ?? $_state['/'];

?>
<form action="<?php echo $url . '/' . $_path . $url->query('&amp;'); ?>" class="form-user form-user:<?php echo ($_enter = Is::user()) ? 'exit' : 'enter'; ?>" method="post" name="user">
  <?php echo $alert; ?>
  <?php if ($_enter): ?>
  <p style="
    text-align: center;
  ">
    <a class="button" href="<?php echo $url . '/' . $_state['/'] . '/' . substr($_enter, 1); ?>"><?php echo $language->profile; ?></a> <button class="button" name="x" title="<?php echo $_enter; ?>" type="submit" value="<?php echo $_enter; ?>"><?php echo $language->doExit; ?></button>
  </p>
  <?php else: ?>
  <?php if ($users->count() > 1): ?>
  <p title="<?php echo $language->user; ?>">
    <input autofocus class="input width" name="key" placeholder="<?php echo $language->user; ?>" type="text">
  </p>
  <?php endif; ?>
  <p title="<?php echo $language->pass; ?>">
    <input class="input width" name="pass" placeholder="<?php echo $language->pass; ?>" type="password">
  </p>
  <p>
    <button class="button" name="v" type="submit" value="1"><?php echo $language->doEnter; ?></button>
  </p>
  <?php endif; ?>
  <input name="token" type="hidden" value="<?php echo token('user'); ?>">
  <?php if ("" !== ($_kick = strtr((string) ($_GET['kick'] ?? ""), ['&' => '&amp;']))): ?>
  <input name="kick" type="hidden" value="<?php echo $_kick; ?>">
  <?php endif; ?>
</form>
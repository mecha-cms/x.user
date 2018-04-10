<?php $_url = $url . '/' . Extend::state('user', 'path'); ?>
<form class="form-user form-user:<?php echo ($_user = Is::user()) ? 'exit' : 'enter'; ?>" action="<?php echo $_url . $url->query('&amp;'); ?>" method="post" style="display:block;max-width:15em;margin-right:auto;margin-left:auto;">
  <?php echo $message; ?>
  <?php if (!$_user): ?>
  <?php if (count($users) > 1): ?>
  <p title="<?php echo $language->user; ?>"><?php echo Form::text('key', null, $language->user, ['class[]' => ['input', 'block']]); ?></p>
  <?php endif; ?>
  <p title="<?php echo $language->pass; ?>"><?php echo Form::password('pass', null, $language->pass, ['class[]' => ['input', 'block']]); ?></p>
  <p><?php echo Form::submit('v', 1, $language->enter, ['class[]' => ['button']]); ?></p>
  <?php else: ?>
  <p style="text-align:center;"><?php echo HTML::a($language->user, $_url . '/' . substr($_user, 1), false, ['class[]' => ['button']]) . ' ' . Form::submit('x', $_user, $language->exit, ['class[]' => ['button'], 'title' => $_user]); ?></p>
  <?php endif; ?>
  <?php echo Form::hidden('token', $token); ?>
  <?php echo Form::hidden('kick', HTTP::get('kick', $url->previous)); ?>
</form>
<script>
(document.forms[0].key || document.forms[0].pass).focus();
</script>
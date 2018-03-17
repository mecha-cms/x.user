<form class="form-user form-user:<?php echo ($is_user = Is::user()) ? 'exit' : 'enter'; ?>" action="<?php echo $url . '/' . Extend::state('user', 'path') . $url->query('&amp;'); ?>" method="post" style="display:block;max-width:15em;margin-right:auto;margin-left:auto;">
  <?php echo $message; ?>
  <?php if (!$is_user): ?>
  <p title="<?php echo $language->user; ?>"><?php echo Form::text('key', null, $language->user, ['class[]' => ['input', 'block']]); ?></p>
  <p title="<?php echo $language->pass; ?>"><?php echo Form::password('pass', null, $language->pass, ['class[]' => ['input', 'block']]); ?></p>
  <p><?php echo Form::submit('v', 1, $language->enter, ['class[]' => ['button']]); ?></p>
  <?php else: ?>
  <p style="text-align:center;"><?php echo Form::submit('x', $is_user, $language->exit, ['class[]' => ['button'], 'title' => $is_user]) . ' ' . HTML::a($language->home, '/', false, ['class[]' => ['button']]); ?></p>
  <?php endif; ?>
  <?php echo Form::hidden('token', $token); ?>
  <?php echo Form::hidden('kick', Request::get('kick', $url->previous)); ?>
</form>
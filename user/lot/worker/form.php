<form action="<?php echo $url . '/' . Extend::state('user', 'path') . $url->query('&amp;'); ?>" method="post" style="display:block;max-width:15em;margin-right:auto;margin-left:auto;">
  <?php echo $message; ?>
  <?php if (!Is::user()): ?>
  <p title="<?php echo $language->user; ?>"><?php echo Form::text('key', null, $language->user, ['class[]' => ['input', 'block']]); ?></p>
  <p title="<?php echo $language->pass; ?>"><?php echo Form::password('pass', null, $language->pass, ['class[]' => ['input', 'block']]); ?></p>
  <p><?php echo Form::submit('v', 1, $language->enter, ['class[]' => ['button']]); ?></p>
  <?php else: ?>
  <p style="text-align:center;"><?php echo Form::submit('x', 1, $language->exit . ' (' . Is::user() . ')', ['class[]' => ['button']]) . ' ' . HTML::a($language->home, '/', false, ['class[]' => ['button']]); ?></p>
  <?php echo Form::hidden('key', Is::user()); ?>
  <?php endif; ?>
  <?php echo Form::hidden('token', $token); ?>
</form>
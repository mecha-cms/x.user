<?php

$_state = Extend::state('user');
$_path = $_state['_path'] ?? $_state['path'];

?>
<form name="user" class="form-user form-user:<?php echo ($_user = Is::user()) ? 'exit' : 'enter'; ?>" action="<?php echo $url . '/' . $_path . $url->query('&amp;'); ?>" method="post" style="display:block;max-width:15em;margin-right:auto;margin-left:auto;">
  <?php echo $message; ?>
  <?php if (!$_user): ?>
  <?php if (count($users) > 1): ?>
  <p title="<?php echo $language->user; ?>"><?php echo Form::text('key', null, $language->user, ['class[]' => ['input', 'block']]); ?></p>
  <?php endif; ?>
  <p title="<?php echo $language->pass; ?>"><?php echo Form::pass('pass', null, $language->pass, ['class[]' => ['input', 'block']]); ?></p>
  <p><?php echo Form::submit('v', 1, $language->enter, ['class[]' => ['button']]); ?></p>
  <?php else: ?>
  <p style="text-align:center;"><?php echo HTML::a($language->profile, $url . '/' . $_state['path'] . '/' . substr($_user, 1), false, ['class[]' => ['button']]) . ' ' . Form::submit('x', $_user, $language->exit, ['class[]' => ['button'], 'title' => $_user]); ?></p>
  <?php endif; ?>
  <?php echo Form::hidden('token', Guardian::token('user')); ?>
  <?php echo Form::hidden('kick', HTTP::get('kick', $url->previous)); ?>
</form>
<script>
(function(doc) {
    var $ = doc.forms.user,
        key = $.key,
        pass = $.pass;
    if (key && key.value) {
        pass.focus();
    } else {
        (key || pass).focus();
    }
})(document);
</script>
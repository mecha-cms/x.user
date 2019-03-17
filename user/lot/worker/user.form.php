<?php

$_state = Extend::state('user');
$_path = $_state['_path'] ?? $_state['path'];

?>
<form name="user" class="form-user form-user:<?php echo ($_user = Is::user()) ? 'exit' : 'enter'; ?>" action="<?php echo $url . '/' . $_path . $url->query('&amp;'); ?>" method="post" style="display:block;max-width:15em;margin-right:auto;margin-left:auto;">
  <?php static::message(); ?>
  <?php if (!$_user): ?>
  <?php if (count($users) > 1): ?>
  <p title="<?php echo $language->user; ?>">
    <input class="input block" name="key" placeholder="<?php echo $language->user; ?>" type="text">
  </p>
  <?php endif; ?>
  <p title="<?php echo $language->pass; ?>">
    <input class="input block" name="pass" placeholder="<?php echo $language->pass; ?>" type="password">
  </p>
  <p>
    <button class="button" name="v" type="submit" value="1"><?php echo $language->enter; ?></button>
  </p>
  <?php else: ?>
  <p style="text-align:center;">
    <a class="button" href="<?php echo $url . '/' . $_state['path'] . '/' . substr($_user, 1); ?>"><?php echo $language->profile; ?></a> <button class="button" name="x" title="<?php echo $_user; ?>" type="submit" value="<?php echo $_user; ?>"><?php echo $language->exit; ?></button>
  </p>
  <?php endif; ?>
  <input name="token" type="hidden" value="<?php echo Guard::token('user'); ?>">
  <input name="kick" type="hidden" value="<?php echo strtr(HTTP::get('kick'), ['&' => '&amp;']); ?>">
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
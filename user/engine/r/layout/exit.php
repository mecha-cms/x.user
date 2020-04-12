<?php

$_state = State::get('x.user', true);

?>
<form class="form-user form-user:exit" target="_top">
  <?= $alert; ?>
  <p style="
    text-align: center;
  ">
    <a class="button" href="<?= $url . $_state['path'] . '/' . $user->name; ?>"><?= i('Profile'); ?></a> <a class="button" href="<?= $url . $_state['path'] . '/' . $user->name . $url->query('&amp;', ['exit' => $user->token]); ?>" target="_top" title="<?= $user->user; ?>"><?= i('Exit'); ?></a>
  </p>
</form>

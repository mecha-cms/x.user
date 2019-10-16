<?php

$_state = State::get('x.user', true);

?>
<?= $alert; ?>
<p>
  <a class="button" href="<?= $url . $_state['path'] . '/' . $user->name; ?>"><?= i('Profile'); ?></a> <a class="button" href="<?= $url . $_state['path'] . '/' . $user->name . $url->query('&amp;', ['exit' => $user->token]); ?>" title="<?= $user->user; ?>"><?= i('Exit'); ?></a>
</p>
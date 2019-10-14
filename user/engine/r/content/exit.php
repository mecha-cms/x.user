<?php

$_state = State::get('x.user', true);

?>
<?= $alert; ?>
<p style="
  text-align: center;
">
  <a class="button" href="<?= $url . $_state['path'] . '/' . $user->name; ?>"><?= $language->profile; ?></a> <a class="button" href="<?= $url . $_state['path'] . '/' . $user->name . $url->query('&amp;', ['exit' => $user->token]); ?>" title="<?= $user->user; ?>"><?= $language->doExit; ?></a>
</p>
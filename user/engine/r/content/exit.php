<?php

$_state = State::get('x.user', true);

?>
<p style="
  text-align: center;
">
  <a class="button" href="<?php echo $url . $_state['path'] . '/' . $user->name; ?>"><?php echo $language->profile; ?></a> <a class="button" href="<?php echo $url . $_state['path'] . '/' . $user->name . $url->query('&amp;', ['exit' => $user->token]); ?>" title="<?php echo $user->user; ?>"><?php echo $language->doExit; ?></a>
</p>
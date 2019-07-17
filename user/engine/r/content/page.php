<!DOCTYPE html>
<html class dir="<?php echo $site->direction; ?>" style="margin:0;padding:0;width:100%;height:100%;display:block;overflow:hidden;">
  <head>
    <meta charset="<?php echo $site->charset; ?>">
    <meta content="width=device-width" name="viewport">
    <meta content="noindex" name="robots">
    <title><?php echo w($t->reverse); ?></title>
    <link href="<?php echo $url; ?>/favicon.ico" rel="shortcut icon">
  </head>
  <body style="margin:0;padding:0;width:100%;height:100%;display:flex;align-contents:center;align-items:center;">
    <div style="flex:1;margin:0 auto;padding:0;max-width:15em;">
      <?php static::form('user'); ?>
    </div>
  </body>
</html>
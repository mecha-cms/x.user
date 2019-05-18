<!DOCTYPE html>
<html class dir="<?php echo $site->direction; ?>" style="margin:0;padding:0;width:100%;height:100%;display:block;overflow:hidden;">
  <head>
    <meta charset="<?php echo $site->charset; ?>">
    <meta content="width=device-width" name="viewport">
    <meta content="noindex" name="robots">
    <title><?php echo To::text($site->trace); ?></title>
    <link href="<?php echo $url; ?>/favicon.ico" rel="shortcut icon">
  </head>
  <body style="margin:0;padding:0;width:100%;height:100%;display:table;overflow:hidden;">
    <div style="margin:0;padding:0;width:100%;height:100%;display:table-row;">
      <div style="margin:0;padding:0;width:100%;height:100%;display:table-cell;vertical-align:middle;">
        <?php static::get('user.form'); ?>
      </div>
    </div>
  </body>
</html>
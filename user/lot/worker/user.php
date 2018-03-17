<!DOCTYPE html>
<html dir="<?php echo $site->direction; ?>" style="margin:0;padding:0;width:100%;height:100%;display:block;overflow:hidden;">
  <head>
    <meta charset="<?php echo $site->charset; ?>">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width">
    <title><?php echo To::text($site->page->title); ?></title>
  </head>
  <body style="margin:0;padding:0;width:100%;height:100%;display:table;overflow:hidden;">
    <div style="margin:0;padding:0;width:100%;height:100%;display:table-row;">
      <div style="margin:0;padding:0;width:100%;height:100%;display:table-cell;vertical-align:middle;">
        <?php Shield::get(__DIR__ . DS . 'form.php'); ?>
      </div>
    </div>
  </body>
</html>
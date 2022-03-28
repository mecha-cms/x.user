<!DOCTYPE html>
<html class dir="<?= $site->direction; ?>">
  <head>
    <meta charset="<?= $site->charset; ?>">
    <meta content="width=device-width" name="viewport">
    <meta content="noindex" name="robots">
    <title>
      <?= w($t->reverse); ?>
    </title>
    <link href="<?= $url; ?>/favicon.ico" rel="icon">
  </head>
  <body>
    <div>
      <?= self::form('user', ['kick' => $_GET['kick'] ?? null]); ?>
    </div>
  </body>
</html>
<!DOCTYPE html>
<html class lang="<?= $site->language; ?>">
  <head>
    <meta charset="utf-8">
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
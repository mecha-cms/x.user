<!DOCTYPE html>
<html class dir="<?= $site->direction; ?>" style="
  margin: 0;
  padding: 0;
  width: 100%;
  height: 100%;
  display: block;
  overflow: hidden;
">
  <head>
    <meta charset="<?= $site->charset; ?>">
    <meta content="width=device-width" name="viewport">
    <meta content="noindex" name="robots">
    <title><?= w($t->reverse); ?></title>
    <link href="<?= $url; ?>/favicon.ico" rel="shortcut icon">
  </head>
  <body style="
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-contents: center;
    align-items: center;
  ">
    <div style="
      flex: 1;
      margin: 0 auto;
      padding: 0;
      max-width: 15em;
    ">
      <?php if (q(g(USER, 'page')) === 0): ?>
      <?= self::form('user.set'); ?>
      <?php else: ?>
      <?= self::form('user.' . (Is::user() ? 'exit' : 'enter')); ?>
      <?php endif; ?>
    </div>
  </body>
</html>
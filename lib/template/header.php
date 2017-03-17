<?php
header('Content-Type: text/html; charset=utf-8');
header('x-ua-compatible: ie=edge');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo (true === empty($cfg['title']))? t('Jirafeau, your web file repository') : $cfg['title']; ?></title>
  <link rel="shortcut icon" href="<?php echo JIRAFEAU_ABSPREFIX . 'media/' . $cfg['style'] . '/favicon.ico'; ?>">
  <link href="<?php echo JIRAFEAU_ABSPREFIX . 'media/' . $cfg['style'] . '/style.css.php'; ?>" rel="stylesheet" type="text/css" />
</head>
<body>
<script type="text/javascript" src="<?php echo JIRAFEAU_ABSPREFIX . 'lib/functions.js.php'; ?>"></script>
<div id="content">
  <h1>
    <a href="<?php echo JIRAFEAU_ABSPREFIX; ?>">
      <?php echo (true === empty($cfg['title']))? t('Jirafeau, your web file repository') : $cfg['title']; ?>
    </a>
  </h1>

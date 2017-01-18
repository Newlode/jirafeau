<?php
header('Vary: Accept');
header('Content-Type: text/html; charset=utf-8');
header('x-ua-compatible: ie=edge');

$protocol = (bool)is_ssl() ? 'https' : 'http';

if ( !empty($cfg['web_root']) ) {
    $cfg['web_root'] = preg_replace('#https?://#', $protocol . '://', $cfg['web_root'], 1);
}

/* Avoids irritating errors with the installer (no conf file is present then). */
if (!isset ($cfg['web_root']))
    $web_root = $protocol+'://' . $_SERVER['HTTP_HOST'] . '/';
else
    $web_root = $cfg['web_root'];

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo (true === empty($cfg['title']))? t('Jirafeau, your web file repository') : $cfg['title']; ?></title>
  <link href="<?php echo $web_root . 'media/' . $cfg['style'] . '/style.css.php'; ?>" rel="stylesheet" type="text/css" />
</head>
<body>
<script type="text/javascript" src="lib/functions.js.php"></script>
<div id="content">
  <h1>
    <a href="<?php echo $web_root; ?>">
      <?php echo (true === empty($cfg['title']))? t('Jirafeau, your web file repository') : $cfg['title']; ?>
    </a>
  </h1>

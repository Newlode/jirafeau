<?php
header('Vary: Accept');

$content_type = 'text/html; charset=utf-8';

if (stristr ($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    $content_type = 'application/xhtml+xml; charset=utf-8';

header('Content-Type: ' . $content_type);

$protocol = (bool)is_ssl() ? 'https' : 'http';

if ( !empty($cfg['web_root']) ) {
    $cfg['web_root'] = preg_replace('#https?://#', $protocol . '://', $cfg['web_root'], 1);
}

/* Avoids irritating errors with the installer (no conf file is present then). */
if (!isset ($cfg['web_root']))
    $web_root = $protocol+'://' . $_SERVER['HTTP_HOST'] . '/';
else
    $web_root = $cfg['web_root'];

if (!isset ($cfg['style']))
    $style = 'default';
else
    $style = $cfg['style'];


?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo t('Jirafeau, your web file repository'); ?></title>
  <meta http-equiv="Content-Type" content="<?php echo $content_type; ?>" />
  <link href="<?php echo $web_root . 'media/' . $style . '/style.css.php'; ?>" rel="stylesheet" type="text/css" />
</head>
<body>
<script type="text/javascript" language="Javascript" src="lib/functions_v7.js"></script>
<div id="content">
<h1><a href="<?php echo $web_root; ?>"><?php echo t('Jirafeau, your web file repository'); ?></a></h1>

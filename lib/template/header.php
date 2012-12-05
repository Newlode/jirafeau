<?php
header("Vary: Accept");
if(stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml')) {
  $content_type = 'application/xhtml+xml; charset=utf-8';
}  else {
  $content_type = 'text/html; charset=utf-8';
}

header('Content-Type: ' . $content_type);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php echo _('Jirafeau, your web file repository'); ?></title>
  <meta http-equiv="Content-Type" content="<?php echo $content_type; ?>" />
  <link href="<?php echo $cfg['web_root'] . 'media/' . $cfg['style'] . '/style.css.php'; ?>" rel="stylesheet" type="text/css" />
</head>
<body>

<div id="content">
<h1><a href="<?php echo $cfg['web_root']; ?>"><?php echo _('Jirafeau, your web file repository'); ?></a></h1>

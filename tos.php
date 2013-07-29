<?php
define ('JIRAFEAU_ROOT', dirname (__FILE__) . '/');
require (JIRAFEAU_ROOT . 'lib/config.original.php');
require (JIRAFEAU_ROOT . 'lib/settings.php');
require (JIRAFEAU_ROOT . 'lib/functions.php');
require (JIRAFEAU_ROOT . 'lib/lang.php');
require (JIRAFEAU_ROOT . 'lib/template/header.php');

$url = $cfg['web_root'] . 'tos.php';
$org = "[THIS WEBSITE]";
$contact = "
By email:
    contact@[THIS WEBSITE]
";

include (JIRAFEAU_ROOT . 'tos_text.php');

echo '<h2>Terms of Service</h2>';
echo '<div>';
echo '<textarea readonly="readonly" rows="210" cols="80">';
echo $tos;
echo '</textarea>';
echo '<p>This license text is under <a href="http://creativecommons.org/licenses/by/3.0/">Creative Commons - Attribution 3.0 Unported</a>.</p><p>It has been based on this work: <a href="http://opensource.org/ToS">http://opensource.org/ToS</a></p>';
echo '</div>';
require (JIRAFEAU_ROOT . 'lib/template/footer.php');
?>

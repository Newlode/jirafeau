<?php
/*
 *  Jyraphe, your web file repository
 *  Copyright (C) 2008  Julien "axolotl" BERNARD <axolotl@magieeternelle.org>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define('JYRAPHE_ROOT', dirname(__FILE__) . '/');

require(JYRAPHE_ROOT . 'lib/config.php');
require(JYRAPHE_ROOT . 'lib/settings.php');
require(JYRAPHE_ROOT . 'lib/functions.php');

if(isset($_GET['h']) && !empty($_GET['h'])) {
  $link_name = $_GET['h'];

  if(!ereg('^[OR][0-9a-f]{32}$', $link_name)) {
    header("HTTP/1.0 404 Not Found");

    require(JYRAPHE_ROOT . 'lib/template/header.php');
    echo '<div class="error"><p>Error 404: Not Found</p></div>';
    require(JYRAPHE_ROOT . 'lib/template/footer.php');
    exit;
  }

  $link_file = VAR_LINKS . $link_name;
  if(file_exists($link_file)) {
    $content = file($link_file);
    $file_name = trim($content[0]);
    $mime_type = trim($content[1]);
    $file_size = trim($content[2]);
    $key = trim($content[3], NL);
    $time = trim($content[4]);

    if($time != JYRAPHE_INFINITY) {
      if(time() > $time) {
        unlink($link_file);
        $new_name = jyraphe_detect_collision($file_name, VAR_TRASH);
        rename(VAR_FILES . $file_name, VAR_TRASH . $new_name);

        require(JYRAPHE_ROOT . 'lib/template/header.php');
        echo '<div class="error"><p>' . _('The time limit of this file has expired. It has been deleted.') . '</p></div>';
        require(JYRAPHE_ROOT . 'lib/template/footer.php');
        exit;

      }
    }

    if(!empty($key)) {
      if(!isset($_POST['key'])) {
        require(JYRAPHE_ROOT . 'lib/template/header.php');
?>
<div id="upload">
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
<input type="hidden" name="jyraphe" value="<?php echo JYRAPHE_VERSION; ?>" />
<fieldset>
  <legend><?php echo _('Key protection'); ?></legend>
  <table>
  <tr>
    <td><?php echo _('Give the key of this file:'); ?> <input type="password" name="key" /></td>
  </tr>
  <tr>
    <td><input type="submit" value="<?php echo _('I have the right to download this file'); ?>" /></td>
  </tr>
  </table>
</fieldset>
</form>
</div>
<?php
        require(JYRAPHE_ROOT . 'lib/template/footer.php');
        exit;
      } else {
        if($key != $_POST['key']) {
          header("HTTP/1.0 403 Forbidden");

          require(JYRAPHE_ROOT . 'lib/template/header.php');
          echo '<div class="error"><p>Error 403: Forbidden</p></div>';
          require(JYRAPHE_ROOT . 'lib/template/footer.php');
          exit;
        }
      }
    }

    header('Content-Length: ' . $file_size);
    header('Content-Type: ' . $mime_type);
    if(!jyraphe_is_viewable($mime_type)) {
      header('Content-Disposition: attachment; filename="' . $file_name . '"');
    }
    readfile(VAR_FILES . $file_name);

    if($link_name[0] == 'O') {
      unlink($link_file);
      $new_name = jyraphe_detect_collision($file_name, VAR_TRASH);
      rename(VAR_FILES . $file_name, VAR_TRASH . $new_name);
    }
    exit;
  } else {
    header("HTTP/1.0 404 Not Found");

    require(JYRAPHE_ROOT . 'lib/template/header.php');
    echo '<div class="error"><p>Error 404: Not Found</p></div>';
    require(JYRAPHE_ROOT . 'lib/template/footer.php');
    exit;
  }
} else {
  header('Location: ' . $cfg['web_root']);
  exit;
}

?>

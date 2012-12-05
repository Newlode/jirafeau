<?php
/*
 *  Jirafeau, your web file repository
 *  Copyright (C) 2008  Julien "axolotl" BERNARD <axolotl@magieeternelle.org>
 *  Copyright (C) 2012  Jerome Jutteau <j.jutteau@gmail.com>
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

define('JIRAFEAU_ROOT', dirname(__FILE__) . '/');
define('DEBUG', true);

require(JIRAFEAU_ROOT . 'lib/config.php');
require(JIRAFEAU_ROOT . 'lib/settings.php');
require(JIRAFEAU_ROOT . 'lib/functions.php');

/* check if the destination dirs are writable */
$writable = is_writable(VAR_FILES) && is_writable(VAR_LINKS);

$res = array();
if($writable && isset($_POST['jirafeau'])) {

  $key = $_POST['key'];

  $time = time();
  switch($_POST['time']) {
  case 'minute':
    $time += JIRAFEAU_MINUTE;
    break;
  case 'hour':
    $time += JIRAFEAU_HOUR;
    break;
  case 'day':
    $time += JIRAFEAU_DAY;
    break;
  case 'week':
    $time += JIRAFEAU_WEEK;
    break;
  case 'month':
    $time += JIRAFEAU_MONTH;
    break;
  default:
    $time = JIRAFEAU_INFINITY;
    break;
  }

  $res = jirafeau_upload($_FILES['file'], isset($_POST['one_time_download']), $key, $time, $cfg, $_SERVER['REMOTE_ADDR']);
}

require(JIRAFEAU_ROOT . 'lib/template/header.php');

/* Checking for errors. */
if(!is_writable(VAR_FILES)) {
  add_error (_('The file directory is not writable!'), VAR_FILES);
}

if(!is_writable(VAR_LINKS)) {
  add_error (_('The link directory is not writable!'), VAR_LINKS);
}

/* Check if the install.php script is still in the directory. */
if (file_exists(JIRAFEAU_ROOT . 'install.php')) {
  add_error (_('Installer script still present'),
                       _('Please make sure to delete the installer script "install.php" before continuing.'));
}

if(!has_error() && !empty($res)) {
  if($res['error']['has_error']) {
      add_error (_('An error occurred.'), $res['error']['why']);
  } else {
    $link = $cfg['web_root'];
    if($cfg['rewrite']) {
      $link .= 'file-' . $res['link'];
    } else {
      $link .= 'file.php?h=' . $res['link']; // h because 'h' looks like a jirafeau ;)
    }
    echo '<div class="message">' . NL;
    echo '<p>' . _('File uploaded! Copy the following URL to get it:') . '<br />' . NL;
    echo '<a href="' . $link . '">' . $link . '</a>' . NL;

    if($time != JIRAFEAU_INFINITY) {
      echo '<br />' . _('This file is valid until the following date:') . '<br /><strong>' . strftime('%c' ,$time) . '</strong>';
    }

    echo '</p></div>';
  }
}

if(has_error ()) {
    show_errors ();
}

if(!has_error () && $writable) {
?>

<div id="upload">
<form enctype="multipart/form-data" action="<?php echo $cfg['web_root']; ?>" method="post">
<div><input type="hidden" name="jirafeau" value="<?php echo JIRAFEAU_VERSION; ?>" /></div>
<fieldset>
  <legend><?php echo _('Upload a file'); ?></legend>
  <p><input type="file" name="file" size="30" /></p>
  <p class="config"><?php printf(_('Maximum file size: %dMB'), jirafeau_get_max_upload_size()/(1024*1024)); ?></p>
  <p><input type="submit" value="<?php echo _('Send in the binary chaos'); ?>" /></p>

  <hr />

  <div id="moreoptions">
  <p><label><input type="checkbox" name="one_time_download" /> <?php echo _('One time download'); ?></label></p>
  <p><label for="input_key"><?php echo _('File key:'); ?></label> <input type="text" name="key" id="input_key" /></p>
  <p><label for="select_time"><?php echo _('Time limit:'); ?></label>
      <select name="time" id="select_time">
        <option value="none"><?php echo _('None'); ?></option>
        <option value="minute"><?php echo _('One minute'); ?></option>
        <option value="hour"><?php echo _('One hour'); ?></option>
        <option value="day"><?php echo _('One day'); ?></option>
        <option value="week"><?php echo _('One week'); ?></option>
        <option value="month"><?php echo _('One month'); ?></option>
      </select>
  </p>
  </div>
</fieldset>
</form>
</div>

<?php
}

require(JIRAFEAU_ROOT . 'lib/template/footer.php');
?>

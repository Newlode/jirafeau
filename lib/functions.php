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

/**
 * transforms a php.ini string representing a value in an integer
 * @param $value the value from php.ini
 * @returns an integer for this value
 */
function jirafeau_ini_to_bytes($value) {
  $modifier = substr($value, -1);
  $bytes = substr($value, 0, -1);
  switch(strtoupper($modifier)) {
  case 'P':
    $bytes *= 1024;
  case 'T':
    $bytes *= 1024;
  case 'G':
    $bytes *= 1024;
  case 'M':
    $bytes *= 1024;
  case 'K':
    $bytes *= 1024;
  default:
    break;
  }
  return $bytes;
}

/**
 * gets the maximum upload size according to php.ini
 * @returns the maximum upload size
 */
function jirafeau_get_max_upload_size() {
  return min(jirafeau_ini_to_bytes(ini_get('post_max_size')), jirafeau_ini_to_bytes(ini_get('upload_max_filesize')));
}

/**
 * detects if a given filename is present in a directory and find an alternate filename
 * @param $name the initial filename
 * @param $dir the directory to explore (finishing with a '/')
 * @returns an alternate filename, possibly the initial filename
 */
function jirafeau_detect_collision($name, $dir) {
  if(!file_exists($dir . $name)) {
    return $name;
  }

  $dot = strpos($name, '.');
  $dot = ($dot === false) ? strlen($name) : $dot;
  $first = substr($name, 0, $dot);
  $second = substr($name, $dot);
  $i = 1;
  do {
    $new_name = $first . '-' . $i . $second;
    $i++;
  } while(file_exists($dir . $new_name));

  return $new_name;
}

/**
 * gets a string explaining the error
 * @param $code the error code
 * @returns a string explaining the error
 */
function jirafeau_upload_errstr($code) {
  switch($code) {
  case UPLOAD_ERR_INI_SIZE:
  case UPLOAD_ERR_FORM_SIZE:
    return _('Your file exceeds the maximum authorized file size.');
    break;

  case UPLOAD_ERR_PARTIAL:
  case UPLOAD_ERR_NO_FILE:
    return _('Your file was not uploaded correctly. You may succeed in retrying.');
    break;

  case UPLOAD_ERR_NO_TMP_DIR:
  case UPLOAD_ERR_CANT_WRITE:
  case UPLOAD_ERR_EXTENSION:
    return _('Internal error. You may not succeed in retrying.');
    break;

  default:
    break;
  }
  return _('Unknown error.');
}

/**
 * handles an uploaded file
 * @param $file the file struct given by $_FILE[]
 * @param $one_time_download is the file a one time download ?
 * @param $key if not empty, protect the file with this key
 * @param $time the time of validity of the file
 * @param $cfg the current configuration
 * @returns an array containing some information
 *   'error' => information on possible errors
 *   'link' => the link name of the uploaded file
 */
function jirafeau_upload($file, $one_time_download, $key, $time, $cfg) {
  if(empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
    return(array('error' => array('has_error' => true, 'why' => jirafeau_upload_errstr($file['error'])), 'link' => ''));
  }

  /* array representing no error */
  $noerr = array('has_error' => false, 'why' => '');

  /* file informations */
  $md5 = md5_file($file['tmp_name']);
  $name = trim($file['name']);
  $mime_type = $file['type'];
  $size = $file['size'];

  /* does file already exist ? */
  $rc = false;
  if(file_exists(VAR_FILES . $md5)) {
    $rc = unlink($file['tmp_name']);
  }
  elseif(move_uploaded_file($file['tmp_name'], VAR_FILES . $md5)) {
    $rc = true;
  }
  if(!$rc)
  {
    return(array(
      'error' => array(
        'has_error' => true,
        'why' => _('Internal error during file creation.')),
      'link' => '')
    );
  }

  /* increment or create count file */
  $counter=0;
  if(file_exists(VAR_FILES . $md5 . '_count')) {
    $content = file(VAR_FILES . $md5 . '_count');
    $counter = trim($content[0]);
  }
  $counter++;
  $handle = fopen(VAR_FILES . $md5 . '_count', 'w');
  fwrite($handle, $counter);
  fclose($handle);

  /* create link file */
  $link_tmp_name = VAR_LINKS . $md5 . rand(0, 10000) . '.tmp';
  $handle = fopen($link_tmp_name, 'w');
  fwrite($handle, $name . NL . $mime_type . NL . $size . NL . $key . NL . $time . NL . $md5 . NL . ($one_time_download ? 'O' : 'R') . NL . date('U') . NL);
  fclose($handle);
  $md5_link = md5_file($link_tmp_name);
  if(!rename($link_tmp_name, VAR_LINKS . $md5_link)) {
    if ($counter > 1) {
      $counter--;
      $handle = fopen(VAR_FILES . $md5 . '_count', 'w');
      fwrite($handle, $counter);
      fclose($handle);
    }
    else {
      unlink($link_tmp_name);
      unlink(VAR_FILE . $md5 . '_count');
      unlink(VAR_FILE . $md5);
    }
    return(array(
      'error' => array(
        'has_error' => true,
        'why' => _('Internal error during file creation.')),
      'link' => '')
    );
  }
  return(array('error' => $noerr, 'link' => $md5_link));
}

/**
 * tells if a mime-type is viewable in a browser
 * @param $mime the mime type
 * @returns a boolean telling if a mime type is viewable
 */
function jirafeau_is_viewable($mime) {
  if(!empty($mime)) {
    // actually, verify if mime-type is an image or a text
    $viewable = array('image', 'text');
    $decomposed = explode('/', $mime);
    return in_array($decomposed[0], $viewable);
  }
  return false;
}


// Error handling functions.
//! Global array that contains all registered errors.
$error_list = array ();

/**
 * Adds an error to the list of errors.
 * @param $title the error's title
 * @param $description is a human-friendly description of the problem.
 */
function add_error ($title, $description) {
    global $error_list;
    $error_list[] = '<p>' . $title . '<br />' . $description . '</p>';
}

/**
 * Informs whether any error has been registered yet.
 * @return true if there are errors.
 */
function has_error () {
    global $error_list;
    return !empty ($error_list);
}

/**
 * Displays all the errors.
 */
function show_errors () {
    if (has_error ()) {
        global $error_list;
        echo '<div class="error">';
        foreach ($error_list as $error) {
            echo $error;
        }
        echo '</div>';
    }
}

?>

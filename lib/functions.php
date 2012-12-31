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
function jirafeau_ini_to_bytes ($value)
{
    $modifier = substr ($value, -1);
    $bytes = substr ($value, 0, -1);
    switch (strtoupper ($modifier))
    {
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
function
jirafeau_get_max_upload_size ()
{
    return min (jirafeau_ini_to_bytes (ini_get ('post_max_size')),
                jirafeau_ini_to_bytes (ini_get ('upload_max_filesize')));
}

/**
 * gets a string explaining the error
 * @param $code the error code
 * @returns a string explaining the error
 */
function
jirafeau_upload_errstr ($code)
{
    switch ($code)
    {
    case UPLOAD_ERR_INI_SIZE:
    case UPLOAD_ERR_FORM_SIZE:
        return _('Your file exceeds the maximum authorized file size. ');
        break;

    case UPLOAD_ERR_PARTIAL:
    case UPLOAD_ERR_NO_FILE:
        return
            _
            ('Your file was not uploaded correctly. You may succeed in retrying. ');
        break;

    case UPLOAD_ERR_NO_TMP_DIR:
    case UPLOAD_ERR_CANT_WRITE:
    case UPLOAD_ERR_EXTENSION:
        return _('Internal error. You may not succeed in retrying. ');
        break;

    default:
        break;
    }
    return _('Unknown error. ');
}

/** Remove link and it's file
 * @param $link the link's name (hash)
 */

function
jirafeau_delete ($link)
{
    if (!file_exists ( VAR_LINKS . $link))
        return;

    $content = file (VAR_LINKS . $link);
    $md5 = trim ($content[5]);
    unlink (VAR_LINKS . $link);

    $counter = 1;
    if (file_exists ( VAR_FILES . $md5. '_count'))
    {
        $content = file ( VAR_FILES . $md5. '_count');
        $counter = trim ($content[0]);
    }
    $counter--;

    if ($counter >= 1)
    {
        $handle = fopen ( VAR_FILES . $md5. '_count', 'w');
        fwrite ($handle, $counter);
        fclose ($handle);
    }

    if ($counter == 0)
    {
        if (file_exists (VAR_FILES . $md5))
            unlink ( VAR_FILES . $md5);
        if (file_exists (VAR_FILES . $md5 . '_count'))
        unlink ( VAR_FILES . $md5. '_count');
    }
}

/**
 * Delete a file and it's links.
 */
function
jirafeau_delete_file ($md5)
{
    $count = 0;
    $links_dir = scandir (VAR_LINKS);
    
    foreach ($links_dir as $link)
    {
        if (strcmp ($link, '.') == 0 || strcmp ($link, '..') == 0 ||
            preg_match ('/\.tmp/i', "$link"))
            continue;
        /* Read link informations. */
        $l = jirafeau_get_link ($link);
        if ($l['md5'] == $md5)
        {
            $count++;
            jirafeau_delete ($link);
        }
    }

    if (file_exists (VAR_FILES . $md5 . '_count'))
        unlink (VAR_FILES . $md5. '_count');
    if (file_exists (VAR_FILES . $md5))
        unlink (VAR_FILES . $md5);

    return $count;
}

/**
 * handles an uploaded file
 * @param $file the file struct given by $_FILE[]
 * @param $one_time_download is the file a one time download ?
 * @param $key if not empty, protect the file with this key
 * @param $time the time of validity of the file
 * @param $cfg the current configuration
 * @param $ip uploader's ip
 * @returns an array containing some information
 *   'error' => information on possible errors
 *   'link' => the link name of the uploaded file
 *   'delete_link' => the link code to delete file
 */
function
jirafeau_upload ($file, $one_time_download, $key, $time, $cfg, $ip)
{
    if (empty ($file['tmp_name']) || !is_uploaded_file ($file['tmp_name']))
    {
        return (array(
                 'error' =>
                   array ('has_error' => true,
                          'why' => jirafeau_upload_errstr ($file['error'])),
                 'link' => '',
                 'delete_link' => ''));
    }

    /* array representing no error */
    $noerr = array ('has_error' => false, 'why' => '');

    /* file informations */
    $md5 = md5_file ($file['tmp_name']);
    $name = trim ($file['name']);
    $mime_type = $file['type'];
    $size = $file['size'];

    /* does file already exist ? */
    $rc = false;
    if (file_exists ( VAR_FILES . $md5))
    {
        $rc = unlink ($file['tmp_name']);
    }
    elseif (move_uploaded_file ($file['tmp_name'],  VAR_FILES . $md5))
    {
        $rc = true;
    }
    if (!$rc)
    {
        return (array(
                 'error' =>
                   array ('has_error' => true,
                          'why' => _('Internal error during file creation. ')),
                 'link' =>'',
                 'delete_link' => ''));
    }

    /* increment or create count file */
    $counter = 0;
    if (file_exists (VAR_FILES . $md5 . '_count'))
    {
        $content = file ( VAR_FILES . $md5. '_count');
        $counter = trim ($content[0]);
    }
    $counter++;
    $handle = fopen ( VAR_FILES . $md5. '_count', 'w');
    fwrite ($handle, $counter);
    fclose ($handle);

    /* Create delete code. */
    $delete_link_code = 0;
    for ($i = 0; $i < 8; $i++)
        $delete_link_code .= dechex (rand (0, 16));

    /* md5 password or empty */
    $password = '';
    if (!empty ($key))
        $password = md5 ($key);

    /* create link file */
    $link_tmp_name =  VAR_LINKS . $md5.rand (0, 10000) . ' .tmp';
    $handle = fopen ($link_tmp_name, 'w');
    fwrite ($handle,
            $name . NL. $mime_type . NL. $size . NL. $password . NL. $time . NL . $md5.
            NL.($one_time_download ? 'O' : 'R') . NL.date ('U') . NL. $ip . NL.
            $delete_link_code . NL);
    fclose ($handle);
    $md5_link = md5_file ($link_tmp_name);
    if (!rename ($link_tmp_name,  VAR_LINKS . $md5_link))
    {
        unlink ($link_tmp_name);
        $counter--;
        if ($counter >= 1)
        {
            $handle = fopen ( VAR_FILES . $md5. '_count', 'w');
            fwrite ($handle, $counter);
            fclose ($handle);
        }
        else
        {
            unlink ( VAR_FILES . $md5. '_count');
            unlink ( VAR_FILES . $md5);
        }
        return (array(
                 'error' =>
                   array ('has_error' => true,
                          'why' => _('Internal error during file creation. ')),
                 'link' =>'',
                 'delete_link' => ''));
    }
   return (array ('error' => $noerr,
                  'link' => $md5_link,
                  'delete_link' => $delete_link_code));
}

/**
 * tells if a mime-type is viewable in a browser
 * @param $mime the mime type
 * @returns a boolean telling if a mime type is viewable
 */
function
jirafeau_is_viewable ($mime)
{
    if (!empty ($mime))
    {
        /* Actually, verify if mime-type is an image or a text. */
        $viewable = array ('image', 'text');
        $decomposed = explode ('/', $mime);
        return in_array ($decomposed[0], $viewable);
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
function
add_error ($title, $description)
{
    global $error_list;
    $error_list[] = '<p>' . $title. '<br />' . $description. '</p>';
}

/**
 * Informs whether any error has been registered yet.
 * @return true if there are errors.
 */
function
has_error ()
{
    global $error_list;
    return !empty ($error_list);
}

/**
 * Displays all the errors.
 */
function
show_errors ()
{
    if (has_error ())
    {
        global $error_list;
        echo '<div class="error">';
        foreach ($error_list as $error)
        {
            echo $error;
        }
        echo '</div>';
    }
}

/**
 * Read link informations
 * @return array containing informations.
 */
function
jirafeau_get_link ($hash)
{
    $out = array ();
    $link = VAR_LINKS . $hash;

    if (!file_exists ($link))
        return $out;
    
    $c = file ($link);
    $out['file_name'] = trim ($c[0]);
    $out['mime_type'] = trim ($c[1]);
    $out['file_size'] = trim ($c[2]);
    $out['key'] = trim ($c[3], NL);
    $out['time'] = trim ($c[4]);
    $out['md5'] = trim ($c[5]);
    $out['onetime'] = trim ($c[6]);
    $out['upload_date'] = trim ($c[7]);
    $out['ip'] = trim ($c[8]);
    $out['link_code'] = trim ($c[9]);
    
    return $out;
}

function
jirafeau_human_size ($octets)
{
    $u = array ('B', 'KB', 'MB', 'GB', 'TB');
    $o = max ($octets, 0);
    $p = min (floor (($o ? log ($o) : 0) / log (1024)), count ($u) - 1);
    $o /= pow (1024, $p);
    return round ($o, 1) . $u[$p];
} 

/**
 * List files in admin interface.
 */
function
jirafeau_admin_list ($name, $file_hash, $link_hash)
{
    $links_dir = scandir (VAR_LINKS);
    echo '<fieldset><legend>';
    if (!empty ($name))
        echo $name . ' ';
    if (!empty ($file_hash))
        echo $file_hash . ' ';
    if (!empty ($link_hash))
        echo $link_hash . ' ';
    if (empty ($name) && empty ($file_hash) && empty ($link_hash))
        echo _('List all files');
    echo '</legend>';
    echo '<table>';
    echo '<tr>';
    echo '<td>' . _('Filename') . '</td>';
    echo '<td>' . _('Type') . '</td>';
    echo '<td>' . _('Size') . '</td>';
    echo '<td>' . _('Expire') . '</td>';
    echo '<td>' . _('Onetime') . '</td>';
    echo '<td>' . _('Upload date') . '</td>';
    echo '<td>' . _('Origin') . '</td>';
    echo '<td>' . _('Action') . '</td>';
    echo '</tr>';
    foreach ($links_dir as $link)
    {
        if (strcmp ($link, '.') == 0 || strcmp ($link, '..') == 0 ||
            preg_match ('/\.tmp/i', "$link"))
            continue;
        /* Read link informations. */
        $l = jirafeau_get_link ($link);
        
        /* Filter. */
        if (!empty ($name) && $name != $l['file_name'])
            continue;
        if (!empty ($file_hash) && $file_hash != $l['md5'])
            continue;
        if (!empty ($link_hash) && $link_hash != $link)
            continue;
        
        /* Print link informations. */
        echo '<tr>';
        echo '<td>' . $l['file_name'] . '</td>';
        echo '<td>' . $l['mime_type'] . '</td>';
        echo '<td>' . jirafeau_human_size ($l['file_size']) . '</td>';
        echo '<td>' . ($l['time'] == -1 ? '' : strftime ('%c', $l['time'])) .
             '</td>';
        echo '<td>' . $l['onetime'] . '</td>';
        echo '<td>' . strftime ('%c', $l['upload_date']) . '</td>';
        echo '<td>' . $l['ip'] . '</td>';
        echo '<td>' .
        '<form action = "admin.php" method = "post">' .
        '<input type = "hidden" name = "action" value = "delete_link"/>' .
        '<input type = "hidden" name = "link" value = "' . $link . '"/>' .
        '<input type = "submit" value = "' . _('Del link') . '" />' .
        '</form>' .
        '<form action = "admin.php" method = "post">' .
        '<input type = "hidden" name = "action" value = "delete_file"/>' .
        '<input type = "hidden" name = "md5" value = "' . $l['md5'] . '"/>' .
        '<input type = "submit" value = "' . _('Del file and links') . '" />' .
        '</form>' .
        '</td>';
        echo '</tr>';
    }
    echo '</table></fieldset>';
}

/**
 * Clean expired files.
 * @return number of cleaned files.
 */
function
jirafeau_admin_clean ()
{
    $c = 0;
    $links_dir = scandir (VAR_LINKS);

    foreach ($links_dir as $link)
    {
        if (strcmp ($link, '.') == 0 || strcmp ($link, '..') == 0 ||
            preg_match ('/\.tmp/i', "$link"))
            continue;
        /* Read link informations. */
        $l = jirafeau_get_link ($link);
        if ($l['time'] > 0 && $l['time'] < time () || // expired
            !file_exists (VAR_FILES . $l['md5']) || // invalid
            !file_exists (VAR_FILES . $l['md5'] . '_count')) // invalid
        {
            jirafeau_delete ($link);
            $c++;
        }
    }
    return $c;
}
?>

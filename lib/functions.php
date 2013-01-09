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
 * Transform a string in a path by seperating each letters by a '/'.
  * @return path finishing with a '/'
 */
function
s2p ($s)
{
    $p = '';
    for ($i = 0; $i < strlen ($s); $i++)
        $p .= $s{$i} . '/';
    return $p;
}

/**
 * Convert base 16 to base 64
 * @returns A string based on 64 characters (0-9, a-z, A-Z, "-" and "_")
 */
function
base_16_to_64 ($num)
{
    $m = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
    $o = '';    
    $b = '';
    $i = 0;
    $size = strlen ($num);
    for ($i = 0; $i < $size; $i++)
        $b .= base_convert ($num{$i}, 16, 2);
    $size = strlen ($b);
    for ($i = $size - 6; $i >= 0; $i -= 6)
        $o = $m{bindec (substr ($b, $i, 6))} . $o;
    if ($i < 0 && $i > -6)
        $o = $m{bindec (substr ($b, 0, $i + 6))} . $o;
    return $o;
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

function
jirafeau_clean_rm_link ($link)
{
    $p = s2p ("$link");
    if (file_exists (VAR_LINKS . $p . $link))
        unlink (VAR_LINKS . $p . $link);
    $parse = VAR_LINKS . $p;
    $scan = array();
    while (file_exists ($parse)
           && ($scan = scandir ($parse))
           && count ($scan) == 2 // '.' and '..' folders => empty.
           && basename ($parse) != basename (VAR_LINKS)) 
    {
        rmdir ($parse);
        $parse = substr ($parse, 0, strlen($parse) - strlen(basename ($parse)) - 1);
    }
}

function
jirafeau_clean_rm_file ($md5)
{
    $p = s2p ("$md5");
    if (file_exists (VAR_FILES . $p . $md5))
        unlink (VAR_FILES . $p . $md5);
    if (file_exists (VAR_FILES . $p . $md5 . '_count'))
        unlink (VAR_FILES . $p . $md5 . '_count');
    $parse = VAR_FILES . $p;
    $scan = array();
    while (file_exists ($parse)
           && ($scan = scandir ($parse))
           && count ($scan) == 2 // '.' and '..' folders => empty.
           && basename ($parse) != basename (VAR_FILES)) 
    {
        rmdir ($parse);
        $parse = substr ($parse, 0, strlen($parse) - strlen(basename ($parse)) - 1);
    }
}

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
 * @returns the maximum upload size string
 */
function
jirafeau_get_max_upload_size ()
{
    return jirafeau_human_size(
            min (jirafeau_ini_to_bytes (ini_get ('post_max_size')),
                 jirafeau_ini_to_bytes (ini_get ('upload_max_filesize'))));
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
        return t('Your file exceeds the maximum authorized file size. ');
        break;

    case UPLOAD_ERR_PARTIAL:
    case UPLOAD_ERR_NO_FILE:
        return
            t
            ('Your file was not uploaded correctly. You may succeed in retrying. ');
        break;

    case UPLOAD_ERR_NO_TMP_DIR:
    case UPLOAD_ERR_CANT_WRITE:
    case UPLOAD_ERR_EXTENSION:
        return t('Internal error. You may not succeed in retrying. ');
        break;

    default:
        break;
    }
    return t('Unknown error. ');
}

/** Remove link and it's file
 * @param $link the link's name (hash)
 */

function
jirafeau_delete_link ($link)
{
    $l = jirafeau_get_link ($link);
    if (!count ($l))
        return;

    jirafeau_clean_rm_link ($link);

    $md5 = $l['md5'];
    $p = s2p ("$md5");

    $counter = 1;
    if (file_exists (VAR_FILES . $p . $md5. '_count'))
    {
        $content = file (VAR_FILES . $p . $md5. '_count');
        $counter = trim ($content[0]);
    }
    $counter--;

    if ($counter >= 1)
    {
        $handle = fopen (VAR_FILES . $p . $md5. '_count', 'w');
        fwrite ($handle, $counter);
        fclose ($handle);
    }

    if ($counter == 0)
        jirafeau_clean_rm_file ($md5);
}

/**
 * Delete a file and it's links.
 */
function
jirafeau_delete_file ($md5)
{
    $count = 0;
    /* Get all links files. */
    $stack = array (VAR_LINKS);
    while (($d = array_shift ($stack)) && $d != NULL)
    {
        $dir = scandir ($d);

        foreach ($dir as $node)
        {
            if (strcmp ($node, '.') == 0 || strcmp ($node, '..') == 0 ||
                preg_match ('/\.tmp/i', "$node"))
                continue;
            
            if (is_dir ($d . $node))
            {
                /* Push new found directory. */
                $stack[] = $d . $node . '/';
            }
            elseif (is_file ($d . $node))
            {
                /* Read link informations. */
                $l = jirafeau_get_link (basename ($node));
                if (!count ($l))
                    continue;
                if ($l['md5'] == $md5)
                {
                    $count++;
                    jirafeau_delete_link ($node);
                }   
            }
        }
    }
    jirafeau_clean_rm_file ($md5);
    return $count;
}

/**
 * handles an uploaded file
 * @param $file the file struct given by $_FILE[]
 * @param $one_time_download is the file a one time download ?
 * @param $key if not empty, protect the file with this key
 * @param $time the time of validity of the file
 * @param $ip uploader's ip
 * @returns an array containing some information
 *   'error' => information on possible errors
 *   'link' => the link name of the uploaded file
 *   'delete_link' => the link code to delete file
 */
function
jirafeau_upload ($file, $one_time_download, $key, $time, $ip)
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
    $p = s2p ("$md5");
    if (file_exists (VAR_FILES . $p .  $md5))
    {
        $rc = unlink ($file['tmp_name']);
    }
    elseif ((file_exists (VAR_FILES . $p) || @mkdir (VAR_FILES . $p, 0755, true))
            && move_uploaded_file ($file['tmp_name'], VAR_FILES . $p . $md5))
    {
        $rc = true;
    }
    if (!$rc)
    {
        return (array(
                 'error' =>
                   array ('has_error' => true,
                          'why' => t('Internal error during file creation.')),
                 'link' =>'',
                 'delete_link' => ''));
    }

    /* increment or create count file */
    $counter = 0;
    if (file_exists (VAR_FILES . $p . $md5 . '_count'))
    {
        $content = file (VAR_FILES . $p . $md5. '_count');
        $counter = trim ($content[0]);
    }
    $counter++;
    $handle = fopen (VAR_FILES . $p . $md5. '_count', 'w');
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
    $link_tmp_name =  VAR_LINKS . $md5 . rand (0, 10000) . ' .tmp';
    $handle = fopen ($link_tmp_name, 'w');
    fwrite ($handle,
            $name . NL. $mime_type . NL. $size . NL. $password . NL. $time .
            NL . $md5. NL . ($one_time_download ? 'O' : 'R') . NL.date ('U') .
            NL. $ip . NL. $delete_link_code . NL);
    fclose ($handle);
    $md5_link = base_16_to_64 (md5_file ($link_tmp_name));
    $l = s2p ("$md5_link");
    if (!@mkdir (VAR_LINKS . $l, 0755, true) ||
        !rename ($link_tmp_name,  VAR_LINKS . $l . $md5_link))
    {
        if (file_exists ($link_tmp_name))
            unlink ($link_tmp_name);
        
        $counter--;
        if ($counter >= 1)
        {
            $handle = fopen (VAR_FILES . $p . $md5. '_count', 'w');
            fwrite ($handle, $counter);
            fclose ($handle);
        }
        else
        {
            jirafeau_clean_rm_file ($md5_link);
        }
        return (array(
                 'error' =>
                   array ('has_error' => true,
                          'why' => t('Internal error during file creation. ')),
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
    $link = VAR_LINKS . s2p ("$hash") . $hash;

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

/**
 * List files in admin interface.
 */
function
jirafeau_admin_list ($name, $file_hash, $link_hash)
{
    echo '<fieldset><legend>';
    if (!empty ($name))
        echo t('Filename') . ": $name ";
    if (!empty ($file_hash))
        echo t('file') . ": $file_hash ";
    if (!empty ($link_hash))
        echo t('link') . ": $link_hash ";
    if (empty ($name) && empty ($file_hash) && empty ($link_hash))
        echo t('List all files');
    echo '</legend>';
    echo '<table>';
    echo '<tr>';
    echo '<td>' . t('Filename') . '</td>';
    echo '<td>' . t('Type') . '</td>';
    echo '<td>' . t('Size') . '</td>';
    echo '<td>' . t('Expire') . '</td>';
    echo '<td>' . t('Onetime') . '</td>';
    echo '<td>' . t('Upload date') . '</td>';
    echo '<td>' . t('Origin') . '</td>';
    echo '<td>' . t('Action') . '</td>';
    echo '</tr>';

    /* Get all links files. */
    $stack = array (VAR_LINKS);
    while (($d = array_shift ($stack)) && $d != NULL)
    {
        $dir = scandir ($d);
        foreach ($dir as $node)
        {
            if (strcmp ($node, '.') == 0 || strcmp ($node, '..') == 0 ||
                preg_match ('/\.tmp/i', "$node"))
                continue;
            if (is_dir ($d . $node))
            {
                /* Push new found directory. */
                $stack[] = $d . $node . '/';
            }
            elseif (is_file ($d . $node))
            {
                /* Read link informations. */
                $l = jirafeau_get_link ($node);
                if (!count ($l))
                    continue;

                /* Filter. */
                if (!empty ($name) && !preg_match ("/$name/i", $l['file_name']))
                    continue;
                if (!empty ($file_hash) && $file_hash != $l['md5'])
                    continue;
                if (!empty ($link_hash) && $link_hash != $node)
                    continue;
                /* Print link informations. */
                echo '<tr>';
                echo '<td>' .
                '<form action = "admin.php" method = "post">' .
                '<input type = "hidden" name = "action" value = "download"/>' .
                '<input type = "hidden" name = "link" value = "' . $node . '"/>' .
                '<input type = "submit" value = "' . $l['file_name'] . '" />' .
                '</form>';
                echo '</td>';
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
                '<input type = "hidden" name = "link" value = "' . $node . '"/>' .
                '<input type = "submit" value = "' . t('Del link') . '" />' .
                '</form>' .
                '<form action = "admin.php" method = "post">' .
                '<input type = "hidden" name = "action" value = "delete_file"/>' .
                '<input type = "hidden" name = "md5" value = "' . $l['md5'] . '"/>' .
                '<input type = "submit" value = "' . t('Del file and links') . '" />' .
                '</form>' .
                '</td>';
                echo '</tr>';
            }
        }
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
    $count = 0;
    /* Get all links files. */
    $stack = array (VAR_LINKS);
    while (($d = array_shift ($stack)) && $d != NULL)
    {
        $dir = scandir ($d);

        foreach ($dir as $node)
        {
            if (strcmp ($node, '.') == 0 || strcmp ($node, '..') == 0 ||
                preg_match ('/\.tmp/i', "$node"))
                continue;
            
            if (is_dir ($d . $node))
            {
                /* Push new found directory. */
                $stack[] = $d . $node . '/';
            }
            elseif (is_file ($d . $node))
            {
                /* Read link informations. */
                $l = jirafeau_get_link (basename ($node));
                if (!count ($l))
                    continue;
                $p = s2p ($l['md5']);
                if ($l['time'] > 0 && $l['time'] < time () || // expired
                    !file_exists (VAR_FILES . $p . $l['md5']) || // invalid
                    !file_exists (VAR_FILES . $p . $l['md5'] . '_count')) // invalid
                {
                    jirafeau_delete_link ($node);
                    $count++;
                }
            }
        }
    }
    return $count;
}
?>

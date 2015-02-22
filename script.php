<?php
/*
 *  Jirafeau, your web file repository
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

/*
 * This file permits to easyly script file sending, receiving, deleting, ...
 * If you don't want this feature, you can simply delete this file from your
 * web directory.
 */

define ('JIRAFEAU_ROOT', dirname (__FILE__) . '/');

require (JIRAFEAU_ROOT . 'lib/config.original.php');
require (JIRAFEAU_ROOT . 'lib/settings.php');
require (JIRAFEAU_ROOT . 'lib/functions.php');
require (JIRAFEAU_ROOT . 'lib/lang.php');

 global $script_langages;
 $script_langages = array ('bash' => 'Bash');

/* Operations may take a long time.
 * Be sure PHP's safe mode is off.
 */
@set_time_limit(0);
/* Remove errors. */
@error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] == "GET" && count ($_GET) == 0)
{
    require (JIRAFEAU_ROOT . 'lib/template/header.php');
    check_errors ($cfg);
    if (has_error ())
    {
        show_errors ();
        require (JIRAFEAU_ROOT . 'lib/template/footer.php');
        exit;
    }
    echo '<div class="info">';
    echo '<h2>' . t('Welcome to Jirafeau\'s query interface') . '</h2>';
    echo '<p>';
    echo t('This interface permits to script your uploads and downloads.') .
            ' ' . t('The instructions above show how to query this interface.');
    echo '</p>';
    
    echo '<h3>' . t('Get Jirafeau\'s version') . ':</h3>';
    echo '<p>';
    echo t('Send a GET query to') . ': <i>' . $web_root . 'script.php</i><br />';
    echo '<br />';
    echo t('Parameters') . ':<br />';
    echo "<b>get_version=</b>1<i> (" . t('Required') . ")</i> <br />";
    echo '</p>';
    echo '<p>' . t('This will return brut text content.') . ' ' .
            t('First line is the version number.') . '<br /></p>';
    echo '<p>';
    echo t('Example') . ": <a href=\"" . $web_root . "script.php?get_version=1\">" . $web_root . "script.php?get_version=1</a> ";
    echo '</p>';

    echo '<h3>' . t('Get server capacity') . ':</h3>';
    echo '<p>';
    echo t('Send a GET query to') . ': <i>' . $web_root . 'script.php</i><br />';
    echo '<br />';
    echo t('Parameters') . ':<br />';
    echo "<b>get_capacity=</b>1<i> (" . t('Required') . ")</i> <br />";
    echo '</p>';
    echo '<p>' . t('This will return brut text content.') . ' ' .
            t('First line is the server capacity (in Bytes).') . '<br /></p>';
    echo '<p>';
    echo t('Example') . ": <a href=\"" . $web_root . "script.php?get_capacity=1\">" . $web_root . "script.php?get_capacity=1</a> ";
    echo '</p>';
    
    echo '<h3>' . t('Upload a file') . ':</h3>';
    echo '<p>';
    echo t('Send a POST query to') . ': <i>' . $web_root . 'script.php</i><br />';
    echo '<br />';
    echo t('Parameters') . ':<br />';
    echo "<b>file=</b>C:\\your\\file\\path<i> (" . t('Required') . ")</i> <br />";
    echo "<b>time=</b>[minute|hour|day|week|month|none]<i> (" . t('Optional') . ', '. t('default: none') . ")</i> <br />";
    echo "<b>password=</b>your_password<i> (" . t('Optional') . ")</i> <br />";
    echo "<b>one_time_download=</b>1<i> (" . t('Optional') . ")</i> <br />";
    echo '</p>';
    echo '<p>' . t('This will return brut text content.') . ' ' .
         t('First line is the download reference and the second line the delete code.') . '<br /></p>';
    
    echo '<h3>' . t('Get a file') . ':</h3>';
    echo '<p>';
    echo t('Send a GET query to') . ': <i>' . $web_root . 'script.php</i><br />';
    echo '<br />';
    echo t('Parameters') . ':<br />';
    echo "<b>h=</b>your_download_reference<i> (" . t('Required') . ")</i> <br />";
    echo '</p>';
    echo '<p>';
    echo t('If a password has been set, send a POST request with it.');
    echo '<br />';
    echo t('Parameters') . ':<br />';
    echo "<b>password=</b>your_password<i> (" . t('Optional') . ")</i> <br />";
    echo '</p>';
    echo '<p>';
    echo t('Example') . ": <a href=\"" . $web_root . "script.php?h=30ngy0hsDcpfrF8zR7x9iU\">" . $web_root . "script.php?h=30ngy0hsDcpfrF8zR7x9iU</a> ";
    echo '</p>';
    
    echo '<h3>' . t('Delete a file') . ':</h3>';
    echo '<p>';
    echo t('Send a GET query to') . ': <i>' . $web_root . 'script.php</i><br />';
    echo '<br />';
    echo t('Parameters') . ':<br />';
    echo "<b>h=</b>your_download_reference<i> (" . t('Required') . ")</i> <br />";
    echo "<b>d=</b>yout_delete_code<i> (" . t('Required') . ")</i> <br />";
    echo '</p>';
    echo '<p>' . t('This will return "Ok" if succeded, "Error" otherwhise.') . '<br /></p>';
    echo '<p>';
    echo t('Example') . ": <a href=\"" . $web_root . "script.php?h=30ngy0hsDcpfrF8zR7x9iU&amp;d=0d210a952\">" . $web_root . "script.php?h=30ngy0hsDcpfrF8zR7x9iU&amp;d=0d210a952</a> ";
    echo '</p>';
    
    echo '<h3>' . t('Get a generated scripts') . ':</h3>';
    echo '<p>';
    echo t('Send a GET query to') . ': <i>' . $web_root . 'script.php</i><br />';
    echo '<br />';
    echo t('Parameters') . ':<br />';
    echo "<b>lang=</b>[";
    foreach ($script_langages as $lang => $name)
        echo $lang;
    echo "]<i> (" . t('Required') . ")</i> <br />";
    echo '</p>';
    echo '<p>' . t('This will return brut text content of the code.') . '<br /></p>';
    echo '<p>';
    echo t('Example') . ": <br />";
    foreach ($script_langages as $lang => $name)
        echo "$name: <a href=\"" . $web_root . "script.php?lang=$lang\">" . $web_root . "script.php?lang=$lang</a> ";
    echo '</p>';
    
    echo '<h3>' . t('Initalize a asynchronous transfert') . ':</h3>';
    echo '<p>';
    echo t('The goal is to permit to transfert big file, chunk by chunk.') . ' ';
    echo t('Chunks of data must be sent in order.');
    echo '</p>';
    echo '<p>';
    echo t('Send a GET query to') . ': <i>' . $web_root . 'script.php?init_async</i><br />';
    echo '<br />';
    echo t('Parameters') . ':<br />';
    echo "<b>filename=</b>file_name.ext<i> (" . t('Required') . ")</i> <br />";
    echo "<b>type=</b>MIME_TYPE<i> (" . t('Optional') . ")</i> <br />";
    echo "<b>time=</b>[minute|hour|day|week|month|none]<i> (" . t('Optional') . ', '. t('default: none') . ")</i> <br />";
    echo "<b>password=</b>your_password<i> (" . t('Optional') . ")</i> <br />";
    echo "<b>one_time_download=</b>1<i> (" . t('Optional') . ")</i> <br />";
    echo '</p>';
    echo '<p>' . t('This will return brut text content.') . ' ' .
         t('First line is the asynchronous transfert reference and the second line the code to use in the next operation.') . '<br /></p>';

    echo '<h3>' . t('Push data during asynchronous transfert') . ':</h3>';
    echo '<p>';
    echo t('Send a GET query to') . ': <i>' . $web_root . 'script.php?push_async</i><br />';
    echo '<br />';
    echo t('Parameters') . ':<br />';
    echo "<b>ref=</b>async_reference<i> (" . t('Required') . ")</i> <br />";
    echo "<b>data=</b>data_chunk<i> (" . t('Required') . ")</i> <br />";
    echo "<b>code=</b>last_provided_code<i> (" . t('Required') . ")</i> <br />";
    echo '</p>';
    echo '<p>' . t('This will return brut text content.') . ' ' .
         t('Returns the next code to use.') . '<br /></p>';

    echo '<h3>' . t('Finalize asynchronous transfert') . ':</h3>';
    echo '<p>';
    echo t('Send a GET query to') . ': <i>' . $web_root . 'script.php?end_async</i><br />';
    echo '<br />';
    echo t('Parameters') . ':<br />';
    echo "<b>ref=</b>async_reference<i> (" . t('Required') . ")</i> <br />";
    echo "<b>code=</b>last_provided_code<i> (" . t('Required') . ")</i> <br />";
    echo '</p>';
    echo '<p>' . t('This will return brut text content.') . ' ' .
         t('First line is the download reference and the second line the delete code.') . '<br /></p>';

    if ($cfg['enable_blocks'])
    {
        echo '<h3>' . t('Create a data block') . ':</h3>';
        echo '<p>';
        echo t('This interface permits to create a block of data filled with zeros.') .
            ' ' . t('You can read selected parts, write (using a code) and delete the block.') .
            ' ' . t('Blocks may be removed after a month of non usage.');
        echo '</p>';
        echo '<p>';
        echo t('Send a GET query to') . ': <i>' . $web_root . 'script.php?init_block</i><br />';
        echo '<br />';
        echo t('Parameters') . ':<br />';
        echo "<b>size=</b>size_in_bytes<i> (" . t('Required') . ")</i> <br />";
        echo '</p>';
        echo '<p>' . t('This will return brut text content.') . ' ' .
             t('First line is a block id the second line the edit/delete code.') . '<br /></p>';

        echo '<h3>' . t('Get block size') . ':</h3>';
        echo '<p>';
        echo t('Send a GET query to') . ': <i>' . $web_root . 'script.php?get_block_size</i><br />';
        echo '<br />';
        echo t('Parameters') . ':<br />';
        echo "<b>id=</b>block_id<i> (" . t('Required') . ")</i> <br />";
        echo '</p>';
        echo '<p>' . t('This will return asked data or "Error" string.') . '<br /></p>';

        echo '<h3>' . t('Read data in a block') . ':</h3>';
        echo '<p>';
        echo t('Send a GET query to') . ': <i>' . $web_root . 'script.php?read_block</i><br />';
        echo '<br />';
        echo t('Parameters') . ':<br />';
        echo "<b>id=</b>block_id<i> (" . t('Required') . ")</i> <br />";
        echo "<b>start=</b>byte_position_starting_from_zero<i> (" . t('Required') . ")</i> <br />";
        echo "<b>length=</b>length_to_read_in_bytes<i> (" . t('Required') . ")</i> <br />";
        echo '</p>';
        echo '<p>' . t('This will return asked data or "Error" string.') . '<br /></p>';

        echo '<h3>' . t('Write data in a block') . ':</h3>';
        echo '<p>';
        echo t('Send a GET query to') . ': <i>' . $web_root . 'script.php?write_block</i><br />';
        echo '<br />';
        echo t('Parameters') . ':<br />';
        echo "<b>id=</b>block_id<i> (" . t('Required') . ")</i> <br />";
        echo "<b>code=</b>block_code<i> (" . t('Required') . ")</i> <br />";
        echo "<b>start=</b>byte_position_starting_from_zero<i> (" . t('Required') . ")</i> <br />";
        echo "<b>data=</b>data_to_write<i> (" . t('Required') . ")</i> <br />";
        echo '</p>';
        echo '<p>' . t('This will return "Ok" or "Error" string.') . '<br /></p>';

        echo '<h3>' . t('Delete a block') . ':</h3>';
        echo '<p>';
        echo t('Send a GET query to') . ': <i>' . $web_root . 'script.php?delete_block</i><br />';
        echo '<br />';
        echo t('Parameters') . ':<br />';
        echo "<b>id=</b>block_id<i> (" . t('Required') . ")</i> <br />";
        echo "<b>code=</b>block_code<i> (" . t('Required') . ")</i> <br />";
        echo '</p>';
        echo '<p>' . t('This will return "Ok" or "Error" string.') . '<br /></p>';
    }

    echo '</div><br />';
    require (JIRAFEAU_ROOT . 'lib/template/footer.php');
    exit;
}

/* Lets use interface now. */
header('Content-Type: text; charset=utf-8');

check_errors ($cfg);
if (has_error ())
{
    echo "Error";
    exit;
}

/* Upload file */
if (isset ($_FILES['file']) && is_writable (VAR_FILES)
    && is_writable (VAR_LINKS))
{
    if (strlen ($cfg['upload_password']) > 0 && (!isset ($_POST['upload_password']) || $_POST['upload_password'] != $cfg['upload_password']))
    {
        echo "Error";
        exit;
    }

    $key = '';
    if (isset ($_POST['key']))
        $key = $_POST['key'];

    $time = time ();
    if (!isset ($_POST['time']))
        $time = JIRAFEAU_INFINITY;
    else
        switch ($_POST['time'])
        {
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
    $res = jirafeau_upload ($_FILES['file'],
                            isset ($_POST['one_time_download']),
                            $key, $time, $_SERVER['REMOTE_ADDR'],
                            $cfg['enable_crypt'], $cfg['link_name_lenght']);
    
    if (empty($res) || $res['error']['has_error'])
    {
        echo "Error";
        exit;
    }
    /* Print direct link. */
    echo $res['link'];
    /* Print delete link. */
    echo NL;
    echo $res['delete_link'];
    /* Print decrypt key. */
    echo NL;
    echo urlencode($res['crypt_key']);
}
elseif (isset ($_GET['h']))
{
    $link_name = $_GET['h'];
    $key = '';
    if (isset ($_POST['key']))
        $key = $_POST['key'];
    $d = '';
    if (isset ($_GET['d']))
        $d = $_GET['d'];
    
    if (!preg_match ('/[0-9a-zA-Z_-]+$/', $link_name))
    {
        echo "Error";
        exit;
    }
    
    $link = jirafeau_get_link ($link_name);
    if (count ($link) == 0)
    {
        echo "Error";
        exit;
    }
    if (strlen ($d) > 0 && $d == $link['link_code'])
    {
        jirafeau_delete_link ($link_name);
        echo "Ok";
        exit;
    }
    if ($link['time'] != JIRAFEAU_INFINITY && time () > $link['time'])
    {
        jirafeau_delete_link ($link_name);
        echo "Error";
        exit;
    }
    if (strlen ($link['key']) > 0 && md5 ($key) != $link['key'])
    {
        echo "Error";
        exit;
    }
    $p = s2p ($link['md5']);
    if (!file_exists (VAR_FILES . $p . $link['md5']))
    {
        echo "Error";
        exit;
    }

    /* Read file. */
    header ('Content-Length: ' . $link['file_size']);
    header ('Content-Type: ' . $link['mime_type']);
    header ('Content-Disposition: attachment; filename="' .
            $link['file_name'] . '"');

    $r = fopen (VAR_FILES . $p . $link['md5'], 'r');
    while (!feof ($r))
    {
        print fread ($r, 1024);
        ob_flush();
    }
    fclose ($r);

    if ($link['onetime'] == 'O')
        jirafeau_delete_link ($link_name);
    exit;
}
elseif (isset ($_GET['get_capacity']))
{
    echo min (jirafeau_ini_to_bytes (ini_get ('post_max_size')),
              jirafeau_ini_to_bytes (ini_get ('upload_max_filesize')));
}
elseif (isset ($_GET['get_version']))
{
    echo JIRAFEAU_VERSION;
}
elseif (isset ($_GET['lang']))
{
    $l=$_GET['lang'];
    if ($l == "bash")
    {
?>
#!/bin/bash

# This script has been auto-generated by Jirafeau but you can still edit 
# options below.

# Config
proxy='' # ex: proxy='proxysever.test.com:3128' or set JIRAFEAU_PROXY global variable
url='<?php echo $cfg['web_root'] . 'script.php'; ?>' # or set JIRAFEAU_URL ex: url='http://mysite/jirafeau/script.php'
time='none' # minute, hour, day, week, month or none. Or set JIRAFEAU_TIME.
one_time='' # ex: one_time="1" or set JIRAFEAU_ONE_TIME.
curl='' # curl path to download or set JIRAFEAU_CURL_PATH.
# End of config

if [ -n "$JIRAFEAU_PROXY" ]; then
    proxy="$JIRAFEAU_PROXY"
fi

if [ -n "$JIRAFEAU_URL" ]; then
    url="$JIRAFEAU_URL"
fi

if [ -z "$url" ]; then
    echo "Please set url in script parameters or export JIRAFEAU_URL"
fi

if [ -n "$JIRAFEAU_TIME" ]; then
    time="$JIRAFEAU_TIME"
fi

if [ -n "$JIRAFEAU_ONE_TIME" ]; then
    one_time='1'
fi

if [ -z "$curl" ]; then
    curl="$JIRAFEAU_CURL_PATH"
fi

if [ -z "$curl" ] && [ -e "/usr/bin/curl" ]; then
    curl="/usr/bin/curl"
fi

if [ -z "$curl" ] && [ -e "/bin/curl.exe" ]; then
    curl="/bin/curl.exe"
fi

if [ -z "$curl" ]; then
    echo "Please set your curl binary path (by editing this script or export JIRAFEAU_CURL_PATH global variable)."
    exit
fi

if [ -z "$2" ]; then
    echo "man:"
    echo "    $0 send PATH [PASSWORD]"
    echo "    $0 get URL [PASSWORD]"
    echo "    $0 delete URL"
    echo ""
    echo "Global variables to export:"
    echo "    JIRAFEAU_PROXY : example: proxysever.test.com:3128"
    echo "    JIRAFEAU_URL : example: http://mysite/jirafeau/script.php"
    echo "    JIRAFEAU_TIME : minute, hour, day, week, month or none"
    echo "    JIRAFEAU_ONE_TIME : set anything or set empty"
    echo "    JIRAFEAU_CURL : path to your curl binary"

    exit 0
fi

if [ -n "$proxy" ]; then
    proxy="-x $proxy"
fi

options=''
if [ -n "$one_time" ]; then
    options="$options -F one_time_download=1"
fi

password=''
if [ -n "$3" ]; then
    password="$3"
    options="$options -F key=$password"
fi

if [ "$1" == "send" ]; then
    if [ ! -f "$2" ]; then
        echo "File \"$2\" does not exists."
        exit
    fi

    # Ret result
    res=$($curl -X POST --http1.0 $proxy $options \
                  -F "time=$time" \
                  -F "file=@$2" \
                  $url)

    if [[ "$res" == "Error" ]]; then
        echo "Error while uploading."
        exit
    fi

    # Not using head or tail to minimise command dependencies
    code=$(cnt=0; echo "$res" | while read l; do
        if [[ "$cnt" == "0" ]]; then
            echo "$l"
        fi
        cnt=$(( cnt + 1 ))
        done)
    del_code=$(cnt=0; echo "$res" | while read l; do
        if [[ "$cnt" == "1" ]]; then
            echo "$l"
        fi
        cnt=$(( cnt + 1 ))
        done)
    echo "${url}?h=$code"
    echo "${url}?h=$code&d=$del_code"
elif [ "$1" == "get" ]; then
    if [ -z "$password" ]; then
        $curl $proxy -OJ "$2"
    else
        $curl $proxy -OJ -X POST -F key=$password "$2"
    fi
elif [ "$1" == "delete" ]; then
    $curl $proxy "$2"
fi
<?php
    }
    else
    {
        echo "Error";
        exit;
    }
}
/* Initialize an asynchronous upload. */
elseif (isset ($_GET['init_async']))
{
    if (strlen ($cfg['upload_password']) > 0 && (!isset ($_POST['upload_password']) || $_POST['upload_password'] != $cfg['upload_password']))
    {
        echo "Error";
        exit;
    }

    if (!isset ($_POST['filename']))
    {
        echo "Error";
        exit;
    }

    $type = '';
    if (isset ($_POST['type']))
        $type = $_POST['type'];
    
    $key = '';
    if (isset ($_POST['key']))
        $key = $_POST['key'];

    $time = time ();
    if (!isset ($_POST['time']))
        $time = JIRAFEAU_INFINITY;
    else
        switch ($_POST['time'])
        {
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
    echo jirafeau_async_init ($_POST['filename'],
                              $type,
                              isset ($_POST['one_time_download']),
                              $key,
                              $time,
                              $_SERVER['REMOTE_ADDR']);
}
/* Continue an asynchronous upload. */
elseif (isset ($_GET['push_async']))
{
    if ((!isset ($_POST['ref']))
        || (!isset ($_FILES['data']))
        || (!isset ($_POST['code'])))
        echo "Error";
    else
        echo jirafeau_async_push ($_POST['ref'], $_FILES['data'], $_POST['code']);                                      
}
/* Finalize an asynchronous upload. */
elseif (isset ($_GET['end_async']))
{
    if (!isset ($_POST['ref'])
        || !isset ($_POST['code']))
        echo "Error";
    else
        echo jirafeau_async_end ($_POST['ref'], $_POST['code'], $cfg['enable_crypt'], $cfg['link_name_lenght']);
}
/* Initialize block. */
elseif (isset ($_GET['init_block']) && $cfg['enable_blocks'])
{
    if (strlen ($cfg['upload_password']) > 0 && (!isset ($_POST['upload_password']) || $_POST['upload_password'] != $cfg['upload_password']))
    {
        echo "Error";
        exit;
    }

    if (!isset ($_POST['size']))
        echo "Error";
    else
        echo jirafeau_block_init ($_POST['size']);
}
/* Get block size. */
elseif (isset ($_GET['get_block_size']) && $cfg['enable_blocks'])
{
    if (!isset ($_POST['id']))
        echo "Error";
    else
        echo jirafeau_block_get_size ($_POST['id']);
}
/* Read data in block. */
elseif (isset ($_GET['read_block']) && $cfg['enable_blocks'])
{
    if (!isset ($_POST['id'])
        || !isset ($_POST['start'])
        || !isset ($_POST['length']))
        echo "Error";
    else
        jirafeau_block_read ($_POST['id'], $_POST['start'], $_POST['length']);
}
/* Write data in block. */
elseif (isset ($_GET['write_block']) && $cfg['enable_blocks'])
{
    if (!isset ($_POST['id'])
        || !isset ($_POST['start'])
        || !isset ($_FILES['data'])
        || !isset ($_POST['code']))
        echo "Error";
    else
        echo jirafeau_block_write ($_POST['id'], $_POST['start'], $_FILES['data'], $_POST['code']);
}
/* Delete block. */
elseif (isset ($_GET['delete_block']) && $cfg['enable_blocks'])
{
    if (!isset ($_POST['id'])
        || !isset ($_POST['code']))
        echo "Error";
    else
        echo jirafeau_block_delete ($_POST['id'], $_POST['code']);
}
else
    echo "Error";
exit;
?>

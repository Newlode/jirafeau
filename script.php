<?php
/*
 *  Jirafeau, your web file repository
 *  Copyright (C) 2015  Jerome Jutteau <j.jutteau@gmail.com>
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
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/*
 * This file permits to easyly script file sending, receiving, deleting, ...
 * If you don't want this feature, you can simply delete this file from your
 * web directory.
 */
define ('JIRAFEAU_ROOT', dirname (__FILE__) . '/');

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
    ?>
    <div class="info">
    <h2>Scripting interface</h2>
    <p>This interface permits to script your uploads and downloads.</p>
    <p>See <a href="https://gitlab.com/mojo42/Jirafeau/blob/master/script.php">source code</a> of this interface to get available calls :)</p>
    <p>Alternatively, go to <a href="<?php echo $cfg['web_root'] . 'script.php?lang=bash'; ?>">this page</a> to download a bash script.</p>
    </div>
    <br />
    <?php
    require (JIRAFEAU_ROOT . 'lib/template/footer.php');
    exit;
}

/* Lets use interface now. */
header('Content-Type: text/plain; charset=utf-8');

check_errors ($cfg);
if (has_error ())
{
    echo 'Error 1';
    exit;
}

/* Upload file */
if (isset ($_FILES['file']) && is_writable (VAR_FILES)
    && is_writable (VAR_LINKS))
{
    if (!jirafeau_challenge_upload_ip ($cfg, get_ip_address($cfg)))
    {
        echo 'Error 2';
        exit;
    }

    if (jirafeau_has_upload_password ($cfg) &&
         (!isset ($_POST['upload_password']) ||
          !jirafeau_challenge_upload_password ($cfg, $_POST['upload_password'])))
    {
        echo 'Error 3';
        exit;
    }

    $key = '';
    if (isset ($_POST['key']))
        $key = $_POST['key'];

    $time = time ();
    if (!isset ($_POST['time']) || !$cfg['availabilities'][$_POST['time']])
    {
        echo 'Error 4: The parameter time is invalid.';
        exit;
    }
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
            case 'quarter':
                $time += JIRAFEAU_QUARTER;
                break;
            case 'year':
                $time += JIRAFEAU_YEAR;
                break;
           default:
                $time = JIRAFEAU_INFINITY;
                break;
        }

    // Check file size
    if ($cfg['maximal_upload_size'] > 0 &&
        $_FILES['file']['size'] > $cfg['maximal_upload_size'] * 1024 * 1024)
    {
        echo 'Error 5: Your file exceeds the maximum authorized file size.';
        exit;
    }

    $res = jirafeau_upload ($_FILES['file'],
                            isset ($_POST['one_time_download']),
                            $key, $time, get_ip_address($cfg),
                            $cfg['enable_crypt'], $cfg['link_name_length']);

    if (empty($res) || $res['error']['has_error'])
    {
        echo 'Error 6 ' . $res['error']['why'];
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
        echo 'Error 7';
        exit;
    }

    $link = jirafeau_get_link ($link_name);
    if (count ($link) == 0)
    {
        echo 'Error 8';
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
        echo 'Error 9';
        exit;
    }
    if (strlen ($link['key']) > 0 && md5 ($key) != $link['key'])
    {
        sleep (2);
        echo 'Error 10';
        exit;
    }
    $p = s2p ($link['md5']);
    if (!file_exists (VAR_FILES . $p . $link['md5']))
    {
        echo 'Error 11';
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
elseif (isset ($_GET['get_maximal_upload_size']))
{
    echo $cfg['maximal_upload_size'];
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

# This script has been auto-generated by Jirafeau but you can still edit options below.

# Config begin
proxy='' # Or set JIRAFEAU_PROXY.
url='<?php echo $cfg['web_root']; ?>' # Or set JIRAFEAU_URL.
time='<?php echo $cfg['availability_default']; ?>' # Or set JIRAFEAU_TIME.
one_time='' # Or set JIRAFEAU_ONE_TIME.
curl='' # Or set JIRAFEAU_CURL_PATH.
# Config end

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
    echo "Jirafeau Bash Script <?php echo JIRAFEAU_VERSION; ?>"
    echo "--------------------------"
    echo "Usage:"
    echo "    $0 OPTIONS"
    echo
    echo "Options:"
    echo "    $0 send FILE [PASSWORD]"
    echo "    $0 get URL [PASSWORD]"
    echo "    $0 delete URL"
    echo
    echo "Global variables to export:"
    echo "    JIRAFEAU_PROXY: Domain and port of proxy server, eg. »proxysever.example.com:3128«"
    echo "    JIRAFEAU_URL : URI to Jirafeau installation with trailing slash, eg. »https://example.com/jirafeau/«"
    echo "    JIRAFEAU_TIME : expiration time, eg. »minute«, »hour«, »day«, »week«, »month«, »quarter«, »year« or »none«"
    echo "    JIRAFEAU_ONE_TIME : self-destroy after first download, eg. »1« to enable or »« (empty) to disable"
    echo "    JIRAFEAU_CURL : alternative path to curl binary"

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

apipage='script.php'
downloadpage='f.php'

if [ "$1" == "send" ]; then
    if [ ! -f "$2" ]; then
        echo "File \"$2\" does not exists."
        exit
    fi

    # Ret result
    res=$($curl -X POST --http1.0 $proxy $options \
                  -F "time=$time" \
                  -F "file=@$2" \
                  $url$apipage)

    if [[ "$res" == Error* ]]; then
        echo "Error while uploading."
        echo $res
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

    echo
    echo "Download page:"
    echo "    ${url}${downloadpage}?h=$code"
    echo "Direct download:"
    echo "    ${url}${downloadpage}?h=$code&d=1"
    echo "Delete link:"
    echo "    ${url}${downloadpage}?h=$code&d=$del_code"
    echo
    echo "Download via API:"
    echo "    ${0} get ${url}${apipage}?h=$code [PASSWORD}"
    echo "Delete via API:"
    echo "    ${0} delete ${url}${downloadpage}?h=$code&d=$del_code"

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
        echo 'Error 12';
        exit;
    }
}
/* Create alias. */
elseif (isset ($_GET['alias_create']))
{
    $ip = get_ip_address($cfg);
    if (!jirafeau_challenge_upload_ip ($cfg, $ip))
    {
        echo 'Error 13';
        exit;
    }

    if (jirafeau_has_upload_password ($cfg) &&
         (!isset ($_POST['upload_password']) ||
          !jirafeau_challenge_upload_password ($cfg, $_POST['upload_password'])))
    {
        echo 'Error 14';
        exit;
    }

    if (!isset ($_POST['alias']) ||
        !isset ($_POST['destination']) ||
        !isset ($_POST['password']))
    {
        echo 'Error 15';
        exit;
    }

    echo jirafeau_alias_create ($_POST['alias'],
                                $_POST['destination'],
                                $_POST['password'],
                                $ip);
}
/* Get alias. */
elseif (isset ($_GET['alias_get']))
{
    if (!isset ($_POST['alias']))
    {
        echo 'Error 16';
        exit;
    }

    echo jirafeau_alias_get ($_POST['alias']);
}
/* Update alias. */
elseif (isset ($_GET['alias_update']))
{
    if (!isset ($_POST['alias']) ||
        !isset ($_POST['destination']) ||
        !isset ($_POST['password']))
    {
        echo 'Error 17';
        exit;
    }

    $new_password = '';
    if (isset ($_POST['new_password']))
        $new_password = $_POST['new_password'];

    echo jirafeau_alias_update ($_POST['alias'],
                                $_POST['destination'],
                                $_POST['password'],
                                $new_password,
                                get_ip_address($cfg));
}
/* Delete alias. */
elseif (isset ($_GET['alias_delete']))
{
    if (!isset ($_POST['alias']) ||
        !isset ($_POST['password']))
    {
        echo 'Error 18';
        exit;
    }

    echo jirafeau_alias_delete ($_POST['alias'],
                                $_POST['password']);
}
/* Initialize an asynchronous upload. */
elseif (isset ($_GET['init_async']))
{
    if (!jirafeau_challenge_upload_ip ($cfg, get_ip_address($cfg)))
    {
        echo 'Error 19';
        exit;
    }

    if (jirafeau_has_upload_password ($cfg) &&
         (!isset ($_POST['upload_password']) ||
          !jirafeau_challenge_upload_password ($cfg, $_POST['upload_password'])))
    {
        echo 'Error 20';
        exit;
    }

    if (!isset ($_POST['filename']))
    {
        echo 'Error 21';
        exit;
    }

    $type = '';
    if (isset ($_POST['type']))
        $type = $_POST['type'];

    $key = '';
    if (isset ($_POST['key']))
        $key = $_POST['key'];

    $time = time ();
    if (!isset ($_POST['time']) || !$cfg['availabilities'][$_POST['time']])
    {
        echo 'Error 22';
        exit;
    }
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
            case 'quarter':
                $time += JIRAFEAU_QUARTER;
                break;
            case 'year':
                $time += JIRAFEAU_YEAR;
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
                              get_ip_address($cfg));
}
/* Continue an asynchronous upload. */
elseif (isset ($_GET['push_async']))
{
    if ((!isset ($_POST['ref']))
        || (!isset ($_FILES['data']))
        || (!isset ($_POST['code'])))
        echo 'Error 23';
    else
    {
        echo jirafeau_async_push ($_POST['ref'],
                                  $_FILES['data'],
                                  $_POST['code'],
                                  $cfg['maximal_upload_size']);
    }
}
/* Finalize an asynchronous upload. */
elseif (isset ($_GET['end_async']))
{
    if (!isset ($_POST['ref'])
        || !isset ($_POST['code']))
        echo 'Error 24';
    else
        echo jirafeau_async_end ($_POST['ref'], $_POST['code'], $cfg['enable_crypt'], $cfg['link_name_length']);
}
else
    echo 'Error 25';
exit;
?>

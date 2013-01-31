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
define ('JIRAFEAU_ROOT', dirname (__FILE__) . '/');

require (JIRAFEAU_ROOT . 'lib/lang.php');
require (JIRAFEAU_ROOT . 'lib/config.php');
require (JIRAFEAU_ROOT . 'lib/settings.php');
require (JIRAFEAU_ROOT . 'lib/functions.php');

if (!isset ($_GET['h']) || empty ($_GET['h']))
{
    header ('Location: ' . $cfg['web_root']);
    exit;
}

$link_name = $_GET['h'];

if (!preg_match ('/[0-9a-zA-Z_-]{22}$/', $link_name))
{
    require (JIRAFEAU_ROOT.'lib/template/header.php');
    echo '<div class="error"><p>' . t('Sorry, the requested file is not found') . '</p></div>';
    require (JIRAFEAU_ROOT.'lib/template/footer.php');
    exit;
}

$link = jirafeau_get_link ($link_name);
if (count ($link) == 0)
{
    require (JIRAFEAU_ROOT.'lib/template/header.php');
    echo '<div class="error"><p>' . t('Sorry, the requested file is not found') .
    '</p></div>';
    require (JIRAFEAU_ROOT.'lib/template/footer.php');
    exit;
}

$delete_code = '';
if (isset ($_GET['d']) && !empty ($_GET['d']))
    $delete_code = $_GET['d'];

$button_download = false;
if (isset ($_GET['bd']) && !empty ($_GET['bd']))
    $button_download = true;

$button_preview = false;
if (isset ($_GET['bp']) && !empty ($_GET['bp']))
    $button_preview = true;

$p = s2p ($link['md5']);
if (!file_exists (VAR_FILES . $p . $link['md5']))
{
    jirafeau_delete_link ($link_name);
    require (JIRAFEAU_ROOT.'lib/template/header.php');
    echo '<div class="error"><p>'.t('File not available.').
    '</p></div>';
    require (JIRAFEAU_ROOT.'lib/template/footer.php');
    exit;
}

if (!empty ($delete_code) && $delete_code == $link['link_code'])
{
    jirafeau_delete_link ($link_name);
    require (JIRAFEAU_ROOT.'lib/template/header.php');
    echo '<div class="message"><p>'.t('File has been deleted.').
     '</p></div>';
    require (JIRAFEAU_ROOT.'lib/template/footer.php');
    exit;
}

if ($link['time'] != JIRAFEAU_INFINITY && time () > $link['time'])
{
    jirafeau_delete_link ($link_name);
    require (JIRAFEAU_ROOT.'lib/template/header.php');
    echo '<div class="error"><p>'.
    t('The time limit of this file has expired.') . ' ' .
    t('File has been deleted.') .
    '</p></div>';
    require (JIRAFEAU_ROOT . 'lib/template/footer.php');
    exit;
}

$password_challenged = false;
if (!empty ($link['key']))
{
    if (!isset ($_POST['key']))
    {
        require (JIRAFEAU_ROOT.'lib/template/header.php');
        echo '<div>' .
             '<form action = "' . $_SERVER['REQUEST_URI'] . '" ' .
                'method = "post" id = "submit">'; ?>
             <input type = "hidden" name = "jirafeau" value = "<?php echo JIRAFEAU_VERSION ?>"/><?php
        echo '<fieldset>' .
             '<legend>' . t('Password protection') .
             '</legend><table><tr><td>' .
             t('Give the password of this file') . ' : ' .
             '<input type = "password" name = "key" />' .
             '</td></tr>' .
             '<tr><td>' .
             t('By using our services, you accept of our'). ' <a href="' . $cfg['web_root'] . '/tos.php' . '">' . t('Term Of Service') . '</a>' .
             '</td></tr>' .
             '<tr><td>';
            ?><input type="submit" id = "submit_download"  value="<?php echo t('Download'); ?>"
            onclick="document.getElementById('submit').action='<?php echo $_SERVER['REQUEST_URI'] ?>&amp;bd=1';
                     document.getElementById('submit_download').submit ();"/><?php

        if ($cfg['download_page'] && $cfg['preview'])
        {
            ?><input type="submit" id = "submit_preview"  value="<?php echo t('Preview'); ?>"
            onclick="document.getElementById('submit').action='<?php echo $_SERVER['REQUEST_URI'] ?>&amp;bp=1';
                     document.getElementById('submit_preview').submit ();"/><?php
        }
        echo '</td></tr></table></fieldset></form></div>';
        require (JIRAFEAU_ROOT.'lib/template/footer.php');
        exit;
    }
    else
    {
        if ($link['key'] != md5 ($_POST['key']))
        {
            header ("Access denied");
            require (JIRAFEAU_ROOT.'lib/template/header.php');
            echo '<div class="error"><p>' . t('Access denied') .
            '</p></div>';
            require (JIRAFEAU_ROOT.'lib/template/footer.php');
            exit;
        }
        else
            $password_challenged = true;
    }
}

if ($cfg['download_page'] && !$password_challenged && !$button_download && !$button_preview)
{
        require (JIRAFEAU_ROOT.'lib/template/header.php');
        echo '<div>' .
             '<form action = "' . $_SERVER['REQUEST_URI'] . '" ' .
                'method = "post" id = "submit">'; ?>
             <input type = "hidden" name = "jirafeau" value = "<?php echo JIRAFEAU_VERSION ?>"/><?php
        echo '<fieldset><legend>' . $link['file_name'] . '</legend><table>' .
             '<tr><td>' .
             t('You are about to download') . ' "' . $link['file_name'] . '" (' . jirafeau_human_size($link['file_size']) . ')' .
             '</td></tr>' .
             '<tr><td>' .
             t('By using our services, you accept of our'). ' <a href="' . $cfg['web_root'] . '/tos.php' . '">' . t('Term Of Service') . '</a>';
            ?><input type="submit" id = "submit_download"  value="<?php echo t('Download'); ?>"
            onclick="document.getElementById('submit').action='<?php echo $_SERVER['REQUEST_URI'] ?>&amp;bd=1';
                     document.getElementById('submit_download').submit ();"/><?php

        if ($cfg['download_page'] && $cfg['preview'])
        {
            ?><input type="submit" id = "submit_preview"  value="<?php echo t('Preview'); ?>"
            onclick="document.getElementById('submit').action='<?php echo $_SERVER['REQUEST_URI'] ?>&amp;bp=1';
                     document.getElementById('submit_preview').submit ();"/><?php
        }
        echo '</td></tr>';
        echo '</table></fieldset></form></div>';
        require (JIRAFEAU_ROOT.'lib/template/footer.php');
        exit;
}

header ('Content-Length: ' . $link['file_size']);
header ('Content-Type: ' . $link['mime_type']);
if (!jirafeau_is_viewable ($link['mime_type']) || !$cfg['preview'] || $button_download)
{
    header ('Content-Disposition: attachment; filename="' .
        $link['file_name'] . '"');
}
readfile (VAR_FILES . $p . $link['md5']);

if ($link['onetime'] == 'O')
    jirafeau_delete_link ($link_name);
exit;

?>

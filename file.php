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

if (isset ($_GET['h']) && !empty ($_GET['h']))
{
    $link_name = $_GET['h'];

    $delete_code = '';
    if (isset ($_GET['d']) && !empty ($_GET['d']))
        $delete_code = $_GET['d'];

    if (!preg_match ('/[0-9a-f]{32}$/', $link_name))
    {
        require (JIRAFEAU_ROOT.'lib/template/header.php');
        echo '<div class="error"><p>' . _('Sorry, the requested file is not found') . '</p></div>';
        require (JIRAFEAU_ROOT.'lib/template/footer.php');
        exit;
    }

    $link = jirafeau_get_link ($link_name);
    if (count ($link) == 0)
    {
        require (JIRAFEAU_ROOT.'lib/template/header.php');
        echo '<div class="error"><p>' . _('Sorry, the requested file is not found') .
        '</p></div>';
        require (JIRAFEAU_ROOT.'lib/template/footer.php');
        exit;
    }
    
    if (!file_exists (VAR_FILES . $link['md5']))
    {
        jirafeau_delete ($link_name);
        require (JIRAFEAU_ROOT.'lib/template/header.php');
        echo '<div class="error"><p>'._('File not available.').
        '</p></div>';
        require (JIRAFEAU_ROOT.'lib/template/footer.php');
        exit;
    }

    if (!empty ($delete_code) && $delete_code == $link['link_code'])
    {
        jirafeau_delete ($link_name);
        require (JIRAFEAU_ROOT.'lib/template/header.php');
        echo '<div class="message"><p>'._('File has been deleted.').
         '</p></div>';
        require (JIRAFEAU_ROOT.'lib/template/footer.php');
        exit;
    }

    if ($link['time'] != JIRAFEAU_INFINITY && time ()> $link['time'])
    {
        jirafeau_delete ($link_name);
        require (JIRAFEAU_ROOT.'lib/template/header.php');
        echo '<div class="error"><p>'.
        _('The time limit of this file has expired.') . ' ' .
        _('File has been deleted.') .
        '</p></div>';
        require (JIRAFEAU_ROOT.'lib/template/footer.php');
        exit;
    }

    if (!empty ($link['key']))
    {
        if (!isset ($_POST['key']))
        {
        require (JIRAFEAU_ROOT.'lib/template/header.php');
        ?><div id = "upload">
            <form action =
            "<?php echo $_SERVER['REQUEST_URI']; ?>" method =
            "post"> <input type = "hidden" name = "jirafeau" value =
            "<?php echo JIRAFEAU_VERSION; ?>" /><fieldset>
            <legend><?php echo _('Password protection');
        ?></legend> <table> <tr>
            <td><?php echo _('Give the password of this file') . ' : ';
        ?><input type = "password" name =
            "key" /></td> </tr> <tr> <td><input type =
            "submit" value =
            "<?php echo _('Download'); ?>"
            /></td> </tr> </table> </fieldset> </form> </div>
            <?php require (JIRAFEAU_ROOT.'lib/template/footer.php');
        exit;
        }
        else
        {
        if ($link['key'] != md5 ($_POST['key']))
        {
            header ("Access denied");

            require (JIRAFEAU_ROOT.'lib/template/header.php');
            echo '<div class="error"><p>' . _('Access denied') .
            '</p></div>';
            require (JIRAFEAU_ROOT.'lib/template/footer.php');
            exit;
        }
        }
    }

    header ('Content-Length: ' . $link['file_size']);
    header ('Content-Type: ' . $link['mime_type']);
    if (!jirafeau_is_viewable ($link['mime_type']))
    {
        header ('Content-Disposition: attachment; filename="' .
            $link['file_name'] . '"');
    }
    readfile (VAR_FILES . $link['md5']);

    if ($link['onetime'] == 'O')
        jirafeau_delete ($link_name);
    exit;
}
else
{
    header ('Location: '.$cfg['web_root']);
    exit;
}

?>

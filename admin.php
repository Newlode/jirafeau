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
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
define ('JIRAFEAU_ROOT', dirname (__FILE__) . '/');

require (JIRAFEAU_ROOT . 'lib/config.original.php');
require (JIRAFEAU_ROOT . 'lib/settings.php');
require (JIRAFEAU_ROOT . 'lib/functions.php');
require (JIRAFEAU_ROOT . 'lib/lang.php');

/* Check if installation is OK. */
if (file_exists (JIRAFEAU_ROOT . 'install.php')
    && !file_exists (JIRAFEAU_ROOT . 'lib/config.local.php'))
{
    header('Location: install.php'); 
    exit;
}

/* Disable admin interface if we have a empty admin password. */
if (empty($cfg['admin_password']) && empty($cfg['admin_http_auth_user']))
{
    require (JIRAFEAU_ROOT . 'lib/template/header.php');
    echo '<div class="error"><p>'.
         t('Sorry, the admin interface is not enabled.') .
         '</p></div>';
    require (JIRAFEAU_ROOT.'lib/template/footer.php');
    exit;
}

/* Check session. */
session_start();

/* Unlog if asked. */
if (isset ($_POST['action']) && (strcmp ($_POST['action'], 'logout') == 0))
    $_SESSION['admin_auth'] = false;

/* Check classic admin password authentification. */
if (isset ($_POST['admin_password']) && empty($cfg['admin_http_auth_user']))
{
    if ($cfg['admin_password'] === $_POST['admin_password'])
        $_SESSION['admin_auth'] = true;
    else
    {
        $_SESSION['admin_auth'] = false;
        require (JIRAFEAU_ROOT . 'lib/template/header.php');
        echo '<div class="error"><p>'.
             t('Wrong password.') . '</p></div>';
        require (JIRAFEAU_ROOT.'lib/template/footer.php');
        exit;
    }
}
/* Ask for classic admin password authentification. */
elseif ((!isset ($_SESSION['admin_auth']) || $_SESSION['admin_auth'] != true)
        && empty($cfg['admin_http_auth_user']))
{
    require (JIRAFEAU_ROOT . 'lib/template/header.php'); ?>
    <form action = "<?php echo basename(__FILE__); ?>" method = "post">
    <fieldset>
        <table>
        <tr>
            <td class = "label"><label for = "enter_password">
            <?php echo t('Administration password') . ':';?></label>
            </td>
            <td class = "field"><input type = "password"
            name = "admin_password" id = "admin_password"
            size = "40" />
            </td>
        </tr>
        <tr class = "nav">
            <td></td>
            <td class = "nav next">
            <input type = "submit" name = "key" value =
            "<?php echo t('Login'); ?>" />
            </td>
        </tr>
        </table>
    </fieldset>
    </form>
    <?php
    require (JIRAFEAU_ROOT.'lib/template/footer.php');
    exit;
}
/* Check authenticated user if HTTP authentification is enable. */
elseif ((!isset ($_SESSION['admin_auth']) || $_SESSION['admin_auth'] != true)
        && !empty($cfg['admin_http_auth_user']))
{
    if ($cfg['admin_http_auth_user'] == $_SERVER['PHP_AUTH_USER'])
        $_SESSION['admin_auth'] = true;
}

/* Be sure that no one can access further without admin_auth. */
if (!isset ($_SESSION['admin_auth']) || $_SESSION['admin_auth'] != true)
{
         $_SESSION['admin_auth'] = false;
        require (JIRAFEAU_ROOT . 'lib/template/header.php');
        echo '<div class="error"><p>'.
         t('Sorry, you are not authenticated on admin interface.') .
         '</p></div>';
        require (JIRAFEAU_ROOT.'lib/template/footer.php');
        exit;
}

/* Operations may take a long time.
 * Be sure PHP's safe mode is off.
 */
@set_time_limit(0);
/* Remove errors. */
@error_reporting(0);

/* Show admin interface if not downloading a file. */
if (!(isset ($_POST['action']) && strcmp ($_POST['action'], 'download') == 0))
{
        require (JIRAFEAU_ROOT . 'lib/template/header.php');
        ?><h2><?php echo t('Admin interface'); ?></h2><?php
        ?><h2>(version <?php echo JIRAFEAU_VERSION ?>)</h2><?php

        ?><div id = "admin">
        <fieldset><legend><?php echo t('Actions');?></legend>
        <table>
        <form action = "<?php echo basename(__FILE__); ?>" method = "post">
        <tr>
            <input type = "hidden" name = "action" value = "clean"/>
            <td class = "info">
                <?php echo t('Clean expired files'); ?>
            </td>
            <td></td>
            <td>
                <input type = "submit" value = "<?php echo t('Clean'); ?>" />
            </td>
        </tr>
        </form>
        <form action = "<?php echo basename(__FILE__); ?>" method = "post">
        <tr>
            <input type = "hidden" name = "action" value = "clean_async"/>
            <td class = "info">
                <?php echo t('Clean old unfinished transfers'); ?>
            </td>
            <td></td>
            <td>
                <input type = "submit" value = "<?php echo t('Clean'); ?>" />
            </td>
        </tr>
        </form>
        <form action = "<?php echo basename(__FILE__); ?>" method = "post">
        <tr>
            <input type = "hidden" name = "action" value = "list"/>
            <td class = "info">
                <?php echo t('List all files'); ?>
            </td>
            <td></td>
            <td>
                <input type = "submit" value = "<?php echo t('List'); ?>" />
            </td>
        </tr>
        </form>
        <form action = "<?php echo basename(__FILE__); ?>" method = "post">
        <tr>
            <input type = "hidden" name = "action" value = "search_by_name"/>
            <td class = "info">
                <?php echo t('Search files by name'); ?>
            </td>
            <td>
                <input type = "text" name = "name" id = "name"/>
            </td>
            <td>
                <input type = "submit" value = "<?php echo t('Search'); ?>" />
            </td>
        </tr>
        </form>
        <form action = "<?php echo basename(__FILE__); ?>" method = "post">
        <tr>
            <input type = "hidden" name = "action" value = "search_by_file_hash"/>
            <td class = "info">
                <?php echo t('Search files by file hash'); ?>
            </td>
            <td>
                <input type = "text" name = "hash" id = "hash"/>
            </td>
            <td>
                <input type = "submit" value = "<?php echo t('Search'); ?>" />
            </td>
        </tr>
        </form>
        <form action = "<?php echo basename(__FILE__); ?>" method = "post">
        <tr>
            <input type = "hidden" name = "action" value = "search_link"/>
            <td class = "info">
                <?php echo t('Search a specific link'); ?>
            </td>
            <td>
                <input type = "text" name = "link" id = "link"/>
            </td>
            <td>
                <input type = "submit" value = "<?php echo t('Search'); ?>" />
            </td>
        </tr>
        </form>
        </table>
        <form action = "<?php echo basename(__FILE__); ?>" method = "post">
            <input type = "hidden" name = "action" value = "logout" />
            <input type = "submit" value = "<?php echo t('Logout'); ?>" />
        </form>
        </fieldset></div><?php
}

/* Check for actions */
if (isset ($_POST['action']))
{
    if (strcmp ($_POST['action'], 'clean') == 0)
    {
        $total = jirafeau_admin_clean ();
        echo '<div class="message">' . NL;
        echo '<p>';
        echo t('Number of cleaned files') . ' : ' . $total;
        echo '</p></div>';
    }
    elseif (strcmp ($_POST['action'], 'clean_async') == 0)
    {
        $total = jirafeau_admin_clean_async ();
        echo '<div class="message">' . NL;
        echo '<p>';
        echo t('Number of cleaned files') . ' : ' . $total;
        echo '</p></div>';
    }
    elseif (strcmp ($_POST['action'], 'list') == 0)
    {
        jirafeau_admin_list ("", "", "");
    }
    elseif (strcmp ($_POST['action'], 'search_by_name') == 0)
    {
        jirafeau_admin_list ($_POST['name'], "", "");
    }
    elseif (strcmp ($_POST['action'], 'search_by_file_hash') == 0)
    {
        jirafeau_admin_list ("", $_POST['hash'], "");
    }
    elseif (strcmp ($_POST['action'], 'search_link') == 0)
    {
        jirafeau_admin_list ("", "", $_POST['link']);
    }
    elseif (strcmp ($_POST['action'], 'delete_link') == 0)
    {
        jirafeau_delete_link ($_POST['link']);
        echo '<div class="message">' . NL;
        echo '<p>' . t('Link deleted') . '</p></div>';
    }
    elseif (strcmp ($_POST['action'], 'delete_file') == 0)
    {
        $count = jirafeau_delete_file ($_POST['md5']);
        echo '<div class="message">' . NL;
        echo '<p>' . t('Deleted links') . ' : ' . $count . '</p></div>';
    }
    elseif (strcmp ($_POST['action'], 'download') == 0)
    {
        $l = jirafeau_get_link ($_POST['link']);
        if (!count ($l))
            return;
        $p = s2p ($l['md5']);
        header ('Content-Length: ' . $l['file_size']);
        header ('Content-Type: ' . $l['mime_type']);
        header ('Content-Disposition: attachment; filename="' .
                $l['file_name'] . '"');
        if (file_exists(VAR_FILES . $p . $l['md5']))
            readfile (VAR_FILES . $p . $l['md5']);
        exit;
    }
}

require (JIRAFEAU_ROOT.'lib/template/footer.php');

?>

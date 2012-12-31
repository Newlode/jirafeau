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
 
define ('JIRAFEAU_ROOT', dirname (__FILE__) . '/');

require (JIRAFEAU_ROOT . 'lib/config.php');
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

/* Check if the install.php script is still in the directory. */
if (file_exists (JIRAFEAU_ROOT . 'install.php'))
{
    require (JIRAFEAU_ROOT . 'lib/template/header.php');
    echo '<div class="error"><p>'.
         t('Installer script still present') .
         '</p></div>';
    require (JIRAFEAU_ROOT.'lib/template/footer.php');
    exit;
}

/* Disable admin interface if we have a empty admin password. */
if (!$cfg['admin_password'])
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

/* Check password. */
if (isset ($_POST['admin_password']))
{
    if (strcmp ($cfg['admin_password'], $_POST['admin_password']) == 0)
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
/* Ask for password. */
elseif (!isset ($_SESSION['admin_auth']) || $_SESSION['admin_auth'] != true)
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

/* Admin interface. */
require (JIRAFEAU_ROOT . 'lib/template/header.php');
?><h2><?php echo t('Admin interface'); ?></h2><?php

/* Show admin interface. */
{
        ?><div id = "install">
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
        <input type = "hidden" name = "action" value = "logout"/>
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
        jirafeau_delete ($_POST['link']);
        echo '<div class="message">' . NL;
        echo '<p>' . t('Link deleted') . '</p></div>';
    }
    elseif (strcmp ($_POST['action'], 'delete_file') == 0)
    {
        $count = jirafeau_delete_file ($_POST['md5']);
        echo '<div class="message">' . NL;
        echo '<p>' . t('Deleted links') . ' : ' . $count . '</p></div>';
    }
}

require (JIRAFEAU_ROOT.'lib/template/footer.php');

?>
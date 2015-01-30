<?php
/*
 *  Jirafeau, your web file repository
 *  Copyright (C) 2013
 *  Jerome Jutteau <j.jutteau@gmail.com>
 *  Jimmy Beauvois <jimmy.beauvois@gmail.com>
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
require (JIRAFEAU_ROOT . 'lib/template/header.php');

check_errors ($cfg);
if (has_error ())
{
    show_errors ();
    require (JIRAFEAU_ROOT . 'lib/template/footer.php');
    exit;
}

/* Ask password if upload password is set. */
if (strlen ($cfg['upload_password']) > 0)
{
    session_start();

    /* Unlog if asked. */
    if (isset ($_POST['action']) && (strcmp ($_POST['action'], 'logout') == 0))
        $_SESSION['upload_auth'] = false;

    /* Auth. */
    if (isset ($_POST['upload_password']))
    {
        if (strcmp ($cfg['upload_password'], $_POST['upload_password']) == 0)
            $_SESSION['upload_auth'] = true;
        else
        {
            $_SESSION['admin_auth'] = false;
            echo '<div class="error"><p>' . t('Wrong password.') . '</p></div>';
            require (JIRAFEAU_ROOT.'lib/template/footer.php');
            exit;
        }
    }

    /* Show auth page. */
    if (!isset ($_SESSION['upload_auth']) || $_SESSION['upload_auth'] != true)
    {
	?>
        <form action = "<?php echo basename(__FILE__); ?>" method = "post">
        <fieldset>
            <table>
            <tr>
                <td class = "label"><label for = "enter_password">
                <?php echo t('Upload password') . ':';?></label>
                </td>
                <td class = "field"><input type = "password"
                name = "upload_password" id = "upload_password"
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
}

?>
<div id="upload_finished">
    <p>
    <?php echo t('File uploaded! Copy the following URL to get it') ?>:
    <br />
    <a id="upload_link" href=""></a>
    <br />
    </p>

    <p>
    <?php echo t('Keep the following URL to delete it at any moment'); ?>:
    <br />
    <a id="delete_link" href=""></a>
    </p>
    
    <p id="validity">
    <?php echo t('This file is valid until the following date'); ?>:
    <br /><strong><div id="date"></div></strong>
    </p>
</div>

<div id="uploading">
    <p>
    <?php echo t ('Uploading ...'); ?><div id="uploaded_percentage"></div>
    </p>
</div>

<div id="upload">
<fieldset>
    <legend>
    <?php echo t('Select a file'); ?> 
    </legend>
    <p>
    <input type="file" id="file_select" size="30"
    onchange="
        document.getElementById('options').style.display = '';
        document.getElementById('send').style.display = '';
    "/>
    </p>
    
    <div id="options">
        <table id="option_table">
        <tr>
        <td><?php echo t('One time download'); ?>:</td>
        <td><input type="checkbox" id="one_time_download" /></td>
        </tr>
        <tr>
        <td><label for="input_key"><?php echo t('Password') . ':'; ?></label></td>
        <td><input type="text" name="key" id="input_key" /></td>
        </tr>
        <tr>
        <td><label for="select_time"><?php echo t('Time limit') . ':'; ?></label></td>
        <td><select name="time" id="select_time">
        <option value="none"><?php echo t('None'); ?></option>
        <option value = "minute"><?php echo t('One minute'); ?></option>
        <option value = "hour"><?php echo t('One hour'); ?></option>
        <option value = "day"><?php echo t('One day'); ?></option>
        <option value = "week"><?php echo t('One week'); ?></option>
        <option value = "month"><?php echo t('One month');?></option>
        </select></td>
        </tr>
		<p id="max_file_size" class="config"></p>
    <p>

    <input type="hidden" id="upload_password" name="upload_password" value="<?php echo $cfg['upload_password']?>"/>
    <input type="submit" id="send" value="<?php echo t('Send'); ?>"
    onclick="
        document.getElementById('upload').style.display = 'none';
        document.getElementById('uploading').style.display = '';
        upload ('<?php echo $cfg['web_root']; ?>', <?php echo jirafeau_get_max_upload_size_bytes (); ?>);
    "/>
    </p>
        </table>
    </div> </fieldset>

    <?php
    if (strlen ($cfg['upload_password']) > 0)
    {
    ?>
    <form action = "<?php echo basename(__FILE__); ?>" method = "post">
        <input type = "hidden" name = "action" value = "logout"/>
        <input type = "submit" value = "<?php echo t('Logout'); ?>" />
    </form>
    <?php
    }
    ?>

</div>

<script lang="Javascript">
    document.getElementById('uploading').style.display = 'none';
    document.getElementById('upload_finished').style.display = 'none';
    document.getElementById('options').style.display = 'none';
    document.getElementById('send').style.display = 'none';
    if (!check_html5_file_api ())
        document.getElementById('max_file_size').innerHTML = '<?php
             echo t('You browser may not support HTML5 so the maximum file size is ') . jirafeau_get_max_upload_size ();
             ?>';
</script>
<?php require (JIRAFEAU_ROOT . 'lib/template/footer.php'); ?>

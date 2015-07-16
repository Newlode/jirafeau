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

check_errors ($cfg);
if (has_error ())
{
    show_errors ();
    require (JIRAFEAU_ROOT . 'lib/template/footer.php');
    exit;
}

require (JIRAFEAU_ROOT . 'lib/template/header.php');

/* Check if user is allowed to upload. */
if (!jirafeau_challenge_upload_ip ($cfg, get_ip_address($cfg)))
{
    echo '<div class="error"><p>' . t('Access denied') . '</p></div>';
    require (JIRAFEAU_ROOT.'lib/template/footer.php');
    exit;
}

/* Ask password if upload password is set. */
if (jirafeau_has_upload_password ($cfg))
{
    session_start();

    /* Unlog if asked. */
    if (isset ($_POST['action']) && (strcmp ($_POST['action'], 'logout') == 0))
        session_unset ();

    /* Auth. */
    if (isset ($_POST['upload_password']))
    {
        if (jirafeau_challenge_upload_password ($cfg, $_POST['upload_password']))
        {
            $_SESSION['upload_auth'] = true;
            $_SESSION['user_upload_password'] = $_POST['upload_password'];
        }
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
    <p><?php echo t('File uploaded !') ?></p>

    <div id="upload_finished_download_page">
    <p>
          <?php echo t('Download page') ?> 
          <a id="upload_link_email" href=""><img id="upload_image_email"/></a>
    </p>
    <p><a id="upload_link" href=""></a></p>
    </div>

    <?php if ($cfg['preview'] == true) { ?>
    <div id="upload_finished_preview">
    <p><?php echo t('View link') ?>:</p>
    <p><a id="preview_link" href=""></a></p>
    </div>
    <?php } ?>

    <div id="upload_direct_download">
    <p><?php echo t('Direct download link') ?>:</p>
    <p><a id="direct_link" href=""></a></p>
    </div>

    <div>
    <p><?php echo t('Delete link') ?>:</p>
    <p><a id="delete_link" href=""></a></p>
    </div>

    <div id="validity">
    <p><?php echo t('This file is valid until the following date'); ?>:</p>
    <p id="date"></p>
    </div>
</div>

<div id="uploading">
    <p>
    <?php echo t ('Uploading ...'); ?>
    <div id="uploaded_percentage"></div>
    <div id="uploaded_speed"></div>
    <div id="uploaded_time"></div>
    </p>
</div>

<div id="error_pop" class="error">
</div>

<div id="upload">
<fieldset>
    <legend>
    <?php echo t('Select a file'); ?> 
    </legend>
    <p>
    <input type="file" id="file_select" size="30"
    onchange="control_selected_file_size(<?php echo $cfg['maximal_upload_size'] ?>, '<?php echo t ('File is too big') . ', ' . t ('File size is limited to') . " " . $cfg['maximal_upload_size'] . " MB"; ?>')"/>
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
        <?php if ($cfg['availabilities']['none']) { ?>
        <option value="none"><?php echo t('None'); ?></option>
        <?php } ?>
        <?php if ($cfg['availabilities']['year']) { ?>
        <option value = "year"><?php echo t('One year');?></option>
        <?php } ?>
        <?php if ($cfg['availabilities']['month']) { ?>
        <option value = "month"><?php echo t('One month');?></option>
        <?php } ?>
        <?php if ($cfg['availabilities']['week']) { ?>
        <option value = "week"><?php echo t('One week'); ?></option>
        <?php } ?>
        <?php if ($cfg['availabilities']['day']) { ?>
        <option value = "day"><?php echo t('One day'); ?></option>
        <?php } ?>
        <?php if ($cfg['availabilities']['hour']) { ?>
        <option value = "hour"><?php echo t('One hour'); ?></option>
        <?php } ?>
        <?php if ($cfg['availabilities']['minute']) { ?>
        <option value = "minute"><?php echo t('One minute'); ?></option>
        <?php } ?>
        </select></td>
        </tr>

        <?php
        if ($cfg['maximal_upload_size'] > 0)
        {
        echo '<p class="config">' . t ('File size is limited to');
        echo " " . $cfg['maximal_upload_size'] . " MB</p>";
        }
        ?>

		<p id="max_file_size" class="config"></p>
    <p>
    <?php
    if (jirafeau_has_upload_password ($cfg) && $_SESSION['upload_auth'])
    {
    ?>
    <input type="hidden" id="upload_password" name="upload_password" value="<?php echo $_SESSION['user_upload_password'] ?>"/>
    <?php
    }
    else
    {
    ?>
    <input type="hidden" id="upload_password" name="upload_password" value=""/>
    <?php
    }
    ?>
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
    if (jirafeau_has_upload_password ($cfg))
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
    document.getElementById('error_pop').style.display = 'none';
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

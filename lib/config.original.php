<?php
/*
 *  Jirafeau, your web file repository
 *  Copyright (C) 2008  Julien "axolotl" BERNARD <axolotl@magieeternelle.org>
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

/**
 * Default configuration
 *
 * To overwrite these settings copy the file,
 * rename it to »config.local.php« and adapt the parameters.
 **/

/* URL of installation, with traling slash (eg. »https://exmaple.com/jirafeau/«)
 */
$cfg['web_root'] = '';

/* Path to data directory, with trailing slash (eg. »/var/www/data/var_314159265358979323846264«
 */
$cfg['var_root'] = '';

/* Language - choice between 'auto' or any language located in the /lib/locales/ folder.
 * The mode »auto« will cause the script to detect the user's browser information
 * and offer a matching language, and use »en« if it is not available.
 */
$cfg['lang'] = 'auto';

/* Select a theme - see media folder for available themes
 */
$cfg['style'] = 'courgette';

/* Propose a preview link if file type is previewable
 */
$cfg['preview'] = true;

/* Enable the encryption feature
 * By enabling it, file-level deduplication won't work anymore. See FAQ.
 */
$cfg['enable_crypt'] = false;

/* Length of link reference
 */
$cfg['link_name_length'] = 8;

/* Upload password(s).
 * An empty array will disable the password authentification.
 * $cfg['upload_password'] = array();               // No password
 * $cfg['upload_password'] = array('psw1');         // One password
 * $cfg['upload_password'] = array('psw1', 'psw2'); // Two passwords
 */
$cfg['upload_password'] = array();

/* List of IP allowed to upload a file.
 * If the list is empty, then there is no upload restriction based on IP.
 * Elements of the list can be a single IP (e.g. "123.45.67.89") or
 * an IP range (e.g. "123.45.0.0/16").
 * Note that CIDR notation is available for IPv4 only for the moment.
 */
$cfg['upload_ip'] = array();

/* Password for the admin interface.
 * An empty password will disable the password authentification.
 * The password is a sha256 hash of the original version.
 */
$cfg['admin_password'] = '';

/* If set, let the user be authenticated as administrator.
 * The user provided here is the user authenticated by HTTP authentication.
 * Note that Jirafeau does not manage the HTTP login part, it just checks
 * that the provided user is logged in.
 * If »admin_password« parameter is set, then the »admin_password« is ignored.
 */
$cfg['admin_http_auth_user'] = '';

/* Allow user to select different options for file expiration time.
 * Possible values in array:
 * 'minute': file is available for one minute
 * 'hour': file available for one hour
 * 'day': file available for one day
 * 'week': file available for one week
 * 'month': file is available for one month
 * 'quarter': file is available for three month
 * 'year': file available for one year
 * 'none': unlimited availability
 */
$cfg['availabilities'] = array (
    'minute' => true,
    'hour' => true,
    'day' => true,
    'week' => true,
    'month' => true,
    'quarter' => false,
    'year' => false,
    'none' => false
);

/* Set a default value for the expiration time.
 * The value has to equal one of the enabled options in »availabilities«, e.g. »month«.
 */
$cfg['availability_default'] = 'month';

/* Set maximal upload size expressed in MB.
 * »0« means unlimited upload size.
 */
$cfg['maximal_upload_size'] = 0;

/* Proxy IP
 * If the installation is behind some reverse proxies, it is possible to set
 * the allowed proxy IP.
 * $cfg['proxy_ip'] = array('12.34.56.78');
 * Jirafeau will then get a visitor's IP from HTTP_X_FORWARDED_FOR
 * instead of REMOTE_ADDR.
 */
$cfg['proxy_ip'] = array();

/* Required flag to test if the installation is already installed
 * or needs to start the installation script
 */
$cfg['installation_done'] = false;

?>
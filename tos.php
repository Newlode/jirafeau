<?php
/*
 *  Jirafeau, your web file repository
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

define ('JIRAFEAU_ROOT', dirname (__FILE__) . '/');

require (JIRAFEAU_ROOT . 'lib/settings.php');
require (JIRAFEAU_ROOT . 'lib/functions.php');
require (JIRAFEAU_ROOT . 'lib/lang.php');

// Read ToS template
if (is_readable(JIRAFEAU_ROOT . 'lib/tos.local.txt')) {
    $content = file_get_contents(JIRAFEAU_ROOT . 'lib/tos.local.txt');
} else {
    $content = file_get_contents(JIRAFEAU_ROOT . 'lib/tos.original.txt');
}

// Replace markers and print ToS
require (JIRAFEAU_ROOT . 'lib/template/header.php');

echo '<h2>Terms of Service</h2>';
echo '<div>' . jirafeau_replace_markers($content, true) . '</div>';

require (JIRAFEAU_ROOT . 'lib/template/footer.php');

?>
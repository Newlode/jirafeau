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

 global $languages_list;
 $languages_list = array ('auto' => 'Automatic',
                          'en' => 'English',
                          'fr' => 'Français');

/* Translation */
function _ ($text)
{
    $cfg = $GLOBALS['cfg'];
    $languages_list = $GLOBALS['languages_list'];

    /* Detect user's langage if we are in automatic mode. */
    if (strcmp ($cfg['lang'], 'auto') == 0)
        $l = substr ($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    else
        $l = $cfg['lang'];

    /* Is the langage in the list ? */
    $found = false;
    foreach ($languages_list as $key => $v)
        if (strcmp ($l, $key) == 0)
            $found = true;

    /* Get translation execpt for english. */
    if ($found && strcmp ($l, "en"))
    {
        /* $tr is defined in this requirement. */
        require (JIRAFEAU_ROOT . "lib/lang/$l.php");

        foreach ($tr as $o => $t)
            if (strcmp ($text, $o) == 0)
                return "$t";
    }
    /* Return original text if no translation is found or already in english. */
    return ($text);
}

?>
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

global $languages_list;
$languages_list = array('auto' => 'Automatic',
                         'de'   => 'Deutsch',
                         'en'   => 'English',
                         'el'   => 'Ελληνικά',
                         'es'   => 'Español',
                         'hu'   => 'Magyar',
                         'fi'   => 'Suomi',
                         'fr'   => 'Français',
                         'it'   => 'Italiano',
                         'nl'   => 'Nederlands',
                         'pt'   => 'português',
                         'pt_BR'   => 'português (Brasil)',
                         'ro'   => 'Limba română',
                         'ru'   => 'ру́сский',
                         'sk'   => 'Slovenčina',
                         'tr'   => 'Türkçe',
                         'zh'   => '汉语');

/* Translation */
function t($text)
{
    $cfg = $GLOBALS['cfg'];
    $languages_list = $GLOBALS['languages_list'];

    /* Detect user's langage if we are in automatic mode. */
    if (strcmp($cfg['lang'], 'auto') == 0) {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $l = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        } else {
            $l = "en";
        }
    } else {
        $l = $cfg['lang'];
    }

    /* Is the langage in the list ? */
    $found = false;
    foreach ($languages_list as $key => $v) {
        if (strcmp($l, $key) == 0) {
            $found = true;
        }
    }

    /* Don't translate english. */
    if (!($found && strcmp($l, "en"))) {
        return $text;
    }

    /* Open translation file. */
    $trans_j = file_get_contents(JIRAFEAU_ROOT . "lib/locales/$l.json");
    if ($trans_j === false) {
        return $text;
    }

    /* Decode JSON. */
    $trans = json_decode($trans_j, true);
    if ($trans === null) {
        return $text;
    }

    /* Try to find translation. */
    if (!array_key_exists($text, $trans)) {
        return $text;
    }

    return $trans[$text];
}

function json_lang_generator()
{
    $cfg = $GLOBALS['cfg'];
    $languages_list = $GLOBALS['languages_list'];

    /* Detect user's langage if we are in automatic mode. */
    if (strcmp($cfg['lang'], 'auto') == 0) {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $l = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        } else {
            $l = "en";
        }
    } else {
        $l = $cfg['lang'];
    }

    /* Is the langage in the list ? */
    $found = false;
    foreach ($languages_list as $key => $v) {
        if (strcmp($l, $key) == 0) {
            $found = true;
        }
    }

    /* Don't translate english. */
    if (!($found && strcmp($l, "en"))) {
        return "{}";
    }

    /* Open translation file. */
    $trans_j = file_get_contents(JIRAFEAU_ROOT . "lib/locales/$l.json");
    return $trans_j;
}

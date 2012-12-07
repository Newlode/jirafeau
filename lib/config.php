<?php
/*
 *  Jirafeau, your web file repository
 *  Copyright (C) 2008  Julien "axolotl" BERNARD <axolotl@magieeternelle.org>
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
/*
 * default configuration
 * if you want to change this, overwrite in a config.local.php file
 */
// don't forget the ending '/'
    $cfg['web_root'] = '';

$cfg['var_root'] = '';

$cfg['lang'] = '';

$cfg['style'] = 'default';

$cfg['rewrite'] = false;

$cfg['password'] = '';

if ((basename (__FILE__) != 'config.local.php')
    && file_exists (JIRAFEAU_ROOT.'lib/config.local.php'))
{
    require (JIRAFEAU_ROOT.'lib/config.local.php');
}

?>

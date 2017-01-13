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
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/* Jirafeau constants */
define ('JIRAFEAU_PACKAGE', 'Jirafeau');
define ('JIRAFEAU_VERSION', '1.1');

/* Directories. */
define ('VAR_FILES', $cfg['var_root'] . 'files/');
define ('VAR_LINKS', $cfg['var_root'] . 'links/');
define ('VAR_ASYNC', $cfg['var_root'] . 'async/');
define ('VAR_ALIAS', $cfg['var_root'] . 'alias/');

/* Useful constants. */
if (!defined ('NL'))
    define ('NL', "\n");

define ('JIRAFEAU_INFINITY', -1);
define ('JIRAFEAU_MINUTE', 60); // 60
define ('JIRAFEAU_HOUR', 3600); // JIRAFEAU_MINUTE * 60
define ('JIRAFEAU_DAY', 86400); // JIRAFEAU_HOUR * 24
define ('JIRAFEAU_WEEK', 604800); // JIRAFEAU_DAY * 7
define ('JIRAFEAU_MONTH', 2419200); // JIRAFEAU_WEEK * 4
define ('JIRAFEAU_QUARTER', 7257600); // JIRAFEAU_MONTH * 3
define ('JIRAFEAU_YEAR', 29030400); // JIRAFEAU_MONTH * 12

?>

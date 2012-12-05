<?php
/*
 *  Jyraphe, your web file repository
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
 * This stylesheet is the default stylesheet for Jyraphe.
 * The content is dynamically generated for easier handling.
 */

$dark = '#8B4513';

header("Content-type: text/css");

?>

body {
  text-align: center;
  font-family: sans-serif;
  width: 60em;
  margin: 2ex auto;
  border: <?php echo $dark; ?> 5px solid;
}

h1 a {
  text-decoration: none;
  color: black;
  border-bottom: <?php echo $dark; ?> 1px dotted;
}

#content {
  padding: 0 4em;
  background: url('jyraphe.png') left top repeat-y;
}

#upload {
  width: 25em;
  margin: 5ex auto;
}

#upload table {
  width: 100%;
}

#upload .config {
  font-size: smaller;
}

#upload .info {
  text-align: left;
  font-size: smaller;
  border-bottom: <?php echo $dark; ?> 1px dashed;
}

#upload .more {
  cursor: pointer;
}

#upload .more:after {
  content: ' ▼';
}

#upload .activation {
  text-align: left;
  font-style: italic;
}

#upload .label {
  text-align: left;
  vertical-align: top;
  font-size: smaller;
}

#upload .field {
  text-align: right;
  vertical-align: bottom;
}

#upload p {
  margin: 0.8ex 0;
}

#moreoptions p {
  text-align: left;
}

#copyright {
  font-size: smaller;
}

.error, .message {
  width: 50em;
  margin: 5ex auto;
}

.error {
  padding-bottom: 1ex;
  border: red 2px solid;
  background-color: #FBB;
}

.error p:before {
  content: url('error.png');
  padding-right: 1ex;
}

.message {
  padding: 1ex;
  border: green 2px solid;
  background-color: #BFB;
}

.message p:before {
  content: url('ok.png');
  padding-right: 1ex;
}

#install {
  width: 40em;
  margin: 5ex auto;
}

#install table {
  width: 100%;
}

#install label {
  font-style: italic;
}

#install .info {
  text-align: justify;
  padding-bottom: 1ex;
}

#install .label {
  text-align: justify;
  vertical-align: top;
}

#install .field {
  text-align: right;
  vertical-align: bottom;
}

#install .nav {
  padding-top: 2em;
}

#install .next {
  text-align: right;
}

#install .previous {
  text-align: left;
}

#install .navright { 
  float: right;
}

#install .navleft {
  float: left;
}
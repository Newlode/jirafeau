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
  font-family: sans-serif;
  text-align: center;
  margin: 2ex auto;
  background: white;
  /* border: <?php echo $dark; ?> 5px solid; */
}

fieldset {
  text-align: left;
  width: 40em;
  margin: auto;
  background: #E2f5ff;
  border: 2px solid #02233f;
  -moz-border-radius: 10px;
  -webkit-border-radius: 10px;
}

fieldset legend {
  color: white;
  background: #02233f;
  border: 2px solid #02233f;
  padding: 1px 5px;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
}

h1 {
  width: 100%;
  text-align: center;
  background: url('bandeau.png') left top repeat-x;
  height: 70px;
  padding-top: 30px;
}

h1 a {
  text-decoration: none;
  color: white;
}

fieldset p {
  margin-left: 25%;
}

.jyraphe_info {
  font-size: small;
  margin-left: 30%;
}

label {
  float: left;
  width: 12em;
}

input[type=text], input[type=submit], select {
  color: black;
  width: 15em;
  border: 1px #02233f solid;
  background: white;
}

input:hover {
  color: white;
  background: #02233f;
}

#jyraphe {
  background: url('jyraphe.png') right bottom no-repeat;
  position: fixed;
  bottom: 0;
  right: 0;
	height: 100px;
  width: 100px;
	clear:both;
}

#copyright {
  text-align: center;
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

.info {
  text-align: left;
  width: 40em;
  margin: auto;
  background: #E2f5ff;
  border: 2px solid #02233f;
  -moz-border-radius: 10px;
  -webkit-border-radius: 10px;
}

.info h2 {
  text-align: center;
}

.info h3 {
  text-align: center;
}

.info p {
  margin-left: 5%;
  margin-right: 5%;
}

#upload {}

#uploading {
  text-align: center;
  width: 30em;
  background: #E2f5ff;
  border: 2px solid #02233f;
  margin: auto;
}

#upload_finished {
  text-align: center;
  width: 60em;
  background: #E2f5ff;
  border: 2px solid #02233f;
  margin: auto;
}

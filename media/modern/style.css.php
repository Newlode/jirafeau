<?php
/*
 *  Jyraphe, your web file repository
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

/*
 * This stylesheet is the default stylesheet for Jyraphe.
 * The content is dynamically generated for easier handling.
 */

$dark = '#8B4513';

header("Content-type: text/css");

?>

body {
font-family:"Lucida Grande","Lucida Sans Unicode",Tahoma,sans-serif;
font-size: 100%;
color:#333333;
margin:0;
background:#e5e5e5;
}

a, a:link, a:visited {
	color: #223344;
	text-decoration: underlined;
}

fieldset {
  text-align: left;
  font-size:90%;
  width: 50em;
  margin: auto;
  background: white;
  border: 2px solid #dbdbdb;
-webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
}

fieldset legend {

  color: white;
  font-size:130%;
  background: #cf3b19;
  border: 1px solid #A52E13;
  padding: 5px 20px;
-webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
}

h1 {

  font-size: 175%;
  width: 100%;
  text-align: center;
  height: 50px;
  padding-top: 30px;
}

h1 a {
  text-decoration: none;
  color: #333;
}

h2 {
  text-decoration: none;
  color: #333;
  text-align: center;
}

fieldset p {
  margin-left: 25%;
}

.jyraphe_info {
  font-size: 120%;
  margin-left: 30%;
}

label {
  float: left;
  width: 12em;
}

input[type=text], input[type=submit], select {
  width: 15em;
  font-family:"Lucida Grande","Lucida Sans Unicode",Tahoma,sans-serif;

}


#jyraphe {
  background: url('jyraphe.png') right bottom no-repeat;
  position: fixed;
  bottom: 0;
  right: 0;
  height: 50px;
  width: 50px;
  clear:both;
}

#copyright {
  text-align: center;
  font-size: 70%;

}

.error, .message {
  width: 50em;
  margin: 5ex auto;
}

.error {
text-align: center;
  padding-bottom: 1ex;
  border: #FB7373 2px solid;
  background-color: #FBB;
    -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  border-radius: 5px;
}

.error p:before {
text-align: center;
  content: url('error.png');
  padding-right: 1ex;
    -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  border-radius: 5px;
}

.message {
text-align: center;
  padding: 1ex;
  border: #91C27C 2px solid;
  background-color: #BFB;
    -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  border-radius: 5px;
}

.message p:before {
  content: url('ok.png');
  padding-right: 1ex;
}

.info {
  text-align: left;
  width: 50em;
  margin: auto;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
}

.info h2 {
  text-align: left;
}

.info h3 {
  text-align: center;
}

.info p {
  margin-left: 5%;
  margin-right: 5%;
}

#upload {
  text-align: left;
  font-size: 90%;
  width: 50em;
  background: #e5e5e5;
  border: 0px solid #CCCCCC;
  margin: auto;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  border-radius: 5px;
}

#uploading {
  text-align: center;
  width: 50em;
  background: white;
  border: 2px solid #CCCCCC;
  margin: auto;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
   border-radius: 5px;
}

#upload_finished {
font-size:90%;
  text-align: center;
  padding-top: 20px;
  padding-bottom: 20px;
  width: 50em;
  background: #B6D7A8;
  border: 2px solid #91C27C;
  margin: auto;
  -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
}

#self_destruct {
  font-weight: bold;
  color: red;
}

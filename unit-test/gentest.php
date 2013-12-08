#! /usr/bin/env php
<?php
/*
* This file is part of FaberScriptorum-PHP.
*
* FaberScriptorum-PHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* FaberScriptorum-PHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with FaberScriptorum-PHP.  If not, see <http://www.gnu.org/licenses/>.
*
*/

ini_set('include_path', "..:" . ini_get('include_path'));

require_once("Fabscript/code_creator.php");
require_once("Fabscript/edit_section_parser.php");
use Fabscript\ProgLang;

$creator = new Fabscript_CodeCreator();
$creator->setGlobalVars(array(
    "objName" => "Person",
    "fields" => array("firstName", "lastName", "birthday", "sex")
));

$creator->createFileFromTemplate("object.template", "person.php", ProgLang::PHP);

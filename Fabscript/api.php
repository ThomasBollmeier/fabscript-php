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
namespace Fabscript;

require_once('Fabscript/code_creator.php');

/**
 * Paste code from template at caller position
 *
 * @param $templatePath: 	path to the template
 * @param $bindings: 		array map that binds the variables used in 
 * 							the script to the ones defined by caller     
 */
function paste_from_template($templatePath, $bindings) {

	fabscript_addText($templatePath, $bindings);

}

/**
 * Set the search paths for template directories
 *
 * @param $templateDirPaths:	either an array of directory paths 
 *								or the directories as a colon separated string
 */
function set_template_dirs($templateDirPaths) {

	fabscript_setTemplateDirs($templateDirPaths);

}
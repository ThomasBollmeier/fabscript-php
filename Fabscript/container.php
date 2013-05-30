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

interface Fabscript_Element {

    public function getLines($env);

}

interface Fabscript_TextElement extends Fabscript_Element {

    public function addRawLine($rawLine);

}

interface Fabscript_Container extends Fabscript_TextElement {

    public function addElement(Fabscript_Element $element);

}

?>
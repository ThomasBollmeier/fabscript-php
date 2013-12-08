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

require_once("Fabscript/block.php");
require_once("Fabscript/code_creator.php");

class EditSection extends \Fabscript_Block {

	public function __construct($sectionNameExpr) {

        parent::__construct();
		$this->nameExpr = $sectionNameExpr;
        $this->editedLines = NULL;

	}

    public function getLines($env) {

        $creator = \Fabscript_CodeCreator::getCurrent();
        if ($creator === NULL) {
            throw new \Exception("Code creator is not set!");
        }

        $config = $creator->getEditSectionConfig();
        if ($config === NULL) {
            throw new \Exception("Output for editable sections is not configured!");
        }

        $sectionName = $this->nameExpr->getValue($env);

        $res = array($config->getStartComment($sectionName));

        $editedLines = $creator->getEditedLines($sectionName);

        if ($editedLines !== FALSE) {
            // editable section has already been edited => keep edited lines
            $res = array_merge($res, $editedLines);
        } else {
            // new editable section => insert default lines into editable section
            $res = array_merge($res, parent::getLines($env));
        }

        array_push($res, $config->getEndComment($sectionName));

        return $res;

    }

    private $nameExpr;

}
<?php
/*
 * This file is part of FaberScriptorum-PHP. FaberScriptorum-PHP is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version. FaberScriptorum-PHP is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with FaberScriptorum-PHP. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fabscript;

require_once ('container.php');

class BreakStatement implements \Fabscript_Element {

	public function getLines($env) {
		
		throw new ControlException(ControlException::BREAK_CMD); 
		
	}
}

class ContinueStatement implements \Fabscript_Element {

	public function getLines($env) {
		
		throw new ControlException(ControlException::CONTINUE_CMD);
	
	}
}

class ControlException extends \Exception {
	
	const BREAK_CMD = 1;
	const CONTINUE_CMD = 2;
	
	public $command;
	
	public function __construct($command) {
		
		parent::__construct("");
	
		$this->command = $command;
		
	}

}
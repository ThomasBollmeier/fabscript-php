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

require_once 'Fabscript/parser.php';
require_once 'Fabscript/expression.php';
require_once 'Fabscript/interpreter.php';

interface Fabscript_Block {

	public function getLines($env);

}

class Fabscript_Text implements Fabscript_Block {

	public function __construct() {

		$this->rawLines = array();
		$this->symbolRegex = '/\$\{([^$]*)\}/';
		$this->parser = new Fabscript_Symbol_Parser();
		$this->interpreter = new Fabscript_Interpreter();

	}

	public function addRawLine($rawLine) {

		array_push($this->rawLines, $rawLine);

	}

	public function getLines($env) {

		$res = array();
		$this->env = $env;

		foreach ($this->rawLines as $rawLine) {

			$line = preg_replace_callback($this->symbolRegex, 
				array($this, "replaceSymbols"), 
				$rawLine
				);

			array_push($res, $line);

		}

		return $res;

	}

	private function replaceSymbols($matches) {

		$ast = $this->parser->parseString($matches[1]);
		$expr = $this->interpreter->interpret($ast); 

		return $expr->getValue($this->env);

	}

	private $rawLines;
	private $symbolRegex;
	private $parser;
	private $interpreter;
	private $env = null;

}

?>
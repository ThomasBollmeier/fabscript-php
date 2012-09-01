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
require_once 'Fabscript/environment.php';

interface Fabscript_Element {
	
	public function getLines($env);

}

interface Fabscript_TextElement extends Fabscript_Element {

	public function addRawLine($rawLine);

}

interface Fabscript_Container {

	public function addElement(Fabscript_Element $element);

}

class Fabscript_Text implements Fabscript_TextElement {

	public function __construct() {

		$this->rawLines = array();
		$this->symbolRegex = '/\$\{([^$]*)\}/';
		$this->parser = new Fabscript_Symbol_Parser();
		$this->interpreter = new Fabscript_Interpreter();

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

	public function addRawLine($rawLine) {

		array_push($this->rawLines, $rawLine);

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

class Fabscript_Loop implements Fabscript_TextElement, Fabscript_Container {

	public function __construct($table, $line="", $key="", $filter=null) {

		$this->table = $table;
		$this->line = $line;
		$this->key = $key;
		$this->filter = $filter; 
		$this->innerElements = array();

		$this->addElement(new Fabscript_Text());

	}

	public function getLines($env) {

		$res = array();
		$innerEnv = new Fabscript_Environment($env);

		if ($this->line == "") {
			
			// Looping at list without explicit line variable

			$list = $this->table->getValue($env);
			if ($this->table instanceof Fabscript_Variable) {
				$lineVarName = $this->table->getAbsoluteName();
			} else {
				$lineVarName = "";
			}

			foreach ($list as $item) {
				
				if ($lineVarName != "") {
					$innerEnv->set($lineVarName, $item);
				}

				if ($this->filter != null && !$this->filter->isTrue($innerEnv)) {
					continue;
				}

				$res = array_merge($res, $this->getLinesPerStep($innerEnv));

			}

		} else if ($this->key == "") {

			// Looping at list with explicit line variable
			
			$list = $this->table->getValue($env);

			foreach ($list as $item) {
				
				$innerEnv->set($this->line, $item);
				
				if ($this->filter != null && !$this->filter->isTrue($innerEnv)) {
					continue;
				}

				$res = array_merge($res, $this->getLinesPerStep($innerEnv));

			}

		} else {

			// Looping at dictionary with key value pairs
			
			$dict = $this->table->getValue($env);

			foreach ($dict as $key => $value) {
				
				$innerEnv->set($this->key, $key);
				$innerEnv->set($this->line, $value);
				
				if ($this->filter != null && !$this->filter->isTrue($innerEnv)) {
					continue;
				}

				$res = array_merge($res, $this->getLinesPerStep($innerEnv));

			}

		}

		return $res;

	}

	public function addRawLine($rawLine) {

		if (!($this->current instanceof Fabscript_Text)) {
			$this->addBlock(new Fabscript_Text());			
		}

		$this->current->addRawLine($rawLine);

	}

	public function addElement(Fabscript_Element $element) {

		$this->innerElements[] = $element;
		$this->current = $element;

	}

	private function getLinesPerStep($env) {

		$res = array();

		foreach ($this->innerElements as $element) {
			$res = array_merge($res, $element->getLines($env));
		}

		return $res;

	}

	private $table;
	private $line;
	private $key;
	private $filter;
	private $innerElements;
	private $current;

}

class Fabscript_Branch implements Fabscript_Container {

	public function __construct($condition) {

		$this->branches = array(array($condition, array(new Fabscript_Text())));

	}

	public function addBranch($condition) {

		$element = new Fabscript_Text();
		$this->branches[] = array($condition, array($element));

	}

	public function addRawLine($rawLine) {

		$elements = $this->getCurrentContent();
		$numElems = count($elements);

		if ($elements[$numElems-1] instanceof Fabscript_Text) {
			$element = $elements[$numElems-1];
		} else {
			$element = new Fabscript_Text();
			$elements[] = $element;
		}

		$element->addRawLine($rawLine);

	}

	public function getLines($env) {

		$res = array();

		foreach ($this->branches as $branch) {
			
			$condition = $branch[0];
			
			if ($condition->isTrue($env)) {
				$elements = $branch[1];
				foreach ($elements as $element) {
					$res = array_merge($res, $element->getLines($env));
				}
				return $res;
			}

		}

		return $res;

	}

	public function addElement(Fabscript_Element $element) {

		$elements = $this->getCurrentContent();
		$blocks[] = $block;

	}

	private function getCurrentContent() {

		$numBranches = count($this->branches);

		return $this->branches[$numBranches-1][1];

	}

	private $branches;

}

?>
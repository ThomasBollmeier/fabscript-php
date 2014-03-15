<?php
/*
 * This file is part of FaberScriptorum-PHP. FaberScriptorum-PHP is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version. FaberScriptorum-PHP is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with FaberScriptorum-PHP. If not, see <http://www.gnu.org/licenses/>.
 */
require_once 'Fabscript/parser.php';
require_once 'Fabscript/expression.php';
require_once 'Fabscript/interpreter.php';
require_once 'Fabscript/environment.php';
require_once 'Fabscript/container.php';
require_once 'Fabscript/break_continue.php';

use \Fabscript\ControlException as ControlException;

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
					array($this, "replaceSymbols"), $rawLine);

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

class Fabscript_Block implements Fabscript_Container {

	public function __construct() {

		$this->elements = array(new Fabscript_Text());
		$this->lines = array();

	}

	public function getLines($env) {

		$this->lines = array();
		$this->currentElement = null;

		foreach ($this->elements as $element) {
			// getLines may throw ControlException
			// lines collected so far can be retrieved by method getLinesBeforeExit
			$this->currentElement = $element;
			$this->lines = array_merge($this->lines, $element->getLines($env));
		}

		return $this->lines;

	}

	/**
	 *
	 * @return buffered lines that have been collected before BREAK or CONTINUE occurred
	 */

	public function getLinesBeforeExit() {
		
		$res = $this->lines;
		
		$cur = $this->currentElement;
		
		if (!$cur) {
			return $res;
		}
		
		if ( $cur instanceof self || $cur instanceof Fabscript_Branch) {
			$res = array_merge($res, $cur->getLinesBeforeExit());
		}

		return $res;

	}

	public function addRawLine($rawLine) {

		$element = end($this->elements);

		if (!($element instanceof Fabscript_Text)) {
			$element = new Fabscript_Text();
			$this->elements[] = $element;
		}

		$element->addRawLine($rawLine);

	}

	public function addElement(Fabscript_Element $element) {

		$this->elements[] = $element;

	}

	private $elements;
	private $lines = array();
	private $currentElement = null;

}

class Fabscript_Loop implements Fabscript_Container {

	public function __construct($table, $line = "", $key = "", $filter = null) {

		$this->table = $table;
		$this->line = $line;
		$this->key = $key;
		$this->filter = $filter;
		$this->body = new Fabscript_Block();

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

			$filtered = array();

			foreach ($list as $item) {

				if ($lineVarName != "") {
					$innerEnv->set($lineVarName, $item);
				}

				if ($this->filter != null && !$this->filter->isTrue($innerEnv)) {
					continue;
				}

				$filtered[] = $item;
			}

			$idx = 0;
			$lastIdx = count($filtered) - 1;
			$breakOccurred = FALSE;

			foreach ($filtered as $item) {

				if ($lineVarName != "") {
					$innerEnv->set($lineVarName, $item);
				}

				$innerEnv->set("isFirst", $idx == 0);
				$innerEnv->set("isLast", $idx == $lastIdx);

				$this->_getLines($innerEnv, $res, $idx, $breakOccurred);
				if ($breakOccurred) {
					break;
				}

			}

		} else if ($this->key == "") {

			// Looping at list with explicit line variable

			$list = $this->table->getValue($env);
			$filtered = array();

			foreach ($list as $item) {

				$innerEnv->set($this->line, $item);

				if ($this->filter != null && !$this->filter->isTrue($innerEnv)) {
					continue;
				}

				$filtered[] = $item;
			}

			$idx = 0;
			$lastIdx = count($filtered) - 1;
			$breakOccurred = FALSE;

			foreach ($filtered as $item) {

				$innerEnv->set($this->line, $item);

				$innerEnv->set("isFirst", $idx == 0);
				$innerEnv->set("isLast", $idx == $lastIdx);

				$this->_getLines($innerEnv, $res, $idx, $breakOccurred);
				if ($breakOccurred) {
					break;
				}

			}

		} else {

			// Looping at dictionary with key value pairs

			$dict = $this->table->getValue($env);
			$filtered = array();

			foreach ($dict as $key => $value) {

				$innerEnv->set($this->key, $key);
				$innerEnv->set($this->line, $value);

				if ($this->filter != null && !$this->filter->isTrue($innerEnv)) {
					continue;
				}

				$filtered[$key] = $value;
			}

			$idx = 0;
			$lastIdx = count($filtered) - 1;
			$breakOccurred = FALSE;

			foreach ($filtered as $key => $value) {

				$innerEnv->set($this->key, $key);
				$innerEnv->set($this->line, $value);

				$innerEnv->set("isFirst", $idx == 0);
				$innerEnv->set("isLast", $idx == $lastIdx);

				$this->_getLines($innerEnv, $res, $idx, $breakOccurred);
				if ($breakOccurred) {
					break;
				}

			}
		}

		return $res;
	}

	public function addRawLine($rawLine) {

		$this->body->addRawLine($rawLine);
	}

	public function addElement(Fabscript_Element $element) {

		$this->body->addElement($element);
	}

	private $table;
	private $line;
	private $key;
	private $filter;
	private $body;

	private function _getLines($env, &$lines, &$idx, &$breakOccurred) {

		$breakOccurred = FALSE;

		try {

			$newLines = $this->body->getLines($env);
			$idx++;
			$lines = array_merge($lines, $newLines);

		} catch (ControlException $controlExc) {

			switch ($controlExc->command) {
			case ControlException::CONTINUE_CMD:
				$idx++;
				break;
			case ControlException::BREAK_CMD:
				$breakOccurred = TRUE;
				break;
			}

			$lines = array_merge($lines, $this->body->getLinesBeforeExit());

		}

	}
}

class Fabscript_WhileLoop implements Fabscript_Container {

	public function __construct($condition) {

		$this->condition = $condition;
		$this->body = new Fabscript_Block();
	}

	public function getLines($env) {

		$res = array();

		while (TRUE) {

			if (!($this->condition->isTrue($env))) {
				break;
			}

			try {

				$res = array_merge($res, $this->body->getLines($env));

			} catch (ControlException $controlExc) {

				$res = array_merge($res, $this->body->getLinesBeforeExit());

				if ($controlExc->command == ControlException::BREAK_CMD) {
					break;
				}
			}
		}

		return $res;
	}

	public function addRawLine($rawLine) {

		$this->body->addRawLine($rawLine);
	}

	public function addElement(Fabscript_Element $element) {

		$this->body->addElement($element);
	}

	private $condition;
	private $body;

}

class Fabscript_Branch implements Fabscript_Container {

	public function __construct($condition) {

		$this->branches = array(array($condition, new Fabscript_Block()));
		$this->hasDefault = FALSE;
	}

	public function addBranch($condition) {

		if ($this->hasDefault) {
			throw new Exception(
					"ELSEIF branch must not be inserted after ELSE branch");
		}

		$this->branches[] = array($condition, new Fabscript_Block());
	}

	public function addDefaultBranch() {

		$this->branches[] = array(null, new Fabscript_Block());
		$this->hasDefault = TRUE;
	}

	public function addRawLine($rawLine) {

		$branch = end($this->branches);
		$branch[1]->addRawLine($rawLine);
	}

	public function getLines($env) {

		$this->currentBranch = null;

		foreach ($this->branches as $branch) {

			$condition = $branch[0];

			if ($condition == null || $condition->isTrue($env)) {
				$this->currentBranch = $branch[1];
				return $branch[1]->getLines($env);
			}
		}

		return array();
	}

	public function getLinesBeforeExit() {

		return $this->currentBranch->getLinesBeforeExit();
	}

	public function addElement(Fabscript_Element $element) {

		$branch = end($this->branches);
		$branch[1]->addElement($element);
	}

	private $branches;
	private $hasDefault;
	private $currentBranch = null;

}

class Fabscript_Declaration implements Fabscript_Element {

	public function __construct($varName, $initExpr = null) {

		$this->varName = $varName;
		$this->initExpr = $initExpr;
	}

	public function getLines($env) {

		$initValue = $this->initExpr ? $this->initExpr->getValue($env) : null;

		if (!($env->hasKey($this->varName))) {

			$env->set($this->varName, $initValue);
		}

		return array();
	}

	private $varName;
	private $initExpr;

}

class Fabscript_Declarations implements Fabscript_Element {

	public function __construct() {
		$this->declarations = array();
	}

	public function add($decl) {
		array_push($this->declarations, $decl);
	}

	public function getLines($env) {

		foreach ($this->declarations as $decl) {
			$decl->getLines($env);
		}

		return array(); //dummy
	}

	private $declarations;

}

class Fabscript_Assignment implements Fabscript_Element {

	public function __construct($varName, $valueExpr) {

		$this->varName = $varName;
		$this->valueExpr = $valueExpr;
	}

	public function getLines($env) {

		$defEnv = $env->getDefiningEnv($this->varName);
		if ($defEnv == null) {
			throw new Exception(
					"Variable '{$this->varName}' has not been declared", 1);
		}

		$defEnv->set($this->varName, $this->valueExpr->getValue($env));

		return array();
	}

	private $varName;
	private $valueExpr;

}

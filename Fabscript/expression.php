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

abstract class Fabscript_Expression {

	abstract public function getValue($env);

}

class Fabscript_BooleanLiteral extends Fabscript_Expression {

	public function __construct($text) {

		$this->text = $text;

	}

	public function getValue($env) {

		return ($this->text == "TRUE");

	}

	private $text;

}

class Fabscript_Number extends Fabscript_Expression {

	public function __construct($digits, $decimals="", $isNegative=FALSE) {

		$valStr = $digits;
		if ($decimals != "") {
			$valStr .= "." . $decimals;
		}

		$this->value = floatval($valStr);
		if ($isNegative) {
			$this->value = -$this->value;
		}

	}

	public function getValue($env) {

		return $this->value;

	}

	private $value;

}

class Fabscript_Literal extends Fabscript_Expression {

	public function __construct($text) {

		$this->text = $text;

	}

	public function getValue($env) {

		return $this->text;

	}

	private $text;

}

abstract class Fabscript_Path extends Fabscript_Expression {

	public function __construct($parent=null) {

		$this->parent = $parent;

	}

	public function getParent() {

		return $this->parent;

	}

	protected $parent;

}

class Fabscript_Variable extends Fabscript_Path {

	public function __construct($name, $parent=null) {

		parent::__construct($parent);
		$this->name = $name;

	}

	public function getAbsoluteName() {

		$res = $this->name;

		$pathElement = $this->parent;
		while ($pathElement) {
			if ($pathElement instanceof Fabscript_Variable) {
				$res = $pathElement->name . "." . $res;
			} else {
				$res = "";
				break;
			}
			$pathElement = $pathElement->parent;
		}

		return $res;

	}

	public function getValue($env) {

		if ($this->parent == null) {
			return $env->get($this->name);
		} else {
			$instance = $this->parent->getValue($env);
			$name = $this->name;
			return $instance->$name;
		}

	}

	private $name;

}

class Fabscript_Call extends Fabscript_Path {

	public function __construct($name, $arguments=array(), $parent=null) {

		parent::__construct($parent);

		$this->name = $name;
		$this->arguments = $arguments;

	}

	public function getValue($env) {


		$args = array();
		foreach ($this->arguments as $argument) {
			array_push($args, $argument->getValue($env));
		}

		if ($this->parent == null) {

			$funcname = $env->get($this->name);
			return call_user_func_array($funcname, $args);

		} else {

			$instance = $this->parent->getValue($env);
			return call_user_func_array(array($instance, $this->name), $args);			

		}

	}

	private $name;
	private $arguments;

}

class Fabscript_ListElement extends Fabscript_Path {

	public function __construct($listExpr, $indicesExprs ) { 

		parent::__construct($listExpr->getParent());

		$this->listExpr = $listExpr;
		$this->indicesExprs = $indicesExprs;

	}

	public function getValue($env) {

		$res = $this->listExpr->getValue($env);

		foreach ($this->indicesExprs as $indexExpr) {
			$res = $res[$indexExpr->getValue($env)];				
		}

		return $res;

	}

	private $listExpr;
	private $indicesExprs;

}

class Fabscript_BinOp extends Fabscript_Expression {

	const OP_PLUS = 1;
	const OP_MINUS = 2;

	public function __construct($op, $operand1, $operand2) {

		$this->op = $op;
		$this->operand1 = $operand1;
		$this->operand2 = $operand2;

	}

	public function getValue($env) {

		$op1 = $this->operand1->getValue($env);
		$op2 = $this->operand2->getValue($env);

		switch ($this->op) {

			case self::OP_PLUS:
				if (!is_string($op1) && !is_string($op2)) {
					return $op1 + $op2;
				} else {
					return "" . $op1 . $op2;
				}
			case self::OP_MINUS:
				return $op1 - $op2;
		}

	}

	private $op;
	private $operand1;
	private $operand2;

}

?>
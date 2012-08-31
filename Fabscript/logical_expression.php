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

interface Fabscript_LogicalExpr {

	public function isTrue($env);

}

class Fabscript_BooleanPath implements Fabscript_LogicalExpr {

	public function __construct($pathExpr) {

		$this->pathExpr = $pathExpr;

	}

	public function isTrue($env) {

		return $this->pathExpr->getValue($env);

	}

	private $pathExpr;

}

class Fabscript_Comparison implements Fabscript_LogicalExpr {

	const EQ = "==";
	const NE = "<>";
	const GT = ">";
	const GE = ">=";
	const LT = "<";
	const LE = "<=";

	public function __construct($operator, $leftHandSide, $rightHandSide) {

		$this->op = $operator;
		$this->lhs = $leftHandSide;
		$this->rhs = $rightHandSide;

	}

	public function isTrue($env) {

		$lval = $this->lhs->getValue($env);

		if ($this->rhs instanceof Fabscript_Expression) {
			$rval = $this->rhs->getValue($env);
		} else if ($this->rhs instanceof Fabscript_LogicalExpr) {
			$rval = $this->rhs->isTrue($env);
		} else {
			throw new Exception("Unsupported type of right hand side in comparison");
		}

		switch ($this->op) {

			case self::EQ:
				return $lval == $rval;
			case self::NE:
				return $lval != $rval;
			case self::GT:
				return $lval > $rval;
			case self::GE:
				return $lval >= $rval;
			case self::LT:
				return $lval < $rval;
			case self::LE:
				return $lval <= $rval;
			default:
				throw new Exception("Unsupported operator");
		}

	}

	private $lhs; 
	private $rhs;
	private $op;

}

class Fabscript_Range implements Fabscript_LogicalExpr {

	public function __construct($valueExpr, $minExpr, $maxExpr) {

		$this->valueExpr = $valueExpr;
		$this->minExpr = $minExpr;
		$this->maxExpr = $maxExpr;

	}

	public function isTrue($env) {

		$value = $this->valueExpr->getValue($env);
		$min = $this->minExpr->getValue($env);
		$max = $this->maxExpr->getValue($env);

		return $value >= $min && $value <= $max;

	}

	private $valueExpr;
	private $minExpr;
	private $maxExpr;

}

class Fabscript_Negation implements Fabscript_LogicalExpr {

	public function __construct($logicalExpr) {

		$this->logicalExpr = $logicalExpr;

	}

	public function isTrue($env) {

		return !($this->logicalExpr->isTrue($env));

	}

	private $logicalExpr;

}

class Fabscript_Conjunction implements Fabscript_LogicalExpr {

	public function __construct($logicalParts) {

		$this->parts = $logicalParts;

	}

	public function isTrue($env) {

		foreach ($this->parts as $p) {
			if (!$p->isTrue($env)) {
				return FALSE;
			}
		}

		return TRUE;

	}

	public function add($logicalExpr) {

		array_push($this->parts, $logicalExpr);

	}

	private $parts;

}

class Fabscript_Disjunction implements Fabscript_LogicalExpr {

	public function __construct($logicalParts) {

		$this->parts = $logicalParts;

	}

	public function isTrue($env) {

		foreach ($this->parts as $p) {
			if ($p->isTrue($env)) {
				return TRUE;
			}
		}

		return FALSE;

	}

	public function add($logicalExpr) {

		array_push($this->parts, $logicalExpr);

	}

	private $parts;

}

?>
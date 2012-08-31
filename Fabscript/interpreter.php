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
require_once 'Fabscript/logical_expression.php';

class Fabscript_Interpreter {

	public function interpret($ast) {

		switch ($ast->getName()) {

			case "literal":
			case "boolean":
			case "number":
			case "var_name":
			case "call":
			case "path":
				return $this->interpret_expr($ast);

			case "or":
			case "and":
			case "negation":
			case "comparison":
			case "range":
				return $this->interpret_logical_expr($ast);

			default:
				throw new Exception("Interpreter error: '" . $ast->getName() . "'");

		}

	}

	private function interpret_logical_expr($ast) {

		switch ($ast->getName()) {

			case "or":
				return $this->interpret_or($ast);

			case "and":
				return $this->interpret_and($ast);

			case "negation":
				return $this->interpret_negation($ast);

			case "comparison":
				return $this->interpret_comparison($ast);

			case "range":
				return $this->interpret_range($ast);

			default:
				throw new Exception("Interpreter error");

		}

	}

	private function interpret_or($ast) {

		return $this->interpret_composed($ast, TRUE);

	}

	private function interpret_and($ast) {

		return $this->interpret_composed($ast, FALSE);

	}

	private function interpret_composed($ast, $isDisjunction) {

		$children = $ast->getChildren();
		$parts = array();

		foreach ($children as $child) {
			$hlp = $this->interpret($child);
			$logicalExpr = !($hlp instanceof Fabscript_Path) ? $hlp : new Fabscript_BooleanPath($hlp);
			array_push($parts, $logicalExpr);
		}

		return $isDisjunction ? new Fabscript_Disjunction($parts) : new Fabscript_Conjunction($parts);

	}

	private function interpret_negation($ast) {

		$children = $ast->getChildren();
		$expr = $this->interpret($children[0]);
		$logicalExpr = !($expr instanceof Fabscript_Path) ? $expr : new Fabscript_BooleanPath($expr);

		return new Fabscript_Negation($logicalExpr);

	}

	private function interpret_comparison($ast) {

		$children = $ast->getChildren();

		foreach ($children as $child) {

			switch ($child->getName()) {

				case 'operator':
					$op = $child->getText();	
					break;

				case 'left':
					$hlp = $child->getChildren();
					$lhs = $this->interpret($hlp[0]);
					break;

				case 'right':
					$hlp = $child->getChildren();
					$rhs = $this->interpret($hlp[0]);
					break;
				
			}

		}

		return new Fabscript_Comparison($op, $lhs, $rhs);

	}

	private function interpret_range($ast) {

		$children = $ast->getChildren();

		foreach ($children as $child) {

			$hlp = $child->getChildren();

			switch ($child->getName()) {

				case 'value':
					$val = $this->interpret($hlp[0]);
					break;

				case 'min':
					$min = $this->interpret($hlp[0]);
					break;

				case 'max':
					$max = $this->interpret($hlp[0]);
					break;
				
			}

		}

		return new Fabscript_Range($val, $min, $max);

	}

	private function interpret_path($ast) {

		$elements = $ast->getChildren();
		$parent = null;
		$res = null;

		foreach ($elements as $element) {
			
			switch ($element->getName()) {
				case "var_name":
					$res = $this->interpret_var($element, $parent);
					break;
				case "call":
					$res = $this->interpret_call($element, $parent);
					break;
				default:
					throw new Exception("Interpreter error: '" . $element->getName() . "'");
			}

			$parent = $res;

		}

		return $res;

	}

	private function interpret_var($ast, $parent=null) {

		$varName = $ast->getText();

		return new Fabscript_Variable($varName, $parent);

	}

	private function interpret_call($ast, $parent=null) {

		$children = $ast->getChildren();
		$funcName = $children[0]->getText();
		$args = array();

		for ($i=1; $i<count($children); $i++) {
			array_push($args, $this->interpret_expr($children[$i]));
		}

		return new Fabscript_Call($funcName, $args, $parent);

	}

	private function interpret_expr($ast) {

		switch ($ast->getName()) {

			case "literal":
				return new Fabscript_Literal($ast->getText());

			case "boolean":
				return new Fabscript_BooleanLiteral($ast->getText());

			case "number":
				$digits = $ast->getChild("digits")->getText();
				$hlp = $ast->getChild("decimals");
				$decimals = $hlp != null ? $hlp->getText() : "";
				$isNegative = $ast->getChild("negative") != null;
				return new Fabscript_Number($digits, $decimals, $isNegative);

			case "var_name":
				return $this->interpret_var($ast);

			case "call":
				return $this->interpret_call($ast);

			case "path":
				return $this->interpret_path($ast);

			default:
				throw new Exception("Interpreter error");	

		}

	}

}

?>
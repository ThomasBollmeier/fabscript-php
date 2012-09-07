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
require_once 'Fabscript/block.php';

class Fabscript_Interpreter {

	public function interpret($ast) {

		switch ($ast->getName()) {

			case "literal":
			case "boolean":
			case "number":
			case "var_name":
			case "call":
			case "list-element":
			case "path":
			case "sum":
			case "diff":
				return $this->interpret_expr($ast);

			case "or":
			case "and":
			case "negation":
			case "comparison":
			case "range":
				return $this->interpret_logical_expr($ast);

			case "loop_begin":
				return $this->interpret_loop_begin($ast);

			case "if_begin":
				return $this->interpret_if_begin($ast);
			case "elseif":
				return $this->interpret_elseif($ast);

			case "case_begin":
				return $this->interpret_case_begin($ast);
			case "case_branch":
				return $this->interpret_case_branch($ast);

			case "var_decl":
				return $this->interpret_var_declaration($ast);
			case "assign":
				return $this->interpret_assignment($ast);

			default:
				throw new Exception("Interpreter error: '" . $ast->getName() . "'");

		}

	}

	private function interpret_loop_begin($ast) {

		$children = $ast->getChildren();

		$table = null;
		$line = "";
		$key = "";
		$filter = null;

		foreach ($children as $child) {

			$hlp = $child->getChildren();
			$node = $hlp[0];

			switch ($child->getName()) {
				case "table":
				case "dictionary":
					$table = $this->interpret($node);
					break;
				case "line":
				case "value":
					$line = $node->getText();
					break;
				case "key":
					$key = $node->getText();
					break;
				case "filter":
					$filter = $this->interpret($node);
					break;
			}

		}

		return new Fabscript_Loop($table, $line, $key, $filter);

	}

	private function interpret_if_begin($ast) {

		$children = $ast->getChildren();
		$conditionNode = $children[0];
		$condition = $this->convertToLogicalExpr($this->interpret($conditionNode));

		return new Fabscript_Branch($condition);

	}

	private function interpret_elseif($ast) {

		$children = $ast->getChildren();
		$conditionNode = $children[0];
		
		return $this->convertToLogicalExpr($this->interpret($conditionNode));

	}

	private function interpret_case_begin($ast) {

		$children = $ast->getChildren();

		return $this->interpret($children[0]); // <-- path expression from "case <pathExpr> in..."

	}

	private function interpret_case_branch($ast) {

		$res = array();

		$children = $ast->getChildren();
		foreach ($children as $child) {
			$res[] = $this->interpret($child); // <-- expression from "<expr> [,<expr2> [,...] ])"
		}

		return $res;

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
				case "list-element":
					$res = $this->interpret_list_element($element, $parent);
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

	private function interpret_list_element($ast, $parent=null) {

		$children = $ast->getChildren();

		$list = null;
		$indices = array();

		for ($i=0; $i < count($children); $i++) {

			if ($i == 0) {

				switch ($children[$i]->getName()) {
					case "var_name":
						$list = $this->interpret_var($children[$i], $parent);
						break;
					case "call":
						$list = $this->interpret_call($children[$i], $parent);
						break;
					default:
						throw new Exception("Error in list element");
				}

			} else {

				$indices[] = $this->interpret($children[$i]);

			}

		}

		return new Fabscript_ListElement($list, $indices);

	}

	private function interpret_binop($ast) {

		$children = $ast->getChildren();
		$op1 = $this->interpret($children[0]);
		$op2 = $this->interpret($children[1]);

		switch ($ast->getName()) {
			case "sum":
				return new Fabscript_BinOp(Fabscript_BinOp::OP_PLUS, $op1, $op2);
			case "diff":
				return new Fabscript_BinOp(Fabscript_BinOp::OP_MINUS, $op1, $op2);
			default:
				throw new Exception("Unknown binary operator");

		}

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

			case "list-element":
				return $this->interpret_list_element($ast);

			case "path":
				return $this->interpret_path($ast);

			case "sum":
			case "diff":
				return $this->interpret_binop($ast);

			default:
				throw new Exception("Interpreter error");	

		}

	}

	private function interpret_var_declaration($ast) {

		$children = $ast->getChildren();
		$varName = $children[0]->getText();

		if (count($children) == 2) {
			$initExpr = $this->interpret($children[1]);
		} else {
			$initExpr = null;
		}

		return new Fabscript_Declaration($varName, $initExpr);

	}

	private function interpret_assignment($ast) {

		$children = $ast->getChildren();
		$varName = $children[0]->getText();
		$valueExpr = $this->interpret($children[1]);

		return new Fabscript_Assignment($varName, $valueExpr);

	}

	private function convertToLogicalExpr($expr) {

		if ($expr instanceof Fabscript_LogicalExpr) {
			return $expr;
		} elseif ($expr instanceof Fabscript_Path) {
			return new Fabscript_BooleanPath($expr);
		} else {
			throw new Exception("Cannot convert to logical expression");
		}

	}

}

?>
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

			default:
				throw new Exception("Symbol interpreter error");

		}

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
					throw new Exception("Symbol interpreter error: '" . $element->getName() . "'");
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
				$child = $ast->getChildAccess();
				$digits = $child->digits->getText();
				$decimals = $child->decimals ? $child->decimals->getText() : "";
				$isNegative = $child->negative != null;
				return new Fabscript_Number($digits, $decimals, $isNegative);

			case "var_name":
				return $this->interpret_var($ast);

			case "call":
				return $this->interpret_call($ast);

			case "path":
				return $this->interpret_path($ast);

			default:
				throw new Exception("Symbol interpreter error");	

		}

	}

}

?>
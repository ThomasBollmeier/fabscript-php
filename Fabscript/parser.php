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

require_once 'Bovinus/parser.php';

// edit-section init {

function fabscript_literal($tokenNode) {

	$text = trim($tokenNode->getText(), "\"'");
	$res = new Bovinus_AstNode('literal', $text);

	return $res;

}

// } edit-section-end

class Fabscript_Symbol_Parser extends Bovinus_Parser {

	public function __construct() {
		
		parent::__construct(new _Fabscript_Symbol_Grammar());
		
		$this->enableLineComments('#');
		$this->enableFullBacktracking(TRUE);
		
	}
}

class Fabscript_Command_Parser extends Bovinus_Parser {

	public function __construct() {
		
		parent::__construct(new _Fabscript_Command_Grammar());
		
		$this->enableLineComments('#');
		$this->enableFullBacktracking(TRUE);
		
	}
}

// ========== Private section ==========

$Fabscript_all_token_types = array();

$Fabscript_ID = new Bovinus_Word('[a-zA-Z_][a-zA-Z0-9_]*');
array_push($Fabscript_all_token_types, $Fabscript_ID);

$Fabscript_DIGITS = new Bovinus_Word('[0-9]+');
array_push($Fabscript_all_token_types, $Fabscript_DIGITS);

$Fabscript_LIT = Bovinus_Literal::get();
array_push($Fabscript_all_token_types, $Fabscript_LIT);

$Fabscript_BRACE_OPEN = new Bovinus_Separator('[', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_BRACE_OPEN);

$Fabscript_BRACE_CLOSE = new Bovinus_Separator(']', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_BRACE_CLOSE);

$Fabscript_PAR_OPEN = new Bovinus_Separator('(', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_PAR_OPEN);

$Fabscript_PAR_CLOSE = new Bovinus_Separator(')', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_PAR_CLOSE);

$Fabscript_DOT = new Bovinus_Separator('.', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_DOT);

$Fabscript_COMMA = new Bovinus_Separator(',', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_COMMA);

$Fabscript_PLUS = new Bovinus_Separator('+', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_PLUS);

$Fabscript_MINUS = new Bovinus_Separator('-', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_MINUS);

$Fabscript_EQ = new Bovinus_Separator('==', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_EQ);

$Fabscript_NE = new Bovinus_Separator('<>', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_NE);

$Fabscript_GE = new Bovinus_Separator('>=', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_GE);

$Fabscript_LE = new Bovinus_Separator('<=', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_LE);

$Fabscript_ASSIGN = new Bovinus_Separator('=', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_ASSIGN);

$Fabscript_GT = new Bovinus_Separator('>', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_GT);

$Fabscript_LT = new Bovinus_Separator('<', TRUE, TRUE);
array_push($Fabscript_all_token_types, $Fabscript_LT);

$Fabscript_KEY_1 = new Bovinus_Keyword('for', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_1);

$Fabscript_KEY_2 = new Bovinus_Keyword('each', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_2);

$Fabscript_KEY_3 = new Bovinus_Keyword('key', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_3);

$Fabscript_KEY_4 = new Bovinus_Keyword('value', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_4);

$Fabscript_KEY_5 = new Bovinus_Keyword('pair', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_5);

$Fabscript_KEY_6 = new Bovinus_Keyword('in', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_6);

$Fabscript_KEY_7 = new Bovinus_Keyword('all', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_7);

$Fabscript_KEY_8 = new Bovinus_Keyword('where', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_8);

$Fabscript_KEY_9 = new Bovinus_Keyword('do', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_9);

$Fabscript_KEY_10 = new Bovinus_Keyword('endfor', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_10);

$Fabscript_KEY_11 = new Bovinus_Keyword('done', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_11);

$Fabscript_KEY_12 = new Bovinus_Keyword('while', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_12);

$Fabscript_KEY_13 = new Bovinus_Keyword('endwhile', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_13);

$Fabscript_KEY_14 = new Bovinus_Keyword('if', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_14);

$Fabscript_KEY_15 = new Bovinus_Keyword('elseif', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_15);

$Fabscript_KEY_16 = new Bovinus_Keyword('then', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_16);

$Fabscript_KEY_17 = new Bovinus_Keyword('begin', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_17);

$Fabscript_KEY_18 = new Bovinus_Keyword('else', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_18);

$Fabscript_KEY_19 = new Bovinus_Keyword('endif', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_19);

$Fabscript_KEY_20 = new Bovinus_Keyword('case', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_20);

$Fabscript_KEY_21 = new Bovinus_Keyword('*', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_21);

$Fabscript_KEY_22 = new Bovinus_Keyword('endcase', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_22);

$Fabscript_KEY_23 = new Bovinus_Keyword('declare', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_23);

$Fabscript_KEY_24 = new Bovinus_Keyword('define', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_24);

$Fabscript_KEY_25 = new Bovinus_Keyword('snippet', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_25);

$Fabscript_KEY_26 = new Bovinus_Keyword('endsnippet', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_26);

$Fabscript_KEY_27 = new Bovinus_Keyword('paste', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_27);

$Fabscript_KEY_28 = new Bovinus_Keyword('indent', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_28);

$Fabscript_KEY_29 = new Bovinus_Keyword('by', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_29);

$Fabscript_KEY_30 = new Bovinus_Keyword('or', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_30);

$Fabscript_KEY_31 = new Bovinus_Keyword('and', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_31);

$Fabscript_KEY_32 = new Bovinus_Keyword('not', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_32);

$Fabscript_KEY_33 = new Bovinus_Keyword('between', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_33);

$Fabscript_KEY_34 = new Bovinus_Keyword('TRUE', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_34);

$Fabscript_KEY_35 = new Bovinus_Keyword('FALSE', TRUE);
array_push($Fabscript_all_token_types, $Fabscript_KEY_35);

class _Fabscript_Loop_End_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('loop_end', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		$start->connect($this->_sub_2())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section loop_end-transform {
		
		return new Bovinus_AstNode($astNode->getName());
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		return $this->_sub_1_1();
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_KEY_10;
		
		return bovinus_tokenNode($Fabscript_KEY_10);
		
	}
	
	private function _sub_2() {
		
		return $this->_sub_2_1();
		
	}
	
	private function _sub_2_1() {
		
		global $Fabscript_KEY_11;
		
		return bovinus_tokenNode($Fabscript_KEY_11);
		
	}
	
	// edit-section loop_end-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Else_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('else', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section else-transform {
		
		return new Bovinus_AstNode($astNode->getName());
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		return $this->_sub_1_1();
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_KEY_18;
		
		return bovinus_tokenNode($Fabscript_KEY_18);
		
	}
	
	// edit-section else-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Snippet_End_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('snippet_end', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		
		return new Bovinus_AstNode('snippet_end');
		
		
	}
	
	private function _sub_1() {
		
		return $this->_sub_1_1();
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_KEY_26;
		
		return bovinus_tokenNode($Fabscript_KEY_26);
		
	}
	
	// edit-section snippet_end-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Case_End_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('case_end', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section case_end-transform {
		
		return new Bovinus_AstNode($astNode->getName());
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		return $this->_sub_1_1();
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_KEY_22;
		
		return bovinus_tokenNode($Fabscript_KEY_22);
		
	}
	
	// edit-section case_end-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_If_End_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('if_end', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section if_end-transform {
		
		return new Bovinus_AstNode($astNode->getName());
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		return $this->_sub_1_1();
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_KEY_19;
		
		return bovinus_tokenNode($Fabscript_KEY_19);
		
	}
	
	// edit-section if_end-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Snippet_Begin_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('snippet_begin', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		
		$res = new Bovinus_AstNode('snippet_begin');
		
		$name = $astNode->getChildById('name')->getText();
		$res->addChild(new Bovinus_AstNode('name', $name));
		
		$paramNodes = $astNode->getChildrenById('param');
		foreach ($paramNodes as $paramNode) {
		    $res->addChild(new Bovinus_AstNode('param', $paramNode->getText()));
		}
		
		return $res;
		
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		array_push($elements, $this->_sub_1_3());
		array_push($elements, $this->_sub_1_4());
		array_push($elements, $this->_sub_1_5());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_KEY_25;
		
		return bovinus_tokenNode($Fabscript_KEY_25);
		
	}
	
	private function _sub_1_2() {
		
		global $Fabscript_ID;
		
		return bovinus_tokenNode($Fabscript_ID, 'name');
		
	}
	
	private function _sub_1_3() {
		
		global $Fabscript_PAR_OPEN;
		
		return bovinus_tokenNode($Fabscript_PAR_OPEN);
		
	}
	
	private function _sub_1_4() {
		
		return bovinus_zero_to_one($this->_sub_1_4_1());
		
	}
	
	private function _sub_1_4_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_4_1_1());
		array_push($elements, $this->_sub_1_4_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_4_1_1() {
		
		global $Fabscript_ID;
		
		return bovinus_tokenNode($Fabscript_ID, 'param');
		
	}
	
	private function _sub_1_4_1_2() {
		
		return bovinus_zero_to_many($this->_sub_1_4_1_2_1());
		
	}
	
	private function _sub_1_4_1_2_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_4_1_2_1_1());
		array_push($elements, $this->_sub_1_4_1_2_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_4_1_2_1_1() {
		
		global $Fabscript_COMMA;
		
		return bovinus_tokenNode($Fabscript_COMMA);
		
	}
	
	private function _sub_1_4_1_2_1_2() {
		
		global $Fabscript_ID;
		
		return bovinus_tokenNode($Fabscript_ID, 'param');
		
	}
	
	private function _sub_1_5() {
		
		global $Fabscript_PAR_CLOSE;
		
		return bovinus_tokenNode($Fabscript_PAR_CLOSE);
		
	}
	
	// edit-section snippet_begin-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Number_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('number', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section number-transform {

		$res = new Bovinus_AstNode('number');
		$child = $astNode->getChildAccess();

		$digits = new Bovinus_AstNode('digits', $child->digits->getText());
		$res->addChild($digits);

		$node = $child->decimals;
		if ($node) {
			$decimals = new Bovinus_AstNode('decimals', $node->getText());
			$res->addChild($decimals);
		}

		$node = $child->sign;
		if ($node && $node->getText() == '-') {
			$res->addChild(new Bovinus_AstNode('negative'));
		}
		
		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		array_push($elements, $this->_sub_1_3());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		$branches = array();
		array_push($branches, $this->_sub_1_1_1());
		array_push($branches, $this->_sub_1_1_2());
		
		return bovinus_zero_to_one(new Bovinus_Fork($branches));
		
	}
	
	private function _sub_1_1_1() {
		
		return $this->_sub_1_1_1_1();
		
	}
	
	private function _sub_1_1_1_1() {
		
		global $Fabscript_PLUS;
		
		return bovinus_tokenNode($Fabscript_PLUS, 'sign');
		
	}
	
	private function _sub_1_1_2() {
		
		return $this->_sub_1_1_2_1();
		
	}
	
	private function _sub_1_1_2_1() {
		
		global $Fabscript_MINUS;
		
		return bovinus_tokenNode($Fabscript_MINUS, 'sign');
		
	}
	
	private function _sub_1_2() {
		
		global $Fabscript_DIGITS;
		
		return bovinus_tokenNode($Fabscript_DIGITS, 'digits');
		
	}
	
	private function _sub_1_3() {
		
		return bovinus_zero_to_one($this->_sub_1_3_1());
		
	}
	
	private function _sub_1_3_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_3_1_1());
		array_push($elements, $this->_sub_1_3_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_3_1_1() {
		
		global $Fabscript_DOT;
		
		return bovinus_tokenNode($Fabscript_DOT);
		
	}
	
	private function _sub_1_3_1_2() {
		
		global $Fabscript_DIGITS;
		
		return bovinus_tokenNode($Fabscript_DIGITS, 'decimals');
		
	}
	
	// edit-section number-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Var_Name_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('var_name', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section var_name-transform {

		$children = $astNode->getChildren();
		$res = new Bovinus_AstNode($astNode->getName(), $children[0]->getText());
		
		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		return $this->_sub_1_1();
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_ID;
		
		return bovinus_tokenNode($Fabscript_ID);
		
	}
	
	// edit-section var_name-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_While_End_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('while_end', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section while_end-transform {
		
		return new Bovinus_AstNode($astNode->getName());
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		return $this->_sub_1_1();
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_KEY_13;
		
		return bovinus_tokenNode($Fabscript_KEY_13);
		
	}
	
	// edit-section while_end-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Boolean_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('boolean', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		$start->connect($this->_sub_2())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section boolean-transform {

		$children = $astNode->getChildren();
		
		return new Bovinus_AstNode($astNode->getName(), $children[0]->getText());
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		return $this->_sub_1_1();
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_KEY_34;
		
		return bovinus_tokenNode($Fabscript_KEY_34);
		
	}
	
	private function _sub_2() {
		
		return $this->_sub_2_1();
		
	}
	
	private function _sub_2_1() {
		
		global $Fabscript_KEY_35;
		
		return bovinus_tokenNode($Fabscript_KEY_35);
		
	}
	
	// edit-section boolean-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Expr_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('expr', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		$start->connect($this->_sub_2())->connect($end);
		$start->connect($this->_sub_3())->connect($end);
		$start->connect($this->_sub_4())->connect($end);
		$start->connect($this->_sub_5())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section expr-transform {

		$children = $astNode->getChildren();

		if ($children[0]->getId() != 'lit') {
			return $children[0];
		} else {
			return fabscript_literal($children[0]);
		}

		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		return $this->_sub_1_1();
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_LIT;
		
		return bovinus_tokenNode($Fabscript_LIT, 'lit');
		
	}
	
	private function _sub_2() {
		
		return $this->_sub_2_1();
		
	}
	
	private function _sub_2_1() {
		
		return new _Fabscript_Number_Rule();
		
	}
	
	private function _sub_3() {
		
		return $this->_sub_3_1();
		
	}
	
	private function _sub_3_1() {
		
		return new _Fabscript_Boolean_Rule();
		
	}
	
	private function _sub_4() {
		
		return $this->_sub_4_1();
		
	}
	
	private function _sub_4_1() {
		
		return new _Fabscript_Path_Rule();
		
	}
	
	private function _sub_5() {
		
		return $this->_sub_5_1();
		
	}
	
	private function _sub_5_1() {
		
		return new _Fabscript_Sum_Rule();
		
	}
	
	// edit-section expr-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Operand_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('operand', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		$start->connect($this->_sub_2())->connect($end);
		$start->connect($this->_sub_3())->connect($end);
		$start->connect($this->_sub_4())->connect($end);
		$start->connect($this->_sub_5())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section operand-transform {

		$children = $astNode->getChildren();

		if (count($children) == 1) {

			$res = $children[0];

			if ($res->getName() == "token") {
				$res = fabscript_literal($res);
			}

			return $res;

		} else {

			return $children[1];

		}
		
		return $astNode;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		array_push($elements, $this->_sub_1_3());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_PAR_OPEN;
		
		return bovinus_tokenNode($Fabscript_PAR_OPEN);
		
	}
	
	private function _sub_1_2() {
		
		return new _Fabscript_Expr_Rule();
		
	}
	
	private function _sub_1_3() {
		
		global $Fabscript_PAR_CLOSE;
		
		return bovinus_tokenNode($Fabscript_PAR_CLOSE);
		
	}
	
	private function _sub_2() {
		
		return $this->_sub_2_1();
		
	}
	
	private function _sub_2_1() {
		
		global $Fabscript_LIT;
		
		return bovinus_tokenNode($Fabscript_LIT);
		
	}
	
	private function _sub_3() {
		
		return $this->_sub_3_1();
		
	}
	
	private function _sub_3_1() {
		
		return new _Fabscript_Number_Rule();
		
	}
	
	private function _sub_4() {
		
		return $this->_sub_4_1();
		
	}
	
	private function _sub_4_1() {
		
		return new _Fabscript_Boolean_Rule();
		
	}
	
	private function _sub_5() {
		
		return $this->_sub_5_1();
		
	}
	
	private function _sub_5_1() {
		
		return new _Fabscript_Path_Rule();
		
	}
	
	// edit-section operand-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Path_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('path', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section path-transform {
		
		$child = $astNode->getChildAccess();

		$elements = $child->sub;
		if (!is_array($elements)) {

			$res = $elements;
			$res->setId('');

		} else {

			$res = new Bovinus_AstNode($astNode->getName());
			foreach ($elements as $element) {
				$element->setId('');
				$res->addChild($element);
			}

		}

		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		return new _Fabscript_Path_Element_Rule('sub');
		
	}
	
	private function _sub_1_2() {
		
		return bovinus_zero_to_many($this->_sub_1_2_1());
		
	}
	
	private function _sub_1_2_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_2_1_1());
		array_push($elements, $this->_sub_1_2_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_2_1_1() {
		
		global $Fabscript_DOT;
		
		return bovinus_tokenNode($Fabscript_DOT);
		
	}
	
	private function _sub_1_2_1_2() {
		
		return new _Fabscript_Path_Element_Rule('sub');
		
	}
	
	// edit-section path-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Var_Decl_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('var_decl', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		$start->connect($this->_sub_2())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section var_decl-transform {

		$res = new Bovinus_AstNode($astNode->getName());
		$child = $astNode->getChildAccess();

		$name = $child->name;
		$name->setId('');
		$res->addChild($name);

		$value = $child->value;
		if ($value) {
			$value->setId('');
			$res->addChild($value);
		}
		
		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_KEY_23;
		
		return bovinus_tokenNode($Fabscript_KEY_23);
		
	}
	
	private function _sub_1_2() {
		
		return new _Fabscript_Var_Name_Rule('name');
		
	}
	
	private function _sub_2() {
		
		$elements = array();
		array_push($elements, $this->_sub_2_1());
		array_push($elements, $this->_sub_2_2());
		array_push($elements, $this->_sub_2_3());
		array_push($elements, $this->_sub_2_4());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_2_1() {
		
		global $Fabscript_KEY_24;
		
		return bovinus_tokenNode($Fabscript_KEY_24);
		
	}
	
	private function _sub_2_2() {
		
		return new _Fabscript_Var_Name_Rule('name');
		
	}
	
	private function _sub_2_3() {
		
		global $Fabscript_ASSIGN;
		
		return bovinus_tokenNode($Fabscript_ASSIGN);
		
	}
	
	private function _sub_2_4() {
		
		return new _Fabscript_Expr_Rule('value');
		
	}
	
	// edit-section var_decl-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Assign_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('assign', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section assign-transform {
		
		$res = new Bovinus_AstNode($astNode->getName());
		$child = $astNode->getChildAccess();

		$name = $child->name;
		$name->setId('');
		$res->addChild($name);

		$val = $child->value;
		$val->setId('');
		$res->addChild($val);
		
		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		array_push($elements, $this->_sub_1_3());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		return new _Fabscript_Var_Name_Rule('name');
		
	}
	
	private function _sub_1_2() {
		
		global $Fabscript_ASSIGN;
		
		return bovinus_tokenNode($Fabscript_ASSIGN);
		
	}
	
	private function _sub_1_3() {
		
		return new _Fabscript_Expr_Rule('value');
		
	}
	
	// edit-section assign-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Symbol_Grammar extends Bovinus_Grammar {

	public function __construct() {
	
		global $Fabscript_all_token_types;
		
		parent::__construct('_Fabscript_Symbol_Grammar', $Fabscript_all_token_types);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section symbol-transform {

		$children = $astNode->getChildren();
		$children[0]->setId('');
		
		return $children[0];
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		return $this->_sub_1_1();
		
	}
	
	private function _sub_1_1() {
		
		return new _Fabscript_Path_Rule();
		
	}
	
	// edit-section symbol-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Range_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('range', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section range-transform {

		$res = new Bovinus_AstNode($astNode->getName());
		$child = $astNode->getChildAccess();

		$node = new Bovinus_AstNode('value');
		$res->addChild($node);
		$node2 = $child->value;
		if ($node2->getName() == "token") {
			$node2 = fabscript_literal($node2);
		}
		$node2->setId('');
		$node->addChild($node2);

		$node = new Bovinus_AstNode('min');
		$res->addChild($node);
		$node2 = $child->min;
		if ($node2->getName() == "token") {
			$node2 = fabscript_literal($node2);
		}
		$node2->setId('');
		$node->addChild($node2);

		$node = new Bovinus_AstNode('max');
		$res->addChild($node);
		$node2 = $child->max;
		if ($node2->getName() == "token") {
			$node2 = fabscript_literal($node2);
		}
		$node2->setId('');
		$node->addChild($node2);

		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		array_push($elements, $this->_sub_1_3());
		array_push($elements, $this->_sub_1_4());
		array_push($elements, $this->_sub_1_5());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		$branches = array();
		array_push($branches, $this->_sub_1_1_1());
		array_push($branches, $this->_sub_1_1_2());
		array_push($branches, $this->_sub_1_1_3());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_1_1_1() {
		
		return $this->_sub_1_1_1_1();
		
	}
	
	private function _sub_1_1_1_1() {
		
		global $Fabscript_LIT;
		
		return bovinus_tokenNode($Fabscript_LIT, 'value');
		
	}
	
	private function _sub_1_1_2() {
		
		return $this->_sub_1_1_2_1();
		
	}
	
	private function _sub_1_1_2_1() {
		
		return new _Fabscript_Number_Rule('value');
		
	}
	
	private function _sub_1_1_3() {
		
		return $this->_sub_1_1_3_1();
		
	}
	
	private function _sub_1_1_3_1() {
		
		return new _Fabscript_Path_Rule('value');
		
	}
	
	private function _sub_1_2() {
		
		global $Fabscript_KEY_33;
		
		return bovinus_tokenNode($Fabscript_KEY_33);
		
	}
	
	private function _sub_1_3() {
		
		$branches = array();
		array_push($branches, $this->_sub_1_3_1());
		array_push($branches, $this->_sub_1_3_2());
		array_push($branches, $this->_sub_1_3_3());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_1_3_1() {
		
		return $this->_sub_1_3_1_1();
		
	}
	
	private function _sub_1_3_1_1() {
		
		global $Fabscript_LIT;
		
		return bovinus_tokenNode($Fabscript_LIT, 'min');
		
	}
	
	private function _sub_1_3_2() {
		
		return $this->_sub_1_3_2_1();
		
	}
	
	private function _sub_1_3_2_1() {
		
		return new _Fabscript_Number_Rule('min');
		
	}
	
	private function _sub_1_3_3() {
		
		return $this->_sub_1_3_3_1();
		
	}
	
	private function _sub_1_3_3_1() {
		
		return new _Fabscript_Path_Rule('min');
		
	}
	
	private function _sub_1_4() {
		
		global $Fabscript_KEY_31;
		
		return bovinus_tokenNode($Fabscript_KEY_31);
		
	}
	
	private function _sub_1_5() {
		
		$branches = array();
		array_push($branches, $this->_sub_1_5_1());
		array_push($branches, $this->_sub_1_5_2());
		array_push($branches, $this->_sub_1_5_3());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_1_5_1() {
		
		return $this->_sub_1_5_1_1();
		
	}
	
	private function _sub_1_5_1_1() {
		
		global $Fabscript_LIT;
		
		return bovinus_tokenNode($Fabscript_LIT, 'max');
		
	}
	
	private function _sub_1_5_2() {
		
		return $this->_sub_1_5_2_1();
		
	}
	
	private function _sub_1_5_2_1() {
		
		return new _Fabscript_Number_Rule('max');
		
	}
	
	private function _sub_1_5_3() {
		
		return $this->_sub_1_5_3_1();
		
	}
	
	private function _sub_1_5_3_1() {
		
		return new _Fabscript_Path_Rule('max');
		
	}
	
	// edit-section range-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Comparison_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('comparison', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section comparison-transform {

		$res = new Bovinus_AstNode($astNode->getName());
		$child = $astNode->getChildAccess();

		$node = new Bovinus_AstNode('left');
		$lhs = $child->lhs;
		$lhs->setId('');
		$node->addChild($lhs);
		$res->addChild($node);

		$node = new Bovinus_AstNode('operator', $child->op->getText());
		$res->addChild($node);

		$node = new Bovinus_AstNode('right');
		$rhs = $child->rhs;
		$rhs->setId('');
		$node->addChild($rhs);
		$res->addChild($node);

		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		array_push($elements, $this->_sub_1_3());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		return new _Fabscript_Expr_Rule('lhs');
		
	}
	
	private function _sub_1_2() {
		
		$branches = array();
		array_push($branches, $this->_sub_1_2_1());
		array_push($branches, $this->_sub_1_2_2());
		array_push($branches, $this->_sub_1_2_3());
		array_push($branches, $this->_sub_1_2_4());
		array_push($branches, $this->_sub_1_2_5());
		array_push($branches, $this->_sub_1_2_6());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_1_2_1() {
		
		return $this->_sub_1_2_1_1();
		
	}
	
	private function _sub_1_2_1_1() {
		
		global $Fabscript_EQ;
		
		return bovinus_tokenNode($Fabscript_EQ, 'op');
		
	}
	
	private function _sub_1_2_2() {
		
		return $this->_sub_1_2_2_1();
		
	}
	
	private function _sub_1_2_2_1() {
		
		global $Fabscript_NE;
		
		return bovinus_tokenNode($Fabscript_NE, 'op');
		
	}
	
	private function _sub_1_2_3() {
		
		return $this->_sub_1_2_3_1();
		
	}
	
	private function _sub_1_2_3_1() {
		
		global $Fabscript_GT;
		
		return bovinus_tokenNode($Fabscript_GT, 'op');
		
	}
	
	private function _sub_1_2_4() {
		
		return $this->_sub_1_2_4_1();
		
	}
	
	private function _sub_1_2_4_1() {
		
		global $Fabscript_GE;
		
		return bovinus_tokenNode($Fabscript_GE, 'op');
		
	}
	
	private function _sub_1_2_5() {
		
		return $this->_sub_1_2_5_1();
		
	}
	
	private function _sub_1_2_5_1() {
		
		global $Fabscript_LT;
		
		return bovinus_tokenNode($Fabscript_LT, 'op');
		
	}
	
	private function _sub_1_2_6() {
		
		return $this->_sub_1_2_6_1();
		
	}
	
	private function _sub_1_2_6_1() {
		
		global $Fabscript_LE;
		
		return bovinus_tokenNode($Fabscript_LE, 'op');
		
	}
	
	private function _sub_1_3() {
		
		$branches = array();
		array_push($branches, $this->_sub_1_3_1());
		array_push($branches, $this->_sub_1_3_2());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_1_3_1() {
		
		return $this->_sub_1_3_1_1();
		
	}
	
	private function _sub_1_3_1_1() {
		
		return new _Fabscript_Expr_Rule('rhs');
		
	}
	
	private function _sub_1_3_2() {
		
		return $this->_sub_1_3_2_1();
		
	}
	
	private function _sub_1_3_2_1() {
		
		return new _Fabscript_Disjunction_Rule('rhs');
		
	}
	
	// edit-section comparison-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Paste_Snippet_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('paste_snippet', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		
		$res = new Bovinus_AstNode('paste_snippet');
		
		$name = $astNode->getChildById('name')->getText();
		$res->addChild(new Bovinus_AstNode('name', $name));
		
		$indentLevel = $astNode->getChildById('indent');
		if ($indentLevel != null) {
		    $indentLevel->setId('');
		    $indent = new Bovinus_AstNode('indent_by');
		    $indent->addChild($indentLevel);
		    $res->addChild($indent);
		}
		
		$argNodes = $astNode->getChildrenById('arg');
		if (count($argNodes) > 0) {
		    $args = new Bovinus_AstNode('arguments');
		    $res->addChild($args);
		    foreach ($argNodes as $argNode) {
		        $argNode->setId('');
		        $args->addChild($argNode);
		    }
		}
		
		return $res;
		
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		array_push($elements, $this->_sub_1_3());
		array_push($elements, $this->_sub_1_4());
		array_push($elements, $this->_sub_1_5());
		array_push($elements, $this->_sub_1_6());
		array_push($elements, $this->_sub_1_7());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_KEY_27;
		
		return bovinus_tokenNode($Fabscript_KEY_27);
		
	}
	
	private function _sub_1_2() {
		
		global $Fabscript_KEY_25;
		
		return bovinus_tokenNode($Fabscript_KEY_25);
		
	}
	
	private function _sub_1_3() {
		
		global $Fabscript_ID;
		
		return bovinus_tokenNode($Fabscript_ID, 'name');
		
	}
	
	private function _sub_1_4() {
		
		global $Fabscript_PAR_OPEN;
		
		return bovinus_tokenNode($Fabscript_PAR_OPEN);
		
	}
	
	private function _sub_1_5() {
		
		return bovinus_zero_to_one($this->_sub_1_5_1());
		
	}
	
	private function _sub_1_5_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_5_1_1());
		array_push($elements, $this->_sub_1_5_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_5_1_1() {
		
		return new _Fabscript_Expr_Rule('arg');
		
	}
	
	private function _sub_1_5_1_2() {
		
		return bovinus_zero_to_many($this->_sub_1_5_1_2_1());
		
	}
	
	private function _sub_1_5_1_2_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_5_1_2_1_1());
		array_push($elements, $this->_sub_1_5_1_2_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_5_1_2_1_1() {
		
		global $Fabscript_COMMA;
		
		return bovinus_tokenNode($Fabscript_COMMA);
		
	}
	
	private function _sub_1_5_1_2_1_2() {
		
		return new _Fabscript_Expr_Rule('arg');
		
	}
	
	private function _sub_1_6() {
		
		global $Fabscript_PAR_CLOSE;
		
		return bovinus_tokenNode($Fabscript_PAR_CLOSE);
		
	}
	
	private function _sub_1_7() {
		
		return bovinus_zero_to_one($this->_sub_1_7_1());
		
	}
	
	private function _sub_1_7_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_7_1_1());
		array_push($elements, $this->_sub_1_7_1_2());
		array_push($elements, $this->_sub_1_7_1_3());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_7_1_1() {
		
		global $Fabscript_KEY_28;
		
		return bovinus_tokenNode($Fabscript_KEY_28);
		
	}
	
	private function _sub_1_7_1_2() {
		
		global $Fabscript_KEY_29;
		
		return bovinus_tokenNode($Fabscript_KEY_29);
		
	}
	
	private function _sub_1_7_1_3() {
		
		return new _Fabscript_Expr_Rule('indent');
		
	}
	
	// edit-section paste_snippet-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Case_Branch_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('case_branch', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section case_branch-transform {

		$children = $astNode->getChildren();

		if ($children[0]->getId() != 'default') {
			$res = new Bovinus_AstNode($astNode->getName());
			foreach ($children as $child) {
				if ($child->getId() == 'expr') {
					$child->setId('');
					$res->addChild($child);		
				}
			}
			return $res;
		} else {
			return new Bovinus_AstNode('default_branch');
		}
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		$branches = array();
		array_push($branches, $this->_sub_1_1_1());
		array_push($branches, $this->_sub_1_1_2());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_1_1_1() {
		
		return $this->_sub_1_1_1_1();
		
	}
	
	private function _sub_1_1_1_1() {
		
		global $Fabscript_KEY_21;
		
		return bovinus_tokenNode($Fabscript_KEY_21, 'default');
		
	}
	
	private function _sub_1_1_2() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1_2_1());
		array_push($elements, $this->_sub_1_1_2_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1_2_1() {
		
		return new _Fabscript_Expr_Rule('expr');
		
	}
	
	private function _sub_1_1_2_2() {
		
		return bovinus_zero_to_many($this->_sub_1_1_2_2_1());
		
	}
	
	private function _sub_1_1_2_2_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1_2_2_1_1());
		array_push($elements, $this->_sub_1_1_2_2_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1_2_2_1_1() {
		
		global $Fabscript_COMMA;
		
		return bovinus_tokenNode($Fabscript_COMMA);
		
	}
	
	private function _sub_1_1_2_2_1_2() {
		
		return new _Fabscript_Expr_Rule('expr');
		
	}
	
	private function _sub_1_2() {
		
		global $Fabscript_PAR_CLOSE;
		
		return bovinus_tokenNode($Fabscript_PAR_CLOSE);
		
	}
	
	// edit-section case_branch-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Call_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('call', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section call-transform {

		$res = new Bovinus_AstNode($astNode->getName());
		$child = $astNode->getChildAccess();

		$name = new Bovinus_AstNode('name', $child->name->getText());
		$res->addChild($name);

		$args = $child->arg;
		if ($args) {
			if ( !is_array($args) ) {
				$args->setId('');
				$res->addChild($args);
			} else {
				foreach ($args as $arg) {
					$arg->setId('');
					$res->addChild($arg);
				}
			}
		}
		
		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_ID;
		
		return bovinus_tokenNode($Fabscript_ID, 'name');
		
	}
	
	private function _sub_1_2() {
		
		$branches = array();
		array_push($branches, $this->_sub_1_2_1());
		array_push($branches, $this->_sub_1_2_2());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_1_2_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_2_1_1());
		array_push($elements, $this->_sub_1_2_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_2_1_1() {
		
		global $Fabscript_PAR_OPEN;
		
		return bovinus_tokenNode($Fabscript_PAR_OPEN);
		
	}
	
	private function _sub_1_2_1_2() {
		
		global $Fabscript_PAR_CLOSE;
		
		return bovinus_tokenNode($Fabscript_PAR_CLOSE);
		
	}
	
	private function _sub_1_2_2() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_2_2_1());
		array_push($elements, $this->_sub_1_2_2_2());
		array_push($elements, $this->_sub_1_2_2_3());
		array_push($elements, $this->_sub_1_2_2_4());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_2_2_1() {
		
		global $Fabscript_PAR_OPEN;
		
		return bovinus_tokenNode($Fabscript_PAR_OPEN);
		
	}
	
	private function _sub_1_2_2_2() {
		
		return new _Fabscript_Expr_Rule('arg');
		
	}
	
	private function _sub_1_2_2_3() {
		
		return bovinus_zero_to_many($this->_sub_1_2_2_3_1());
		
	}
	
	private function _sub_1_2_2_3_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_2_2_3_1_1());
		array_push($elements, $this->_sub_1_2_2_3_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_2_2_3_1_1() {
		
		global $Fabscript_COMMA;
		
		return bovinus_tokenNode($Fabscript_COMMA);
		
	}
	
	private function _sub_1_2_2_3_1_2() {
		
		return new _Fabscript_Expr_Rule('arg');
		
	}
	
	private function _sub_1_2_2_4() {
		
		global $Fabscript_PAR_CLOSE;
		
		return bovinus_tokenNode($Fabscript_PAR_CLOSE);
		
	}
	
	// edit-section call-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Sum_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('sum', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		$start->connect($this->_sub_2())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section sum-transform {

		$child = $astNode->getChildAccess();

		$op1 = $child->op1;
		$operator = $child->op;
		$op2 = $child->op2;

		if ($operator->getText() == "+") {
			
			$res = new Bovinus_AstNode("sum");

		} else {

			$res = new Bovinus_AstNode("diff");

		}

		$op1->setId('');
		$op2->setId('');

		$res->addChild($op1);
		$res->addChild($op2);
		
		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		array_push($elements, $this->_sub_1_3());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		return new _Fabscript_Operand_Rule('op1');
		
	}
	
	private function _sub_1_2() {
		
		$branches = array();
		array_push($branches, $this->_sub_1_2_1());
		array_push($branches, $this->_sub_1_2_2());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_1_2_1() {
		
		return $this->_sub_1_2_1_1();
		
	}
	
	private function _sub_1_2_1_1() {
		
		global $Fabscript_PLUS;
		
		return bovinus_tokenNode($Fabscript_PLUS, 'op');
		
	}
	
	private function _sub_1_2_2() {
		
		return $this->_sub_1_2_2_1();
		
	}
	
	private function _sub_1_2_2_1() {
		
		global $Fabscript_MINUS;
		
		return bovinus_tokenNode($Fabscript_MINUS, 'op');
		
	}
	
	private function _sub_1_3() {
		
		return new _Fabscript_Expr_Rule('op2');
		
	}
	
	private function _sub_2() {
		
		$elements = array();
		array_push($elements, $this->_sub_2_1());
		array_push($elements, $this->_sub_2_2());
		array_push($elements, $this->_sub_2_3());
		array_push($elements, $this->_sub_2_4());
		array_push($elements, $this->_sub_2_5());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_2_1() {
		
		global $Fabscript_PAR_OPEN;
		
		return bovinus_tokenNode($Fabscript_PAR_OPEN);
		
	}
	
	private function _sub_2_2() {
		
		return new _Fabscript_Operand_Rule('op1');
		
	}
	
	private function _sub_2_3() {
		
		$branches = array();
		array_push($branches, $this->_sub_2_3_1());
		array_push($branches, $this->_sub_2_3_2());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_2_3_1() {
		
		return $this->_sub_2_3_1_1();
		
	}
	
	private function _sub_2_3_1_1() {
		
		global $Fabscript_PLUS;
		
		return bovinus_tokenNode($Fabscript_PLUS, 'op');
		
	}
	
	private function _sub_2_3_2() {
		
		return $this->_sub_2_3_2_1();
		
	}
	
	private function _sub_2_3_2_1() {
		
		global $Fabscript_MINUS;
		
		return bovinus_tokenNode($Fabscript_MINUS, 'op');
		
	}
	
	private function _sub_2_4() {
		
		return new _Fabscript_Expr_Rule('op2');
		
	}
	
	private function _sub_2_5() {
		
		global $Fabscript_PAR_CLOSE;
		
		return bovinus_tokenNode($Fabscript_PAR_CLOSE);
		
	}
	
	// edit-section sum-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Case_Begin_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('case_begin', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section case_begin-transform {

		$res = new Bovinus_AstNode($astNode->getName());

		$children = $astNode->getChildren();
		$children[1]->setId('');
		$res->addChild($children[1]);
		
		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		array_push($elements, $this->_sub_1_3());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_KEY_20;
		
		return bovinus_tokenNode($Fabscript_KEY_20);
		
	}
	
	private function _sub_1_2() {
		
		return new _Fabscript_Path_Rule();
		
	}
	
	private function _sub_1_3() {
		
		global $Fabscript_KEY_6;
		
		return bovinus_tokenNode($Fabscript_KEY_6);
		
	}
	
	// edit-section case_begin-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Atomic_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('atomic', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		$start->connect($this->_sub_2())->connect($end);
		$start->connect($this->_sub_3())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section atomic-transform {

		$children = $astNode->getChildren();
		$children[0]->setId('');
		
		return $children[0];
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		return $this->_sub_1_1();
		
	}
	
	private function _sub_1_1() {
		
		return new _Fabscript_Comparison_Rule('bool_comparison');
		
	}
	
	private function _sub_2() {
		
		return $this->_sub_2_1();
		
	}
	
	private function _sub_2_1() {
		
		return new _Fabscript_Range_Rule('bool_range');
		
	}
	
	private function _sub_3() {
		
		return $this->_sub_3_1();
		
	}
	
	private function _sub_3_1() {
		
		return new _Fabscript_Path_Rule('bool_path');
		
	}
	
	// edit-section atomic-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Path_Element_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('path_element', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		$start->connect($this->_sub_2())->connect($end);
		$start->connect($this->_sub_3())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section path_element-transform {

		$children = $astNode->getChildren();

		if (count($children) == 1) {
			
			$children[0]->setId('');
			return $children[0];

		} else {

			$res = new Bovinus_AstNode('list-element');

			$child = $astNode->getChildAccess();
			$res->addChild($child->list);

			foreach ($astNode->getChildrenById('index') as $index) {
				$res->addChild($index);
			}
			
			return $res;

		}
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		return $this->_sub_1_1();
		
	}
	
	private function _sub_1_1() {
		
		return new _Fabscript_Call_Rule('call');
		
	}
	
	private function _sub_2() {
		
		return $this->_sub_2_1();
		
	}
	
	private function _sub_2_1() {
		
		return new _Fabscript_Var_Name_Rule('var');
		
	}
	
	private function _sub_3() {
		
		$elements = array();
		array_push($elements, $this->_sub_3_1());
		array_push($elements, $this->_sub_3_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_3_1() {
		
		$branches = array();
		array_push($branches, $this->_sub_3_1_1());
		array_push($branches, $this->_sub_3_1_2());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_3_1_1() {
		
		return $this->_sub_3_1_1_1();
		
	}
	
	private function _sub_3_1_1_1() {
		
		return new _Fabscript_Call_Rule('list');
		
	}
	
	private function _sub_3_1_2() {
		
		return $this->_sub_3_1_2_1();
		
	}
	
	private function _sub_3_1_2_1() {
		
		return new _Fabscript_Var_Name_Rule('list');
		
	}
	
	private function _sub_3_2() {
		
		return bovinus_one_to_many($this->_sub_3_2_1());
		
	}
	
	private function _sub_3_2_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_3_2_1_1());
		array_push($elements, $this->_sub_3_2_1_2());
		array_push($elements, $this->_sub_3_2_1_3());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_3_2_1_1() {
		
		global $Fabscript_BRACE_OPEN;
		
		return bovinus_tokenNode($Fabscript_BRACE_OPEN);
		
	}
	
	private function _sub_3_2_1_2() {
		
		return new _Fabscript_Expr_Rule('index');
		
	}
	
	private function _sub_3_2_1_3() {
		
		global $Fabscript_BRACE_CLOSE;
		
		return bovinus_tokenNode($Fabscript_BRACE_CLOSE);
		
	}
	
	// edit-section path_element-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Condition_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('condition', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section condition-transform {

		$child = $astNode->getChildAccess();

		$node = $child->non_atomic;
		if ($node == null) {
			$node = $child->atomic;
		}
		$node->setId('');

		if ($child->neg == null) {
			$res = $node;
		} else {
			$res = new Bovinus_AstNode('negation');
			$res->addChild($node);
		}
		
		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		return bovinus_zero_to_one($this->_sub_1_1_1());
		
	}
	
	private function _sub_1_1_1() {
		
		return $this->_sub_1_1_1_1();
		
	}
	
	private function _sub_1_1_1_1() {
		
		global $Fabscript_KEY_32;
		
		return bovinus_tokenNode($Fabscript_KEY_32, 'neg');
		
	}
	
	private function _sub_1_2() {
		
		$branches = array();
		array_push($branches, $this->_sub_1_2_1());
		array_push($branches, $this->_sub_1_2_2());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_1_2_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_2_1_1());
		array_push($elements, $this->_sub_1_2_1_2());
		array_push($elements, $this->_sub_1_2_1_3());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_2_1_1() {
		
		global $Fabscript_PAR_OPEN;
		
		return bovinus_tokenNode($Fabscript_PAR_OPEN);
		
	}
	
	private function _sub_1_2_1_2() {
		
		return new _Fabscript_Disjunction_Rule('non_atomic');
		
	}
	
	private function _sub_1_2_1_3() {
		
		global $Fabscript_PAR_CLOSE;
		
		return bovinus_tokenNode($Fabscript_PAR_CLOSE);
		
	}
	
	private function _sub_1_2_2() {
		
		return $this->_sub_1_2_2_1();
		
	}
	
	private function _sub_1_2_2_1() {
		
		return new _Fabscript_Atomic_Rule('atomic');
		
	}
	
	// edit-section condition-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Disjunction_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('disjunction', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section disjunction-transform {

		$parts = $astNode->getChildrenById('part');

		if (count($parts) == 1) {

			$res = $parts[0];

		} else {

			$res = new Bovinus_AstNode('or');
			foreach ($parts as $p) {
				$p->setId('');
				$res->addChild($p);
			}

		}
		
		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		return new _Fabscript_Conjunction_Rule('part');
		
	}
	
	private function _sub_1_2() {
		
		return bovinus_zero_to_many($this->_sub_1_2_1());
		
	}
	
	private function _sub_1_2_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_2_1_1());
		array_push($elements, $this->_sub_1_2_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_2_1_1() {
		
		global $Fabscript_KEY_30;
		
		return bovinus_tokenNode($Fabscript_KEY_30);
		
	}
	
	private function _sub_1_2_1_2() {
		
		return new _Fabscript_Conjunction_Rule('part');
		
	}
	
	// edit-section disjunction-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Loop_Begin_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('loop_begin', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section loop_begin-transform {

		$res = new Bovinus_AstNode($astNode->getName());
		$child = $astNode->getChildAccess();

		$table = $child->table;
		$table->setId('');

		$line = $child->line;

		if ($line) {

			$node = new Bovinus_AstNode('table');
			$res->addChild($node);
			$node->addChild($table);

			$node = new Bovinus_AstNode('line');
			$res->addChild($node);
			$line->setId('');
			$node->addChild($line);

		} else {

			$key = $child->key;
			if ($key) {

				$node = new Bovinus_AstNode('dictionary');
				$res->addChild($node);
				$node->addChild($table);

				$node = new Bovinus_AstNode('key');
				$res->addChild($node);
				$key->setId('');
				$node->addChild($key);

				$node = new Bovinus_AstNode('value');
				$res->addChild($node);
				$value = $child->value;
				$value->setId('');
				$node->addChild($value);

			} else {

				$node = new Bovinus_AstNode('table');
				$res->addChild($node);
				$node->addChild($table);

			}

		}

		$filter = $child->filter;

		if ($filter) {

			$node = new Bovinus_AstNode('filter');
			$res->addChild($node);

			$filter->setId('');
			$node->addChild($filter);

		}
		
		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		array_push($elements, $this->_sub_1_3());
		array_push($elements, $this->_sub_1_4());
		array_push($elements, $this->_sub_1_5());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_KEY_1;
		
		return bovinus_tokenNode($Fabscript_KEY_1);
		
	}
	
	private function _sub_1_2() {
		
		$branches = array();
		array_push($branches, $this->_sub_1_2_1());
		array_push($branches, $this->_sub_1_2_2());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_1_2_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_2_1_1());
		array_push($elements, $this->_sub_1_2_1_2());
		array_push($elements, $this->_sub_1_2_1_3());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_2_1_1() {
		
		global $Fabscript_KEY_2;
		
		return bovinus_tokenNode($Fabscript_KEY_2);
		
	}
	
	private function _sub_1_2_1_2() {
		
		$branches = array();
		array_push($branches, $this->_sub_1_2_1_2_1());
		array_push($branches, $this->_sub_1_2_1_2_2());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_1_2_1_2_1() {
		
		return $this->_sub_1_2_1_2_1_1();
		
	}
	
	private function _sub_1_2_1_2_1_1() {
		
		return new _Fabscript_Var_Name_Rule('line');
		
	}
	
	private function _sub_1_2_1_2_2() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_2_1_2_2_1());
		array_push($elements, $this->_sub_1_2_1_2_2_2());
		array_push($elements, $this->_sub_1_2_1_2_2_3());
		array_push($elements, $this->_sub_1_2_1_2_2_4());
		array_push($elements, $this->_sub_1_2_1_2_2_5());
		array_push($elements, $this->_sub_1_2_1_2_2_6());
		array_push($elements, $this->_sub_1_2_1_2_2_7());
		array_push($elements, $this->_sub_1_2_1_2_2_8());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_2_1_2_2_1() {
		
		global $Fabscript_KEY_3;
		
		return bovinus_tokenNode($Fabscript_KEY_3);
		
	}
	
	private function _sub_1_2_1_2_2_2() {
		
		global $Fabscript_MINUS;
		
		return bovinus_tokenNode($Fabscript_MINUS);
		
	}
	
	private function _sub_1_2_1_2_2_3() {
		
		global $Fabscript_KEY_4;
		
		return bovinus_tokenNode($Fabscript_KEY_4);
		
	}
	
	private function _sub_1_2_1_2_2_4() {
		
		global $Fabscript_MINUS;
		
		return bovinus_tokenNode($Fabscript_MINUS);
		
	}
	
	private function _sub_1_2_1_2_2_5() {
		
		global $Fabscript_KEY_5;
		
		return bovinus_tokenNode($Fabscript_KEY_5);
		
	}
	
	private function _sub_1_2_1_2_2_6() {
		
		return new _Fabscript_Var_Name_Rule('key');
		
	}
	
	private function _sub_1_2_1_2_2_7() {
		
		global $Fabscript_COMMA;
		
		return bovinus_tokenNode($Fabscript_COMMA);
		
	}
	
	private function _sub_1_2_1_2_2_8() {
		
		return new _Fabscript_Var_Name_Rule('value');
		
	}
	
	private function _sub_1_2_1_3() {
		
		global $Fabscript_KEY_6;
		
		return bovinus_tokenNode($Fabscript_KEY_6);
		
	}
	
	private function _sub_1_2_2() {
		
		return $this->_sub_1_2_2_1();
		
	}
	
	private function _sub_1_2_2_1() {
		
		global $Fabscript_KEY_7;
		
		return bovinus_tokenNode($Fabscript_KEY_7);
		
	}
	
	private function _sub_1_3() {
		
		return $this->_sub_1_3_1();
		
	}
	
	private function _sub_1_3_1() {
		
		return $this->_sub_1_3_1_1();
		
	}
	
	private function _sub_1_3_1_1() {
		
		return new _Fabscript_Path_Rule('table');
		
	}
	
	private function _sub_1_4() {
		
		return bovinus_zero_to_one($this->_sub_1_4_1());
		
	}
	
	private function _sub_1_4_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_4_1_1());
		array_push($elements, $this->_sub_1_4_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_4_1_1() {
		
		global $Fabscript_KEY_8;
		
		return bovinus_tokenNode($Fabscript_KEY_8);
		
	}
	
	private function _sub_1_4_1_2() {
		
		return new _Fabscript_Disjunction_Rule('filter');
		
	}
	
	private function _sub_1_5() {
		
		global $Fabscript_KEY_9;
		
		return bovinus_tokenNode($Fabscript_KEY_9);
		
	}
	
	// edit-section loop_begin-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_While_Begin_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('while_begin', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section while_begin-transform {

		$res = new Bovinus_AstNode($astNode->getName());

		$child = $astNode->getChildAccess();

		$condition = $child->cond;
		$condition->setId('');
		$res->addChild($condition);

		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		array_push($elements, $this->_sub_1_3());
		array_push($elements, $this->_sub_1_4());
		array_push($elements, $this->_sub_1_5());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		global $Fabscript_KEY_12;
		
		return bovinus_tokenNode($Fabscript_KEY_12);
		
	}
	
	private function _sub_1_2() {
		
		global $Fabscript_BRACE_OPEN;
		
		return bovinus_tokenNode($Fabscript_BRACE_OPEN);
		
	}
	
	private function _sub_1_3() {
		
		return new _Fabscript_Disjunction_Rule('cond');
		
	}
	
	private function _sub_1_4() {
		
		global $Fabscript_BRACE_CLOSE;
		
		return bovinus_tokenNode($Fabscript_BRACE_CLOSE);
		
	}
	
	private function _sub_1_5() {
		
		global $Fabscript_KEY_9;
		
		return bovinus_tokenNode($Fabscript_KEY_9);
		
	}
	
	// edit-section while_begin-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Conjunction_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('conjunction', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section conjunction-transform {
		
		$parts = $astNode->getChildrenById('part');

		if (count($parts) == 1) {

			$res = $parts[0];

		} else {

			$res = new Bovinus_AstNode('and');
			foreach ($parts as $p) {
				$p->setId('');
				$res->addChild($p);
			}

		}
		
		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		return new _Fabscript_Condition_Rule('part');
		
	}
	
	private function _sub_1_2() {
		
		return bovinus_zero_to_many($this->_sub_1_2_1());
		
	}
	
	private function _sub_1_2_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_2_1_1());
		array_push($elements, $this->_sub_1_2_1_2());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_2_1_1() {
		
		global $Fabscript_KEY_31;
		
		return bovinus_tokenNode($Fabscript_KEY_31);
		
	}
	
	private function _sub_1_2_1_2() {
		
		return new _Fabscript_Condition_Rule('part');
		
	}
	
	// edit-section conjunction-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_If_Begin_Rule extends Bovinus_Rule {

	public function __construct($identifier="") {
	
		parent::__construct('if_begin', $identifier);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section if_begin-transform {

		$child = $astNode->getChildAccess();

		if ($child->if) {
			$res = new Bovinus_AstNode('if_begin');
		} else {
			$res = new Bovinus_AstNode('elseif');
		}

		$condition = $child->cond;
		$condition->setId('');
		$res->addChild($condition);
		
		return $res;
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		$elements = array();
		array_push($elements, $this->_sub_1_1());
		array_push($elements, $this->_sub_1_2());
		array_push($elements, $this->_sub_1_3());
		array_push($elements, $this->_sub_1_4());
		array_push($elements, $this->_sub_1_5());
		array_push($elements, $this->_sub_1_6());
		
		return new Bovinus_Sequence($elements);
		
	}
	
	private function _sub_1_1() {
		
		$branches = array();
		array_push($branches, $this->_sub_1_1_1());
		array_push($branches, $this->_sub_1_1_2());
		
		return new Bovinus_Fork($branches);
		
	}
	
	private function _sub_1_1_1() {
		
		return $this->_sub_1_1_1_1();
		
	}
	
	private function _sub_1_1_1_1() {
		
		global $Fabscript_KEY_14;
		
		return bovinus_tokenNode($Fabscript_KEY_14, 'if');
		
	}
	
	private function _sub_1_1_2() {
		
		return $this->_sub_1_1_2_1();
		
	}
	
	private function _sub_1_1_2_1() {
		
		global $Fabscript_KEY_15;
		
		return bovinus_tokenNode($Fabscript_KEY_15, 'elseif');
		
	}
	
	private function _sub_1_2() {
		
		global $Fabscript_BRACE_OPEN;
		
		return bovinus_tokenNode($Fabscript_BRACE_OPEN);
		
	}
	
	private function _sub_1_3() {
		
		return new _Fabscript_Disjunction_Rule('cond');
		
	}
	
	private function _sub_1_4() {
		
		global $Fabscript_BRACE_CLOSE;
		
		return bovinus_tokenNode($Fabscript_BRACE_CLOSE);
		
	}
	
	private function _sub_1_5() {
		
		global $Fabscript_KEY_16;
		
		return bovinus_tokenNode($Fabscript_KEY_16);
		
	}
	
	private function _sub_1_6() {
		
		global $Fabscript_KEY_17;
		
		return bovinus_tokenNode($Fabscript_KEY_17);
		
	}
	
	// edit-section if_begin-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

class _Fabscript_Command_Grammar extends Bovinus_Grammar {

	public function __construct() {
	
		global $Fabscript_all_token_types;
		
		parent::__construct('_Fabscript_Command_Grammar', $Fabscript_all_token_types);
		
	}
	
	public function expand($start, $end, $context) {
		
		$start->connect($this->_sub_1())->connect($end);
		$start->connect($this->_sub_2())->connect($end);
		$start->connect($this->_sub_3())->connect($end);
		$start->connect($this->_sub_4())->connect($end);
		$start->connect($this->_sub_5())->connect($end);
		$start->connect($this->_sub_6())->connect($end);
		$start->connect($this->_sub_7())->connect($end);
		$start->connect($this->_sub_8())->connect($end);
		$start->connect($this->_sub_9())->connect($end);
		$start->connect($this->_sub_10())->connect($end);
		$start->connect($this->_sub_11())->connect($end);
		$start->connect($this->_sub_12())->connect($end);
		$start->connect($this->_sub_13())->connect($end);
		$start->connect($this->_sub_14())->connect($end);
		$start->connect($this->_sub_15())->connect($end);
		
	}
	
	public function transform($astNode) {
		
		// edit-section command-transform {
		
		$children = $astNode->getChildren();
		$children[0]->setId('');
		
		return $children[0];
		
		// } edit-section-end
		
	}
	
	private function _sub_1() {
		
		return $this->_sub_1_1();
		
	}
	
	private function _sub_1_1() {
		
		return new _Fabscript_Loop_Begin_Rule();
		
	}
	
	private function _sub_2() {
		
		return $this->_sub_2_1();
		
	}
	
	private function _sub_2_1() {
		
		return new _Fabscript_Loop_End_Rule();
		
	}
	
	private function _sub_3() {
		
		return $this->_sub_3_1();
		
	}
	
	private function _sub_3_1() {
		
		return new _Fabscript_While_Begin_Rule();
		
	}
	
	private function _sub_4() {
		
		return $this->_sub_4_1();
		
	}
	
	private function _sub_4_1() {
		
		return new _Fabscript_While_End_Rule();
		
	}
	
	private function _sub_5() {
		
		return $this->_sub_5_1();
		
	}
	
	private function _sub_5_1() {
		
		return new _Fabscript_If_Begin_Rule();
		
	}
	
	private function _sub_6() {
		
		return $this->_sub_6_1();
		
	}
	
	private function _sub_6_1() {
		
		return new _Fabscript_Else_Rule();
		
	}
	
	private function _sub_7() {
		
		return $this->_sub_7_1();
		
	}
	
	private function _sub_7_1() {
		
		return new _Fabscript_If_End_Rule();
		
	}
	
	private function _sub_8() {
		
		return $this->_sub_8_1();
		
	}
	
	private function _sub_8_1() {
		
		return new _Fabscript_Case_Begin_Rule();
		
	}
	
	private function _sub_9() {
		
		return $this->_sub_9_1();
		
	}
	
	private function _sub_9_1() {
		
		return new _Fabscript_Case_Branch_Rule();
		
	}
	
	private function _sub_10() {
		
		return $this->_sub_10_1();
		
	}
	
	private function _sub_10_1() {
		
		return new _Fabscript_Case_End_Rule();
		
	}
	
	private function _sub_11() {
		
		return $this->_sub_11_1();
		
	}
	
	private function _sub_11_1() {
		
		return new _Fabscript_Var_Decl_Rule();
		
	}
	
	private function _sub_12() {
		
		return $this->_sub_12_1();
		
	}
	
	private function _sub_12_1() {
		
		return new _Fabscript_Assign_Rule();
		
	}
	
	private function _sub_13() {
		
		return $this->_sub_13_1();
		
	}
	
	private function _sub_13_1() {
		
		return new _Fabscript_Snippet_Begin_Rule();
		
	}
	
	private function _sub_14() {
		
		return $this->_sub_14_1();
		
	}
	
	private function _sub_14_1() {
		
		return new _Fabscript_Snippet_End_Rule();
		
	}
	
	private function _sub_15() {
		
		return $this->_sub_15_1();
		
	}
	
	private function _sub_15_1() {
		
		return new _Fabscript_Paste_Snippet_Rule();
		
	}
	
	// edit-section command-further-private-methods {
	
	// add your methods here...
	
	// } edit-section-end
	
}

?>

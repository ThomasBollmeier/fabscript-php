<?php

ini_set('include_path', "..:" . ini_get('include_path'));

require_once 'Fabscript/environment.php';
require_once 'Fabscript/expression.php';
require_once 'Fabscript/logical_expression.php';
require_once 'Fabscript/block.php';

class Person {

	public function __construct($lastName, $firstName = "", $isAlive = TRUE) {

		$this->lastName = $lastName;
		$this->firstName = $firstName;
		$this->isAlive = $isAlive;

	}

	public function getFullname($isMale) {

		$res = $this->lastName;
		if ($this->firstName != "") {
			$res = $this->firstName . " " . $res;
		}

		if ($isMale) {
			$res = "Mr. " . $res;
		} else {
			$res = "Mrs. " . $res;
		}

		return $res;

	}

	public $lastName;
	public $firstName;

}

class ModelTest extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$this->command_parser = new Fabscript_Command_Parser();
		$this->symbol_parser = new Fabscript_Symbol_Parser();
		$this->interpreter = new Fabscript_Interpreter();

	}

	public function tearDown() {

		$this->command_parser = null;
		$this->symbol_parser = null;
		$this->interpreter = null;

	}

	public function testEnvironment() {

		$globalEnv = new Fabscript_Environment();
		$localEnv = new Fabscript_Environment($globalEnv);

		$globalEnv->set("n", 42);
		
		$this->assertEquals(42, $localEnv->get("n"));

		$localEnv->set("n", 666);
		$this->assertEquals(666, $localEnv->get("n"));
		$this->assertEquals(42, $globalEnv->get("n"));

	}

	public function testExpressions() {

		$env = new Fabscript_Environment();
		$env->set("myNumber", 42);
		$env->set("he", new Person("Schachtschmidt", "Horst") );
		$env->set("she", new Person("Schachtschmidt", "Hannelore") );

		$number = new Fabscript_Number("42", "345", TRUE);
		$this->assertEquals(-42.345, $number->getValue($env));    

		$literal = new Fabscript_Literal("Hello World!");
		$this->assertEquals("Hello World!", $literal->getValue($env));
		
		$var = new Fabscript_Variable("myNumber");
		$this->assertEquals(42, $var->getValue($env));
		
		$var = new Fabscript_Variable("firstName", new Fabscript_Variable("he"));
		$this->assertEquals("Horst", $var->getValue($env));

		$call = new Fabscript_Call(
			"getFullName", 
			array( new Fabscript_BooleanLiteral("TRUE") ),
			new Fabscript_Variable("he")
			);
		$this->assertEquals("Mr. Horst Schachtschmidt", $call->getValue($env));

		$call = new Fabscript_Call(
			"getFullName", 
			array( new Fabscript_BooleanLiteral("FALSE") ),
			new Fabscript_Variable("she")
			);
		$this->assertEquals("Mrs. Hannelore Schachtschmidt", $call->getValue($env));

	}

	public function testLogicalExpressions() {

		$env = new Fabscript_Environment();
		$env->set("myNumber", 42);

		$person = new Person("Normalverbraucher", "Otto");
		$env->set("person", $person);

		$path = new Fabscript_Variable("isAlive", new Fabscript_Variable("person"));
		$logicalExpr = new Fabscript_BooleanPath($path);

		$this->assertEquals(TRUE, $logicalExpr->isTrue($env));
		$person->isAlive = FALSE;
		$this->assertEquals(FALSE, $logicalExpr->isTrue($env));

		$val = new Fabscript_Variable("myNumber");
		$forty_two = new Fabscript_Number("42");
		$fourty = new Fabscript_Number("40");
		$fifty = new Fabscript_Number("50");

		$logicalExpr = new Fabscript_Comparison("==", $val, $forty_two);
		$this->assertEquals(TRUE, $logicalExpr->isTrue($env));

		$logicalExpr = new Fabscript_Comparison("<>", $val, $forty_two);
		$this->assertEquals(FALSE, $logicalExpr->isTrue($env));

		$logicalExpr = new Fabscript_Negation($logicalExpr);
		$this->assertEquals(TRUE, $logicalExpr->isTrue($env));

		$logicalExpr = new Fabscript_Range($val, $fourty, $fifty);
		$this->assertEquals(TRUE, $logicalExpr->isTrue($env));

		$logicalExpr = new Fabscript_Conjunction(array(
			new Fabscript_Comparison(">=", $val, $fourty),
			new Fabscript_Comparison("<=", $val, $fifty)
			));
		$this->assertEquals(TRUE, $logicalExpr->isTrue($env));

		$logicalExpr = new Fabscript_Conjunction(array(
			new Fabscript_Comparison("==", $val, $fourty),
			new Fabscript_Comparison("<=", $val, $fifty)
			));
		$this->assertEquals(FALSE, $logicalExpr->isTrue($env));

		$logicalExpr = new Fabscript_Disjunction(array(
			new Fabscript_Comparison("==", $val, $fourty)
			));
		$this->assertEquals(FALSE, $logicalExpr->isTrue($env));
		$logicalExpr->add(new Fabscript_Comparison("<=", $val, $fifty));
		$this->assertEquals(TRUE, $logicalExpr->isTrue($env));

	}

	public function testBlocks() {

		$env = new Fabscript_Environment();
		$env->set("myNumber", 42);
		$person = new Person("Normalverbraucher", "Otto");
		$env->set("person", $person);

		$numbers = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
		$env->set("numbers", $numbers);

		$employees = array( "4711" => "Ballermeier", "4712" => "Schmidtke" );
		$env->set("myEmployees", $employees);

		$block = new Fabscript_Text();
		$block->addRawLine('int answer = ${myNumber};');
		$block->addRawLine('printf("Guten Tag %s %s\n", "${person.firstName}", "${person.lastName}");');
		$block->addRawLine('printf("Hello %s!\n", "${person.getFullname(TRUE)}");');

		$lines = $block->getLines($env);
		echo "\n";
		foreach ($lines as $line) {
			echo $line . "\n";
		}

		$block = $this->createBlock("for each num in numbers do");
		$block->addRawLine('member[${num}].init();');

		$lines = $block->getLines($env);
		$this->assertEquals(10, count($lines));
		echo "\n";
		foreach ($lines as $line) {
			echo $line . "\n";
		}

		$block = $this->createBlock("for each num in numbers where num between 4 and 6 do");
		$block->addRawLine('member[${num}].init();');

		$lines = $block->getLines($env);
		$this->assertEquals(3, count($lines));
		echo "\n";
		foreach ($lines as $line) {
			echo $line . "\n";
		}

		$block = $this->createBlock("for each key-value-pair id, name in myEmployees where id between '4711' and '4712' do");
		$block->addRawLine('Der Mitarbeiter mit der ID "${id}" heisst "${name}".');

		$lines = $block->getLines($env);
		$this->assertEquals(2, count($lines));
		echo "\n";
		foreach ($lines as $line) {
			echo $line . "\n";
		}

		$branch = $this->createBlock("if [ person.firstName <> 'Otto'] then begin");
		$branch->addRawLine('Vorname: "${person.firstName}"');
		$condition = new Fabscript_Comparison(
			"<>", 
			new Fabscript_Variable("lastName", new Fabscript_Variable("person")),
			new Fabscript_Literal("Normalverbraucher")
			);
		$branch->addBranch($condition);
		$branch->addRawLine('Nachname: "${person.lastName}"');
		$branch->addDefaultBranch();
		$branch->addRawLine('Sie müssen Otto Normalverbraucher sein.');

		$lines = $branch->getLines($env);
		$this->assertEquals(1, count($lines));
		echo "\n";
		foreach ($lines as $line) {
			echo $line . "\n";
		}

	}

	public function testLogicals() {

		$env = new Fabscript_Environment();
		$env->set("myNumber", 42);
		$person = new Person("Normalverbraucher", "Otto");
		$env->set("person", $person);

		$logicalExpr = $this->interpretCondition(" myNumber == 42 ");
		$this->assertEquals(TRUE, $logicalExpr->isTrue($env));

		$logicalExpr = $this->interpretCondition(" myNumber < 42.35 ");
		$this->assertEquals(TRUE, $logicalExpr->isTrue($env));

		$logicalExpr = $this->interpretCondition(" myNumber == 42.3 ");
		$this->assertEquals(FALSE, $logicalExpr->isTrue($env));

		$logicalExpr = $this->interpretCondition(" myNumber between 30 and 50 ");
		$this->assertEquals(TRUE, $logicalExpr->isTrue($env));

		$logicalExpr = $this->interpretCondition(
			" person.firstName == 'Otto' and person.lastName == 'Normalverbraucher' "
			);
		$this->assertEquals(TRUE, $logicalExpr->isTrue($env));

		$logicalExpr = $this->interpretCondition(
			" not ( person.firstName == 'Otto' and person.lastName == 'Normalverbraucher' ) "
			);
		$this->assertEquals(FALSE, $logicalExpr->isTrue($env));

		$logicalExpr = $this->interpretCondition(
			" person.firstName <> 'Otto' or person.lastName <> 'Normalverbraucher' "
			);
		$this->assertEquals(FALSE, $logicalExpr->isTrue($env));

		$logicalExpr = $this->interpretCondition(
			" person.getFullname(TRUE) == 'Mr. Otto Normalverbraucher' "
			);
		$this->assertEquals(TRUE, $logicalExpr->isTrue($env));

	}

	private function createBlock($command) {

		$ast = $this->command_parser->parseString($command);
		$this->assertTrue($ast != null);

		$res = $this->interpreter->interpret($ast); 
		$this->assertTrue($res != null);

		return $res;

	}

	private function interpretCondition($condStr) {

		$code = "if [ " . $condStr . " ] then begin";

		$ast = $this->command_parser->parseString($code);
		$this->assertTrue($ast != null);

		$children = $ast->getChildren();
		$this->assertEquals(1, count($children));

		$cond = $children[0];

		return $this->interpreter->interpret($cond); 

	}

	private $command_parser;
	private $symbol_parser;
	private $interpreter;

}

?>
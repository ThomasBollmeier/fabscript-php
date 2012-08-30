<?php

ini_set('include_path', "..:" . ini_get('include_path'));

require_once 'Fabscript/environment.php';
require_once 'Fabscript/expression.php';

class Person {

	public function __construct($lastName, $firstName = "") {

		$this->lastName = $lastName;
		$this->firstName = $firstName;

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

class EnvironmentTest extends PHPUnit_Framework_TestCase {

    public function setUp() {

    }

    public function tearDown() {

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
}

?>
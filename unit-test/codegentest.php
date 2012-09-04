<?php

ini_set('include_path', "..:" . ini_get('include_path'));

require_once 'Fabscript/code_creator.php';

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

function firstLetter($name) {

	return strtoupper($name[0]);

}

class CodeGenerationTest extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$this->creator = new Fabscript_CodeCreator();

	}

	public function tearDown() {

		$this->creator = null;

	}

	public function testLoop() {

		$employees = array( "A1" => "Meier", "A2" => "Müller", "A3" => "Schulze" );
		$this->creator->setGlobalVar("mitarbeiter", $employees);
		$this->creator->setGlobalVar("ersterBuchstabe", "firstLetter");

		$this->creator->processRawLine("Unsere Mitarbeiter: ");	
		$this->creator->processCommand("for each key-value-pair id, name in mitarbeiter do");
		$this->creator->processRawLine("\t" . 'Der Mitarbeiter mit der ID "${id}" heißt "${name}".');
		$this->creator->processCommand("endfor");

		$this->creator->processRawLine("Unsere Mitarbeiter im Bereich 'M': ");	
		$this->creator->processCommand("for each key-value-pair id, name in mitarbeiter where ersterBuchstabe(name) == 'M' do");
		$this->creator->processRawLine("\t" . '${name} (ID=${id})');
		$this->creator->processCommand("endfor");

		$lines = $this->creator->getLines();
		$this->showLines($lines);

	}

	public function testBranch() {

		$employees = array( "A1" => "Meier", "A2" => "Müller", "A3" => "Schulze", "A4" => "Kalkreuth", "A5" => "Itzenplitz" );
		$this->creator->setGlobalVar("mitarbeiter", $employees);
		$this->creator->setGlobalVar("ersterBuchstabe", "firstLetter");

		$this->creator->processRawLine("Unsere Mitarbeiter: ");	
		$this->creator->processCommand("for each key-value-pair id, name in mitarbeiter do");
		$this->creator->processCommand("if [ isFirst ] then begin");
		$this->creator->processRawLine("\t" . 'Der Mitarbeiter mit der ID "${id}" heißt "${name}" (ERSTER).');
		$this->creator->processCommand("elseif [ isLast ] then begin");
		$this->creator->processRawLine("\t" . 'Der Mitarbeiter mit der ID "${id}" heißt "${name}" (LETZTER).');
		$this->creator->processCommand("else");
		$this->creator->processRawLine("\t" . 'Der Mitarbeiter mit der ID "${id}" heißt "${name}".');
		$this->creator->processCommand("endif");
		$this->creator->processCommand("endfor");

		$this->creator->processCommand("for each key-value-pair id, name in mitarbeiter do");
		$this->creator->processCommand("  case ersterBuchstabe(name) in");
		$this->creator->processCommand("  	'I')");
		$this->creator->processRawLine("\t'I' -> " . '${name} (ID=${id})');
		$this->creator->processCommand("  	'K', 'M')");
		$this->creator->processRawLine("\t'K oder M' -> " . '${name} (ID=${id})');
		$this->creator->processCommand("  	*)");
		$this->creator->processRawLine("\tSonstige -> " . '${name} (ID=${id})');
		$this->creator->processCommand("  endcase");
		$this->creator->processCommand("endfor");

		$lines = $this->creator->getLines();
		$this->assertEquals(11, count($lines));
		$this->showLines($lines);

	}

	public function testVarDeclaration() {

		$employees = array( "A1" => "Meier", "A2" => "Müller", "A3" => "Schulze", "A4" => "Kalkreuth", "A5" => "Itzenplitz" );
		$this->creator->setGlobalVar("mitarbeiter", $employees);
		$this->creator->setGlobalVar("ersterBuchstabe", "firstLetter");

		$this->creator->processCommand("declare employee_id");
		$this->creator->processCommand("for each key-value-pair id, name in mitarbeiter do");
		$this->creator->processCommand("  employee_id = id");
		$this->creator->processCommand("  define employee_name=name");
		$this->creator->processRawLine('${employee_name} hat die ID "${employee_id}"');
		$this->creator->processCommand("endfor");
		$this->creator->processRawLine('Letzte ID: "${employee_id}"');

		$lines = $this->creator->getLines();
		$this->assertEquals(6, count($lines));
		$this->showLines($lines);

	}

	private function showLines($lines) {

		echo "\n";
		foreach ($lines as $line) {
			echo $line . "\n";
		}

	}

	private $creator;

}

?>
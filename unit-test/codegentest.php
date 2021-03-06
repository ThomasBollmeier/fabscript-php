<?php

ini_set('include_path', "..:" . ini_get('include_path'));

require_once 'Fabscript/code_creator.php';
require_once 'Fabscript/edit_section.php';

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

function is_empty($text) {

    return empty($text);

}

class EditSectionForC implements Fabscript\EditSectionConfig {

    public function getStartComment($sectionName)
    {
        return "/* begin-of-edit-section ".$sectionName." { */";
    }

    public function getEndComment($sectionName)
    {
        return "/* } end-of-edit-section */";
    }

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
	
	public function testForLoopWithExit() {
		
		$numbers = array("eins", "zwei", "drei", "vier", "fünf");
		
		$template = new Fabscript_StringsInput();
		$template->addLine(":> define i = 0");
		$template->addLine(":> for each number in numbers do");
		$template->addLine('echo "${number}"');
		$template->addLine("	:> i = i + 1");
		$template->addLine("	:> if [ i == 3 ] then begin");
		$template->addLine('Das ist das Ende!');
		$template->addLine("		:> leave");
		$template->addLine("	:> endif");
		$template->addLine(":> done");
		
		$this->creator->numbers = $numbers;
		$this->creator->processTemplate($template);
		
		$lines = $this->creator->getLines();
		$this->showLines($lines);
		
	}

	public function testWhileLoop() {

		$stream = new Fabscript_StringsInput();
		$stream->addLine(":> define i = 0");
		$stream->addLine(":> while [ i < 4 ] do");
		$stream->addLine(":>   case i in");
		$stream->addLine(":>     0)");
		$stream->addLine("eins");
		$stream->addLine(":>     1)");
		$stream->addLine("zwei");
		$stream->addLine(":>     2)");
		$stream->addLine("drei");
		$stream->addLine(":>     *)");
		$stream->addLine("ganz viele");
		$stream->addLine(":>   endcase");
		$stream->addLine(":>   i = i + 1");
		$stream->addLine(":> endwhile");

		$this->creator->processTemplate($stream);

		$lines = $this->creator->getLines();
		$this->assertEquals(4, count($lines));
		$this->showLines($lines);

	}
	
	public function testWhileLoopWithExit() {
	
		$numbers = array("eins", "zwei", "drei", "vier", "fünf");
	
		$template = new Fabscript_StringsInput();
		$template->addLine(":> define i = 0");
		$template->addLine(":> while [ i < 10 ] do");
		$template->addLine("	:> i = i + 1");
		$template->addLine("	:> if [ i == 5 ] then begin");
		$template->addLine('Halbzeit!');
		$template->addLine("		:> next");
		$template->addLine("	:> endif");
		$template->addLine('while ${i}');
		$template->addLine(":> endwhile");
	
		$this->creator->numbers = $numbers;
		$this->creator->processTemplate($template);
	
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

    public function testEditableSection() {

        $this->creator->setGlobalVar("values", array("eins", "zwei", "drei"));
        $this->creator->setEditSectionConfig(new EditSectionForC());

        $stream = new Fabscript_StringsInput();
        $stream->addLine(':> for each value in values do');
        $stream->addLine('  :> edit value');
        $stream->addLine('// add your code for section "${value}" here...');
        $stream->addLine('  :> endedit');
        $stream->addLine('');
        $stream->addLine(':> endfor');

        $this->creator->processTemplate($stream);

        $lines = $this->creator->getLines();
        $this->assertEquals(12, count($lines));

        //$this->expectOutputString("");
        $this->showLines($lines);

    }

	public function testTemplate() {

		$employees = array( 
			"A1" => "Meier", 
			"A2" => "Müller", 
			"A3" => "Schulze", 
			"A4" => "Kalkreuth", 
			"A5" => "Itzenplitz" 
			);
		$people = array(
			"chancellors" => array( 
				new Person("Bismarck", "Otto", FALSE),
				new Person("Adenauer", "Konrad", FALSE),
				new Person("Merkel", "Angela")
				)
			);
			
		$this->creator->mitarbeiter = $employees;
		$this->creator->ersterBuchstabe = "firstLetter";
		$this->creator->people = $people;
		/*
		$this->creator->setGlobalVar("mitarbeiter", $employees);
		$this->creator->setGlobalVar("ersterBuchstabe", "firstLetter");
		$this->creator->setGlobalVar("people", $people);
		*/

		$stream = new Fabscript_StringsInput();
		$stream->addLine(':> define counter = 10');
		$stream->addLine(':> for each key-value-pair id, name in mitarbeiter do');
		$stream->addLine(':> counter = counter + 1');
		$stream->addLine('  :> if [ isFirst ] then begin');
		$stream->addLine('>>>>>');
		$stream->addLine('  :> endif');
		$stream->addLine('printf("%d -> ID: %s, Name: %s\n", ${counter}, "${id}", "${name}")');
		$stream->addLine('  :> if [ isLast ] then begin');
		$stream->addLine('<<<<<');
		$stream->addLine('  :> endif');
		$stream->addLine(':> endfor');

		$this->creator->processTemplate($stream);

		$lines = $this->creator->getLines();
		$this->assertEquals(7, count($lines));
		$this->showLines($lines);

		$this->creator->reset();
		$stream = new Fabscript_StringsInput();
		$stream->addLine(':> for each key-value-pair id, name in mitarbeiter \\');
		$stream->addLine('   where id == "A4" or id == "A5" \\');
		$stream->addLine('   do');
		$stream->addLine('printf("ID: %s, Name: %s\n", "${id}", "${name}")');
		$stream->addLine(':> endfor');

		$this->creator->processTemplate($stream);

		$lines = $this->creator->getLines();
		$this->assertEquals(2, count($lines));
		$this->showLines($lines);

		$this->creator->reset();
		
		$this->creator->processTemplate(new Fabscript_FileInput("mitarbeiter.template"));

		$lines = $this->creator->getLines();
		$this->assertEquals(29, count($lines));
		$this->showLines($lines);

		$this->creator->reset();
		$stream = new Fabscript_StringsInput();
		$stream->addLine(':> if [ people["chancellors"][0].isAlive ] then begin');
		$stream->addLine('${people["chancellors"][0].lastName} ist wohlauf.');
		$stream->addLine(':> else');
		$stream->addLine('${people["chancellors"][0].lastName} ist Geschichte.');
		$stream->addLine(':> endif');

		$this->creator->processTemplate($stream);

		$lines = $this->creator->getLines();
		$this->assertEquals(1, count($lines));
		$this->showLines($lines);

		$this->creator->reset();
		$stream = new Fabscript_StringsInput();
		$stream->addLine(':> for all mitarbeiter do');
		$stream->addLine(':>   define counter = 1');
		$stream->addLine(':>   counter = counter + 1');
		$stream->addLine('Zaehler -> ${counter}');
		$stream->addLine(':> endfor');

		$this->creator->processTemplate($stream);

		$lines = $this->creator->getLines();
		$this->showLines($lines);

	}

	public function testInclude() {

		$this->creator->setGlobalVar("items", array("eins", "zwei", "drei"));

		$stream = new Fabscript_StringsInput();
		$stream->addLine('----- Anfang -----');
		$stream->addLine(':> include "items.template"');
		$stream->addLine('----- Ende -----');

		$this->creator->processTemplate($stream);

		$lines = $this->creator->getLines();
		$this->assertEquals(5, count($lines));
		$this->showLines($lines);

	}

    public function testSnippet() {

        $this->creator->is_empty = "is_empty";

        $stream = new Fabscript_StringsInput();
        $stream->addLine(':> snippet greeting(name, first_name)');
        $stream->addLine('  :> if [ not is_empty(first_name) ] then begin');
        $stream->addLine('Hallo ${first_name} ${name}!');
        $stream->addLine('  :> else');
        $stream->addLine('Hallo Herr/Frau ${name}!');
        $stream->addLine('  :> endif');
        $stream->addLine(':> endsnippet');

        $stream->addLine(':> define level = 0');
        $stream->addLine(':> paste snippet greeting("Bollmeier", "") indent by level');
        $stream->addLine(':> level = level + 1');
        $stream->addLine(':> paste snippet greeting("Bollmeier", "Thomas") indent by level');

        $this->creator->processTemplate($stream);

        $lines = $this->creator->getLines();
        $this->assertEquals(2, count($lines));
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

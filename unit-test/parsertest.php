<?php

ini_set('include_path', "..:" . ini_get('include_path'));

require_once 'Fabscript/parser.php';
require_once 'Bovinus/string_input.php';

class CommandParserTest extends PHPUnit_Framework_TestCase {

    public function setUp() {

        $this->parser = new Fabscript_Command_Parser();
    }

    public function tearDown() {

        $this->parser = null;
    }


    public function testLoop() {

		$this->_parse("for all query.getResult().getItems(status, getToday()) do");
		$this->_parse("for each item in items do");
        $this->_parse("for each key-value-pair k, v in items do");
        $this->_parse("for all items where items.status == Status.RELEASED do");

        $this->_parse("endfor");
        $this->_parse("done");

    }

    public function testCase() {

        $this->_parse("case status in");
        $this->_parse("case item.status in");
        $this->_parse("case item.getStatus() in");

        $this->_parse("'released')");
        $this->_parse("Status.RELEASED)");
        $this->_parse("*)");

    }

    public function testVarDef() {

        $this->_parse("define i");
        $this->_parse("define x = 4.2");
        $this->_parse("define today = Date.getToday()");

    }

    public function testAssign() {

        $this->_parse("i = 42");
        $this->_parse("x = 4.2");
        $this->_parse("y = -5.43");

    }

    public function testEditSection() {

        $this->_parse("edit-section 'myfunc' begin");
        $this->_parse("edit-section method.name begin");

    }

    private function _parse($code) {

        try {
            $ast = $this->parser->parseString($code);
        } catch (Bovinus_ParseError $err) {
            $ast = null;
            echo "\n" . $err->getMessage();
        }

        $this->assertTrue($ast != null);

        if ($ast != null) {
            echo "\n" . $ast->toXml();
        }
    }

	private function _run_lexer($code) {

		$lexer = $this->parser->getLexer();
		$lexer->setInputStream(new Bovinus_StringInput($code));

		echo "\n";

		$token = $lexer->getNextToken();
		while ($token != null) {
			echo "Token: " . $token->getText() . "\n";
			$token = $lexer->getNextToken();
		}

	}

    private $parser;

}

class SymbolParserTest extends PHPUnit_Framework_TestCase {

    public function setUp() {

        $this->parser = new Fabscript_Symbol_Parser();
    }

    public function tearDown() {

        $this->parser = null;
    }

    public function testPath() {

    }

    private function _parse($code) {

        try {
            $ast = $this->parser->parseString($code);
        } catch (Bovinus_ParseError $err) {
            $ast = null;
            echo "\n" . $err->getMessage();
        }

        $this->assertTrue($ast != null);

        if ($ast != null) {
            echo "\n" . $ast->toXml();
        }
    }

}

?>

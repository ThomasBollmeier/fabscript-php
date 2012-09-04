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
        $this->_parse("for each item in items where not item.status == Status.RELEASED and item.duedate <= Date.getToday() do");
        $this->_parse("for each item in items where ( item.orderDate between '2012-07-01' and '2012-07-31' ) do");

        $this->_parse("endfor");
        $this->_parse("done");

    }

    public function testIf() {

        $this->_parse("if [ hasTodo == ( isOpen(item) or item.status <> Status.RELEASED ) ] then begin" );
        $this->_parse("elseif [ not ( isOpen(item) or item.status <> Status.RELEASED ) ] then begin" );
        $this->_parse("else");
        $this->_parse("endif");

    }

    public function testCase() {

        $this->_parse("case status in");
        $this->_parse("case item.status in");
        $this->_parse("case item.getStatus() in");

        $this->_parse("'released')");
        $this->_parse("Status.RELEASED)");
        $this->_parse("Status.RELEASED, Status.CANCELLED)");
        $this->_parse("*)");

        $this->_parse("endcase");

    }

    public function testVarDecl() {

        $this->_parse("declare i");
        $this->_parse("define x = 4.2");
        $this->_parse("define today = Date.getToday()");
        $this->_parse("define YES = TRUE");

    }

    public function testAssign() {

        $this->_parse("i = 42");
        $this->_parse("x = 4.2");
        $this->_parse("y = -5.43");

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

        $this->_parse("person.firstName");

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

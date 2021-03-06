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
require_once 'Fabscript/environment.php';
require_once 'Fabscript/interpreter.php';
require_once 'Fabscript/block.php';
require_once 'Fabscript/preprocessor.php';
require_once 'Fabscript/break_continue.php';
require_once 'Fabscript/edit_section_parser.php';

function fabscript_addText($templatePath, $globalVars) {

	$creator = new Fabscript_CodeCreator();
	$creator->setGlobalVars($globalVars);
	$creator->createFromTemplate($templatePath);

}

function fabscript_setTemplateDirs($searchDirs = array(".")) {
	
	$curdir = dirname($_SERVER["SCRIPT_FILENAME"]);
	
	// searchDirs can be passed as array or as colon separated string:
	if (is_array($searchDirs)) {
		$dirList = $searchDirs;
	} else {
		$dirList = explode(":", $searchDirs);
	}
	
	$dirs = array();		
	foreach ($dirList as $searchDir) {
		if ("/" == $searchDir[0]) { // absolute path
			array_push($dirs, $searchDir);
		} else { // relative path
			array_push($dirs, $curdir . "/" . $searchDir);
		}
	}
	
	Fabscript_Preprocessor::$searchDirs = $dirs;
	Fabscript_CodeCreator::$templateDirs = $dirs;

}

class Fabscript_CodeCreator {
	
	public static $templateDirs = array(".");

    public static function getCurrent() {

        return Fabscript_CodeCreator::$currentInstance;

    }

	public function __construct() {

		$this->preprocessor = new Fabscript_Preprocessor();
		$this->parser = new Fabscript_Command_Parser();
		$this->interpreter = new Fabscript_Interpreter();
		$this->globalEnv = new Fabscript_Environment();
		$this->reset();
		
	}

	public function reset($complete = FALSE) {

		$this->stack = array(array("element" => "", "object" => new Fabscript_Block()));
		
		if ($complete) {
			$this->globalEnv = new Fabscript_Environment();
			$this->snippets = array();
            $this->editSections = array();
		}

	}

	public function setGlobalVar($name, $value) {

		$this->globalEnv->set($name, $value);

	}

	public function setGlobalVars($namesValues) {

		foreach ($namesValues as $name => $value) {
			$this->globalEnv->set($name, $value);			
		}

	}
	
	public function __set($name, $value) {
		
		$this->setGlobalVar($name, $value);
		
	}
	
	/**
	 * Make all current global variables available 
	 */
	public function useGlobalVars() {
		
		foreach ($GLOBALS as $key => $value) {
			if ($this !== $value) {
				$this->setGlobalVar($key, $value);
			}
		}
		
	}

    /**
     * @param $config instance of \Fabscript\EditSectionConfig
     */
    public function setEditSectionConfig($config) {

        $this->editSectionConfig = $config;

    }

    public function getEditSectionConfig() {

        return $this->editSectionConfig;

    }

    public function setEditSectionParser($parser) {

        $this->editSectionParser = $parser;

    }

    /**
     * @param $sectionName
     * @return array of lines or FALSE if there is no section with name $sectionName
     */
    public function getEditedLines($sectionName) {

        if (array_key_exists($sectionName, $this->editSections)) {
            return $this->editSections[$sectionName];
        } else {
            return FALSE;
        }
    }

    /**
     * Create or update a file from a given template
     *
     * @param $templatePath : path to template file
     * @param $outFilePath : path of output file
     * @param $progLanguage : programming language (required for editable section generation)
     */
    public function createFileFromTemplate($templatePath, $outFilePath, $progLanguage) {

        $this->reset();

        $completePath = $this->getCompletePath($templatePath);
        $this->processTemplate(new Fabscript_FileInput($completePath));

        // If output file exists already we have to save the edited sections
        // before (re)generation

        $factory = new \Fabscript\EditSectionParserFactory($progLanguage);
        $this->setEditSectionParser($factory->getParser());

        $this->scanEditableSections($outFilePath);

        // Now generate the file:

        $this->setEditSectionConfig($factory->getConfig());
        $lines = $this->getLines();

        $fp = fopen($outFilePath, "w");
        foreach ($lines as $line) {
            fwrite($fp, $line."\n");
        }
        fclose($fp);

    }

	public function createFromTemplate($templatePath) {

		$this->reset();

		$completePath = $this->getCompletePath($templatePath);
		$this->processTemplate(new Fabscript_FileInput($completePath));

		foreach ($this->getLines() as $line) {
			echo $line . "\n";
		}

	}

	public function processTemplate(Fabscript_LineInStream $template) {

		$lineInfoList = $this->preprocessor->getLineInfo($template);

		foreach ($lineInfoList as $lineInfo) {

			if ($lineInfo['lineType'] == Fabscript_LineType::COMMAND) {
				$this->processCommand($lineInfo['content']);
			} else {
				$this->processRawLine($lineInfo['content']);
			}

		}

	}

    public function processCommand($command) {

		$ast = $this->parser->parseString($command);
		$name = $ast->getName();

		switch ($name) {

			case "loop_begin":
				$loop = $this->interpreter->interpret($ast);
				$this->push($name, $loop);
				break;
			case "loop_end":
				$loop = $this->getCurrContainer("loop_begin");
				$this->pop();
				$this->getCurrContainer()->addElement($loop);
				break;

			case "while_begin":
				$whileLoop = $this->interpreter->interpret($ast);
				$this->push($name, $whileLoop);
				break;
			case "while_end":
				$whileLoop = $this->getCurrContainer("while_begin");
				$this->pop();
				$this->getCurrContainer()->addElement($whileLoop);
				break;
				
			case "break":
				$this->getCurrContainer()->addElement(new \Fabscript\BreakStatement());
				break;
				
			case "continue":
				$this->getCurrContainer()->addElement(new \Fabscript\ContinueStatement());
				break;
				
			case "if_begin":
				$branch = $this->interpreter->interpret($ast);
				$this->push($name, $branch);
				break;
			case "elseif":
				$condition = $this->interpreter->interpret($ast);
				$branch = $this->getCurrContainer("if_begin");
				$branch->addBranch($condition);
				break;
			case "else":
				$branch = $this->getCurrContainer("if_begin");
				$branch->addDefaultBranch();
				break;
			case "if_end":
				$branch = $this->getCurrContainer("if_begin");
				$this->pop();
				$this->getCurrContainer()->addElement($branch);
				break;

			case "case_begin":
				$selectPath = $this->interpreter->interpret($ast);
				$this->push($name, array("path" => $selectPath, "branch" => null));
				break;
			case "case_branch":
				$caseExprs = $this->interpreter->interpret($ast);
				$numExprs = count($caseExprs);
				$obj = $this->getCurrObj("case_begin");
				$path = $obj["path"];
				if ($numExprs == 1) {
					$condition = new Fabscript_Comparison(
						Fabscript_Comparison::EQ,
						$path,
						$caseExprs[0]
						);
				} else {
					$conditions = array();
					foreach ($caseExprs as $caseExpr) {
						$conditions[] = new Fabscript_Comparison(
							Fabscript_Comparison::EQ,
							$path,
							$caseExpr
							);
					}
					$condition = new Fabscript_Disjunction($conditions);
				}
				
				$branch = $obj["branch"];
				if ($branch != null) {
					$branch->addBranch($condition);
				} else {
					$lastIdx = count($this->stack) - 1;
					$branch = new Fabscript_Branch($condition);
					$this->stack[$lastIdx] = array(
						"element" => "case_begin",
						"object" => array("path" => $path, "branch" => $branch)
						);
				}
				break;
			case "default_branch":
				$obj = $this->getCurrObj("case_begin");
				$branch = $obj["branch"];
				if ($branch == null) {
					throw new Exception("Error in CASE statement");
				}
				$branch->addDefaultBranch();
				break;
			case "case_end":
				$obj = $this->getCurrObj("case_begin");
				$branch = $obj["branch"];
				if ($branch == null) {
					throw new Exception("Error in CASE statement");
				}
				$this->pop();
				$this->getCurrContainer()->addElement($branch);
				break;

			case "var_decl":
			case "assign":
				$varDeclOrAssignment = $this->interpreter->interpret($ast);
				$obj = $this->getCurrObj();
				$obj->addElement($varDeclOrAssignment);
				break;

            case "snippet_begin":
                $snippet = $this->interpreter->interpret($ast);
                $snippetName = $snippet->getName();
                if (array_key_exists($snippetName, $this->snippets)) {
                    throw new Exception("Snippet '{$snippetName}' exists already");
                }
                $this->snippets[$snippetName] = $snippet;
                $this->push($name, $snippet);
                break;

            case "snippet_end":
                $this->pop();
                break;

            case "paste_snippet":
                $pasteData = $this->interpreter->interpret($ast);
                $snippetName = $pasteData["name"];
                if (array_key_exists($snippetName, $this->snippets)) {
                    $snippetDef = $this->snippets[$snippetName];
                    $snippet = $snippetDef->paste($pasteData["arguments"], $pasteData["indentLevel"]);
                    $this->getCurrContainer()->addElement($snippet);
                } else {
                    throw new Exception("Snippet '{$snippetName}' could not be found");
                }
                break;

            case "edit_section_begin":
                $editSection = $this->interpreter->interpret($ast);
                $this->push($name, $editSection);
                break;

            case "edit_section_end":
                $editSection = $this->getCurrObj("edit_section_begin");
                $this->pop();
                $this->getCurrContainer()->addElement($editSection);
                break;

		}

	}

	public function processRawLine($line) {

		$curr = $this->getCurrContainer();
		if (!($curr instanceof Fabscript_TextElement)) {
			throw new Exception("Code creation error");
		}

		$curr->addRawLine($line);
		
	}

	public function getLines() {

        $res = array();

        self::$currentInstance = $this;

		$document = $this->stack[0]["object"];

		$originalLines = $document->getLines($this->globalEnv);

        // Pretty print - strip off multiple empty lines:
        $lastLine = "";
        foreach ($originalLines as $line) {
            if ("" != $lastLine || "" != $line) {
                array_push($res, $line);
                $lastLine = $line;
            }
        }

        return $res;

    }
    
	public function getCompletePath($path) {
		
		if ("/" == $path[0]) return $path;
		
		foreach (self::$templateDirs as $dir) {
			$fullpath = $dir . "/" . $path;
			if (file_exists($fullpath)) {
				return $fullpath;
			}
		}

		return "";
				
	}
    
	private function push($elementName, $obj) {

		$this->stack[]  = array("element" => $elementName, "object" => $obj);

	}

	private function pop() {

		array_pop($this->stack);

	}

	private function getCurrObj($expectedElement = "") {

		$curr = end($this->stack);

		if ($expectedElement != "" && $curr["element"] != $expectedElement) {
			throw new Exception("Incorrect nesting");
		}

		return $curr["object"];

	}

	private function getCurrContainer($expectedElement = "") {

		$curr = end($this->stack);

		if ($expectedElement != "" && $curr["element"] != $expectedElement) {
			throw new Exception("Incorrect nesting");
		}

		if ($curr["element"] != "case_begin") {
			return $curr["object"];	
		} else {
			// Special treatment for case branches needed:
			return $curr["object"]["branch"];
		}
		
	}

    private function scanEditableSections($filePath) {

        if ($this->editSectionParser === NULL) {
            throw new Exception("No edit section parser configured!");
        }

        $this->editSections = array();

        if (!file_exists($filePath)) {
            return;
        }

        $fp = fopen($filePath, "r");

        $currentSection = "";
        $editedLines = array();

        while (!feof($fp)) {

            $line = fgets($fp);
            $line = rtrim($line); // remove end of line

            $sectionName = $this->editSectionParser->getSectionName($line);
            if ($sectionName !== FALSE) {
                if (strlen($currentSection) === 0) {
                    $currentSection = $sectionName;
                    $editedLines = array();
                } else {
                    fclose($fp);
                    throw new Exception("Edit sections must not be nested!");
                }
            } else if ($this->editSectionParser->isEndComment($line)) {
                if (strlen($currentSection) > 0) {
                    $this->editSections[$currentSection] = $editedLines;
                    $currentSection = "";
                    $editedLines = array();
                } else {
                    throw new Exception("Encountered unexpected section end!");
                }
            } else {
                if (strlen($currentSection) > 0) {
                    array_push($editedLines, $line);
                }
            }

        }

        fclose($fp);
    }

    private static $currentInstance = NULL;
	private $preprocessor;
	private $parser;
	private $interpreter;
	private $globalEnv;
	private $stack;
    private $snippets = array();
    private $editSectionConfig = NULL;
    private $editSectionParser = NULL;
    private $editSections = array();

}


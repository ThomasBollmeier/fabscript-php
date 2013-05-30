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

require_once 'Fabscript/container.php';
require_once 'Fabscript/block.php';
require_once 'Fabscript/environment.php';

class Fabscript_SnippetFactory implements Fabscript_Container {

    public function __construct($name) {

        $this->name = $name;
        $this->block = new Fabscript_Block();
        $this->paramNames = array();

    }

    public function addParameter($paramName) {

        array_push($this->paramNames, $paramName);

    }

    public function addRawLine($rawLine) {

        $this->block->addRawLine($rawLine);

    }

    public function addElement(Fabscript_Element $element) {

        $this->block->addElement($element);

    }

    public function getLines($env)
    {

        return array();

    }

    public function getName() {

        return $this->name;

    }

    public function paste($argExprs=array(), $indentLevelExpr=null) {

        $numParams = count($this->paramNames);
        if ($numParams != count($argExprs)) {
            throw new Exception("# of arguments does not match # of parameters");
        }

        $args = array();
        for ($i=0; $i < $numParams; $i++) {
            $args[$this->paramNames[$i]] = $argExprs[$i];
        }

        return new Fabscript_Snippet($this->block, $args, $indentLevelExpr);

    }

    private $name;
    private $block;
    private $paramNames;

}

class Fabscript_Snippet implements Fabscript_Element {

    public function __construct($block, $arguments, $indentLevelExpr=null) {

        $this->block = $block;
        $this->arguments = $arguments;
        $this->indentLevelExpr = $indentLevelExpr;

    }

    public function getLines($env) {

        $snippetEnv = new Fabscript_Environment($env);

        foreach ($this->arguments as $name => $valueExpr) {
            $snippetEnv->set($name, $valueExpr->getValue($env));
        }

        $lines = $this->block->getLines($snippetEnv);

        if (!$this->indentLevelExpr) {

            return $lines;

        } else {

            $res = array();
            $indentLevel = $this->indentLevelExpr->getValue($env);

            foreach ($lines as $line) {
                $tmp = $line;
                for ($i=0; $i < $indentLevel; $i++) {
                    $tmp = "\t" . $tmp;
                }
                array_push($res, $tmp);
            }

            return $res;

        }

    }

    private $arguments;
    private $block;
    private $indentLevelExpr;

}

?>
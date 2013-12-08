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

namespace Fabscript;

interface EditSectionConfig
{

    public function getStartComment($sectionName);

    public function getEndComment($sectionName);

}

interface EditSectionParser
{

    /**
     * @param $codeLine
     * @return sectionName if $codeLine is the
     * start comment of an editable section, FALSE otherwise
     */
    public function getSectionName($codeLine);

    /**
     * @param $codeLine
     * @return TRUE if $codeLine ends an editable section
     */
    public function isEndComment($codeLine);

}

class ProgLang
{

    const PHP = "PHP";

}

class EditSectionParserFactory
{

    public function __construct($progLang)
    {
        $this->progLang = $progLang;
        $this->parser = NULL;
    }

    public function getParser()
    {

        if ($this->parser === NULL) {
            $this->init();
        }
        return $this->parser;

    }

    public function getConfig()
    {

        if ($this->parser === NULL) {
            $this->init();
        }
        return $this->parser;

    }

    private $progLang;
    private $parser;

    private function init()
    {

        switch ($this->progLang) {
            case ProgLang::PHP:
                $this->parser = new EditSectionParserPHP();
                break;
            default:
                throw new \Exception("Programming language " . $this->progLang . " is not supported!");
        }

    }

}

class EditSectionParserPHP implements EditSectionParser, EditSectionConfig
{

    public function __construct()
    {

        $this->regexStartComment = "#^\s*//\s*editable-begin\s+(\w+)\s*\{#";
        $this->regexEndComment = "#^\s*//\s*}\s+editable-end\s+\(\w+\)\s*#";

    }

    public function getStartComment($sectionName)
    {
        return "// editable-begin " . $sectionName . " {";
    }

    public function getEndComment($sectionName)
    {
        return "// } editable-end (" . $sectionName . ")";
    }

    public function getSectionName($codeLine)
    {
        if (preg_match($this->regexStartComment, $codeLine, $matches)) {
            return $matches[1];
        } else {
            return FALSE;
        }
    }

    public function isEndComment($codeLine)
    {
        return preg_match($this->regexEndComment, $codeLine) ? TRUE : FALSE;
    }

    private $regexStartComment;
    private $regexEndComment;
}

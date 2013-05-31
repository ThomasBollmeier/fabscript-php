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

require_once 'Fabscript/io.php';

class Fabscript_LineType {

	const COMMAND = 1;
	const RAWLINE = 2;
	const INCLUDE_LINE = 3;

}

class Fabscript_Preprocessor {

	public static $searchDirs = array(".");

	public function __construct() {

	}

	public function getLineInfo(Fabscript_LineInStream $instream) {

		$res = array();

		$instream->open();

		$line = $instream->getNextLine();
		$continueWithCommand = FALSE;
		$cmd = "";

		while ($line !== null) {

			if (!$continueWithCommand) {
		
				$parseResult = $this->parseLine($line);
				$content = $parseResult['content'];

				switch ($parseResult['lineType']) {

				case Fabscript_LineType::COMMAND:

					if ($content[strlen($content)-1] == $this->lineContinueChar) {

						$cmd = rtrim($content, $this->lineContinueChar);
						$continueWithCommand = TRUE;

					} else {

						$res[] = array(
							'lineType' => Fabscript_LineType::COMMAND, 
							'content' => $content
							);
					
					}

					break;

				case Fabscript_LineType::RAWLINE:

					$res[] = $parseResult;
					break;

				case Fabscript_LineType::INCLUDE_LINE:

					$res = array_merge($res, $this->getIncludeInfo($content));
					break;

				}

			} else {

				$content = rtrim($line);

				if ($content[strlen($content)-1] == $this->lineContinueChar) {

					$cmd .= rtrim($content, $this->lineContinueChar);

				} else {

					$cmd .= $content;

					$res[] = array(
						'lineType' => Fabscript_LineType::COMMAND, 
						'content' => $cmd
						);

					$cmd = "";
					$continueWithCommand = FALSE;
					
				}

			}

			$line = $instream->getNextLine();

		}

		$instream->close();

		return $res;

	}

	private function parseLine($line) {

		if (preg_match($this->regexIncludeLine, $line, $matches) === 1) {

			$includeName = $matches[1];

			return array(
				'lineType' => Fabscript_LineType::INCLUDE_LINE,
				'content' => $includeName
				);

		}

		if (preg_match($this->regexCmdLine, $line, $matches) === 1) {

			$content = trim($matches[1]);

			return array(
				'lineType' => Fabscript_LineType::COMMAND, 
				'content' => $content
				);

		} else {

			return array(
				'lineType' => Fabscript_LineType::RAWLINE, 
				'content' => $line
				);

		}

	}

	private function getIncludeInfo($includeName) {

		$includePath = $this->getInclPath($includeName);

		if (array_key_exists($includePath, $this->includes)) {
			return $this->includes[$includePath];
		} 

		$stream = new Fabscript_FileInput($includePath);

		$res = $this->getLineInfo($stream);

		$this->includes[$includePath] = $res;

		return $res;

	}

	private function getInclPath($includeName) {

		foreach (Fabscript_Preprocessor::$searchDirs as $dir) {
			$path = $dir . "/" . $includeName;
			if (file_exists($path)) {
				return $path;
			}
		}

		return "";

	}

	private $regexIncludeLine = '/^[[:blank:]]*:>[[:blank:]]*include[[:blank:]]+"(.+)"/';
	private $regexCmdLine = '/^[[:blank:]]*:>(.+)/';
	private $lineContinueChar = '\\';
	private $includes = array();

}

?>

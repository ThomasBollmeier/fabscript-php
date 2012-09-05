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

}

class Fabscript_Preprocessor {

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

				if ($parseResult['lineType'] == Fabscript_LineType::COMMAND) {

					if ($content[strlen($content)-1] == $this->lineContinueChar) {

						$cmd = rtrim($content, $this->lineContinueChar);
						$continueWithCommand = TRUE;

					} else {

						$res[] = array(
							'lineType' => Fabscript_LineType::COMMAND, 
							'content' => $content
							);
					
					}

				} else {

					$res[] = $parseResult;

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

	private $regexCmdLine = '/^[[:blank:]]*:>(.+)/';
	private $lineContinueChar = '\\';

}

?>
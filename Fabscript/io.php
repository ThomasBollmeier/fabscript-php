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

interface Fabscript_LineInStream {

	public function open();
	public function close();
	public function getNextLine();

}

class Fabscript_StringsInput implements Fabscript_LineInStream {

	public function __construct($lines = array()) {

		$this->currIdx = 0;
		$this->lines = $lines;

	}

	public function addLine($line) {

		$this->lines[] = $line;

	}

	public function open() {

		$this->isOpen = TRUE;
		$this->currIdx = 0;

	}

	public function close() {

		$this->isOpen = FALSE;

	}

	public function getNextLine() {

		if ($this->isOpen && $this->currIdx < count($this->lines)) {

			$res = $this->lines[$this->currIdx++];
			return $res;

		}

		return null;

	}

	private $currIdx;
	private $lines;
	private $isOpen = FALSE;

}

class Fabscript_FileInput implements Fabscript_LineInStream {

	public function __construct($filePath) {

		$this->filePath = $filePath;
		$this->file = null;

	}

	public function open() {

		$this->file = fopen($this->filePath, "r");

	}

	public function close() {

		if ($this->file !== null) {

			fclose($this->file);
			$this->file = null;

		}

	}

	public function getNextLine() {

		if ($this->file == null || feof($this->file)) {
			return null;
		}

		return rtrim(fgets($this->file));

	}

	private $filePath;
	private $file;

}

?>
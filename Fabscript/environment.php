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

class Fabscript_Environment {

	public function __construct($parent=null) {

		$this->parent = $parent;
		$this->symbols = array();

	}

	public function set($name, $value) {

		$this->symbols[$name] = $value;

	}

	public function get($name) {

		$env = $this;

		while ($env) {

			if (array_key_exists($name, $env->symbols)) {
				return $env->symbols[$name];
			}

			$env = $env->parent;

		}

		throw new Exception("Symbol '" . $name . "' is unknown", 1);
		
	}

	public function getDefiningEnv($name) {

		$env = $this;

		while ($env) {

			if (array_key_exists($name, $env->symbols)) {
				return $env;
			}

			$env = $env->parent;

		}

		return null;

	}

	public function hasKey($name) {

		return array_key_exists($name, $this->symbols);

	}

	private $parent = null;
	private $symbols;

}

?>
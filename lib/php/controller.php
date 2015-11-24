<?php
/*

Copyright 2015 Christos Dimas <specktator@totallynoob.com>

This file is part of femto.

femto is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

femto is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with femto.  If not, see <http://www.gnu.org/licenses/>.
Source: https://github.com/specktator/scraper

*/
defined('ALPHA') or die('Get Out');
/**
* controller
*/
class controller
{
	private $error = FALSE;

	function __construct()
	{
		#validate/sanitize all inputs
		$this->request = $_REQUEST;
		$this->validate();
		$this->sanitize();
		$this->checkErrors();
		$this->action();
	}

	function validate(){
		$this->error[] = (!empty($this->request['action']))? array(FALSE) : array(TRUE,'action',$this->request['action']);
	}

	function sanitize(){
			$this->request['action'] = strtolower($this->request['action']);
	}

	function checkErrors(){
		$bitWise = FALSE;
		foreach ($this->error as $key => $value) {
			if($value[0]){
				error_log('controller input error: '.$value[1].' => '.$value[2]);
			}
			$bitWise = $bitWise | $value[0];
		}
		!$bitWise or die(); // if error states == false then ok or die
	}

	function action(){

		if(class_exists($this->request['action'])){

			$this->model = new $this->request['action']();
			
		}else{

			throw new Exception("Error controller: class does not exist.");
			
		}
		// return $this;
	}
}

?>
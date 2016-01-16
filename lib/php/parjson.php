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


/**
* json file parser
*/
defined('ALPHA') or die('Get Out');
class parjson
{
	function __construct($filename=NULL)
	{
		$this->filename = (!empty($filename))? $filename : 'audio.json';
		if(!preg_match('/http/',$this->filename) or preg_match('/http/',$this->filename) <> 1){ // if it's not a url reads local file
			$this->fileLocation = 'local';
			$this->filename = JSONDBPATH.$this->filename;
		}else{
			$this->fileLocation = 'remote';
		}
			// $this->handle = fopen($this->filename,"r+");

	}

	function read(){
		if($this->fileLocation == 'local'){
			// fseek($this->handle,0);
			// @$data = fread($this->handle,filesize($this->filename));
			@$data = file_get_contents($this->filename);
			return json_decode($data);
		}elseif($this->fileLocation == 'remote') {
			return json_decode(file_get_contents($this->filename));
		}else{
			error_log("psrjson:: error:: reading");
			throw new Exception(_CLASS__.": ".__METHOD__.": Error writing to DB", 1);
			return FALSE;
		}
	}

	function write($data){
		
		// fseek($this->handle,0);
		// ftruncate($this->handle, filesize($this->filename));
		// if(!fwrite($this->handle, $data)){error_log("parjson:: error :: writing");}
		$result = file_put_contents($this->filename,$data, LOCK_EX );
		if ( $result === FALSE) {
			error_log(_CLASS__.": ".__METHOD__.": Error writing to DB");
			throw new Exception(_CLASS__.": ".__METHOD__.": Error writing to DB", 1);

		}
	}

	function close(){
		
		// if(!fclose($this->handle)){error_log("parjson:: error:: closing file");}
	}
}
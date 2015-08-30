<?php
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
			return FALSE;
		}
	}

	function write($data){
		
		// fseek($this->handle,0);
		// ftruncate($this->handle, filesize($this->filename));
		// if(!fwrite($this->handle, $data)){error_log("parjson:: error :: writing");}
		file_put_contents($this->filename,$data);
	}

	function close(){
		
		// if(!fclose($this->handle)){error_log("parjson:: error:: closing file");}
	}
}
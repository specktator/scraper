<?php
/**
* json file parser
*/
class parjson
{
	
	function __construct($filename=NULL)
	{
		$this->filename = (!empty($filename))? $filename : jsonfile;
		if(!preg_match('/http/',$this->filename) or preg_match('/http/',$this->filename) <> 1){ // if it's not a url reads local file
			$this->fileLocation = 'remote';
			$this->handle = fopen($this->filename,"r+");
		}else{
			$this->fileLocation = 'local';
		}
	}

	function read(){
		if($this->fileLocation == 'local'){
			@$data = fread($this->handle,filesize($this->filename));
			return json_decode($data);
		}elseif($this->fileLocation == 'remote') {
			return json_decode(file_get_contents($this->filename));
		}else{
			error_log("psrjson:: error:: reading");
			return FALSE;
		}
	}

	function write($data){

		if(!fwrite($this->handle, $data)){error_log("parjson:: error :: writing");}
	}

	function close(){
		
		if(!fclose($this->handle)){error_log("parjson:: error:: closing file");}
	}
}
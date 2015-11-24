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
	* general functions class
	*/
	class functions
	{
		public $tempfile;

		
		public function saveTempFile($imgData){
			/*	
				saves a temp image file for later usage with getMimeType()
				returns temp file path
			*/
			// $bt = debug_backtrace();
			// $caller = array_shift($bt);
			// error_log(__METHOD__.":: ".$caller['file']." ".$caller['line']);

			$this->tempfile = tempnam(TEMPDIR, "SCRAP_FILE_");
			$fhandle = fopen($this->tempfile,'wb');
			if( preg_match('/http/', $imgData) == 1 ){
				// $data =  fread(fopen($imgData,"rb"),$this->getRemoteFileSize($imgData));
				$imgData =  file_get_contents($imgData);
			}
			fwrite($fhandle,$imgData); //writes tempfile
			fclose($fhandle);
			return $this->tempfile;
		}	

		public function getRemoteFileSize($url){ 
			$head = ""; 
			$url_p = parse_url($url); 
			$host = $url_p["host"]; 
			if(!preg_match("/[0-9]*\.[0-9]*\.[0-9]*\.[0-9]*/",$host)){
				// a domain name was given, not an IP
				$ip=gethostbyname($host);
				if(!preg_match("/[0-9]*\.[0-9]*\.[0-9]*\.[0-9]*/",$ip)){
					//domain could not be resolved
					return -1;
				}
			}
			$port = intval($url_p["port"]); 
			if(!$port) $port=80;
			$path = $url_p["path"]; 
			//echo "Getting " . $host . ":" . $port . $path . " ...";
			$fp = fsockopen($host, $port, $errno, $errstr, 20); 
			if(!$fp) { 
				return false; 
				} else { 
				fputs($fp, "HEAD "  . $url  . " HTTP/1.1\r\n"); 
				fputs($fp, "HOST: " . $host . "\r\n"); 
				fputs($fp, "User-Agent: http://www.example.com/my_application\r\n");
				fputs($fp, "Connection: close\r\n\r\n"); 
				$headers = ""; 
				while (!feof($fp)) { 
					$headers .= fgets ($fp, 128); 
					} 
				} 
			fclose ($fp); 
			//echo $errno .": " . $errstr . "";
			$return = -2; 
			$arr_headers = explode("\n", $headers); 
			// echo "HTTP headers for ..." . substr($url,strlen($url)-20). ":";
			// echo "";
			foreach($arr_headers as $header) { 
				// if (trim($header)) echo trim($header) . "";
				$s1 = "HTTP/1.1"; 
				$s2 = "Content-Length: "; 
				$s3 = "Location: "; 
				if(substr(strtolower ($header), 0, strlen($s1)) == strtolower($s1)) $status = substr($header, strlen($s1)); 
				if(substr(strtolower ($header), 0, strlen($s2)) == strtolower($s2)) $size   = substr($header, strlen($s2));  
				if(substr(strtolower ($header), 0, strlen($s3)) == strtolower($s3)) $newurl = substr($header, strlen($s3));  
				} 
			// echo "";
			if(intval($size) > 0) {
				$return=intval($size);
			} else {
				$return=$status;
			}
			// echo intval($status) .": [" . $newurl . "]";
			if (intval($status)==302 && strlen($newurl) > 0) {
				// 302 redirect: get HTTP HEAD of new URL
				$return=remote_file_size($newurl);
			}
			return $return; 
		}
	}
?>
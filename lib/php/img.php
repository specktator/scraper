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
* image manipulation
*/
class img extends functions
{
	private $storePath;

	function __construct(){
		$this->storePath = ROOT_PATH.'../images/';
		$this->db = new db();
		$this->db->settings()->read()->read_settings();
	}

	function b642img(){
	}

	function img2b64($imgData){

		/* 
			@ $image string (local path or url) or data
			return base64 string
		*/

		$this->getImageDataType($imgData);
		$this->getMimeType($this->decideToCache($imgData));
		// echo $this->filepath."<br>";
		// echo $this->imageDataType."<br>";
		if ($this->imageDataType === 'binary') {
			$data = $this->filepath; // now filepath contains image binary string
		}else{
			$data = file_get_contents($this->filepath);
		}
		return $base64 = 'data:' . $this->mimeType . ';base64,' . base64_encode($data);
		unlink($this->tempfile); // delete temp file

	}

	function compressImg($dest,$imgblob,$quality=75){
	  	
		if (class_exists('Imagick')) {
		  	$im = new Imagick();
			$im->readImageBlob($imgblob);
			$im->setImageFormat('jpeg');
			$im->setImageCompressionQuality($quality);
			$im->writeImage($dest);
		}else{
			$this->db->settings->imageCompression = false;
			$this->db->write_settings()->write();
			throw new Exception("Imagick doesn't exist", 1);
			
		}
	}

	function saveImg($imgData){

		$this->getImageDataType($imgData);
		$this->getMimeType($this->decideToCache($imgData));

		if($this->db->settings->imageCompression === true){
			$fileName = $this->createFileName($imgData).".jpg";
		}else{
			$fileName = $this->createFileName($imgData).$this->mimeToExtention($this->mimeType);
		}

		if(!file_exists($fileName)){
			try {

				if($this->db->settings->imageCompression === true){
					$this->compressImg(IMAGES_SAVE_DIR.$fileName,$imgData);
				}else{
					file_put_contents(IMAGES_SAVE_DIR.$fileName,$imgData);
				}

				return IMAGES_SAVE_DIR_REL.$fileName;
				
			} catch (Exception $e) {
				error_log($e->getMessage());
			}
		}else{

			return IMAGES_SAVE_DIR_REL.$fileName;
		}

		unlink($this->tempfile); //delete temp image file
	}

	function createFileName($imgData){
		return $fileName = md5($imgData);
	}

	function getImageDataType($imgData){

		/*
			Determine Image Data Type and location
			$imgData can be a string or **binary string**
		*/
		if (preg_match('/http/', $imgData) == 1) { // remote file, http url

			return $this->imageDataType = 'remote';
			
		}elseif (!ctype_punct($imgData)) { // binary string

			return $this->imageDataType = 'binary';

		}elseif (ctype_punct($imgData)){ // local file path
			
			return $this->imageDataType = 'local';

		}else{
			throw new Exception("Can't find Image file Data Type.");
			return $this->imageDataType = FALSE;

		}
	}

	function decideToCache($imgData){

		if($this->imageDataType === 'local'){

			return $this->filepath = $imgData; // return path

		}elseif ($this->imageDataType === 'remote') {
			
			return $this->filepath = $this->saveTempFile($imgData); //return temp file path

		}elseif ($this->imageDataType === 'binary') {
			
			// return $this->filepath = $this->saveTempFile($imgData); //return temp file path
			return $this->filepath = $imgData; //return temp file path

		}else{
			throw new Exception("Can't decide if image file is to be cached.");
		}
	}

	function getMimeType($data)
	{
		/*
			returns the image file mime type or FALSE on error
			$data can be **binary String** or a normal filePath string
		*/
	    $this->mimeType = FALSE;
	    if ($this->imageDataType === 'binary' && class_exists('finfo')) {
	    	
	    	$finfo = new finfo(FILEINFO_MIME);
			$this->mimeType = $finfo->buffer($data);

	    }elseif($this->imageDataType === 'remote' || $this->imageDataType === 'local' && function_exists('exif_imagetype')) { 
	    	
	    	if(in_array($imageTypeConsantNum = exif_imagetype($data),range(1,17),TRUE)){
        		$this->mimeType = image_type_to_mime_type($imageTypeConsantNum);
        	}

	    }elseif($this->imageDataType === 'remote' || $this->imageDataType === 'local' && function_exists('mime_content_type')) {
	       
	       $this->mimeType = mime_content_type($data);

	    }
	    if(!$this->mimeType){
	    	
	    	throw new Exception("Dying: can't find mime type") and die();

	    }
	    return $this->mimeType;
	}

	function mimeToExtention($mimetype){

		$extentions = ['image/jpeg'=>'.jpg', 'image/png'=>'.png', 'image/bmp'=> '.bmp', 'image/tiff'=>'.tiff', 'image/gif'=>'.gif'];
		$mimetype = preg_replace('/\;(.*)/','',$mimetype);
		foreach ($extentions as $key => $ext) {

			if($key === $mimetype){
				return $this->extention = $ext;
			}
		}

		throw new Exception("Can't find image extention from this mime type $mimetype");
		
	}
}


?>
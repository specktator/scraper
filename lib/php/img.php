<?php
defined('ALPHA') or die('Get Out');
/**
* image manipulation
*/
class img extends functions
{
	private $storePath;

	function __construct(){
		$this->storePath = ROOT_PATH.'../images/';
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

	function saveImg($imgData){

		$this->getImageDataType($imgData);
		$this->getMimeType($this->decideToCache($imgData));

		$fileName = $this->createFileName($imgData).$this->mimeToExtention($this->mimeType);

		if(!file_exists($fileName)){
			try {
				
				file_put_contents(IMAGES_SAVE_DIR.$fileName,$imgData);
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
			Determine Image Data Type
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
<?php
/**
* Local dir  scan
*/
class localdirscraper
{
	private $ftypes;
	
	function __construct()
	{
		$this->ftypes = "(.mp3|.ogg)";
	}
	
	function directoryScan($dir, $onlyfiles = true, $fullpath = true) {
		if (isset($dir) && is_readable($dir)) {
			$dlist = Array();
			$dir = realpath($dir);
			if ($onlyfiles) {
				$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
			} else {
				$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);
			}
			
					$db = new db();
					$db->audio()->read();
					
			foreach($objects as $entry => $object){	
				if (!$fullpath) {
					$entry = str_replace($dir, '', $entry);
				}
				if(preg_match('/'.$this->ftypes.'$/i', $entry) === 1){
					$dlist[] = $entry;
					// $db->settings()->read();
					$db->write_track($entry)->write();

				}
			}
			
			return $dlist;
		}
	}
}

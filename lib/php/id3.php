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

// ini_set('xdebug.profiler_enable',1);
require ROOT_PATH.'vendor/autoload.php';
use PhpId3\Id3TagsReader;

/**
* meta tags class
*/
class id3 extends functions
{
	private $filename;
	
	function __construct()
	{
		// set_time_limit('120');
		$this->filename = urldecode($_REQUEST['url']);
		$this->trackid = $_REQUEST['track-id'];
		$this->validate(); 
		$this->getID3 = new getID3; // Initialize getID3 engine
		$this->img = new img(); // Initialize img class
		$this->getMeta();
		
	}

	function validate(){
		if (preg_match('/[a-z0-9]+/',$this->trackid) != 1) {
			$this->error('track-id is not valid.',1);
		}

		if(preg_match('/http\:\/\//',$this->filename) != 1){

			$this->error('url is not valid.',1);
		}

		// if (filter_var($this->filename,FILTER_VALIDATE_URL)) {

		// }else{
			// $this->filename = FALSE;
			// error_log('id3: error: not valid url');
			// json_encode(array('e'=>'id3: error: not valid url'));
			// die();
		// }
	}

	function getMeta(){

		try{
			// FIRST TRY LOCAL JSON DATABASE AND THEN INIT THE REQUEST
			if(!$this->getMetaJson()){
				$this->decideToCache();
				set_time_limit(ID3_EXEC_TIME);
				$tags = $this->getID3->analyze($this->filename);
				getid3_lib::CopyTagsToComments($tags);
				set_time_limit(NORMAL_EXEC_TIME);
				$artist = ($tags['comments']['artist'][0])? $tags['comments']['artist'][0] : null;
				$title = ($tags['comments']['title'][0])? $tags['comments']['title'][0] : null;
				$album = ($tags['comments']['album'][0])? $tags['comments']['album'][0] : null;
				$genre = ($tags['comments']['genre'][0])? $tags['comments']['genre'][0] : null;
				$albumart = (@$tags['comments']['picture'][0]['data'])? $this->img->saveImg($tags['comments']['picture'][0]['data']) : null;

				$tagsArray = ['trackid'=>$this->trackid, 'artist'=>$artist, 'title'=>$title, 'album'=>$album,'genre'=>$genre,'albumart'=>$albumart];
				
				//writing found tags in db
				$db = new db();
				$db->audio()->read()->write_tags($tagsArray)->write();
				echo json_encode($tagsArray);
				unset($db);
			}
		}catch (Exception $e){
			echo json_encode(array('e'=>$e->getMessage(),'msg'=>'Can\'t load ID3 tags', 'title'=>basename(urldecode($_REQUEST['url']))));
			// if track doent have valid id3 tags then leave it as is.
		}
		@unlink($this->filename);
	}

	function setMeta(){} // will set meta tags from user input? / retrieved from search engines? / meta tags online libraries?

	function getMetaJson(){

		try {
			$db = new db();
			$db->audio()->read()->load_tags($this->trackid);
			if(!empty($db->tags)){
				

				echo json_encode($db->tags);
				return TRUE;
			}
			unset($db);

		} catch (Exception $e) {
			echo json_encode(array('e'=>$e->getMessage()));
			return FALSE;
		}

	}

	function decideToCache(){
		$urlType = preg_match('/'.$_SERVER['SERVER_NAME'].'/', $this->filename);
		if($urlType == 1){ // local url
			$this->filename = $this->saveTempFile(preg_replace('/\s/','%20',$this->filename)); // to be continued, don't re-download a local file via http
		}elseif ($urlType == 0) {// remote url
			try {
				$this->filename = $this->saveTempFile(preg_replace('/\s/','%20',$this->filename));
			} catch (Exception $e) {
				echo json_encode(array('e'=>$e->getMessage()));
			}
		}
	}

	function error($errormsg,$fatal){
		echo json_encode(array('e'=>$errormsg));
		throw new Exception($errormsg);
		if($fatal === 1) die();
	}
}
?>

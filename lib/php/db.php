<?php
defined('ALPHA') or die('Get Out');
class db{

	public $parjson;
	public $audio;

	public function audio(){
		$this->parjson = new parjson('audio.json');
		return $this;
	}

	public function video(){
		$this->parjson = new parjson('video.json');
		return $this;
	}

	public function read(){
		$this->records = $this->parjson->read();
		return $this;
	}

	public function load_songs(){
		if (!empty($this->records->tracks)) {
			foreach ($this->records->tracks as $id => $obj) {
				$this->urls [] = array(
					'id'=>$id,
					'url'=>urldecode($obj->url),
					'title'=>basename(urldecode($obj->url))
					);
			}
			$this->out = $this->urls;
		}else{
			throw new Exception('Library is empty.');
			$this->out = ['e'=>'Library is empty.'];
		}
		return $this;
	}

	/*
	*	@load_tags
	*	@returns: null if id doesn't exist or object with the tags
	*/

	public function load_tags($id){
		@$this->out = $this->tags = $this->records->tracks[$id]->tags;
		return $this;
	}

	public function load_stats($id){
		$this->out = $this->stats = $this->records->tracks[$id]->stats;
		return $this;
	}

	public function write_track($url){

		if(isset($url) && ctype_print($url)) {
			try {
				$urlMd5 = md5($url);
				$this->checkDupes($urlMd5);
				@$this->records->tracks[]->url = $url;
				@$this->records->tracks[count($this->records->tracks)-1]->md5 = $urlMd5;
				@$this->records->tracks[count($this->records->tracks)-1]->host = parse_url($url)['host'];
			} catch (Exception $e) {
				echo json_encode( ['e'=>$e->getMessage()] );
			}
				$this->out = $this->records;

		}else{
			throw new Exception("Empty or non printable characters provided in url.");
			
		}
		return $this;
	}

	public function checkDupes($md5){

		foreach ($this->records->tracks as $id => $urlHash) {
			if($md5 === $urlHash->md5){

				throw new Exception("Duplicate found, song id:$id");
				
			}
		}
		return TRUE;
	}

	public function write_tags($tagsArray){
		$id = $tagsArray['trackid'];
		unset($tagsArray['trackid']);
		$checkedTags = array_map('ctype_print', $tagsArray);
		foreach (array_keys($tagsArray) as $key) {
			
			try {
				
				if($checkedTags[$key] === TRUE) {

					@$this->records->tracks[$id]->tags->{$key} = $tagsArray[$key];

				}else{
					throw new Exception("Error writing tag: $key for track with $id and value '{$tagsArray[$key]}'");
				}
				$this->out = $this->records;
			} catch (Exception $e) {
				error_log( $e->getMessage() );
			}

		}
		return $this;
	}

	public function write(){
		if(!empty($this->out)){
			$this->parjson->write(json_encode($this->out));
		}
		$this->parjson->close();
	}

	public function spit_out(){
		echo json_encode($this->out);
	}


}

?>
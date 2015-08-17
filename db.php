<?php
include 'parjson.php';

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
			$this->out = array('error'=>'Library is empty.');
		}
		return $this;
	}

	/*
	*	@load_tags
	*	@returns: null if id doesn't exist or object with the tags
	*/

	public function load_tags($id){
		$this->out = $this->tags = $this->records->tracks[$id]->tags;
		return $this;
	}

	public function load_stats($id){
		$this->out = $this->stats = $this->records->tracks[$id]->stats;
		return $this;
	}

	public function write_track($url){

		if(isset($url)) {
			@$this->records->tracks[]->url = $url;
			$this->out = $this->records;
		}else{
			error_log('empty url given.');
		}
		return $this;
	}

	public function write_tags($id,$artist,$title,$album){
		$this->records->tracks[$id]->tags->artist = $artist;
		$this->records->tracks[$id]->tags->title = $title;
		$this->records->tracks[$id]->tags->album = $album;
		$this->out = $this->records;
		return $this;
	}

	public function write(){
		$this->parjson->write(json_encode($this->out));
		$this->parjson->close();
	}

	public function spit_out(){
		echo json_encode($this->out);
	}


}

// $db = new db();
// $db->audio()->read()->load_songs()->spit_out();
// $db->audio()->read()->load_tags(0)->spit_out();
// $db->audio()->read()->load_stats(0)->spit_out();
// $db->audio()->read()->write_tags(1,'metallica','one','one')->write();
?>
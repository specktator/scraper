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

	public function playlists(){
		$this->parjson = new parjson('playlists.json');
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

	public function load_artists($tagsArray){ //todo
		try {

			$artists = array_unique(array_filter(array_map(function($value){
                             return is_object($value) ? @$value->artist : @$value['artist'];
                            }, $tagsArray),function($value){
                             return (empty($value))? FALSE : TRUE;
                            }));
            foreach ( $artists as $songid => $name) {
              flush();
              echo '<li class="list-group-item"><a href="#" data-value="'.$name.'">'.$name.'</a></li>';
            }

		} catch (Exception $e) {
			throw new Exception("Error loading tags");
			
		}
	}

	public function load_genres($tagsArray){ //todo
		try {
			$genres = array_unique(array_filter(array_map(function($value){
                             return is_object($value) ? @$value->genre : @$value['genre'];
                            }, $tagsArray),function($value){
                              return (empty($value))? FALSE : TRUE;
                            }));
	        foreach ( $genres as $songid => $name) {
	          flush();
	          echo '<li class="list-group-item"><a href="#" data-value="'.$name.'">'.$name.'</a></li>';
	        }
		} catch (Exception $e) {
			throw new Exception("Error loading genres.");
			
		}
	}

	public function load_by_artist($name){
		try {
			foreach ($this->records->tracks as $id => $trackObj) {
				$needle = $name;
				$needle2 = str_replace(' ','%20', $needle);
				echo $needle2;
				$results = array_filter($db->records->tracks, function($obj) use($needle,$needle2){
					if(preg_match($needle, @$obj->tags->artist) || preg_match($needle2, @$obj->url) ){
						return true;
					}else{
						return false;
					}
				});
				foreach ($results as $id => $trackObj) {
					$title = null;
					$artist = null;
					
					if(@$trackObj->tags){
						$title = (!$trackObj->tags->title)? basename($trackObj->url) : $trackObj->tags->title;
						$artist = $trackObj->tags->artist;
					}
					$tracks [] = ['id'=>$id,
								'url'=>$trackObj->url,
								'title'=>$title,
								'artist'=>$artist];
				}
			}
			$this->out = $tracks;
			
		} catch (Exception $e) {
			throw new Exception("Error loading songs by artist.");
			
		}
	}

	public function load_by_genre($genre){
		try {
			foreach ($this->records->tracks as $id => $trackObj) {
				$needle = $genre;
				$needle2 = str_replace(' ','%20', $needle);
				echo $needle2;
				$results = array_filter($db->records->tracks, function($obj) use($needle,$needle2){
					if(preg_match($needle, @$obj->tags->artist) || preg_match($needle2, @$obj->url) ){
						return true;
					}else{
						return false;
					}
				});
				foreach ($results as $id => $trackObj) {
					$title = null;
					$artist = null;
					
					if(@$trackObj->tags){
						$title = (!$trackObj->tags->title)? basename($trackObj->url) : $trackObj->tags->title;
						$artist = $trackObj->tags->artist;
					}
					$tracks [] = ['id'=>$id,
								'url'=>$trackObj->url,
								'title'=>$title,
								'artist'=>$artist];
				}
			}
			$this->out = $tracks;
			
		} catch (Exception $e) {
			throw new Exception("Error loading songs by artist.");
			
		}
	}

	public function write_track($url){

		if(isset($url) && ctype_print($url)) {
			try {
				$urlMd5 = md5($url);
				$this->checkDupes($urlMd5,'tracks');
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

	public function checkDupes($md5,$root){

		foreach ($this->records->{$root} as $id => $urlHash) {
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
	//PLAYLISTS
	public function write_playlist($name,array $ids){

		if(isset($name) && ctype_print($name) ) {
			
			try {
				$nameMd5 = md5($name);
				$this->checkDupes($nameMd5,'playlists');
				@$this->records->playlists[]->name = $name;
				@$thisRecord = count($this->records->playlists)-1;
				@$this->records->playlists[$thisRecord]->md5 = $nameMd5;
				@$this->records->playlists[$thisRecord]->trackids = $ids;
			} catch (Exception $e) {
				echo json_encode( ['e'=>$e->getMessage()] );
			}

			$this->out = $this->records;

		}else{
			throw new Exception("Empty or non printable characters provided in playlist name.");
			
		}
		return $this;
	}

	public function load_playlist($id){
		try {
			$out = [];
			$playlist = $this->records->playlists[$id];
			$this->audio()->read();
			$tracks = $this->records->tracks;
			$this->playlists()->read();
			
			foreach ($playlist->trackids as $index => $trackid) {
				$out[] = ['id'=>$trackid,
							'url'=>$tracks[$trackid]->url,
							'title'=>(!empty($tracks[$trackid]->tags->title))? $tracks[$trackid]->tags->title : urldecode(basename($tracks[$trackid]->url)),
							'artist'=>$tracks[$trackid]->tags->artist,
							'albumart'=>(empty($tracks[$trackid]->tags->albumart) || !isset($tracks[$trackid]->tags->albumart) )? "app theme/images/vinyl2.png" : $tracks[$trackid]->tags->albumart
							];
			}

			$this->out = $out;
			
		} catch (Exception $e) {
			throw new Exception("Error loading playlist with id: $id.");
		}
		return $this;
	}

	public function load_playlists(){
		try {

			foreach ($this->records->playlists as $id => $playlistObj) {
				$playlists[] = ['id'=>$id,
								'name'=>$playlistObj->name];
			}
			$this->out = $playlists;

		} catch (Exception $e) {
			throw new Exception("Error loading playlists.");
		}

		return $this;
	}

	public function delete_playlist($id){
		try {
			unset($this->records->playlist[$id]);
			$this->out = $this->records;

		} catch (Exception $e) {
			throw new Exception("Error deleting playlist with id: $id");
		}
		return $this;
	}

	public function rename_playlist($id,$name){
		try {
			
			if (@$this->records->playlists[$id]) {
				$nameMd5 = md5($name);
				@$this->records->playlists[$id]->name = $name;
				@$this->records->playlists[$id]->md5 = $nameMd5;
			}
		} catch (Exception $e) {
			throw new Exception("Error renaming playlist");
			
		}
	}

	public function write(){

		try {
			if(!empty($this->out)){
				$this->parjson->write(json_encode($this->out));
			}
			$this->parjson->close();
		} catch (Exception $e) {
			throw new Exception("Database error.", 1);
			
		}

	}

	public function spit_out(){
		echo json_encode($this->out);
	}


}

?>
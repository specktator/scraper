
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
class db{

	public $parjson;
	public $audio;

	function __construct(){
		$this->settings()->read()->read_settings();
		$this->records = null;
		$this->out = null;
	}

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

	public function settings(){
		$this->parjson = new parjson('settings.json');
		return $this;
	}

	public function read(){
		$this->records = $this->parjson->read();
		return $this;
	}

	public function tracks_schema($trackObj){
		/*
			To be used inside foreach or anyother loop
			@return array with one track
		*/
			
		$title = null;
		$artist = null;
		if(is_object($trackObj)){
			$title = (!isset($trackObj->tags->title) || empty($trackObj->tags->title))?  urldecode(basename($trackObj->url)) : $trackObj->tags->title;
			$artist = ( isset($trackObj->tags->artist) )? $trackObj->tags->artist : null;
			$albumart = (empty($trackObj->tags->albumart) || !isset($trackObj->tags->albumart) )? "app_theme/images/vinyl2.png" : $trackObj->tags->albumart;
			return ['id'=>$trackObj->md5,
						'url'=>($trackObj->streaming )? "rq.php?action=streaming&track-id={$trackObj->md5}" :$trackObj->url,
						'title'=>$title,
						'artist'=>$artist,
						'albumart'=>$albumart];
		}else{
			throw new Exception("Error loading tracks schema. variable is not an object", 1);
		}
	}

	public function load_songs(){
		if (!empty($this->records->tracks)) {
			foreach ($this->records->tracks as $id => $obj) {
				// $this->urls [] = array(
				// 	'id'=>$obj->md5,
				// 	'url'=>urldecode($obj->url),
				// 	'title'=>basename(urldecode($obj->url))
				// 	);
				$this->urls[] = $this->tracks_schema($obj);
			}
			$this->out = $this->urls;
		}else{
			throw new Exception('Library is empty.');
			$this->out = ['e'=>'Library is empty.'];
		}
		return $this;
	}

	/*
	*	load_tags
	*	@return: null if id doesn't exist or object with the tags
	*/

	public function load_tags($id){
		$this->out = $this->tags = (isset($this->records->tracks->{$id}->tags))?$this->records->tracks->{$id}->tags : null;
		return $this;
	}

	public function load_artists(){
		try {

			foreach ($this->records->tracks as $index => $trackObj) {
				$tagsArray [] = @$trackObj->tags;
			}

			$mapped = array_map(function($value){
                             return is_object($value) ? @$value->artist : @$value['artist'];
                            }, $tagsArray);
			$filtered = array_filter($mapped,function($value){
                             return (empty($value))? FALSE : TRUE;
                            });
			$artists = array_unique($filtered);
            foreach ( $artists as $songid => $name) {

        		$artistsArr[] = ["name"=>$name];
            }
            $this->out = $artistsArr;

		} catch (Exception $e) {
			throw new Exception("Error loading tags");
			
		}
		return $this;
	}

	public function load_genres(){
		try {

			foreach ($this->records->tracks as $index => $trackObj) {
				$tagsArray [] = @$trackObj->tags;
			}

			$mapped = array_map(function($value){
                             return is_object($value) ? @$value->genre : @$value['genre'];
                            }, $tagsArray);
			$filtered = array_filter($mapped,function($value){
                             return (empty($value))? FALSE : TRUE;
                            });
			$genres = array_unique($filtered);
            foreach ( $genres as $songid => $genre) {

        		$genresArr[] = ["name"=>$genre];
            }
            $this->out = $genresArr;

		} catch (Exception $e) {
			throw new Exception("Error loading tags");
			
		}
		return $this;
	}

	public function load_by_artist($name){
		try {
				$name = strtolower($name);
			    foreach ($this->records->artists->{$name} as $id => $trackidG) {
			        foreach ($this->records->tracks as $trackid => $trackObj) {
			            if($trackidG === $trackid){
			                $tracks[] = $this->tracks_schema($trackObj) ;
			            }
			        }
			    }

			    if(isset($tracks)){
			        $needle2 = str_replace(' ','%20', $name);

			        foreach((array)$this->records->tracks as $id => $obj){ //searching for needle in urls in case some tracks does not provide tags or haven't been scanned yet

			            if(!isset($obj->tags->artist) && preg_match('/'.$needle2.'/i', @$obj->url) ){
			                @$this->records->tracks->{$id}->tags->artist = $name; //updating db to reduce latency
			                @$this->records->artists->{$name}[]=$id; //updating genres references
			                @$this->records->artists = array_unique($this->records->artists->{$name}); //make sure there are no duplicate track ids
			                $tracks[] = $this->tracks_schema($obj);
			            }

			        }
			    }

			$this->out = $tracks;

			
		} catch (Exception $e) {
			throw new Exception("Error loading songs by artist.");
			
		}
		return $this;
	}

	public function load_by_genre($name){
		try {
			$name = strtolower($name);
			foreach ($this->records->genres->{$name} as $id => $trackidG) {
		        foreach ($this->records->tracks as $trackid => $trackObj) {
		            if($trackidG === $trackid){
		                $tracks[] = $this->tracks_schema($trackObj) ;
		            }
		        }
		    }

			$this->out = $tracks;

			
		} catch (Exception $e) {
			throw new Exception("Error loading songs by artist.");
			
		}
		return $this;
	}

	public function load_by_album($name){

	    try {

	    	$name = strtolower($name);
		    foreach ($this->records->albums->{$name} as $album => $trackidA) {
		        foreach ($this->records->tracks as $id => $trackObj) {
		            if ($trackidA === $id) {
		                $tracks[] = $this->tracks_schema($trackObj);
		            }
		        }
		    }

		    $this->out = $tracks;
	    } catch (Exception $e) {
	    		throw new Exception($e->getMessage(), 1);
	    }
	    return $this;
	}

	public function update_references($tagsArray,$trackid){
	    if(isset($tagsArray['artist'])){
	    	$this->records->artists->{strtolower($tagsArray['artist'])}[]=$trackid;
	    	$this->records->artists->{strtolower($tagsArray['artist'])} = array_unique($this->records->artists->{strtolower($tagsArray['artist'])});
	    }
	    if($tagsArray['genre']){
	    	$this->records->genres->{strtolower($tagsArray['genre'])}[]=$trackid;
	    	$this->records->genres->{strtolower($tagsArray['genre'])} = array_unique($this->records->genres->{strtolower($tagsArray['genre'])});
	    }
	    if($tagsArray['album']){
	    	$this->records->albums->{strtolower($tagsArray['album'])}[]=$trackid;
	    	$this->records->albums->{strtolower($tagsArray['album'])} = array_unique($this->records->albums->{strtolower($tagsArray['album'])});

	    }

	}


	public function scan_references(){}

	public function write_track($url){
		//!preg_match("/[^A-Za-z0-9_.\/\-]/", $url)
		if(isset($url) && !empty($url) ) {
			try {
				$urlMd5 = md5($url);
				$this->checkDupes($urlMd5,'tracks');
				if( preg_match('/^http:\/\//', $url) !=1){
					// $url = "rq.php?action=streaming&track-id=$urlMd5";
					@$this->records->tracks->{$urlMd5}->streaming = true;
				}
				@$this->records->tracks->{$urlMd5}->url = $url;
				@$this->records->tracks->{$urlMd5}->md5 = $urlMd5;
				$host = @parse_url($url)['host'];
				@$this->records->tracks->{$urlMd5}->host = ($host)? $host : 'local file';
			} catch (Exception $e) {
				echo json_encode( ['e'=>$e->getMessage()] );
			}
				$this->out = $this->records;

		}else{
			throw new Exception("Empty or non printable characters provided in url.");
			
		}
		return $this;
	}

	public function checkDupes($md5,$root, array $options = []){
		
		$options = array_merge(['prop'=>'urlHash'],$options);
		
		if(preg_match('/[a-z0-9]+/',$md5)){	
			
			foreach ($this->records->{$root} as $id => ${$options['prop']}) {
				if($md5 === ${$options['prop']}->md5){

					throw new Exception("Duplicate found, $root index:$id");
					
				}
			}

		}else{
			throw new Exception("Error id provided hasn't a valid format.");
			
		}
		return TRUE;
	}

	public function find_index($md5,$root, array $options = []){
		
		$options = array_merge(['prop'=>'urlHash'],$options);
		if(preg_match('/^[a-z0-9]+$/',$md5)){
			foreach ($this->records->{$root} as $id => ${$options['prop']}) {
				if($md5 === ${$options['prop']}->md5){

					return $id;
					
				}
			}
		}else{
			throw new Exception("Error id provided hasn't a valid format.");
			
		}
	}

	public function write_tags($tagsArray){
		$id = $tagsArray['trackid'];
		unset($tagsArray['trackid']);
		$checkedTags = array_map('ctype_print', $tagsArray);
		foreach (array_keys($tagsArray) as $key) {
			
			try {
				
				if($checkedTags[$key] === TRUE) {

					@$this->records->tracks->{$id}->tags->{$key} = $tagsArray[$key];

				}else{
					throw new Exception("Error writing tag: $key for track with $id and value '{$tagsArray[$key]}'");
				}
				$this->out = $this->records;
			} catch (Exception $e) {
				error_log( $e->getMessage() );
			}

		}
		try {
			$this->update_references($tagsArray,$id);
		} catch (Exception $e) {
			throw new Exception("Error writing tag: $key for track with $id and value '{$tagsArray[$key]}'");
		}			

		return $this;
	}

	public function write_playbacktimes($id){

		try {

			if(preg_match('/[^a-zA-Z0-9]/', $id) !=1 ){
				$track = @$this->records->tracks->{$id};
				if(@$track->stats->playbacks){
					$track->stats->playbacks = (int)$track->stats->playbacks + 1;
				}else{
					$track->stats->playbacks = 1;
				}
				$this->out = $this->records;
			}else{
				throw new Exception("Error updating playbacks for track with {$id} invalid id.");
				
			}

		} catch (Exception $e) {
			throw new Exception("Error updating playbacks for track with {$id}.");
			
		}
		return $this;
	}

	public function load_track_charts(){
		try {
			
		  	foreach ($this->records->tracks as $id => $track) {
		  		$stats[$id] = @(int)$track->stats->playbacks;

		  	}
			arsort($stats);
			$charted = array_slice($stats,0,(int)$this->settings->tracksChartLength);
			foreach ($charted as $id => $playbacks) {
				$track_schema = $this->tracks_schema($this->records->tracks->{$id});
				$track_schema['playbacks'] = $playbacks; // array_merge could be used but it's slower than insertion

				$chart[] = $track_schema;
			}
			$this->out = $chart;
		} catch (Exception $e) {
			throw new Exception($e->getMessage()." Error loading track charts.");
		}
		return $this;
	}

	//PLAYLISTS
	public function write_playlist($name,array $ids){

		if(isset($name) && !empty($name) && ctype_print($name) ) {
			
			try {
				$salt = (string)microtime(true);
				$id = md5($name.$salt);
				$nameMd5 = md5($name);
				$this->checkDupes($nameMd5,'playlists');
				@$this->records->playlists->{$id}->name = $name;
				@$this->records->playlists->{$id}->md5 = $nameMd5;
				@$this->records->playlists->{$id}->trackids = $ids;
			} catch (Exception $e) {
				throw new Exception( $e->getMessage() );
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

			$playlist = $this->records->playlists->{$id};

			if(!isset($playlist) ){
				throw new Exception("Error playlist md5: $id is not set.");
				
			}

			$this->audio()->read();
			$tracks = $this->records->tracks;
			
			foreach ($playlist->trackids as $index => $trackid) {
				$trackIndex = $this->find_index($trackid,'tracks');
				// $out[] = ['id'=>$trackid,
				// 			'url'=>$tracks->$trackIndex->url,
				// 			'title'=>(!empty($tracks->{$trackIndex}->tags->title))? $tracks->{$trackIndex}->tags->title : urldecode(basename($tracks->{$trackIndex}->url)),
				// 			'artist'=> @$tracks->{$trackIndex}->tags->artist,
				// 			'albumart'=>(empty($tracks->{$trackIndex}->tags->albumart) || !isset($tracks->{$trackIndex}->tags->albumart) )? "app_theme/images/vinyl2.png" : $tracks->{$trackIndex}->tags->albumart
				// 			];
				$out[] = $this->tracks_schema($tracks->{$trackid});
			}

			$this->playlists()->read();
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
			unset($this->records->playlists->{$id});
			// $this->records->playlists = array_values($this->records->playlists);
			$this->out = $this->records;

		} catch (Exception $e) {
			throw new Exception("Error deleting playlist with md5: $id");
		}
		return $this;
	}

	public function rename_playlist($id,$name){
		try {
			
			if (@$this->records->playlists->{$id}) {
				$nameMd5 = md5($name);
				@$this->records->playlists->{$id}->name = $name;
				@$this->records->playlists->{$id}->md5 = $nameMd5;
			}
		} catch (Exception $e) {
			throw new Exception("Error renaming playlist");
			
		}
	}

	public function update_playlist_stats(){}

	public function read_settings(){

		if($this->records->settings){
			$this->settings = $this->records->settings;
			$this->out = $this->settings;
		}else{
			throw new Exception("Error loading settings");
			
		}
		return $this;
	}

	public function write_settings(){
		try {

			foreach ($this->settings as $property => $value) {
				if( preg_match('/[^a-zA-Z0-9]+/',$value) ===1 && !is_bool($value)) {
					$this->settings->{$property} = 'error';
					throw new Exception("Error writing settings, illegal value", 500);
				}else{
					$this->records->settings->{$property} = $value;
				}
			}
			$this->out = $this->records;
		} catch (Exception $e) {
			echo json_encode(['type'=>'danger', 'msg'=>$e->getMessage()]);
			$this->out = null;
		}
		return $this;
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

	public function spit_out($option=null){
		echo json_encode($this->out,$option);
	}


}

?>
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

	public function load_songs(){
		if (!empty($this->records->tracks)) {
			foreach ($this->records->tracks as $id => $obj) {
				$this->urls [] = array(
					'id'=>$obj->md5,
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
	*	load_tags
	*	@return: null if id doesn't exist or object with the tags
	*/

	public function load_tags($id){
		@$this->out = $this->tags = $this->records->tracks[$this->find_index($id,'tracks')]->tags;
		return $this;
	}

	public function load_stats($id){
		$this->out = $this->stats = $this->records->tracks[$this->find_index($id,'tracks')]->stats;
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
                             return is_object($value) ? @$value->genre : @$value['artist'];
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

			$needle = $name;
			$needle2 = str_replace(' ','%20', $needle);

			foreach ($this->records->tracks as $id => $trackObj) {
				$results = array_filter($this->records->tracks, function($obj) use($needle,$needle2){
					if(preg_match('/'.$needle.'/i', @$obj->tags->artist) || preg_match('/'.$needle2.'/i', @$obj->url) ){
						return true;
					}else{
						return false;
					}
				});
			}

			foreach ($results as $id => $trackObj) {
				$title = null;
				$artist = null;
				
				// if(@$trackObj->tags){
					$title = (!isset($trackObj->tags->title) || empty($trackObj->tags->title))?  urldecode(basename($trackObj->url)) : $trackObj->tags->title;
					$artist = $trackObj->tags->artist;
					$albumart = (empty($trackObj->tags->albumart) || !isset($trackObj->tags->albumart) )? "app_theme/images/vinyl2.png" : $trackObj->tags->albumart;
				// }
				$tracks [] = ['id'=>$trackObj->md5,
							'url'=>$trackObj->url,
							'title'=>$title,
							'artist'=>$artist,
							'albumart'=>$albumart];
			}

			$this->out = $tracks;

			
		} catch (Exception $e) {
			throw new Exception("Error loading songs by artist.");
			
		}
		return $this;
	}

	public function load_by_genre($genre){
		try {

			$needle = $genre;

			foreach ($this->records->tracks as $id => $trackObj) {
				$results = array_filter($this->records->tracks, function($obj) use($needle){
					if(preg_match('/'.$needle.'/i', @$obj->tags->genre) ){
						return true;
					}else{
						return false;
					}
				});
			}

			foreach ($results as $id => $trackObj) {
				$title = null;
				$artist = null;
				
				// if(@$trackObj->tags){
					$title = (!isset($trackObj->tags->title) || empty($trackObj->tags->title))?  urldecode(basename($trackObj->url)) : $trackObj->tags->title;
					$artist = ( isset($trackObj->tags->artist) )? $trackObj->tags->artist : null;
					$albumart = (empty($trackObj->tags->albumart) || !isset($trackObj->tags->albumart) )? "app_theme/images/vinyl2.png" : $trackObj->tags->albumart;
				// }
				$tracks [] = ['id'=>$trackObj->md5,
							'url'=>$trackObj->url,
							'title'=>$title,
							'artist'=>$artist,
							'albumart'=>$albumart];
			}

			$this->out = $tracks;

			
		} catch (Exception $e) {
			throw new Exception("Error loading songs by artist.");
			
		}
		return $this;
	}

	public function write_track($url){

		if(isset($url) && !empty($url) && ctype_print($url)) {
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

					@$this->records->tracks[$this->find_index($id,'tracks')]->tags->{$key} = $tagsArray[$key];

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

		if(isset($name) && !empty($name) && ctype_print($name) ) {
			
			try {
				$nameMd5 = md5($name);
				$this->checkDupes($nameMd5,'playlists');
				@$this->records->playlists[]->name = $name;
				@$thisRecord = count($this->records->playlists)-1;
				@$this->records->playlists[$thisRecord]->md5 = $nameMd5;
				@$this->records->playlists[$thisRecord]->trackids = $ids;
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

			$playlist = $this->records->playlists[$this->find_index($id,'playlists')];

			if(!isset($playlist) ){
				throw new Exception("Error playlist md5: $id is not set.");
				
			}

			$this->audio()->read();
			$tracks = $this->records->tracks;
			
			foreach ($playlist->trackids as $index => $trackid) {
				$trackIndex = $this->find_index($trackid,'tracks');
				$out[] = ['id'=>$trackid,
							'url'=>$tracks[$trackIndex]->url,
							'title'=>(!empty($tracks[$trackIndex]->tags->title))? $tracks[$trackIndex]->tags->title : urldecode(basename($tracks[$trackIndex]->url)),
							'artist'=> @$tracks[$trackIndex]->tags->artist,
							'albumart'=>(empty($tracks[$trackIndex]->tags->albumart) || !isset($tracks[$trackIndex]->tags->albumart) )? "app_theme/images/vinyl2.png" : $tracks[$trackIndex]->tags->albumart
							];
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
				$playlists[] = ['id'=>$playlistObj->md5,
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
			unset($this->records->playlists[$this->find_index($id,'playlists')]);
			$this->records->playlists = array_values($this->records->playlists);
			$this->out = $this->records;

		} catch (Exception $e) {
			throw new Exception("Error deleting playlist with md5: $id");
		}
		return $this;
	}

	public function rename_playlist($id,$name){
		try {
			
			$index = $this->find_index($id,'playlists');
			if (@$this->records->playlists[$index]) {
				$nameMd5 = md5($name);
				@$this->records->playlists[$index]->name = $name;
				@$this->records->playlists[$index]->md5 = $nameMd5;
			}
		} catch (Exception $e) {
			throw new Exception("Error renaming playlist");
			
		}
	}

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
				}
			}
			$this->records->settings = $this->settings;
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
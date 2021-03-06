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

/**
 * TRACKS CONTROLLER
 */
 class tracks
 {
 	
 	function __construct()
 	{
 		
		$this->type = $_REQUEST['type'];
		@$this->data = $_REQUEST['data'];

		try {

			$this->validate();
			$this->sanitize();
			$this->db = new db();
			$this->db->audio()->read();
			$this->{$this->type}();

		} catch (Exception $e) {
			echo json_encode( ['notification'=>true, 'type'=>'danger', 'msg'=>$e->getMessage()] );
		}
			// if($e->getCode() === 1) die();
 	}

 	public function loadartists(){
 		$this->db->load_artists()->spit_out();
 	}

 	public function loadbyartist(){
 		$this->db->load_by_artist($this->data['id'])->spit_out();
 	}

 	public function loadgenres(){
 		$this->db->load_genres()->spit_out();
 	}

 	public function loadbygenre(){
 		$this->db->load_by_genre($this->data['id'])->spit_out();
 	}

 	public function playbacktimes(){
 		$this->db->write_playbacktimes($this->data['track-id'])->write();
 	}

 	function remove(){}
 	function delete(){}

 	public function charts(){
 		$this->db->load_track_charts()->spit_out();
 	}

	private function validate(){

		if(preg_match('/[a-z]/', $this->type) != 1){
			error_log("-".$this->type);
			$this->error('type is not valid',1);
		}

		if (!empty($this->data['trackids'])) {
				
			// Check if data are valid, only numbers.
			$results = array_map(function($id){
				
				if (preg_match('/[a-z0-9]/',$id) != 1) {
					return FALSE;
				}

			}, $this->data['trackids']);

			// if FALSE is found in the results then data are invalid for database
			if($key = array_search(FALSE, $results) === FALSE){
				$this->error("Data not valid key: $key value: {$data[$key]}",1);
			}
		}

		if (@$this->data['id']) {
			if(preg_match('/[a-z0-9]/',$this->data['id']) != 1){
				$this->error("Invalid id: {$this->data['id']}",1);
			}
		}

	}

	private function sanitize(){
		$this->type = strtolower($this->type);
		if(isset($this->data)){
			if( @$this->data['name'] )  @$this->data['name'] = preg_replace('/[^a-zA-Z0-9_\s]/','_',$this->data['name']); // replacing all characters except alphanumeric and underscores to underscores
			if( @$this->data['trackids'] ) @$this->data['trackids']	= array_filter($this->data['trackids'],function($id){return preg_match('/[0-9]+/',$id);}); //filter trackids array. values must be only numbers
			if( @$this->data['track-id'] ) @$this->data['trackid'] = preg_match('/[0-9]+/',$id); //filter trackids array. values must be only numbers
		}
	}

	private function error($errormsg,$code){
		echo json_encode( ['e'=>$errormsg] );
		throw new Exception($errormsg,$code);
	}
 } 

 ?>
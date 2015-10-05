<?php	

/**
* PLAYLISTS CONTROLLER
*/
class playlist
{
	
	function __construct()
	{
		$this->type = $_REQUEST['type'];
		@$this->data = $_REQUEST['data'];

		try {

			$this->validate();
			$this->sanitize();
			$this->db = new db();
			$this->db->playlists()->read();
			$this->{$this->type}();

		} catch (Exception $e) {
			echo json_encode( ['type'=>'danger', 'msg'=>$e->getMessage()] );
		}
			// if($e->getCode() === 1) die();



	}


	function save(){

		$this->db->write_playlist($this->data['name'], $this->data['trackids'])->write();
		echo json_encode(['type'=>'success', 'element'=>'label', 'msg'=>'Saved.']);

	}
	
	function load(){
		$this->db->load_playlist($this->data['id'])->spit_out();
	}

	function loadall(){
		$this->db->load_playlists()->spit_out();
	}

	function removeTrack(){
	}

	function rename(){
		$this->db->rename_playlist($this->data['id'],$this->data['name']);
		echo json_encode(['type'=>'success', 'msg'=>'Renamed.']);
	}

	function stats(){}

	function delete(){
		$this->db->delete_playlist($this->data['id'])->write();
		echo json_encode(['type'=>'success', 'msg'=>'Deleted.']);
	}

	function validate(){

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

	function sanitize(){
		$this->type = strtolower($this->type);
		if(isset($this->data)){
			@$this->data['name'] = preg_replace('/[^a-zA-Z0-9_\s]/','_',$this->data['name']); // replacing all characters except alphanumeric and underscores to underscores
			@$this->data['trackids'] = array_filter($this->data['trackids'],function($id){return preg_match('/[0-9]+/',$id);}); //filter trackids array. values must be only numbers
		}
	}

	function error($errormsg,$code){
		echo json_encode( ['e'=>$errormsg] );
		throw new Exception($errormsg,$code);
	}

}
<?php
defined('ALPHA') or die('Get Out');
/**
* controller
*/
class controller
{
	private $error = FALSE;

	function __construct()
	{
		#validate/sanitize all inputs
		$this->request = $_REQUEST;
		$this->validate();
		$this->sanitize();
		$this->checkErrors();
		$this->action();
	}

	function validate(){
		$this->error[] = (!empty($this->request['action']))? array(FALSE) : array(TRUE,'action',$this->request['action']);
	}

	function sanitize(){
			$this->request['action'] = strtolower($this->request['action']);
	}

	function checkErrors(){
		$bitWise = FALSE;
		foreach ($this->error as $key => $value) {
			if($value[0]){
				error_log('controller input error: '.$value[1].' => '.$value[2]);
			}
			$bitWise = $bitWise | $value[0];
		}
		!$bitWise or die(); // if error states == false then ok or die
	}

	function action(){

		$this->model = new $this->request['action']();
		// return $this;
	}
}

?>
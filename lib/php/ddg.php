<?php
defined('ALPHA') or die('Get Out');
class ddg {

	function __construct(){
		$this->httprequest = $_REQUEST;

		try {
			$this->{$this->httprequest['type']}($this->httprequest['q']);
		} catch (Exception $e) {
			echo json_encode(array('e'=>$e->getMessage()));
		}
	}
	
	function instantAnswer($keyword){
		error_log('keyword:'.$keyword);
		$keyword = (string)$keyword;
		if(empty($keyword) or $keyword == 'undefined'){ echo json_encode(null); exit;}
		$this->result = json_decode(file_get_contents('http://api.duckduckgo.com/?q='.$keyword.'&format=json&pretty=0&skip_disambig=1'));
		$abstracttext = $this->result->AbstractText;
		$entity = $this->result->Entity;
		$relatedTopics = array(@$this->result->RelatedTopics[0]->Result,
			@$this->result->RelatedTopics[1]->Result);
		$officialsite = (isset($this->result->Results) && !empty($this->result->Results))? $this->result->Results[0]->Result : null;
		echo json_encode(array(
			'abstract'=>$abstracttext,
			'entity'=>$entity,
			'relatedTopics'=>$relatedTopics,
			'officialsite'=>$officialsite
			));

	}

	function getImgFromTopics(){}


}

// $obj = new ddg();

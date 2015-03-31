<?php

class ddg {

	function __construct(){
		$this->httprequest = $_REQUEST;
		$this->{$this->httprequest['type']}($this->httprequest['q']);
	}
	
	function instantAnswer($keyword){
		error_log('keyword:'.$keyword);
		if(empty($keyword) or $keyword == 'undefined'){ echo json_encode(null); exit;}
		$result = json_decode(file_get_contents('http://api.duckduckgo.com/?q='.$keyword.'&format=json&pretty=0&skip_disambig=1'));
		$abstracttext = $result->AbstractText;
		$entity = $result->Entity;
		$relatedTopics = array($result->RelatedTopics[0]->Result,$result->RelatedTopics[1]->Result);
		$officialsite = $result->Results[0]->Result;
		echo json_encode(array(
			'abstract'=>$abstracttext,
			'entity'=>$entity,
			'relatedTopics'=>$relatedTopics,
			'officialsite'=>$officialsite
			));

	}

}

$obj = new ddg();

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


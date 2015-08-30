<?php

include '../config.php';
// $db  = new db();
// $db->audio()->read();


$tagsArray = ['trackid'=>1, 'artist'=>'eric clapton', 'title'=>'lAYLAL', 'album'=>'layla','genre'=>'blues','albumart'=>'$data'];

// list($trackid) = $tagsArray;
// var_dump($trackid);

$trackid = $tagsArray['trackid'];
unset($tagsArray['trackid']);

$records =  new stdClass();

var_dump($checkedTags = array_map('ctype_print', $tagsArray));
// unset($checkedTags['trackid']);

foreach (array_keys($tagsArray) as $key) {
	
	try {
		if($checkedTags[$key] === TRUE) {
			$records->track[$tagsArray['trackid']]->{$key} = $tagsArray[$key];
		}else{
			throw new Exception("Error writing tag: '$key' for track with {$tagsArray['trackid']}");
		}

	} catch (Exception $e) {
		echo $e->getMessage();
	}

}

var_dump($records);
?>
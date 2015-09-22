<?php
include '../config.php';
ini_set('display_errors',1);
error_reporting(-1);
try {
  $db = new db();
  $db->audio()->read()->load_songs();
  $songs = $db->urls;
  // var_dump($db->records->tracks);exit;
} catch (Exception $e) {
      error_log($e->getMessage());
}

// var_dump($songs);
foreach ($songs as $song) {
	$db->load_tags($song['id']);
	$tagsArray[] = $db->tags;
}

$tagsArray = array_filter($tagsArray,function($value){
	return (empty($value))? FALSE : TRUE;
});

var_dump($tagsArray);

// $artists = array_map(function($value){
// 	return is_object($value) ? $value->artist : $value['artist'];
// }, $tagsArray);

// $albums = array_map(function($value){
// 	return is_object($value) ? $value->album : $value['album'];
// }, $tagsArray);

// $genres = array_map(function($value){
// 	return is_object($value) ? $value->genre : $value['genre'];
// }, $tagsArray);

var_dump(array_unique(array_filter(array_map(function($value){
	return is_object($value) ? @$value->artist : @$value['artist'];
}, $tagsArray),function($value){
	return (empty($value))? FALSE : TRUE;
})));

var_dump(array_unique(array_filter(array_map(function($value){
	return is_object($value) ? @$value->album : @$value['album'];
}, $tagsArray),function($value){
	return (empty($value))? FALSE : TRUE;
})));

var_dump(array_unique(array_filter(array_map(function($value){
	return is_object($value) ? @$value->genre : @$value['genre'];
}, $tagsArray),function($value){
	return (empty($value))? FALSE : TRUE;
})));

?>



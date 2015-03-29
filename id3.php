<?php
ini_set('xdebug.profiler_enable',1);

require 'vendor/autoload.php';
use PhpId3\Id3TagsReader;

try{
  // FIRST TRY LOCAL JSON DATABASE AND THEN INIT THE REQUEST
  $id3 = new Id3TagsReader(fopen(preg_replace('/\s/','%20',$_REQUEST['url']),'rb'));
  $id3->readAllTags();
  $artist = $id3->getId3Array()['TPE1']['body'];
  $title = $id3->getId3Array()['TIT2']['body'];
  $album = $id3->getId3Array()['TALB']['body'];
  // $albumart =
  echo json_encode(array('artist'=>$artist,'title'=>$title,'album'=>$album));
}catch (Exception $e){
  echo json_encode(array('e'=>$e->getMessage(),'title'=>basename(urldecode($_REQUEST['url']))));
  // if track doent have valid id3 tags then leave it as it is.
}
?>

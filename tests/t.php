<html>
<meta charset="utf-8">
<?php
ini_set('display_errors',1);
ini_set('memory_limit','-1');
error_reporting(-1);
require '../vendor/autoload.php';
include_once '../config.php';

// Initialize getID3 engine
$getID3 = new getID3;
// Initialize img class
$img = new img();
set_time_limit('0');
$tags = $getID3->analyze($img->saveTempFile("http://localhost/scraper/music/Archive%20%e2%80%93%20Axiom%20(2014)%20%5bTRIP%20HOP,%20INDIE%20ROCK%5d/03%20-%20Baptism.mp3"));
// $tags = $getID3->analyze("/var/www/scraper/music/4.mp3");
getid3_lib::CopyTagsToComments($tags);
set_time_limit('30');
$artist = $tags['id3v2']['TPE1'][0]['data'];
$title = $tags['id3v2']['TIT2'][0]['data'];
$album = $tags['id3v2']['TALB'][0]['data'];
$cover = $tags['id3v2']['APIC'][0]['data'];
// var_dump($tags['id3v1']);
echo 'sketa comments';
var_dump($tags['comments']);
echo 'marsou';
var_dump($tags['id3v2']['comments']);
echo "<img src=\"{$img->saveImg($tags['comments']['picture'][0]['data'])}\">";

echo dirname(IMAGES_SAVE_DIR);
echo basename(IMAGES_SAVE_DIR);



?>
</html>
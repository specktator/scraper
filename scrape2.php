<?php
// ini_set('xdebug.profiler_enable',1);
ini_set('display_errors',1);
error_reporting(-1);


$links = file('files.txt');

// foreach($links as $link){
//   preg_match('/([a-zA-Z0-9-_.\s]+\..*)/',urldecode($link),$matches);
//   var_dump($matches);
//   echo basename(urldecode($link));exit;
// }


require 'vendor/autoload.php';

echo '<pre>';
use PhpId3\Id3TagsReader;
$id3 = new Id3TagsReader(fopen(preg_replace('/\s/','%20','http://chriscargile.com/music/music/Amos Lee/01 Colors.mp3'),'rb'));
$id3->readAllTags();
print_r($id3->getId3Array());
echo $id3->getId3Array()['TPE1']['body'].' - '.$id3->getId3Array()['TIT2']['body'];
// foreach($id3->getId3Array() as $key => $value) {
//   if( $key !== "APIC" ) { //Skip Image data
//     echo $value["FullTagName"] . ": " . $value["Body"] . "<br />";
//   }
// }
echo '</pre>';
?>

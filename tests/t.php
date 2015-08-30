<html>
<meta charset="utf-8">
<?php
ini_set('display_errors',1);
ini_set('memory_limit','-1');
error_reporting(-1);
require '../vendor/autoload.php';
use PhpId3\Id3TagsReader;
include '../img.php';

$id3 = new Id3TagsReader(fopen("../3.mp3",'rb'));
$id3->readAllTags();
$artist = $id3->getId3Array()['TPE1']['body'];
$title = $id3->getId3Array()['TIT2']['body'];
$album = $id3->getId3Array()['TALB']['body'];
$cover = $id3->getId3Array()['APIC']['body'];
var_dump($id3->getId3Array());

list($mimeType, $image) = $id3->getImage();
var_dump($id3->getImage());
file_put_contents("thumb.jpg", $cover );
$img = new img();
// $img->save_img($cover);
$img->img_b64($cover);
?>
</html>
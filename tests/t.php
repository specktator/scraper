<html>
<meta charset="utf-8">
<?php
ini_set('display_errors',1);
ini_set('memory_limit','-1');
error_reporting(-1);
require '../vendor/autoload.php';
include_once '../config.php';

$localFile = "/var/www/music/Ed Sheeran/Ed Sheeran - + (Plus) [2011 Album]/11 Kiss Me.mp3";
$localURL = "http://femto.local/view_audio_db.php";
$remoteURL = "http://koukou.local/view_audio_db.php";

$local = preg_match('/http\:\/\/(.*)'.$_SERVER['SERVER_NAME'].'(.*)/', $localURL);
$remoteURL = preg_match('/http\:\/\/(.*)(.*)/', $remoteURL);
$file = preg_match('/http\:\/\/(.*)(.*)/', $localFile);

var_dump($local, $remoteURL, $file);
?>
</html>

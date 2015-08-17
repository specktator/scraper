<?php
ini_set('display_errors',1);
error_reporting(-1);
include 'db.php';




$db = new db();
// $db->audio()->read();
// for ($i=0; $i < 5 ; $i++) { 
// 	$db->audio()->write_track("http://google1.gr/link$i");
// }
// $db->spit_out();
// $db->write();
$db->audio()->read()->load_songs();


foreach ($db->urls as $id => $song) {
var_dump($song['url']);
}
echo "total urls indexed:".count($db->urls);
?>
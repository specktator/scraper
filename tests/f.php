<html>
<head>
	<meta charset="utf-8">
</head>
<?php
// ini_set('xdebug.profiler_enable',1);
ini_set('display_errors',1);
ini_set('memory_limit','-1');
error_reporting(-1);
include '../config.php';

// $_REQUEST['type'] = 'instantAnswer';
// $_REQUEST['q'] = 'archive';

// $ddg = new ddg();
// var_dump($ddg->result);
// 
// if($ddg->result->Heading){
// 	foreach ($ddg->result->RelatedTopics as $key => $value) {
// 		if(@$value->Topics && @$value->Name === 'Music')
// 			$music = $value;
// 	}
// 	var_dump($music->Topics[0]);
// 	$img = new img();
// 	echo "<img src=\"{$music->Topics[0]->Icon->URL}\">";
// }


$db = new db();
$db->audio()->read();

foreach ($db->records->tracks as $key => $value) {
	if($value->tags){
		var_dump($value);
		
	}
}

?>
</html>
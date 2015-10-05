<html>
<head>
	<meta charset="utf-8">
</head>
<body>
	<pre>
<?php
// ini_set('xdebug.profiler_enable',1);
ini_set('display_errors',1);
ini_set('memory_limit','-1');
error_reporting(-1);
include '../config.php';


$db = new db();
$db->audio()->read();
$db->load_by_genre('indie');
// var_dump($db->records->tracks);
$db->spit_out(JSON_PRETTY_PRINT);

?>
</pre>
</body>
</html>
<html>
<head>
	<meta charset="utf-8">
</head>
<body>
<?php
// ini_set('xdebug.profiler_enable',1);
ini_set('display_errors',1);
ini_set('memory_limit','-1');
error_reporting(-1);
include '../config.php';


var_dump( shell_exec('php -f '.ROOT_PATH.'/lib/php/scrape.php &') );

?>
</body>
</html>
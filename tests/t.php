<html>
<meta charset="utf-8">
<?php
ini_set('display_errors',1);
ini_set('memory_limit','-1');
error_reporting(-1);
require '../vendor/autoload.php';
include_once '../config.php';

$file  = preg_match('/[^a-z0-9]+/',"asdqwe231231231321$");

var_dump($file);
?>
</html>

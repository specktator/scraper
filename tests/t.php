<html>
<meta charset="utf-8">
<?php
ini_set('display_errors',1);
ini_set('memory_limit','-1');
error_reporting(-1);
require '../vendor/autoload.php';
include_once '../config.php';

$db = new db();
$db->settings()->read();

$db->read_settings()->spit_out(JSON_PRETTY_PRINT);
unset($db->settings->buffer);
$db->write_settings()->write();



var_dump($db->records);





?>
</html>
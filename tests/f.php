
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
// include '../config.php';




// /var/www/scraper/images/2d1e48558d386d80846ff592249ecdb3.png
// /var/www/scraper/images/a8c3bc4c3cd6c8f18cfc96fc4804500b.png


$imgblob = file_get_contents("/var/www/scraper/images/a8c3bc4c3cd6c8f18cfc96fc4804500b.png");

$im = new Imagick();
$im->readImageBlob($imgblob);
$im->setImageFormat('jpeg');
$quality = 65;
$im->setImageCompressionQuality($quality);
$im->writeImage('img.jpg');
echo filesize('img.jpg')/1000;




?>
</pre>
</body>
</html>
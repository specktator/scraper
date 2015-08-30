<?php
// ini_set('xdebug.profiler_enable',1);
ini_set('display_errors',1);
error_reporting(-1);


echo '<pre>';
echo urldecode('http://chriscargile.com/music/music/Amos Lee/01 Colors.mp3')."\n\n";
echo urlencode('http://chriscargile.com/music/music/Amos Lee/01 Colors.mp3')."\n\n";
echo htmlentities('http://chriscargile.com/music/music/Amos Lee/01 Colors.mp3')."\n\n";
echo htmlspecialchars('http://chriscargile.com/music/music/Amos Lee/01 Colors.mp3')."\n\n";
echo htmlspecialchars_decode('http://chriscargile.com/music/music/Amos Lee/01 Colors.mp3')."\n\n";
echo (utf8_encode('http://chriscargile.com/music/music/Amos Lee/01 Colors.mp3'))."\n\n";
$arr = parse_url ( 'http://chriscargile.com/music/music/Amos Lee/01 Colors.mp3' );
$parts = explode ( '/', $arr['path'] );
$fname = $parts[count($parts)-1];
unset($parts[count($parts)-1]);
$url = $arr['scheme'] . '://' . $arr['host'] . join('/', $parts) . '/' . urlencode ( $fname );
var_dump( $url );
echo '</pre>';
?>

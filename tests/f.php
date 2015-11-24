
<html>
<head>
	<meta charset="utf-8">
    <link href="../app_theme/css/bootstrap.min.css" rel="stylesheet">
    <link href="../app_theme/css/bootstrap-slider.min.css" rel="stylesheet">
    <link href="../app_theme/css/font-awesome.min.css" rel="stylesheet">
    <link href="../app_theme/css/styles.css" rel="stylesheet">
    <script src="../app_theme/js/jquery-2.1.4.min.js"></script>
    <script src="../app_theme/js/bootstrap.min.js"></script>
    <script src="../app_theme/js/bootstrap-slider.min.js"></script>
    <style type="text/css">
	body > pre:first-child{
		overflow-y: scroll;
		height:100%;
	}
    </style>
</head>
<body>
	<pre>
<?php
// ini_set('xdebug.profiler_enable',1);
ini_set('display_errors',1);
ini_set('memory_limit','-1');
error_reporting(-1);
include '../config.php';

// $db = new db();
// $db->audio()->read();

// $db->load_track_charts()->spit_out(JSON_PRETTY_PRINT);


$lib = '{
"tracks":{
"123":{"url":"http://google.com"},
"321":{"url":"http://totallynoob.com"}
}
}';

// function time($label,$start){
//     $time_elapsed_secs = microtime(true) - $start;
// 	echo $label.": ".$time_elapsed_secs;
// }

$db = new db();
$db->audio()->read();

// $st = microtime(true);
// $db->load_by_artist("Ed Sheeran");
// var_dump($db->out);
// echo "time: ".(microtime(true) - $st);

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$trans = new stdClass();
$genres = new stdClass();
$artists = new stdClass();
$albums = new stdClass();
$i=0;
foreach ($db->records->tracks as $key => $value) {
	$trans->{$value->md5} = $value;
    if(@$value->tags->genre){
        $value->tags->genre = 'electro';
        $genres->{strtolower($value->tags->genre)}[] = $value->md5;
    }
    if(@$value->tags->artist){
        $artists->{strtolower($value->tags->artist)}[] = $value->md5;
    }
    if(@$value->tags->album){
        $albums->{strtolower($value->tags->album)}[] = $value->md5;
    }
}

$db->records->tracks = $trans;
$db->records->genres = $genres;
$db->records->artists = $artists;
$db->records->albums = $albums;

function load_by_genre(){
    $name = "Other";
    $name = strtolower($name);
    foreach ($db->records->genres->{$name} as $id => $trackidG) {
        foreach ($db->records->tracks as $trackid => $trackObj) {
            if($trackidG === $trackid){
                $t[] = $db->tracks_schema($trackObj) ;
            }
        }
    }
    
}

function load_by_artist(){
    foreach ($db->records->artists->{strtolower($name)} as $id => $trackidG) {
        foreach ($db->records->tracks as $trackid => $trackObj) {
            if($trackidG === $trackid){
                $t[] = $db->tracks_schema($trackObj) ;
            }
        }
    }

    if(isset($t)){
        $needle2 = str_replace(' ','%20', strtolower($name));

        foreach((array)$db->records->tracks as $id => $obj){ //searching for needle in urls in case some tracks does not provide tags or haven't been scanned yet

            if(!isset($obj->tags->artist) && preg_match('/'.$needle2.'/i', @$obj->url) ){
                @$db->records->tracks->{$id}->tags->artist = strtolower($name); //updating db to reduce latency
                @$db->records->artists->{strtolower($name)}[]=$id; //updating genres references
                @$db->records->artists = array_unique($db->records->artists->{strtolower($name)}); //make sure there are no duplicate track ids
                $t[] = $db->tracks_schema($obj);
            }

        }
    }

}


function load_by_album($db){
    $name = "axiom";
    $name = strtolower($name);
    foreach ($db->records->albums->{$name} as $album => $trackidA) {
        foreach ($db->records->tracks as $id => $trackObj) {
            if ($trackidA === $id) {
                $t[] = $db->tracks_schema($trackObj);
            }
        }
    }
    var_dump($t);
}

// var_dump($db->records->albums);
load_by_album($db);

function update_references($tagsArray){
    list($trackid,$artist,,$album,$genre) = $tagsArray;
    @$db->records->artists->{strtolower($artist)}[]=$trackid;
    @$db->records->genres->{strtolower($genre)}[]=$trackid;
    @$db->records->albums->{strtolower($album)}[]=$trackid;

}

function unique_references($tagsArray){
    list($trackid,$artist,,$album,$genre) = $tagsArray;
    $db->records->artists->{strtolower($artist)} = array_unique($db->records->artists);
    $db->records->genres->{strtolower($genre)} = array_unique($db->records->genres);
    $db->records->albums->{strtolower($album)} = array_unique($db->records->albums);
}

// $db->out = $db->records;
// $db->write();

// var_dump($db->records);




?>
<pre>
    <h4>RAM usage (MB)</h4>
<?php
function convert($size)
{
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}
var_dump( convert(memory_get_peak_usage()) );
var_dump( convert(memory_get_peak_usage(true)) );
var_dump( convert(memory_get_usage()) );
var_dump( convert(memory_get_usage(true)) );
?>
</pre>
</pre>
</body>
</html>
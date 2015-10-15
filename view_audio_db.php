
<?php
include 'config.php';
ini_set('display_errors',1);
error_reporting(-1);
try {
  $db = new db();
  $db->audio()->read()->load_songs();
  $songs = $db->urls;
  // var_dump($db->records->tracks);exit;
} catch (Exception $e) {
      error_log($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>view audio database | <?php echo APP_NAME; ?> | for the love of music</title>
    <meta name="generator" content="Bootply" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="app_theme/css/bootstrap.min.css" rel="stylesheet">
    <link href="app_theme/css/bootstrap-slider.min.css" rel="stylesheet">
    <link href="app_theme/css/font-awesome.min.css" rel="stylesheet">
    <link href="app_theme/css/styles.css" rel="stylesheet">
  </head>
  <body>
        <div id="wrapper">
          <div id="sidebar-wrapper" class="col-md-1">
                <div id="sidebar">
                    <ul class="nav list-group">
                        <li>
                            <a class="list-group-item" href="#"><i class="icon-music-tone-alt icon-1x text-success"></i> Songs</a>
                        </li>
                        <li>
                            <a class="list-group-item" href="#"><i class="icon-playlist icon-1x text-warning"></i> Playlists</a>
                        </li>
                        <li>
                            <a class="list-group-item" href="#"><i class="icon-users icon-1x text-danger"></i> Artists</a>
                        </li>
                        <li>
                            <a class="list-group-item" href="#"><i class="icon-music-tone icon-1x text-info"></i> Genres</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div id="main-wrapper" class="col-md-11 pull-right">
                <div id="main">
                 <table class="table table-bordered table-striped table-hover table-condensed">
						<thead><tr><th>id</th><th>file name</th><th>md5</th><th>host</th><th>extra fields</th></tr></thead>
					<?php
					foreach ($db->records->tracks as $id => $songobj) {
						foreach ($songobj as $nestedobj => $nestedvalues) {
							if(is_object($nestedvalues) && isset($nestedvalues)){
								$th = ""; $nth = ""; $out = "";$nestedout ="";
								$nestedpanel = "<div class=\"panel panel-default\"><div class=\"panel-heading\">{$nestedobj}</div>";
								$nestedheadopen = "<table class=\"table table-bordered table-striped table-hover table-condensed\"><thead><tr>";
								$out .= "<tr class=\"info\">";
								foreach ($songobj->$nestedobj as $nkey => $value) {
									$nth .= "<th>$nkey</th>";
									$out .= "<td>$value</td>";
								}
								$nth .= "</tr>";
								$out .= "</tr>";
								$theadclosure ="</tr></thead>";
								$tableclosure = "</table>";
								$nestedout .= $nestedpanel.$nestedheadopen.$nth.$theadclosure.$out.$tableclosure;
							}
						}
						echo "<tr><td>{$id}</td><td>".basename(urldecode($songobj->url))."</td><td>{$songobj->md5}</td><td>{$songobj->host}</td><td>{$nestedout}</td></tr>";
						$nestedout = '';



					}
					?>
					</table>
                </div>
            </div>
        </div>
	<!-- script references -->
	<script src="app_theme/js/jquery-2.1.4.min.js"></script>
	<script src="app_theme/js/bootstrap.min.js"></script>
	<script src="app_theme/js/bootstrap-slider.min.js"></script>
	<script src="lib/js/player.js"></script>
	<script src="lib/js/search.js"></script>
	<script src="lib/js/controls.js"></script>
</body>
</html>

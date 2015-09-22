<?php
include 'config.php';
try {
  $db = new db();
  $db->audio()->read()->load_songs();
  $songs = $db->urls;
} catch (Exception $e) {
      error_log($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title> <?php echo APP_NAME; ?> | for the love of music</title>
    <meta name="generator" content="Bootply" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="app theme/css/bootstrap.min.css" rel="stylesheet">
    <link href="app theme/css/bootstrap-slider.min.css" rel="stylesheet">
    <link href="app theme/css/font-awesome.min.css" rel="stylesheet">
    <link href="app theme/css/styles.css" rel="stylesheet">
  </head>
  <body>
        <!-- <div id="header" class="navbar navbar-default navbar-fixed-top">
            <div class="navbar-header">
                <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".navbar-collapse">
                    <i class="icon-reorder"></i>
                </button>
                <a class="navbar-brand" href="#">
                    Scraper
                </a>
            </div>
            <nav class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="#">Navbar Item 1</a>
                    </li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">Navbar Item 2<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Navbar Item2 - Sub Item 1</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">Navbar Item 3</a>
                    </li>
                </ul>
                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown">
                        <a href="#" id="nbAcctDD" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i>Username<i class="icon-sort-down"></i></a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="#">Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div> -->
        <div id="wrapper">
          <div id="sidebar-wrapper" class="col-lg-1 col-md-1">
                <div id="sidebar">
                    <ul class="nav list-group">
                        <li>
                            <a class="list-group-item" href="#" data-value="main" tabindex="1"><i class="icon-music-tone-alt icon-1x text-success"></i> Songs</a>
                        </li>
                        <li>
                            <a class="list-group-item" href="#" data-value="playlists" tabindex="2"><i class="icon-playlist icon-1x text-warning"></i> Playlists</a>
                        </li>
                        <li>
                            <a class="list-group-item" href="#" data-value="artists" tabindex="3"><i class="icon-users icon-1x text-danger"></i> Artists</a>
                        </li>
                        <li>
                            <a class="list-group-item" href="#" data-value="genres" tabindex="4"><i class="icon-music-tone icon-1x text-info"></i> Genres</a>
                        </li>
                        <li>
                            <a class="list-group-item" href="#" data-value="charts" tabindex="5"><i class="fa fa-bar-chart text-warning"></i> Charts</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div id="main-wrapper" class="col-lg-11 col-md-11 pull-right">
                <div id="main" class="page">
                  <div class="page_wrapper">
                    <div class="row page-row">
                      <div class="songs-list col-lg-12 col-md-12 col-sm-12">
                        <ul id="playlist" class="row">
                          <?php
                          foreach($songs as $link){
                            flush();
                            $db->load_tags($link['id']);
                            $tagsArray[] = $db->tags;
                            $albumartLink = (!isset($db->tags->albumart) || empty($db->tags->albumart))? "app theme/images/vinyl2.png" :$db->tags->albumart ;
                            $linkTitle = (!empty($db->tags->artist) && !empty($db->tags->title))? $db->tags->artist." - ".$db->tags->title : basename(urldecode($link['title']));
                            echo '<li class="col-lg-2 col-md-2 col-sm-6 col-xs-6">
                            <div class="albumart img-thumbnail">
                              <div class="overlay">
                                <a href="#">
                                  <i class="fa fa-minus"></i><i class="fa fa-plus"></i>
                                </a>
                                <a href="#" class="control-play control-center">
                                  <i class="icon-control-play"></i><i class="icon-control-pause" style="display:none;"></i>
                                </a>
                                <div class="overlay-share">
                                <a href="#" data-toggle="tooltip" data-placement="top" title="Share on twitter"><i class="fa fa-twitter"></i></a>
                                <a href="#" data-toggle="tooltip" data-placement="top" title="Share on facebook"><i class="fa fa-facebook"></i></a>
                                </div>
                              </div>
                             <img class="img-responsive" src="'.$albumartLink.'" />
                           </div>
                          <a class="track" track-id="'.$link['id'].'" href="'.urldecode(preg_replace('/\n/','',$link['url'])).'">
                            <div class="title">'.$linkTitle.'</div>
                          </a>
                          </li>';
                          }
                          ?>
                        </ul>
                      </div>
                      <div id="queue_wrapper" class="col-lg-4 col-md-4 hide-element">
                          <div id="queue" class="panel panel-default playlists-tracks">
                              <div class="panel-heading">Queue</div>
                              <div class="panel-body">
                                  <p>Drag 'n' Drop songs below </p>
                              </div> <!-- panel body -->
                              <ul id="queue_sortable" class="list-group">
                              </ul>
                              <div class="panel-footer">
                                  <div class="row">
                                      <div id="queue-playlist-controls" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 queue-playlist-controls">
                                          <a href="#" id="queue_close" class="" data-toggle="tooltip" data-placement="top" title="Turn off the queue. Turning this off means you will use the main playlist."><i class="fa fa-power-off"></i></a>
                                          <a href="#" id="queue_close" class="" data-toggle="tooltip" data-placement="top" title="Save queue to playlist"><i class="fa fa-save"></i></a>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="playlists" class="page hide-page">
                  <div class="page_wrapper">
                    <div class="row page-row">
                     <div class="playlists-list col-lg-5 col-md-5 col-sm-5 col-xs-5">
                        <ul id="playlists" class="list-group ">
                          <?php
                            for ($i=0; $i < 100 ; $i++) { 
                            echo '<li class="list-group-item playlist-item">
                              <a href="#" class="playlist-link" playlist-id="'.$i.'">Playlist name '.$i.'</a>
                              <a href="#" class="playlist-remove inline-controls" data-toggle="tooltip" data-placement="top" title="Delete playlist"><i class="fa fa-remove"></i></a>
                              <a href="#" class="playlist-share inline-controls" data-toggle="tooltip" data-placement="top" title="Share playlist on facebook"><i class="fa fa-facebook"></i></a>
                              <a href="#" class="playlist-share inline-controls" data-toggle="tooltip" data-placement="top" title="Share playlist on twitter"><i class="fa fa-twitter"></i></a>
                            </li>';
                            }
                          ?>
                        </ul>
                      </div>
                      <div id="playlists-tracks" class="playlists-tracks col-lg-5 col-md-5 col-sm-5 col-xs-5 hide-page">
                        <ul id="playlists-tracks-list" class="list-group">
                          <?php
                            for ($i=0; $i < 60 ; $i++) { 
                            echo '<li class="list-group-item playlist-item">
                              <img src="images/23bc5883b3858b304508539bef0ee594.png" class="img-thumbnail img-responsive inline-img" />
                              <a href="#" class="playlist-link track" track-id="'.$i.'">Track name '.$i.'</a>
                              <a href="#" class="playlist-remove inline-controls" data-toggle="tooltip" data-placement="top" title="Delete track"><i class="fa fa-remove"></i></a>
                              <a href="#" class="playlist-share inline-controls" data-toggle="tooltip" data-placement="top" title="Share track on facebook"><i class="fa fa-facebook"></i></a>
                              <a href="#" class="playlist-share inline-controls" data-toggle="tooltip" data-placement="top" title="Share track on twitter"><i class="fa fa-twitter"></i></a>
                              <a href="#" class="playlist-reorder inline-controls" data-toggle="tooltip" data-placement="top" title="Drag track to change order"><i class="fa fa-reorder"></i></a>
                            </li>';
                            }
                          ?>
                        </ul>
                      </div>
                    </div>
              </div>
                </div>
                <div id="artists" class="page hide-page">
                  <div class="page_wrapper">
                    <div class="row page-row">
                     <div class="artists-list col-lg-5 col-md-5 col-sm-5 col-xs-5">
                        <ul id="artists" class="list-group ">
                        <?php
                        $artists = array_unique(array_filter(array_map(function($value){
                             return is_object($value) ? @$value->artist : @$value['artist'];
                            }, $tagsArray),function($value){
                             return (empty($value))? FALSE : TRUE;
                            }));
                        foreach ( $artists as $songid => $name) {
                          flush();
                          echo '<li class="list-group-item"><a href="#" data-value="'.$name.'">'.$name.'</a></li>';
                        }?>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="genres" class="page hide-page">
                  <div class="page_wrapper">
                    <div class="row page-row">
                     <div class="genres-list col-lg-5 col-md-5 col-sm-5 col-xs-5">
                        <ul id="genres" class="list-group ">
                        <?php
                        $genres = array_unique(array_filter(array_map(function($value){
                             return is_object($value) ? @$value->genre : @$value['genre'];
                            }, $tagsArray),function($value){
                              return (empty($value))? FALSE : TRUE;
                            }));
                        foreach ( $genres as $songid => $name) {
                          flush();
                          echo '<li class="list-group-item"><a href="#" data-value="'.$name.'">'.$name.'</a></li>';
                        }?>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="charts" class="page hide-page">
                  <div class="page_wrapper">
                    <div class="row">
                     <div class="charts-list col-lg-5 col-md-5 col-sm-5 col-xs-5">
                        <ul id="charts" class="list-group ">
                          <li class="list-group-item"><a href="#" id="allartists">All</a></li>
                        <?php
             //           $artists = array_unique(array_filter(array_map(function($value){
                    //  return is_object($value) ? @$value->artist : @$value['artist'];
                    // }, $tagsArray),function($value){
                    //  return (empty($value))? FALSE : TRUE;
                    // }));
             //           foreach ( $artists as $songid => $name) {
             //             flush();
             //             echo '<li class="list-group-item"><a href="#" data-value="'.$name.'">'.$name.'</a></li>';
                        //}?>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
        <footer class="footer">
          <div class="container-fluid">
            <div class="row">
              <div id="search" class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <p><i class="icon-magnifier"></i></p>
              <input class="form-control" type"text" placeholder="type to search songs, playlists, artists, albums genres">
              </div>
              <div id="playertitle" class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div id="osd_loader"></div>
                <ul id="osd" class="row">
                  <li id="osd_artist" class="col-lg-4 col-md-4 col-sm-12 col-xs-12"></li>
                  <li id="osd_title" class="col-lg-4 col-md-4 col-sm-12 col-xs-12"></li>
                  <li id="osd_album" class="col-lg-4 col-md-4 col-sm-12 col-xs-12"></li>
                </ul>
                <div id="instantAnswer" style="display:none;">
                  <div id="iaclose"><a href="#"><i class="icon-close"></i></a></div>
                  <ul id="iaul" class="list-group"></ul>
                </div>
              </div>
              <div id="audio_container" class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div id="player" class="row"> <!-- audio player start-->
                  <div id="asset_controls" class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <a id="previous" href="#"><i class="icon-control-rewind"></i></a>
                    <a id="play" href="#"><i id="play_icon" class="icon-control-play"></i><i id="pause_icon" class="icon-control-pause" style="display:none;"></i></a>
                    <a id="next" href="#"><i class="icon-control-forward"></i></a>
                    <a id="queue_control" href="#"><i class="icon-playlist"></i></a>
                  </div><!-- asset controls -->
                  <div id="seek" class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                    <input id="seek_slider" data-slider-id='seek_slider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="0"/>
                  </div> <!-- seek controls -->
                  <div id="secondary_controls" class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                    <div id="secondary_controls_wrapper" class="row">
                      <div id="current_time" class="col-lg-2 col-md-2 col-sm-2 col-xs-2">00:00</div>
                      <div id="total_time" class="col-lg-2 col-md-2 col-sm-2 col-xs-2">00:00</div>
                      <div id="volume_container" class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                        <a id="volume" href="#" class="col-lg-3 col-md-3 col-sm-3 col-xs-3"><i id="volume_low" class="icon-volume-1"></i><i id="volume_high" class="icon-volume-2" style="display:none;"></i><i id="volume_mute" class="icon-volume-off" style="display:none;"></i></a>
                        <div id="volume_slider_container" class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                          <input id="volume_slider" data-slider-id='volume_slider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="40"/>
                        </div>
                      </div>
                      <div id="custom_controls" class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <div id="custom_controls_wrapper" class="row">
                          <a id="shuffle" href="#" class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><i class="icon-shuffle"></i></a>
                          <a id="repeat" href="#" class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><i class="icon-loop"></i></a>
                        </div>
                      </div>
                    </div>
                  </div> <!--secondary controls -->
                </div><!-- audio player end-->
              </div>
            </div>

          </div>
        </footer>
  

  <!-- script references -->
    <script src="app theme/js/jquery-2.1.4.min.js"></script>
    <script src="app theme/js/bootstrap.min.js"></script>
    <script src="app theme/js/bootstrap-slider.min.js"></script>
    <script src="app theme/js/jquery-ui.min.js"></script>
    <script src="lib/js/player.js"></script>
    <script src="lib/js/controls.js"></script>
    <script src="lib/js/navigation.js"></script>
    <script src="lib/js/search.js"></script>
    <script src="lib/js/sortdragdrop.js"></script>
  </body>
  </html>
  

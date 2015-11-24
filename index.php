<?php
/*

Copyright 2015 Christos Dimas <specktator@totallynoob.com>

This file is part of femto.

femto is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

femto is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with femto.  If not, see <http://www.gnu.org/licenses/>.
Source: https://github.com/specktator/scraper

*/
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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <!-- twitter card / opengraph -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="http://femto.rocks">
    <meta name="twitter:site:id" content="@specktator_">
    <meta name="twitter:creator" content="@specktator_">
    <meta name="twitter:title" content="femto - (maybe) the fastest web media player on the web!" />
    <meta name="twitter:description" content="femto is a fast (the fastest maybe) #Free and #OpenSource #Web media player where you can discover new music, listen yours, create a radio station, discuss music. Music library can be yours or indexed from the web!">
    <meta name="og:description" content="femto is a fast (the fastest maybe) #Free and #OpenSource #Web media player where you can discover new music, listen yours, create a radio station, discuss music. Music library can be yours or indexed from the web!">
    <meta name="twitter:app:country" content="GR">
    <meta name="twitter:image" content="<?php echo "http://".$_SERVER['SERVER_NAME']."/app_theme/images/logo_tw_og.png";?>" />
    <meta property="og:title" content="<?php echo APP_NAME; ?> | for the love of music" />
    <meta property="og:type" content="website"/>
    <meta property="og:site_name" content="femto.rocks"/>
    <meta property="og:url" content="http://femto.specktator.net/share.php" />
    <meta property="og:image" content="<?php echo "http://".$_SERVER['SERVER_NAME']."/app_theme/images/logo_fb_og.png";?>" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="1200" />
    <!-- twitter card / opengraph END -->
    <link href="app_theme/css/bootstrap.min.css" rel="stylesheet">
    <link href="app_theme/css/bootstrap-slider.min.css" rel="stylesheet">
    <link href="app_theme/css/font-awesome.min.css" rel="stylesheet">
    <link href="app_theme/css/styles.css" rel="stylesheet">
  </head>
  <body>
        <div id="header" class="navbar navbar-default navbar-fixed-top">
            <div class="navbar-header">
                <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".navbar-collapse">
                    <i class="fa fa-reorder"></i>
                </button>
                <a class="navbar-brand" href="#">
                    <img id="navlogo" class="logo img-responsive" src="app_theme/images/femto_transparent.png">
                </a>
            </div>
            <nav id="sidebar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                        <li>
                            <a class="list-group-item" href="#" data-value="main" tabindex="1"><i class="icon-music-tone-alt icon-1x text-success"></i> Library</a>
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
                    <!-- <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">Navbar Item 2<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Navbar Item2 - Sub Item 1</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">Navbar Item 3</a>
                    </li> -->
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
        </div>
        <div id="wrapper">
          <div id="sidebar-wrapper" class="col-lg-1 col-md-1 hide-element">
                <div id="sidebar">
                    <ul id="navside" class="nav list-group">
                        <li>
                            <a class="list-group-item" href="#" data-value="main" tabindex="1"><i class="icon-music-tone-alt icon-1x text-success"></i> Library</a>
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
            <div id="main-wrapper" class="col-lg-12 col-md-12">
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
                            $albumartLink = (!isset($db->tags->albumart) || empty($db->tags->albumart))? "app_theme/images/vinyl2.png" :$db->tags->albumart ;
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
                              <div class="panel-heading">Play Queue</div>
                              <div class="panel-body">
                                  <p>Drag 'n' Drop songs below </p>
                              </div> <!-- panel body -->
                              <ul id="queue_sortable" class="list-group">
                              </ul>
                              <div class="panel-footer">
                                  <div class="row">
                                      <div id="queue-playlist-controls" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 queue-playlist-controls">
                                          <a href="#" id="queue_save" class="" data-toggle="tooltip" data-placement="top" title="Save as playlist"><i class="fa fa-save"></i></a>
                                          <input type="text" id="saveinput" class="form-control" autocomplete="off" placeholder="write playlist name ...">
                                          <a href="#" id="queue_clear" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Clear list">Clear</a>
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
                     <div class="playlists-list col-lg-2 col-md-2 col-sm-6 col-xs-6">
                        <ul id="playlists" class="list-group ">

                        </ul>
                      </div>
                      <div id="playlists-tracks" class="playlists-tracks col-lg-5 col-md-5 col-sm-6 col-xs-6 hide-page">
                        <ul id="playlists-tracks-list" class="list-group">
                        </ul>
                      </div>
                    </div>
              </div>
                </div>
                <div id="artists" class="page hide-page">
                  <div class="page_wrapper">
                    <div class="row page-row">
                     <div class="artists-list col-lg-2 col-md-2 col-sm-5 col-xs-5">
                        <ul id="artists" class="list-group ">
                         </ul>
                      </div>
                      <div id="artist-tracks" class="playlists-tracks col-lg-5 col-md-5 col-sm-6 col-xs-6 hide-page">
                        <ul id="artist-tracks-list" class="list-group">
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="genres" class="page hide-page">
                  <div class="page_wrapper">
                    <div class="row page-row">
                     <div class="genres-list col-lg-2 col-md-2 col-sm-5 col-xs-5">
                        <ul id="genres" class="list-group ">
                         </ul>
                      </div>
                      <div id="genres-tracks" class="playlists-tracks col-lg-5 col-md-5 col-sm-6 col-xs-6 hide-page">
                        <ul id="genres-tracks-list" class="list-group">
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="charts" class="page hide-page">
                  <div class="page_wrapper">
                    <div class="row">
                     <div class="charts-list col-lg-5 col-md-5 col-sm-5 col-xs-5">
                        <ul id="trackCharts" class="list-group ">
                        </ul>
                      </div>
                     <div class="charts-list col-lg-5 col-md-5 col-sm-5 col-xs-5">
                        <ul id="playlistCharts" class="list-group ">
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
              <input class="form-control" type="text" placeholder="type to search songs, playlists, artists, albums genres">
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
                    <a id="queue_control" href="#" data-toggle="tooltip" data-placement="top" title="Toggle queue"><i class="icon-playlist"></i><span class="badge"></span></a>
                  </div><!-- asset controls -->
                  <div id="seek" class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                    <input id="seek_slider" data-slider-id='seek_slider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="0"/>
                  </div> <!-- seek controls -->
                  <div id="secondary_controls" class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                    <div id="secondary_controls_wrapper" class="row">
                      <div id="current_time" class="col-lg-2 col-md-2 col-sm-2 col-xs-2">00:00</div>
                      <div id="total_time" class="col-lg-2 col-md-2 col-sm-2 col-xs-2">00:00</div>
                      <div id="volume_container" class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                        <a id="volume" href="#" class="col-lg-3 col-md-3 col-sm-3 col-xs-3" data-toggle="tooltip" data-placement="top" title="Mute / Unmute"><i id="volume_low" class="icon-volume-1"></i><i id="volume_high" class="icon-volume-2" style="display:none;"></i><i id="volume_mute" class="icon-volume-off" style="display:none;"></i></a>
                        <div id="volume_slider_container" class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                          <input id="volume_slider" data-slider-id='volume_slider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="40"/>
                        </div>
                      </div>
                      <div id="custom_controls" class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <div id="custom_controls_wrapper" class="row">
                          <a id="shuffle" href="#" class="col-lg-6 col-md-6 col-sm-6 col-xs-6" data-toggle="tooltip" data-placement="top" title="Shuffle"><i class="icon-shuffle"></i></a>
                          <a id="repeat" href="#" class="col-lg-6 col-md-6 col-sm-6 col-xs-6" data-toggle="tooltip" data-placement="top" title="Repeat"><i class="icon-loop"></i></a>
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
    <script src="app_theme/js/jquery-2.1.4.min.js"></script>
    <script src="app_theme/js/bootstrap.min.js"></script>
    <script src="app_theme/js/bootstrap-slider.min.js"></script>
    <script src="app_theme/js/jquery-ui.min.js"></script>
    <script src="lib/js/player.js"></script>
    <script src="lib/js/controls.js"></script>
    <script src="lib/js/navigation.js"></script>
    <script src="lib/js/search.js"></script>
    <script src="lib/js/sortdragdrop.js"></script>
  </body>
  </html>
  

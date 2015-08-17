<?php
include 'db.php';
$db = new db();
$db->audio()->read()->load_songs();
$songs = $db->urls;
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>scraper | for the love of music</title>
    <meta name="generator" content="Bootply" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="tests/app theme/css/bootstrap.min.css" rel="stylesheet">
    <!--[if lt IE 9]>
      <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link href="tests/app theme/css/styles.css" rel="stylesheet">
  </head>
  <body>
        <div id="header" class="navbar navbar-default navbar-fixed-top">
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
        </div>
        <div id="wrapper">
          <div id="sidebar-wrapper" class="col-md-1">
                <div id="sidebar">
                    <ul class="nav list-group">
                        <li>
                            <a class="list-group-item" href="#"><i class="icon-music-tone-alt icon-1x"></i> Songs</a>
                        </li>
                        <li>
                            <a class="list-group-item" href="#"><i class="icon-playlist icon-1x"></i> Playlists</a>
                        </li>
                        <li>
                            <a class="list-group-item" href="#"><i class="icon-users icon-1x"></i> Artists</a>
                        </li>
                        <li>
                            <a class="list-group-item" href="#"><i class="icon-music-tone icon-1x"></i> Genres</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div id="main-wrapper" class="col-md-11 pull-right">
                <div id="main">
                  <div id="instantAnswer"></div>


                  <ul id="playlist" class="row">

                    <?php
                    foreach($songs as $link){
                      flush();
                      echo '<li class="col-lg-2 col-md-2 col-sm-6 col-xs-6"><div class="albumart img-thumbnail"><div class="overlay"><a href="#" class="control-play control-center"><i class="icon-control-play"></i></a></div><img class="img-responsive" src="tests/app theme/images/img1.jpg" /></div><a class="track" href="'.urldecode(preg_replace('/\n/','',$link['url'])).'"><div class="title">'.basename(urldecode($link['title'])).'</div></a></li>';
                    }
                    ?>
                  </ul>
                </div>
            </div>
        </div>
        <footer class="footer">
          <div class="container">
            <div class="col-md-5 col-sm-5 col-lg-5">
              <audio id="audio" preload="auto" tabindex="0" controls="" >
              <source src="#<?php //echo $lines[0];?>">
              Your browser does not support the video tag.
              </audio>
            </div>
            <div class="col-md-5 col-sm-5 col-lg-5"></div>
            <div id="search" class="cold-md-2 col-lg-2 col-sm-2">
            <input class="form-control" type"text" placeholder="type to search">
            </div>
          </div>
        </footer>
  

  <!-- script references -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <script src="tests/app theme/js/bootstrap.min.js"></script>
    <script src="player.js"></script>
    <script src="search.js"></script>
    <script src="controls.js"></script>
  </body>
  </html>
  

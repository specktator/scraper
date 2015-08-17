<?php
$lines = file('videos.txt');
?>

<html>
<head>
  <title>Scraper player</title>
  <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
  <script src="player.js"></script>
  <script src="search.js"></script>
  <script src="videoplayer/mediaelement-and-player.min.js"></script>
  <script src="videoplayer/mediaelementplayer.min.js"></script>
  <script src="videoplayer/mediaelement.min.js"></script>
  <script src="vplayer.js"></script>
  <link href="style.css" rel="stylesheet"></link>
  <link href="videoplayer/mediaelementplayer.min.css" rel="stylesheet"></link>
  <meta charset="utf-8">
</head>

<body>

  <h1>Scraper's player</h1>

  <!-- <embed type="application/x-vlc-plugin"
  name="video1"
  autoplay="no" loop="yes" width="400" height="300"
  target="<?php echo $link; ?>" /> -->

  <div id="playertitle"><?php //echo basename(urldecode($lines[0])); ?></div>
  <div id="instantAnswer"></div>
  <video id="audio" preload="auto" tabindex="0" controls="" >
    <source src="movies/Inherent Vice 2014 720p WEB-DL x264 AAC - Ozlem/Inherent Vice 2014 720p WEB-DL x264 AAC - Ozlem.mp4" type="video/mp4"/>
      Your browser does not support the video tag.
    </video>
    <div id="search">
      <input type"text" value="type to search">
    </div>
    <ul id="playlist">

      <?php
      foreach($lines as $link){
        flush();
        echo '<li><a href="'.urldecode(preg_replace('/\n/','',$link)).'">'.basename(urldecode($link)).'</a></li>';
      }
      ?>
    </ul>

  </body>
  </html>
  

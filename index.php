<?php
include 'db.php';
$db = new db();
$db->audio()->read()->load_songs();
$songs = $db->urls;
?>

<html>
<head>
  <title>Scraper player</title>
  <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
  <script src="player.js"></script>
  <script src="search.js"></script>
  <link href="style.css" rel="stylesheet"></link>
</head>

<body>

  <h1>Scraper's player</h1>

  <!-- <embed type="application/x-vlc-plugin"
  name="video1"
  autoplay="no" loop="yes" width="400" height="300"
  target="<?php echo $link; ?>" /> -->

  <div id="playertitle"><?php //echo basename(urldecode($lines[0])); ?></div>
  <div id="instantAnswer"></div>
  <audio id="audio" preload="auto" tabindex="0" controls="" >
    <source src="#<?php //echo $lines[0];?>">
      Your browser does not support the video tag.
    </audio>
    <div id="search">
      <input type"text" value="type to search">
    </div>
    <ul id="playlist">

      <?php
      foreach($songs as $link){
        flush();
        echo '<li><a href="'.urldecode(preg_replace('/\n/','',$link['url'])).'">'.basename(urldecode($link['title'])).'</a></li>';
      }
      ?>
    </ul>

  </body>
  </html>
  

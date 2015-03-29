<?php
$lines = file('files.txt');
?>

<html>
<head>
  <title>Scraper player</title>
  <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
  <script src="player.js"></script>
  <link href="style.css" rel="stylesheet"></link>
</head>

<body>

<h1>Scraper player</h1>

<!-- <embed type="application/x-vlc-plugin"
         name="video1"
         autoplay="no" loop="yes" width="400" height="300"
         target="<?php echo $link; ?>" /> -->


  <audio id="audio" preload="auto" tabindex="0" controls="" >
    <source src="<?php echo $lines[0];?>">
   Your browser does not support the video tag.
  </audio>

  <ul id="playlist">

    <?php
    foreach($lines as $link){
      echo '<li><a href="'.$link.'">'.urldecode($link).'</a></li>';
    }
    ?>
  </ul>

</body>
</html>

<?php
class streaming {

  function audio($url){
    $path = $url;

    header('Content-type: audio/mpeg');

    header('Content-Length: '.filesize($path)); // provide file size

    header("Expires: -1");

    header("Cache-Control: no-store, no-cache, must-revalidate");

    header("Cache-Control: post-check=0, pre-check=0", false);

    readfile($path);
  }

  function video($url){
    $path = $url;
    header("Content-type: video/mpeg");

    header("Content-Length: ".filesize($path)); // provide file size

    header("Expires: -1");

    header("Cache-Control: no-store, no-cache, must-revalidate");

    header("Cache-Control: post-check=0, pre-check=0", false);

    readfile($path);
  }
}
?>

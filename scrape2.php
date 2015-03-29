<?php
// ini_set('xdebug.profiler_enable',1);
ini_set('display_errors',1);
error_reporting(-1);


class scrape {

    public $urls;
    public $src;
    public $links = array();

    function __construct($targeturl = 'http://localhost'){
        $this-> trailslashpat = '/\/$/'; // remove trailling slash if exists
        $this-> target = preg_replace($this->trailslashpat,'',$targeturl,1); // and return the target url without it
        $this-> src = file_get_contents($this-> target);

        // $this-> dirpat = '/(?:.*)DIR(?:.*)<a\shref="(.*)\/"(?:.*)/';
        $this-> dirpat = '/href="(.*)\/(?=")/';
        // $this-> filepat = '/(?:.*)(VID|SND)(?:.*)<a\shref="([^\"\>]*)(?:)/';
        $this-> filepat = '/href="(.*(.wav|.mp3|.mp4|.avi|.mkv))(?=")/';
    }

    function getfolders(){
      preg_match_all($this-> dirpat,$this-> src, $this-> folders);
      // var_dump($this-> folders);
      if(count($this-> folders[1])>1){ // if there is not another folder getfiles()
        flush();
        echo "<h1>Folders</h1>";
        var_dump($this-> folders[1]);

        foreach ($this-> folders[1] as $index => $foldername) {
          if ($index == '0' and strpos($foldername,'/') !== FALSE or empty($foldername)) {continue;}
          $this-> foldername = $foldername;
          echo 'fname:'.$foldername;
          echo "<h1>Folder scanning: ".$this-> target.'/'.urldecode($foldername)."</h1>";
          $obint = new scrape($this-> target.'/'.$foldername);
          $obint-> getfolders();
        }
      }
      // var_dump($this-> folders);
      $this-> getfiles();
    }

    function getfiles(){

      preg_match_all($this-> filepat,$this-> src, $this-> files);
      if(!empty($this-> files[1][0])){
        echo "\n\n <h1>Get files()</h1>";
        var_dump($this-> files[1]);
        foreach ($this-> files[1] as $index => $filename) {

          // $this->links[] = $this-> target.'/'.$foldername.'/'.$filename;

        }
      }

    }
}
echo '<style>h1{font-family:orbitron !important;}</style>';
// http://chriscargile.com/music/music
// http://www.w32hax0r.net/music/
// http://transmission.specktator.net
$obj = new scrape('http://localhost/temp');
$obj-> getfolders();


// include 'videopage.inc';
?>

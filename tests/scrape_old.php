<?php
// ini_set('xdebug.profiler_enable',1);
ini_set('display_errors',1);
error_reporting(-1);
include 'db.php';

class scrape {

  public $urls;
  public $src;  // current source code scraping
  public $ftypes; //file extentions pattern
  public $ftype; //string declaring which file type the scraper should search for
  public $audiopat = '.wav|.ogg|.mp3';
  public $videopat = '.mp4|.avi|.mkv';

  function __construct($targeturl = 'http://localhost',$ftype='audio only'){

    $this->ftype = $ftype;

    if($this->ftype == 'audio only'){

      $this->ftypes= $this->audiopat;

    }elseif($this->ftype == 'video only'){

      $this->ftypes=$this->videopat;

    }elseif($this->ftype == 'both'){

      $this->ftypes='.wav|.ogg|.mp3|.mp4|.avi|.mkv';

    }else{
      error_log('file type error');
      die('file type error');
    }

    $this->trailslashpat = '/\/$/'; // remove trailling slash if exists
    $this->target = preg_replace($this->trailslashpat,'',$targeturl,1); // and return the target url without it
    $this->src = file_get_contents($this->target);
    $this->debug = false;
    $this->dirpat = '/href="(.*)\/(?=")/';
    $this->filepat = '/href="(.*('.$this->ftypes.'))(?=")/';
  }

  function getfolders(){
    preg_match_all($this->dirpat,$this->src, $this->folders);
    if(count($this->folders[1])>1){ // if there is not another folder getfiles()
      flush();
      if($this->debug) {var_dump(array("output"=>"<h3>Folders</h3>"));}
      if($this->debug) {var_dump(array("output"=>var_dump($this->folders[1])));}

      foreach ($this->folders[1] as $index => $foldername) {
        if ($index == '0' and strpos($foldername,'/') !== FALSE or empty($foldername)) {continue;}
        $this->foldername = $foldername;
        if($this->debug) {var_dump(array("output"=>"<h3>Folder scanning: ".$this->target.'/'.urldecode($foldername)."</h3>"));}
        $obint = new scrape($this->target.'/'.$foldername);
        $obint-> getfolders();
      }
    }
    $this->getfiles();
  }

  function getfiles(){

    preg_match_all($this->filepat,$this->src, $this->files);
    if(!empty($this->files[1][0])){
      if($this->debug) {var_dump(array("output"=>"<h3>Get files()</h3>"));}
      if($this->debug) {var_dump(array("output"=>var_dump($this->files[1])));}
      // $handle = fopen($this->dbfile,'a+');
      $db = new db();
      $db->audio()->read();
      foreach ($this->files[1] as $index => $filename) {

        // fwrite($handle,$this->target.'/'.$filename."\n");
        
        $db->write_track($this->target.'/'.$filename);

      }
      // $db->spit_out();
      $db->write();
      unset($db);
      // fclose($handle);
    }

  }
}
// http://chriscargile.com/music/music
// http://www.w32hax0r.net/music/
// http://transmission.specktator.net

$obj = new scrape('http://localhost/music/','audio only');
$obj-> getfolders();
?>
<pre>
<?php
$rustart = getrusage();
// ini_set('xdebug.profiler_enable',1);
ini_set('display_errors',1);
error_reporting(-1);
// ini_set('memory_limit', '80MB');
set_time_limit(0);
include '../../config.php';

class scrape {

  public $urls;
  public $src;  // current source code scraping
  public $ftypes; //file extentions pattern
  public $ftype; //string declaring which file type the scraper should search for
  public $audiopat = '.wav|.ogg|.mp3';
  public $videopat = '.mp4|.avi|.mkv';
  public $totalTracks = 0;

  function __start($targeturl = 'http://localhost',$ftype='audio only'){

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
    $this->getfolders($this->target);
  }

  function getfolders($target){
    preg_match_all($this->dirpat,$this->src, $this->folders);
    if(count($this->folders[1])>1){ // if there is not another folder getfiles()
      flush();
      if($this->debug) {echo json_encode(array("debug"=>"Folders()"));}

      foreach ($this->folders[1] as $index => $foldername) {
        if ($index == '0' and strpos($foldername,'/') !== FALSE or empty($foldername)) {continue;}
        $this->foldername = $foldername;
        if($this->debug) {echo json_encode(array("debug"=>"Scanning folder : ".$target.'/'.urldecode($foldername)));}
        
        $this->__start($target.'/'.$foldername);
        $this->getfiles();
      }

    }
  }

  function getfiles(){

    preg_match_all($this->filepat,$this->src, $this->files);
    if(!empty($this->files[1][0])){
      if($this->debug) {echo json_encode(array("debug"=>"Get files()"));}
      if($this->debug) {var_dump($this->files[1]);}
      unset($db);
      try {
	      $db = new db();
	      $db->audio()->read();
	      foreach ($this->files[1] as $index => $filename) {

			     if($this->debug) {echo json_encode(array("debug"=>"File: ".$this->target.'/'.$filename));}
			     $db->write_track($this->target.'/'.$filename);

	      }
	      flush();
	      $db->write();
	      unset($db);
	      echo json_encode(array("total tracks"=>$this->totalTracks = count($this->files[1]) + $this->totalTracks));
     } catch (Exception $e) {
          echo json_encode(array('e'=>$e->getMessage()));
     }

    }

  }
}
// http://chriscargile.com/music/music
// http://www.w32hax0r.net/music/
// http://transmission.specktator.net

$obj = new scrape();

$obj->__start('http://localhost/scraper/music','audio only');
// $obj->__start('http://chriscargile.com/music/music','audio only');
// $obj->__start('http://www.w32hax0r.net/music/','audio only');

function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
     -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}

$ru = getrusage();
echo "<br>This process used " . rutime($ru, $rustart, "utime") .
    " ms for its computations <br>";
echo "<br>It spent " . rutime($ru, $rustart, "stime") .
    " ms in system calls <br>";
?>
</pre>
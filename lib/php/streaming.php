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

class streaming {

  private $db;

  function __construct(){
    $this->db = new db();
    $this->db->audio()->read();
    $this->tracks = $this->db->records->tracks;
    // $this->db->video()->read();
    // $this->video = $this->db->records->video;
    // $this->type = $_REQUEST['type'];
    $this->trackid = $_REQUEST['track-id'];
    $this->findStreamingType();

  }

  function findStreamingType(){
    if(isset($this->tracks->{$this->trackid}->url)){
      $this->url = $this->tracks->{$this->trackid}->url;
      $this->saudio($this->url);
    }else{
      throw new Exception("Error item it doesn't exist in database");
    }
    return $this;
  }

  function saudio($path){

    header('Content-type: audio/mpeg');

    header('Content-Length: '.filesize($path)); // provide file size

    header("Expires: -1");

    header("Cache-Control: no-store, no-cache, must-revalidate");

    header("Cache-Control: post-check=0, pre-check=0", false);

    readfile($path);
  }

  function svideo($path){

    header("Content-type: video/mpeg");

    header("Content-Length: ".filesize($path)); // provide file size

    header("Expires: -1");

    header("Cache-Control: no-store, no-cache, must-revalidate");

    header("Cache-Control: post-check=0, pre-check=0", false);

    readfile($path);
  }
}

// $s = new streaming;
// $s->audio($_REQUEST['track-id']);

?>

var volumeSlider;
var seekSlider;
var audio;
var playlist;
var tracks;
var playcontrols;
var current;
var title;
var cover;
var play;
var pause;
var mute;
var muted;
var close;
var duration;
var defaultVolume;
var currTime;
var link;
$(document).ready(function(){
  init();
  function init(){
    current = 0;
    audio = new Audio();
//     cover = $('.cover');
    play = $('#play');
    playIcon = $('#play_icon');
    pauseIcon = $('#pause_icon');
    volume = $('#volume');
    playlist = $('#playlist');
    title = $("#playertitle");
    instantAnswerobj = $('#instantAnswer');
    tracks = playlist.find('li > a');
    len = tracks.length - 1;
    defaultVolume = audio.volume = .40;
    // audio.play();
    
  
// PLAYER CONTROLS 
    // PLAY/PAUSE CONTROL
    play.on('click',function(event){
      event.preventDefault();
      // $(this).children('i').toggle();
      (audio.paused == false) ? audio.pause() : audio.play();
    });
    
    audio.addEventListener('playing',function(event){
      event.preventDefault();
      playIcon.hide();
      pauseIcon.show();
      $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-play').hide();
      $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-pause').show();
      // playlist.siblings('li .albumart .overlay a').children('i').toggle();
    });

   audio.addEventListener('pause',function(event){
      event.preventDefault();
      playIcon.show();
      pauseIcon.hide();
      $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-play').show();
      $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-pause').hide();
    });

// MUTE/UNMUTE CONTROL
    volume.on('click',function(event){
      event.preventDefault();
      $(this).children('i#volume_mute, i#volume_low').toggle();
      (audio.muted == false)? audio.muted = true : audio.muted = false;
    });

    audio.addEventListener('volumechange',function(){
      volumeSlider.setValue(audio.volume*100);
    });

// NEXT TRACK

  function next(){
      current++;
      if(current == len){
        current = 0;
        link = playlist.find('.track');
      }else{
        link = playlist.find('.track')[current];
      }
      $('body').data('currentTrackOverlayBtnObj',$(link).siblings('.albumart').find('a'));
      playlist.animate({scrollTop:$(link).parent().position().top - $('#playlist li:first-child').position().top},"slow");
      run($(link),audio);
      updatetitle($(link),title,instantAnswerobj);
  }

  $('#forward').on('click',function(event){
    event.preventDefault();
    next();
  });

// PREVIOUS TRACK

  function rewind(){
      current--;
      console.log("~~ previous: "+current);
      if(current == len){
        current = 0;
        link = playlist.find('.track');
      }else{
        link = playlist.find('.track')[current];
      }
      $('body').data('currentTrackOverlayBtnObj',$(link).siblings('.albumart').find('a'));
      playlist.animate({scrollTop:$(link).parent().position().top - $('#playlist li:first-child').position().top},"slow");
      run($(link),audio);
      updatetitle($(link),title,instantAnswerobj);
  }

  $('#rewind').on('click',function(event){
    event.preventDefault();
    rewind();
  });

// TIME CONTROLS
    
    //formating seconds like mm:ss 
    timeToDate = function (time){
      minutes = Math.floor(time/60);
      seconds = Math.round(time % 60);
      return String(((String(minutes).length==1)? "0"+minutes : minutes) + ":"+ ((String(seconds).length ==1)? "0"+seconds : seconds)) ;
    }
    
    //duration
    setDuration = function(totalTime){
      $('#total_time').html(totalTime);
    }
    audio.addEventListener('durationchange',function(){
      duration = audio.duration;
      seekSlider.setValue(0);
      setDuration(timeToDate(duration));
      
    });
    
    //currentTime
    audio.addEventListener('timeupdate',function (){
      currentTime = audio.currentTime;
      $("#current_time").html(timeToDate(currentTime));
      seekSlider.setValue(currentTime*100/duration);
      //console.log(currentTime*100/duration);
    });
    
// SEEK SLIDER
    seekSliderChange = function(){
      audio.currentTime = duration*seekSlider.getValue()/100;
    }
    
    seekSlider = $('#seek_slider').slider({precision:15, formatter: function(value){ return timeToDate(duration*value/100);}})
                                           .on('slide change',seekSliderChange)
                                           .data('slider');
    
    // volume slider
    volumeSliderChange = function(){
      audio.volume = volumeSlider.getValue()/100;
    }
    volumeSlider = $('#volume_slider').slider({formatter: function(value){ return value+'%';}})
                                              .on('slide',volumeSliderChange)
                                              .data('slider');
    
// PLAYLIST CONTROLS

    playlist.find('a').click(function(event){
      link = $(this);
      if(link.hasClass('control-play') && link.children('i.icon-control-play').is(':visible')){
        // if overlay play button is visible player is going to get the src url for $this track to play it, which exists in a:first-chlild in $this li item
        parentli =link.parent().parent().parent();
        if(audio.paused && parentli.hasClass('active')){
          // if track is paused and our parent li is the active one then the player should not get the url of the track (already loaded) instead the player should just play the track is already loaded
          audio.play();
          return true
        }
        
        // forces overlay play btn to show up when song is changed        
        if ($('body').data('currentTrackOverlayBtnObj')) {
          $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-play').show();
          $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-pause').hide();
        };

        $('body').data('currentTrackOverlayBtnObj',link); // saving the current link object into .data() in order to use it in eventListeners (toggle play/pause buttons in overlays and the player as well)
        link = parentli.children('a'); // will be used to get song url
        // $('audio').trigger('songchanged');

      }else if(link.hasClass('control-play') && link.children('i.icon-control-pause').is(':visible') && link.parent().parent().parent().hasClass('active')){
          audio.pause();
          return false;
      }else if(!link.hasClass('control-play')){
      //if the clicked link is not an overlay child element then we need to get the sibling overlay control link in order to use it for toggling play/pause buttons in overlay
        overlayObj = link.parent().find('.overlay a');
        $('body').data('currentTrackOverlayBtnObj',overlayObj);

      }

      current = link.parent().index();
      run(link, audio);
      updatetitle($(link),title,instantAnswerobj);
      
      event.preventDefault();
    });
    
    audio.addEventListener('ended',function(e){
      current++;
      if(current == len){
        current = 0;
        link = playlist.find('.track');
      }else{
        link = playlist.find('.track')[current];
      }
      $('body').data('currentTrackOverlayBtnObj',$(link).siblings('.albumart').find('a'));
      playlist.animate({scrollTop:$(link).parent().position().top - $('#playlist li:first-child').position().top},"slow");
      run($(link),audio);
      updatetitle($(link),title,instantAnswerobj);
    });
  }

  //add to playlist
  //remove from playlist
  //share on twitter
  //share on facebook
  
  function run(link, player){
    player.src = link.attr('href');
    par = link.parent();
    par.addClass('active').siblings().removeClass('active');
    par.find('.overlay').css('display','block');
    par.prev().find('.overlay').css('display','none');
    link.scrollTop();
    player.load();
    player.play();
  }

// PLAYER EXTRA FEATURES

  function updatetitle(link,title,instantAnswerobj){
     title.html("Loading id3 tags ...");
    $.get('id3.php',{'url':link.attr('href')}, function(data){
      if(data == 'The MP3 file contains no valid ID3 Tags.'){
        console.log(data);
        title.html("Tags not provided.");
        title.html('Title: '+data.title);
      }else{
        OSDdata= '<ul id="osd" class="row"><li class="col-lg-4 col-md-4 col-sm-12 col-xs-12">Artist: <a href="https://duckduckgo.com/?q='+data.artist+'" target="_blank" >'+data.artist+'</a></li><li class="col-lg-4 col-md-4 col-sm-12 col-xs-12"> Title: <a href="https://duckduckgo.com/?q='+data.title+'" target="_blank">'+data.title+'</a></li><li class="col-lg-4 col-md-4 col-sm-12 col-xs-12"> Album: <a href="https://duckduckgo.com/?q='+data.album+'" target="_blank">'+data.album+'</li></ul>';
        title.html(OSDdata);
        instantAnswer(data.artist,instantAnswerobj);
      }
    },'json');
  }
  
  function instantAnswer(q,instantAnswerobj){

    instantAnswerobj.html('Loading instant Answer...');
    $.get('ddg.php',{'type':'instantAnswer','q':q},function(data){
      console.log(data);
      if(!$.isEmptyObject(data)){
        instantAnswerobj.html('<ul id="iaul">Abstract: <li id="abstract">'+data.abstract+'</li> Entity: <li id="entity">'+data.entity+'</li>Site: <li>'+data.officialsite+'</li> Related Topics: <li>'+data.relatedTopics[0]+'</li><li>'+data.relatedTopics[1]+'</li></ul>');
      }else{
        instantAnswerobj.html('Oops! Data not provided...');
      }
    },"json");


  }

});
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
var shufflestate;
var repeatstate;
$(document).ready(function(){
  init();
  function init(){
    current = 0;
    audio = new Audio();
    play = $('#play');
    playIcon = $('#play_icon');
    pauseIcon = $('#pause_icon');
    volume = $('#volume');
    playlist = $('#playlist');
    title = $("#playertitle");
    osdLoader = $("#playertitle #osd_loader");
    osdArtist = $("#playertitle #osd_artist");
    osdTitle = $("#playertitle #osd_title");
    osdAlbum = $("#playertitle #osd_album");
    instantAnswerobj = $('#instantAnswer');
    repeatbtn = $('#repeat');
    shufflebtn = $('#shuffle');
    tracks = playlist.find('li > a');
    shufflestate = false;
    repeat = false;
    len = Math.abs(tracks.length - 1);
    defaultVolume = audio.volume = .40;
    
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
    
    //ENDED
      audio.addEventListener('ended',function(e){
        console.log(repeatstate);
        (shufflestate)? current : current++;
        console.log(current);
        if(current == len){
          current = 0;
          link = playlist.find('.track');
        }else{
          link = playlist.find('.track')[current];
        }
        $('body').data('currentTrackOverlayBtnObj',$(link).siblings('.albumart').find('a.control-play'));
        playlist.animate({scrollTop:$(link).parent().position().top - $('#playlist li:first-child').position().top},"slow");
        run($(link),audio);
        updatetitle($(link),title,instantAnswerobj);
      });

    // SONG CHANGED EVENT
    
      $(audio).on('songchanged',function(event){
            if($('body').data('currentTrackOverlayBtnObj')) {
              $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-play').show();
              $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-pause').hide();
            }
      });

    //SHUFFLE EVENT
      $(audio).on('shuffling',function(event,state){
        if(len >= 1 && state === true){
          max = len;
          min = 0;
          current = tracknumber = Math.floor(Math.random() * (max - min + 1)) + min;
        }
      });

    //REPEAT EVENT
    // $(audio).on('repeating',function(event,state){
    //   console.log(len+"::"+current);
    //   if(len >=1 && current == len && state === true){
    //     current = 1;
    //   }
    // });

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
        console.log(current + ":: " + len);
        if(current == len){
          current = 0;
          link = playlist.find('.track');
          console.log('current: '+ current);
        }else{
        (shufflestate)? current : current++;
          link = playlist.find('.track')[current];
        }

        $('body').data('currentTrackOverlayBtnObj',$(link).parent().find('a.control-play'));
        playlist.animate({scrollTop:$(link).parent().position().top - $('#playlist li:first-child').position().top},"slow");
        run($(link),audio);
        updatetitle($(link),title,instantAnswerobj);
    }

    $('#next').on('click',function(event){
      event.preventDefault();
      $(audio).trigger("songchanged");
      $(audio).trigger('shuffling',[shufflestate]);
      $(audio).trigger('repeating',[repeatstate]);
      next();
    });

  // PREVIOUS TRACK

    function previous(){
        (shufflestate)? current : current--;
        // console.log("~~ previous: "+current);
        if(current == len){
          current = 0;
          link = playlist.find('.track');
        }else{
          link = playlist.find('.track')[current];
        }
        $(audio).trigger("songchanged",["Custom","Event"]);
        $('body').data('currentTrackOverlayBtnObj',$(link).parent().find('a.control-play'));
        playlist.animate({scrollTop:$(link).parent().position().top - $('#playlist li:first-child').position().top},"slow");
        run($(link),audio);
        updatetitle($(link),title,instantAnswerobj);
    }

    $('#previous').on('click',function(event){
      event.preventDefault();
      $(audio).trigger('shuffling',[shufflestate]);
      previous();
    });

  // RANDOM TRACK
    shufflebtn.on('click',function(event){
        shufflestate =(shufflestate)? false : true;
        shufflebtn.children('i').toggleClass('control-on');
        $(audio).trigger('shuffling',[shufflestate]);
    });

  // REPEAT PLAYLIST 
    repeatbtn.on('click',function(event){
        repeatstate = (repeatstate)? false: true;
        repeatbtn.children('i').toggleClass('control-on');
        // $(audio).trigger('repeating',[repeatstate]);
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
                                                .on('slide change',volumeSliderChange)
                                                .data('slider');

  // PLAYLIST CONTROLS

    playlist.find('a').click(function(event){
      link = $(this);
      if(link.hasClass('control-play') && link.children('i.icon-control-play').is(':visible')){
        // if overlay play button is visible player is going to get the src url for $this track to play it, which exists in a:first-child in $this li item
        parentli =link.parent().parent().parent();
        if(audio.paused && parentli.hasClass('active')){
          // if track is paused and our parent li is the active one then the player should not get the url of the track (already loaded) instead the player should just play the track is already loaded
          audio.play();
          return true
        }
        $(audio).trigger("songchanged");
        $('body').data('currentTrackOverlayBtnObj',link); // saving the current link object into .data() in order to use it in eventListeners (toggle play/pause buttons in overlays and the player as well)
        link = parentli.children('a'); // will be used to get song url

      }else if(link.hasClass('control-play') && link.children('i.icon-control-pause').is(':visible') && link.parent().parent().parent().hasClass('active')){
          audio.pause();
          return false;
      }else if(link.hasClass('track')){
        //if the clicked link is not an overlay child element then we need to get the sibling overlay control link in order to use it for toggling play/pause buttons in overlay
        $(audio).trigger("songchanged");
        overlayObj = link.parent().find('a.control-play');
        $('body').data('currentTrackOverlayBtnObj',overlayObj);
      }

      current = link.parent().index();
      run(link, audio);
      updatetitle($(link),title,instantAnswerobj);
      event.preventDefault();
    });

  }// init() end

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
     instantAnswerobj.slideUp(300);
     osdArtist.html('');
     osdTitle.html('');
     osdAlbum.html('');
     osdLoader.html('<div id="preloader_1"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>');
    $.get('rq.php',{'action':'id3','url':encodeURIComponent(link.attr('href')),'track-id':link.attr('track-id')}, function(data){
      if(data.e){
        console.log(data);
        osdLoader.html("Tags not provided.");
        osdTitle.html('Title: '+data.title);
      }else{
        // link.html(data.artist+' - '+data.title);
        osdLoader.html('');
        osdArtist.html('Artist: <a href="https://duckduckgo.com/?q='+data.artist+'" target="_blank" >'+data.artist+'</a>');
        osdTitle.html('Title: <a href="https://duckduckgo.com/?q='+data.title+'" target="_blank">'+data.title+'</a>');
        osdAlbum.html('Album: <a href="https://duckduckgo.com/?q='+data.album+'" target="_blank">'+data.album+'</a>');
        if (!$.isEmptyObject(data.albumart)) {
          $(link).parent().children('.albumart').children('img').attr('src',data.albumart);
        }
        instantAnswer(data.artist,instantAnswerobj);
      }
    },'json');
  }
  
  function instantAnswer(q,instantAnswerobj){
    instantAnswerobj.slideDown(100);
    $.get('rq.php',{'action':'ddg','type':'instantAnswer','q':q},function(data){
      if(!$.isEmptyObject(data)){
        // need to slide up/down instantAnswer div + hide btn
        instantAnswerobj.children('#iaul').html('<li id="abstract" class="list-group-item"><h4 class="list-group-item-heading">Abstract</h4><p class="list-group-item-text">'+data.abstract+'</p></li><li id="entity" class="list-group-item"><h4 class="list-group-item-heading">Entity </h4> <p class="list-group-item-text">'+data.entity+'</p></li><li class="list-group-item"><h4 class="list-group-item-heading">Site</h4><p class="list-group-item-text">'+data.officialsite+'</p></li><li class="list-group-item"><h4 class="list-group-item-heading">Related Topics</h4><p class="list-group-item-text">'+data.relatedTopics[0]+'</p></li><li class="list-group-item"><p class="list-group-item-text">'+data.relatedTopics[1]+'</p></li>');
      }else{
        instantAnswerobj.children('ul').html('<li>Oops! Data not provided...</li>');
      }
    },"json");


  }

  $('#iaclose').on('click',function(event){
    console.log('as');
    event.preventDefault();
    btncloseHeight = $(this).height();
    ia = $(this).parent();
    if(btncloseHeight == ia.height() ){
      ia.animate({top: origTop,height: origHeight});
    }else{
      origHeight = ia.height();
      origTop = ia.position().top;
      ia.animate({top:-btncloseHeight,height: btncloseHeight});

    }
  });

});
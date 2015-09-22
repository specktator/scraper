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
var queueWrapper;
var removebtn;
$(document).ready(function(){
  
  femto = {
    init: function(){
      audio = new Audio();
      playIcon = $('#play_icon');
      pauseIcon = $('#pause_icon');
      play = $('#play');
      volume = $('#volume');
      nextBtn = $('#next');
      previousBtn = $('#previous');
      title = $("#playertitle");
      totalTimeElement = $('#total_time');
      seekSliderElement = $('#seek_slider');
      osdLoader = $("#playertitle #osd_loader");
      osdArtist = $("#playertitle #osd_artist");
      osdTitle = $("#playertitle #osd_title");
      osdAlbum = $("#playertitle #osd_album");
      instantAnswerobj = $('#instantAnswer');
      iacloseBtn = $('#iaclose');
      repeatbtn = $('#repeat');
      shufflebtn = $('#shuffle');
      queue = $('#queue_control');
      queueWrapper = $('#queue_wrapper');
      femto.player();

    },
    player: function(){
    // PLAYER CONTROLS 
      // PLAY
        play.on('click',function(event){
          event.preventDefault();
          (audio.paused == false) ? audio.pause() : audio.play();
        });

      //PLAYING
        audio.addEventListener('playing',function(event){
          event.preventDefault();
          playIcon.hide();
          pauseIcon.show();
          $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-play').hide();
          $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-pause').show();
          // playlist.siblings('li .albumart .overlay a').children('i').toggle();
        });

      // PAUSE
        audio.addEventListener('pause',function(event){
          event.preventDefault();
          playIcon.show();
          pauseIcon.hide();
          $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-play').show();
          $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-pause').hide();
        });
      
      //ENDED
        audio.addEventListener('ended',function(e){
          console.log("Femto => Repeat state is: "+repeatstate);
          (shufflestate)? current : current++;
          console.log("Femto => Previous Track ended: "+current);
          if(current == len){
            current = 0;
            link = playlist.find('.track');
          }else{
            link = playlist.find('.track')[current];
          }
          $('body').data('currentTrackOverlayBtnObj',$(link).siblings('.albumart').find('a.control-play'));
          playlist.animate({scrollTop:$(link).parent().position().top - playlist.find('li:first-child').position().top},"slow");
          femto.run($(link),audio);
          femto.updatetitle($(link),title,instantAnswerobj);
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
          //   console.log("Femto =>"+len+"::"+current);
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
            console.log("Femto => Next track: "+current + ", Total tracks: " + len);
            if(current == len){
              current = 0;
              link = playlist.find('.track');
              console.log('Femto => current: '+ current);
            }else{
            (shufflestate)? current : current++;
              link = playlist.find('.track')[current];
            }

            $('body').data('currentTrackOverlayBtnObj',$(link).parent().find('a.control-play'));
            playlist.animate({scrollTop:$(link).parent().position().top - playlist.find('li:first-child').position().top},"slow");
            femto.run($(link),audio);
            femto.updatetitle($(link),title,instantAnswerobj);
        }

        nextBtn.on('click',function(event){
          event.preventDefault();
          $(audio).trigger("songchanged");
          $(audio).trigger('shuffling',[shufflestate]);
          $(audio).trigger('repeating',[repeatstate]);
          next();
        });

      // PREVIOUS TRACK

        function previous(){
            (shufflestate)? current : current--;
            console.log("Femto => Next track: "+current + ", Total tracks: " + len);
            if(current == len){
              current = 0;
              link = playlist.find('.track');
            }else{
              link = playlist.find('.track')[current];
            }
            $(audio).trigger("songchanged",["Custom","Event"]);
            $('body').data('currentTrackOverlayBtnObj',$(link).parent().find('a.control-play'));
            playlist.animate({scrollTop:$(link).parent().position().top - playlist.find('li:first-child').position().top},"slow");
            femto.run($(link),audio);
            femto.updatetitle($(link),title,instantAnswerobj);
        }

        previousBtn.on('click',function(event){
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
            totalTimeElement.html(totalTime);
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
          });
          
      // SEEK SLIDER
          seekSliderChange = function(){
            audio.currentTime = duration*seekSlider.getValue()/100;
          }
          
          seekSlider = seekSliderElement.slider({precision:15, formatter: function(value){ return timeToDate(duration*value/100);}})
                                                 .on('slide change',seekSliderChange)
                                                 .data('slider');
          
          // volume slider
          volumeSliderChange = function(){
            audio.volume = volumeSlider.getValue()/100;
          }
          volumeSlider = $('#volume_slider').slider({formatter: function(value){ return value+'%';}})
                                                    .on('slide change',volumeSliderChange)
                                                    .data('slider');
      
      //VOLUME DEFAULT
          defaultVolume = audio.volume = .40;

    },

    run: function (link, player){
        player.src = link.attr('href');
        par = link.parent();
        par.addClass('active').siblings().removeClass('active');
        par.find('.overlay').css('display','block');
        par.prev().find('.overlay').css('display','none');
        link.scrollTop();
        player.load();
        player.play();
      },

    playlist: function (playlistid){

      playlistid = typeof playlistid !== 'undefined' ? playlistid : '#playlist';
      console.log("Femto => Playlist: "+playlistid);
      current = 0;

      playlist = $(playlistid);
      tracks = playlist.find('li > a.track, li > a.control-play');
      shufflestate = false;
      repeat = false;
      removebtn = $('.playlist-remove');
      len = Math.abs(tracks.length - 1);
      

    // QUEUE TOGGLE
        queue.on('click',function(event){
          event.preventDefault();
          enableQueue();
        });

        function enableQueue(){

          if(!queueWrapper.is(":visible")){
            songsList.attr('class',songsList.attr('class').replace(/-([0-9]+)/g,'-8'));
            queueWrapper.show("slide",{direction:"left"},200);
          }else{
            console.log("Femto => closing queue");
             queueWrapper.hide("slide",{direction:"left"},200);
             songsList.attr('class',songsList.attr('class').replace(/-([0-9]+)/g,'-12'));
          }
        }

    // REMOVE FROM PLAYLIST
        removebtn.on('click',function(event){
          $(this).parents('li').remove();
        });

    // PLAYLIST CONTROLS

      playlist.find('a.track, a.control-play').click(function(event){
        link = $(this);
        detectPlaylistChange(link);
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
        femto.run(link, audio);
        femto.updatetitle($(link),title,instantAnswerobj);
        event.preventDefault();
      });

     //DETECT PLAYLIST CHANGE

        function detectPlaylistChange(link){
          if(playlistid !== link.parents('ul').attr('id') ){
            playlistid = '#'+link.parents('ul').attr('id');
            playlist = $(playlistid);
            tracks = playlist.find('li > a.track, li > a.control-play');
            len = Math.abs(tracks.length - 1);
            console.log("Femto => Playlist id: "+playlistid);
          }
        }

    },

    updatetitle: function (link,title,instantAnswerobj){
         instantAnswerobj.hide("slide",{direction:"down"},100);
         osdArtist.html('');
         osdTitle.html('');
         osdAlbum.html('');
         osdLoader.html('<div id="preloader_1"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>');
         $.get('rq.php',{'action':'id3','url':encodeURIComponent(link.attr('href')),'track-id':link.attr('track-id')}, function(data){
            if(data.e){
              console.log("Femto =>"+data);
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
              femto.instantAnswer(data.artist,instantAnswerobj);
            }
        },'json');
    },

    instantAnswer: function (q,instantAnswerobj){
      instantAnswerobj.children('#iaul').html('');
      instantAnswerobj.show("slide",{direction:"up"},100);
      $.get('rq.php',{'action':'ddg','type':'instantAnswer','q':q},function(data){
        if(!$.isEmptyObject(data)){
          // need to slide up/down instantAnswer div + hide btn
          instantAnswerobj.children('#iaul').html('<li id="abstract" class="list-group-item"><h4 class="list-group-item-heading">Abstract</h4><p class="list-group-item-text">'+data.abstract+'</p></li><li id="entity" class="list-group-item"><h4 class="list-group-item-heading">Entity </h4> <p class="list-group-item-text">'+data.entity+'</p></li><li class="list-group-item"><h4 class="list-group-item-heading">Site</h4><p class="list-group-item-text">'+data.officialsite+'</p></li><li class="list-group-item"><h4 class="list-group-item-heading">Related Topics</h4><p class="list-group-item-text">'+data.relatedTopics[0]+'</p></li><li class="list-group-item"><p class="list-group-item-text">'+data.relatedTopics[1]+'</p></li>');
        }else{
          instantAnswerobj.children('ul').html('<li>Oops! Data not provided...</li>');
        }
      },"json");

      iacloseBtn.on('click',function(event){
        event.preventDefault();
        btncloseHeight = $(this).height();
        headingHeight = $('#abstract .list-group-item-heading').height();
        ia = $(this).parent();
        if(btncloseHeight+headingHeight == ia.height() ){
          ia.animate({top: origTop,height: origHeight});
        }else{
          origHeight = ia.height();
          origTop = ia.position().top;
          ia.animate({top:-(btncloseHeight+headingHeight),height: btncloseHeight+headingHeight});

        }
      });
    }


  } //femto object end

femto.init();
femto.playlist();
});
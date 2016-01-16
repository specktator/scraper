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

var atto;
// $(document).ready(function(){
  
  femto = function(){
    
    var local = {
          audio : null,
        }, current, 
        title, // OSD title
        play,  // play buttons
        pause, // pause buttons
        mute, //mute buttons
        close, // OSD close button
        queueWrapper, 
        removeTrackBtn, 
        removePlaylistBtn, 
        queueTracksLength, 
        saveInput, 
        queueSave, // save queue button
        currentPlaylistID,
        link, // url of the song will be played
        currTime,
        duration,
        mutedvolumeSlider, 
        seekSlider, 
        playlist, 
        tracks, 
        playcontrols,
        cover,
        len;

    local.settings = {
      logLevel:Logger.DEBUG,
      follow:true,
      defaultVolume:0.4,
      shufflestate:false,
      repeatstate:false
    },

    init = function(){

      /*  Logger is an external library */
      if( typeof Logger != "undefined" ){
        Logger.useDefaults({
          logLevel: local.settings.logLevel,
          formatter: function (messages, context) {
              messages.unshift('[Femto] \u2771\u2771\u2771\u2771 ');
              if (context.name) messages.unshift('[' + context.name + ']');
          }
        });
      }else{
        console.error("[Femto] Logger is not available!");
      }

      local.audio = new Audio();
      local.songsList = $('div.songs-list');
      playIcon = $('#play_icon');
      pauseIcon = $('#pause_icon');
      play = $('#play');
      local.volumeBtn = $('#volume');
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
      saveInput = $('#saveinput');
      queueSave = $('#queue_save');
      queueTracksLength = 0;
      current = 0;
      local.player();
      for(var modulename in local){
        if(local[modulename].events) local[modulename].events();
      }
      local.playlist.set();
    };

    local.player = function(){

      // PLAYER CONTROLS 
      // PLAY
        play.on('click',function(event){
          event.preventDefault();
          (local.audio.paused == false) ? local.pause() : local.audio.play();
        });

      //PLAYING
         local.audio.addEventListener('playing',function(event){
          event.preventDefault();
          playIcon.hide();
          pauseIcon.show();
          $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-play').hide();
          $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-pause').show();
          // playlist.siblings('li .albumart .overlay a').children('i').toggle();
        });

      // PAUSE
         local.audio.addEventListener('pause',function(event){
          event.preventDefault();
          playIcon.show();
          pauseIcon.hide();
          $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-play').show();
          $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-pause').hide();
        });
      
      //ENDED
         local.audio.addEventListener('ended',function(e){
          Logger.info("Repeat state is: "+local.settings.repeatstate);
          Logger.info("Previous Track ended: "+current);
          Logger.info(["Current Track: ",current," len:",len]);
          if(current == len){
            current = 0;
            link = playlist.find('.track')[current];
            if(!local.settings.repeatstate){
               local.pause();
              return false;
            }
          }else{
            $(local.audio).trigger('shuffling',[local.settings.shufflestate]);
            (local.settings.shufflestate)? current : current++;
            link = playlist.find('.track')[current];
          }
          $(local.audio).trigger("trackStats",{"track-id":$('body').data('currentTrackID')}); //trigger event to update track stats
          $('body').data('currentTrackOverlayBtnObj',$(link).siblings('.albumart').find('a.control-play'));
          if(local.settings.follow) playlist.animate({scrollTop:$(link).parent().position().top - playlist.find('li:first-child').position().top},"slow");
          local.play($(link),local.audio);
          updatetitle($(link),title,instantAnswerobj);
        });

      // SONG CHANGED EVENT
      
        $(local.audio).on('songchanged',function(event){
              if($('body').data('currentTrackOverlayBtnObj')) {
                $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-play').show();
                $('body').data('currentTrackOverlayBtnObj').children('i.icon-control-pause').hide();
              }
        });

      //SHUFFLE EVENT
        $(local.audio).on('shuffling',function(event,state){
          if(len >= 1 && state === true){
            max = len;
            min = 0;
            current = tracknumber = Math.floor(Math.random() * (max - min + 1)) + min;
          }
        });

      //REPEAT EVENT
          // $(local.audio).on('repeating',function(event,state){
          //   console.log("Femto =>"+len+"::"+current);
          //   if(len >=1 && current == len && state === true){
          //     current = 1;
          //   }
          // });

      // MUTE/UNMUTE CONTROL
          local.volumeBtn.on('click',function(event){
            event.preventDefault();
            $(this).children('i#volume_mute, i#volume_low').toggle();
            ( local.audio.muted == false)?  local.audio.muted = true :  local.audio.muted = false;
          });

           local.audio.addEventListener('volumechange',function(){
            volumeSlider.setValue( local.audio.volume*100);
          });

      // NEXT TRACK

        local.next = function(){
            if(current == len){
              current = 0;
              link = playlist.find('.track')[current];
              Logger.info('Current Track: '+ current);
            }else{
              (local.settings.shufflestate)? current : current++;
              link = playlist.find('.track')[current];
            }
            Logger.info("Next track: "+current + ", Total tracks: " + len);

            $('body').data('currentTrackOverlayBtnObj',$(link).parent().find('a.control-play'));
            if(local.settings.follow) playlist.animate({scrollTop:$(link).parent().position().top - playlist.find('li:first-child').position().top},"slow");
            local.play($(link));
            updatetitle($(link),title,instantAnswerobj);
        }

        nextBtn.on('click',function(event){
          event.preventDefault();
          $(local.audio).trigger("songchanged");
          $(local.audio).trigger('shuffling',[local.settings.shufflestate]);
          $(local.audio).trigger('repeating',[local.settings.repeatstate]);
          local.next();
        });

      // PREVIOUS TRACK

        local.previous = function(){
            if(current == len){
              current = 0;
              link = playlist.find('.track')[current];
            }else{
              (!local.settings.shufflestate) ? ( (current-1 < 0)? current = len : current-- ) : current;
              link = playlist.find('.track')[current];
            }
            Logger.info("Next track: "+current + ", Total tracks: " + len);
            $(local.audio).trigger("songchanged",["Custom","Event"]);
            $('body').data('currentTrackOverlayBtnObj',$(link).parent().find('a.control-play'));
            if(local.settings.follow) playlist.animate({scrollTop:$(link).parent().position().top - playlist.find('li:first-child').position().top},"slow");
            local.play($(link),local.audio);
            updatetitle($(link),title,instantAnswerobj);
        }

        previousBtn.on('click',function(event){
          event.preventDefault();
          $(local.audio).trigger('shuffling',[local.settings.shufflestate]);
          local.previous();
        });

      // RANDOM TRACK
        shufflebtn.on('click',function(event){
            local.settings.shufflestate =(local.settings.shufflestate)? false : true;
            shufflebtn.children('i').toggleClass('control-on');
            $(local.audio).trigger('shuffling',[local.settings.shufflestate]);
        });

      // REPEAT PLAYLIST 
        repeatbtn.on('click',function(event){
            local.settings.repeatstate = (local.settings.repeatstate)? false: true;
            repeatbtn.children('i').toggleClass('control-on');
            // $(local.audio).trigger('repeating',[local.settings.repeatstate]);
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
           local.audio.addEventListener('durationchange',function(){
            duration =  local.audio.duration;
            seekSlider.setValue(0);
            setDuration(timeToDate(duration));
            
          });
          
          //currentTime
           local.audio.addEventListener('timeupdate',function (){
            currentTime =  local.audio.currentTime;
            $("#current_time").html(timeToDate(currentTime));
            seekSlider.setValue(currentTime*100/duration);
          });
          
      // SEEK SLIDER
          seekSliderChange = function(){
             local.audio.currentTime = duration*seekSlider.getValue()/100;
          }
          
          seekSlider = seekSliderElement.slider({precision:15, formatter : function(value){ return timeToDate(duration*value/100);}})
                                                 .on('slide change',seekSliderChange)
                                                 .data('slider');
          
          // volume slider
          volumeSliderChange = function(){
             local.audio.volume = volumeSlider.getValue()/100;
          }
          volumeSlider = $('#volume_slider').slider({formatter : function(value){ return value+'%';}})
                                                    .on('slide change',volumeSliderChange)
                                                    .data('slider');
      
      // VOLUME DEFAULT

           local.audio.volume = local.settings.defaultVolume;

      // QUEUE TOGGLE
          queue.on('click',function(event){
            event.preventDefault();
            enableQueue();
          });

          function enableQueue(){
            if(!queueWrapper.is(":visible")){
              local.songsList.attr('class',local.songsList.attr('class').replace(/-([0-9]+)/g,'-8'));
              queueWrapper.show("slide",{direction:"left"},200);
            }else{
              Logger.info("closing queue");
               queueWrapper.hide("slide",{direction:"left"},200);
               local.songsList.attr('class',local.songsList.attr('class').replace(/-([0-9]+)/g,'-12'));
            }
          }

      // QUEUE SAVE

        queueSave.on('click',function(){
          
          var name = saveInput.val()
          var trackIds =[];

          $.each($('#queue_sortable').find('li a.track'),function(key,value){
            
            trackIds[key]= $(this).attr('track-id');

          });
          
          if(name && trackIds){

            data = {'target': '#'+$(this).parent('div').attr('id'), 'name':name, 'trackids':trackIds};
            Logger.info("Saving queue.");
            $(local.audio).trigger('playlistSave',data);

          }else{
            noty({notification:true,type:'danger',msg:'Empty list cannot be saved.'})
          }

        });
    };

    local.play =  function (link){
        local.audio.src = link.attr('href');
        $('body').data("currentTrackID",link.attr("track-id"));
        par = link.parent();
        par.addClass('active').siblings().removeClass('active');
        par.find('.overlay').css('display','block');
        par.prev().find('.overlay').css('display','none');
        if(local.settings.follow) link.scrollTop();
        local.audio.load();
        local.audio.play();
    };

    local.pause = function(){

        local.audio.pause();
    }

    local.playlist = {

      set:function (playlistid){

        currentPlaylistID = playlistid = typeof playlistid !== 'undefined' ? playlistid : '#playlist';
        Logger.info("Playlist: "+playlistid);

        playlist = $(playlistid);
        tracks = playlist.find('li > a.track, li a.control-play');
        // local.settings.shufflestate = false;
        // repeat = false;
        removeTrackBtn = playlist.find('.track-remove');
        len = Math.abs((playlist.find('li > a.track').length-1));

        $(local.audio).trigger('trackRemove');

          // PLAYLIST CONTROLS

        tracks.click(function(event){
          link = $(this);
          detectPlaylistChange(link);
          if(link.hasClass('control-play') && link.children('i.icon-control-play').is(':visible')){
            // if overlay play button is visible player is going to get the src url for $this track to play it, which exists in a:first-child in $this li item
            parentli =link.parents('li');
            if( local.audio.paused && parentli.hasClass('active')){
              // if track is paused and our parent li is the active one then the player should not get the url of the track (already loaded) instead the player should just play the track is already loaded
               local.audio.play();
              return true
            }
            $(local.audio).trigger("songchanged");
            $('body').data('currentTrackOverlayBtnObj',link); // saving the current link object into .data() in order to use it in eventListeners (toggle play/pause buttons in overlays and the player as well)
            link = parentli.children('a.track'); // will be used to get song url

          }else if(link.hasClass('control-play') && link.children('i.icon-control-pause').is(':visible') && link.parents('li').hasClass('active') ){
               local.pause();
              return false;
          }else if(link.hasClass('track')){
            //if the clicked link is not an overlay child element then we need to get the sibling overlay control link in order to use it for toggling play/pause buttons in overlay
            $(local.audio).trigger("songchanged");
            overlayObj = link.parent().find('a.control-play');
            $('body').data('currentTrackOverlayBtnObj',overlayObj);
          }

          
          current = link.parent().index();
          local.play(link);
          updatetitle($(link),title,instantAnswerobj);
          event.preventDefault();
        });

       //DETECT PLAYLIST CHANGE

          function detectPlaylistChange(link){
            if(playlistid !== link.parents('ul').attr('id') ){
              playlistid = '#'+link.parents('ul').attr('id');
              playlist = $(playlistid);
              tracks = playlist.find('li > a.track, li > a.control-play');
              len = Math.abs((playlist.find('li > a.track').length-1));
              Logger.info("Playlist changed to id: "+playlistid);
            }
          }

      },

      events : function(){
        $(local.audio).on('playlistSave',function(event, data){
          Logger.info("Event: Save.");
          local.playlist.actions.save(data);
        });

        $(local.audio).on('playlistStats',function(event, id, stats){

          Logger.info(["Event: playlistStats triggered.",id,stats]);
        });

        $(local.audio).on('playlistDelete',function(event, id,target){
          local.playlist.actions.delete({'id':id,'target':target});
          Logger.info(["Event: Playlist deleted with id:",id]);
        });

        $(local.audio).on('playlistLoadTracks',function(event, target, id){
          local.playlist.actions.loadTracks({'target':target,'id':id});
          Logger.info('playlist loaded with id: '+id);
        });

        $(local.audio).on('playlistsLoadAll',function(event,target){
          local.playlist.actions.loadAll({'target':target});
          Logger.info('loading all playlists.');
        });

        /* playlistUpdate event will initiate an update request to the server, updating tracks order, remove them etc */

        $(local.audio).on('playlistUpdate',function(event,target){
          if( local.tracks.helpers.isPlaylistItem(target) ){
            data = local.playlist.helpers.getCurrentPlaylistData();
            data.target = target;
            local.playlist.actions.update( data );
            Logger.info("Event: Playlist updated, with id:",id);
          }else{
            Logger.debug(target);
          }
        });

      },

      actions: {
        save: function(data){
          $.post("rq.php",{'action':"playlist",'type':"save","data":data},function(response){
              if(response){
                response.target = data.target;
                Logger.time("Saving playlist response: ",response);
                noty(response);
              }
          },'json');
        },
        delete: function(data){
          target = data.target;
          delete data.target;
          $.post("rq.php",{'action':"playlist",'type':"delete","data":data},function(response){
              if(response){
                Logger.warn("delete:",response);
                if(response.type === 'success'){
                  target.fadeOut(800,function(){
                    
                    if( local.playlist.helpers.isPlaylistActive( target.children("a.playlist-link").attr("playlist-id") ) ){ // if the deleted playlist is not the active one there's no reason to switch to another one.
                      var prev = target.prev().children("a.playlist-link");
                      var next = target.next().children("a.playlist-link");

                      if( next.length > 0 ){
                        next.click();
                      }else if( next.length === 0 && prev.length > 0 ){
                        prev.click();
                      }else{
                        target.parent().append( $("<li><small>No playlists to display...<small></li>") );
                      }
                    }

                    target.remove();
                  });

                }
                noty(response);
              }
          },'json');
        },
        rename: function(data){
          //data = id, newName
          $.post("rq.php",{'action':"playlist",'type':"rename","data":data},function(response){
              if(response){
                Logger.time(response);
                noty(response);
              }
          },'json');
        },
        stats: function(data){
          //data = id, stats
          $.post("rq.php",{'action':"playlist",'type':"stats","data":data},function(response){
              if(response){
                Logger.time(response);
                noty(response);
              }
          },'json');
        },
        loadTracks: function(data){

          $.post("rq.php",{'action':"playlist",'type':"load","data":data},function(response){
              if(response){
                out = [];
                $.each(response,function(key, item){
                  var li = $('<li class="playlist-item list-group-item">');
                  var img = $('<img class="img-thumbnail inline-img" src="'+item.albumart+'">');
                  var controlplay = $('<a href="#" class="control-play"><i class="icon-control-play"></i><i class="icon-control-pause" style="display:none;"></i></a>');
                  var tracklink = $('<a href="'+item.url+'" class="track" track-id="'+item.id+'">'+item.title+'</a>');
                  var removebtn = $('<a href="#" class="track-remove inline-controls"><i class="fa fa-remove"></i></a>');
                  var sharefb = $('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on facebook"><i class="fa fa-facebook"></i></a>');
                  var sharetw = $('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on twitter"><i class="fa fa-twitter"></i></a>');
                  var reorder = $('<a href="#" class="playlist-reorder inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Drag to change order"><i class="fa fa-reorder"></i></a>');
                   out [key] = li.append(img,controlplay,tracklink,removebtn,sharefb,sharetw,reorder);
                });

                $(data.target).html(out);
                local.playlist.set(data.target);
                noty(response);
              }else{
                noty(response);
              }
          },'json');
        },
        loadAll:function(data)
        {
          
          $.post("rq.php",{"action":"playlist","type":"loadall"},function(response){
            if(response){
              out = [];
              $.each(response,function(key,item){
                li = $('<li class="list-group-item playlist-item">');
                link = $('<a href="#" class="playlist-link" playlist-id="'+item.id+'">'+item.name+'</a>');
                remove = $('<a href="#" class="playlist-remove inline-controls" data-toggle="tooltip" data-placement="top" title="Delete playlist"><i class="fa fa-remove"></i></a>');
                sharefb = $('<a href="#" class="playlist-share inline-controls" data-toggle="tooltip" data-placement="top" title="Share playlist on facebook"><i class="fa fa-facebook"></i></a>');
                sharetw = $('<a href="#" class="playlist-share inline-controls" data-toggle="tooltip" data-placement="top" title="Share playlist on twitter"><i class="fa fa-twitter"></i></a>');
                out [key] = li.append(link,remove,sharefb,sharetw);
              });

              $(data.target).html(out);
              Logger.warn(response);
              noty(response);

            }else{
              noty(response);
            }

            // LOAD PLAYLIST TRACKS, binding on playlist links after they're rendered
            var playlistLinks = $(data.target).find('a.playlist-link');
            playlistLinks.on('click',function(event){
                
                var playlistTracksListContainer = $('#playlists-tracks');
                var playlistTracksList = '#'+$('#playlists-tracks > ul').attr('id');
                
                if(playlistTracksListContainer.is(':visible')){
                  playlistTracksListContainer.hide("slide",{direction:"left"});
                }

                // set background color to the active playlist list item
                playlistLinks.parent().siblings().removeClass('playlist-active');
                $(this).parent().addClass('playlist-active');

                local.playlistId = $(this).attr('playlist-id'); // current active playlist ID
                
                // get playlist tracks & on success put them in ul
                $(local.audio).trigger('playlistLoadTracks',[playlistTracksList,local.playlistId]);

                playlistTracksListContainer.show("slide",{direction:"left"});
            });

            //DELETE PLAYLIST
            $(data.target).find('a.playlist-remove').on('click',function(event){
              var playlistid = $(this).siblings('a.playlist-link').attr('playlist-id');
              $(local.audio).trigger('playlistDelete',[playlistid,$(this).parents('li')]);
            });

              
          },'json');
        },
        update:function(data)
        {
          target = data.target;
          delete data.target;
          $.post("rq.php",{'action':"playlist",'type':"update","data":data},function(response)
          {
            if(response){
              Logger.warn(response);
                if(response.type === 'success'){

                }
              noty(response);
            }else{
              noty(response);
            }
          },"json");
        }
      },
      helpers:
      {
        getCurrentPlaylistData: function()
        {
          var trackIds =[];

          $.each( $('#playlists-tracks-list').find('li a.track'),function(key,value){
            
            trackIds[key]= $(this).attr('track-id');

          });

          return {
                  "id": local.playlistId,
                  "trackids":trackIds
                  };
        },

        isPlaylistActive: function(playlistId){
          return ( local.playlistId && local.playlistId === playlistId )? true : false;
        }
      }

    }

    local.tracks = {
        events: function(){
          $(local.audio).on('trackRemove',function(){
              // REMOVE TRACK FROM PLAYLIST
              removeTrackBtn.on('click',function(event){
                
                // console.log("remove track target: ",$(this).parents("li"));
                $(this).parents("li").fadeOut(600,function(){ //fadeOut is not a function!!!
                  $(this).remove(); // can remove be applied to array of objects
                  $(local.audio).trigger('playlistUpdate', [$(this)] ); //conflict with queue playlist, when removing a track from queue - triggers playlistUpdate
                });


                queue.children('span.badge').text($('#queue_sortable').find('li').length);
              });
          });

          $(local.audio).on('loadArtists',function(event,target){
              local.tracks.actions.loadArtists({'target':target});
          });
          $(local.audio).on('loadGenres',function(event,target){
              local.tracks.actions.loadGenres({'target':target});
          });

          $(local.audio).on('loadByArtist',function(event,target,id){
              local.tracks.actions.loadByArtist({'target':target,'id':id});
          });

          $(local.audio).on('loadByGenre',function(event,target,id){
              local.tracks.actions.loadByGenre({'target':target,'id':id});
          });

          $(local.audio).on('trackDelete',function(event,id){
            Logger.debug('trackDelete');
          });

          $(local.audio).on('trackStats',function(event,data){
            local.tracks.actions.stats(data);
            Logger.debug('track stats updaded');
          });
        },

        actions:{
          loadArtists: function(data){
            
            $.post("rq.php",{"action":"tracks","type":"loadartists"},function(response){
              if(response){
                out = [];
                $.each(response,function(key,item){
                  li = $('<li class="list-group-item playlist-item">');
                  link = $('<a href="#" class="playlist-link" artist-id="'+item.name+'">'+item.name+'</a>');
                  sharefb = $('<a href="#" class="playlist-share inline-controls" data-toggle="tooltip" data-placement="top" title="Share playlist on facebook"><i class="fa fa-facebook"></i></a>');
                  sharetw = $('<a href="#" class="playlist-share inline-controls" data-toggle="tooltip" data-placement="top" title="Share playlist on twitter"><i class="fa fa-twitter"></i></a>');
                  out [key] = li.append(link,sharefb,sharetw);
                });

                  $(data.target).html(out);

                // console.log($(data.target).find('a.playlist-link'));
              // LOAD TRACKS by artist, binding on playlist links after they're rendered
                var playlistLinks = $(data.target).find('a.playlist-link');
                playlistLinks.on('click',function(event){
                  var playlistTracksListContainer = $('#artist-tracks');
                  var playlistTracksList = '#'+$('#artist-tracks > ul').attr('id');

                  // set background color to the active playlist list item
                  playlistLinks.parent().siblings().removeClass('playlist-active');
                  $(this).parent().addClass('playlist-active');

                  if(playlistTracksListContainer.is(':visible')){
                    playlistTracksListContainer.hide("slide",{direction:"left"});
                  }

                  var playlistid = $(this).attr('artist-id');
                // get playlist tracks & on success put them in ul
                  $(local.audio).trigger('loadByArtist',[playlistTracksList,playlistid]);

                  playlistTracksListContainer.show("slide",{direction:"left"});
                  noty(response);
                });

              }else{
                Logger.debug("empty response.");
              }
            },'json');
          },
          loadByArtist: function(data){
            var targetElem = data.target;
            delete targetElem;
            $.post("rq.php",{'action':"tracks",'type':"loadbyartist","data":data},function(response){
                if(response){
                  Logger.warn(response);
                  out = [];
                  $.each(response,function(key, item){
                    var li = $('<li class="playlist-item list-group-item">');
                    var img = $('<img class="img-thumbnail inline-img" src="'+item.albumart+'">');
                    var controlplay = $('<a href="#" class="control-play"><i class="icon-control-play"></i><i class="icon-control-pause" style="display:none;"></i></a>');
                    var tracklink = $('<a href="'+item.url+'" class="track" track-id="'+item.id+'">'+item.title+'</a>');
                    var sharefb = $('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on facebook"><i class="fa fa-facebook"></i></a>');
                    var sharetw = $('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on twitter"><i class="fa fa-twitter"></i></a>');
                    var reorder = $('<a href="#" class="playlist-reorder inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Drag to change order"><i class="fa fa-reorder"></i></a>');
                    out [key] = li.append(img,controlplay,tracklink,sharefb,sharetw,reorder);
                  });
                  

                  $(targetElem).html(out);
                  noty(response);
                  local.playlist.set(targetElem);
                }
            },'json');

          },
          loadGenres: function(data){
            var targetElem = data.target;
            delete data.target;
            $.post("rq.php",{'action':"tracks",'type':"loadgenres"},function(response){
                if(response){
                  Logger.warn(response);
                  out = [];
                  $.each(response,function(key, item){
                    var li = $('<li class="playlist-item list-group-item">');
                    var tracklink = $('<a href="#" class="playlist-link" genre-id="'+item.name+'">'+item.name+'</a>');
                    var sharefb = $('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on facebook"><i class="fa fa-facebook"></i></a>');
                    var sharetw = $('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on twitter"><i class="fa fa-twitter"></i></a>');
                    out [key] = li.append(tracklink,sharefb,sharetw);
                  });
                  

                  $(targetElem).html(out);


                  // LOAD PLAYLIST TRACKS, binding on playlist links after they're rendered
                  var playlistLinks = $(targetElem).find('a.playlist-link');
                   playlistLinks.on('click',function(event){
                    
                      var playlistTracksListContainer = $('#genres-tracks');
                      var playlistTracksList = '#'+$('#genres-tracks > ul').attr('id');
                      
                      // set background color to the active playlist list item
                      playlistLinks.parent().siblings().removeClass('playlist-active');
                      $(this).parent().addClass('playlist-active');

                      if(playlistTracksListContainer.is(':visible')){
                        playlistTracksListContainer.hide("slide",{direction:"left"});
                      }

                      var playlistid = $(this).attr('genre-id');
                      // get playlist tracks & on success put them in ul
                      $(local.audio).trigger('loadByGenre',[playlistTracksList,playlistid]);

                      playlistTracksListContainer.show("slide",{direction:"left"});
                      noty(response);
                  });

                }
            },'json');

          },
          loadByGenre: function(data){
            var targetElem = data.target;
            delete data.target;
            $.post("rq.php",{'action':"tracks","type":"loadbygenre","data":data},function(response){
                if(response){
                  Logger.warn(response);
                  out = [];
                  $.each(response,function(key, item){
                    var li = $('<li class="playlist-item list-group-item">');
                    var img = $('<img class="img-thumbnail inline-img" src="'+item.albumart+'">');
                    var controlplay = $('<a href="#" class="control-play"><i class="icon-control-play"></i><i class="icon-control-pause" style="display:none;"></i></a>');
                    var tracklink = $('<a href="'+item.url+'" class="track" track-id="'+item.id+'">'+item.title+'</a>');
                    var sharefb = $('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on facebook"><i class="fa fa-facebook"></i></a>');
                    var sharetw = $('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on twitter"><i class="fa fa-twitter"></i></a>');
                    var reorder = $('<a href="#" class="playlist-reorder inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Drag to change order"><i class="fa fa-reorder"></i></a>');
                    out [key] = li.append(img,controlplay,tracklink,sharefb,sharetw,reorder);
                  });
                  

                  $(targetElem).html(out);
                  local.playlist.set(targetElem);
                  noty(response);
                }
            },'json');

          },

          stats:function(data){
            $.post("rq.php",{"action":"tracks","type":"playbacktimes","data":data},function(response){
              if(response){
                Logger.warn(response);
                noty(response);
              }
            });
          },

        },
      helpers:{
        isPlaylistItem:function(currentElement){
          return /*currentElement.hasClass('playlist-item') &&*/ currentElement.parent("ul#playlists-tracks-list");
        }
      }
    };

    local.charts = {
      events:function(){
        $(local.audio).on('loadTrackCharts',function(event,target){
          local.charts.actions.loadTrackCharts({"target":target});
        });
      },
      actions:{
        loadTrackCharts:function(data){
          Logger.debug("action loadTrackChart");
          targetElem = data.target;
          delete data.target;
          $.post("rq.php",{'action':"tracks",'type':"charts"},function(response){
              if(response){
                out = [];
                $.each(response,function(key, item){
                  var li = $('<li class="playlist-item list-group-item">');
                  var img = $('<img class="img-thumbnail inline-img" src="'+item.albumart+'">');
                  var controlplay = $('<a href="#" class="control-play"><i class="icon-control-play"></i><i class="icon-control-pause" style="display:none;"></i></a>');
                  var tracklink = $('<a href="'+item.url+'" class="track" track-id="'+item.id+'">'+item.title+'</a>');
                  var sharefb = $('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on facebook"><i class="fa fa-facebook"></i></a>');
                  var sharetw = $('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on twitter"><i class="fa fa-twitter"></i></a>');
                   out [key] = li.append(img,controlplay,tracklink,sharefb,sharetw);
                });
                Logger.time(targetElem);
                $(targetElem).html(out);
                local.playlist.set(targetElem);
                noty(response);
              }
          },'json');

        }
      }
    };

    share = function(){

    };

    noty = function(data){
      var output;
      
      if(data.notification){
        if(!data.element){data.element = 'fixed';}
        if(data.type && data.element === 'label'){
          output = $('<span class="label label-'+data.type+' notification">'+data.msg+'</span>');
          removeExistingNotification(data.target);
          $(data.target).append(output);
          setTimeout(function(){
            $('.notification').remove();
          },3000);
          Logger.time("Notification displayed.");

        }else if(data.type && data.element === 'fixed'){
          output = ('<div class="alert alert-'+data.type+' alert-dismissible alert-fixed notification" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+data.msg+'</div>');

          removeExistingNotification('body');
          $('body').append(output);
          setTimeout(function(){$('.notification').remove();},3000);

          Logger.time("Notification displayed.");
        }else{
          Logger.time('notification type not defined');
          return false;
        }

      }

      function removeExistingNotification(target){
        existingNotification = $(target).find('.notification');
        if (existingNotification) {
          existingNotification.fadeOut(1000,function(){
            $(this).remove();
          });
        }
      }

    };

    updatetitle = function (link,title,instantAnswerobj){

         instantAnswerobj.hide("slide",{direction:"down"},100);
         osdArtist.html('');
         osdTitle.html('');
         osdAlbum.html('');
         osdLoader.html('<div id="preloader_1"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>');
         $.get('rq.php',{'action':'id3','track-id':link.attr('track-id')}, function(data){
            if(data){

              Logger.time(["id3 data:=>",data]);
              osdLoader.html("Tags not provided.");
              osdTitle.html('Title: '+data.title);
            
              // link.html(data.artist+' - '+data.title);
              osdLoader.html('');
              osdArtist.html('Artist: <a href="https://duckduckgo.com/?q='+data.artist+'" target="_blank" >'+data.artist+'</a>');
              osdTitle.html('Title: <a href="https://duckduckgo.com/?q='+data.title+'" target="_blank">'+data.title+'</a>');
              osdAlbum.html('Album: <a href="https://duckduckgo.com/?q='+data.album+'" target="_blank">'+data.album+'</a>');
              if (!$.isEmptyObject(data.albumart)) {
                $(link).parent().children('.albumart').children('img').attr('src',data.albumart);
              }else{

                // image search fallbacks here!
                prevResult = false;
                for(var index in imgFallbacks){

                  if (prevResult == false) {

                    imgFallbacks[index](data.artist,function(imgURL){

                      prevResult = (imgURL)? true: false;
                      if(prevResult === true ){

                        $(link).parent().children('.albumart').children('img').attr('src',imgURL);
                      }
                    });
                  }
                }

              }
              instantAnswer(data.artist,instantAnswerobj);
            }
            noty(data);
            data = null;
        },'json');

    };

    instantAnswer = function(q,instantAnswerobj){

      instantAnswerobj.children('#iaul').html('');
      instantAnswerobj.show("slide",{direction:"up"},100);
      $.get('rq.php',{'action':'ddg','type':'instantAnswer','q':q},function(data){
        if(!$.isEmptyObject(data)){
          // need to slide up/down instantAnswer div + hide btn
          instantAnswerobj.children('#iaul').html('<li id="abstract" class="list-group-item"><h4 class="list-group-item-heading">Artist Info</h4><p class="list-group-item-text">'+data.abstract+'</p></li><li id="entity" class="list-group-item"><h4 class="list-group-item-heading">Entity </h4> <p class="list-group-item-text">'+data.entity+'</p></li><li class="list-group-item"><h4 class="list-group-item-heading">Site</h4><p class="list-group-item-text">'+data.officialsite+'</p></li><li class="list-group-item"><h4 class="list-group-item-heading">Related Topics</h4><p class="list-group-item-text">'+data.relatedTopics[0]+'</p></li><li class="list-group-item"><p class="list-group-item-text">'+data.relatedTopics[1]+'</p></li>');
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

    };

    imgFallbacks = {

       ddgImg : function(q,callback){
          $.ajax({
            type:'GET',
            url:'https://api.duckduckgo.com/',
            data: {'q': q, format:'json',skip_disambig:1},
            dataType:'jsonp',
            error: function(data){Logger.debug("error: ",data);}
          }).done(function (data) {
            if (data) {
              ddgResults = data;
              results = findMusicTopic(data);
              scores = wordCounting( results );
              relevantTopic = chooseRelevantTopic(results,scores);
              imgURL = findImage(relevantTopic,data);
              callback(imgURL); // @return false or url
              Logger.info([scores,relevantTopic,imgURL]);
            } else {
              Logger.debug("ddgImg: empty response");
            }
          });

          function findMusicTopic(results){
            for(var index in results.RelatedTopics){ 
              try{ 
                if(results.RelatedTopics[index].Name === "Music"){ 
                  return results.RelatedTopics[index].Topics;
                }else{
                  return results.RelatedTopics;
                }
              } catch(err){
                Logger.debug(err.message);
                return false;
              }
            }
          }

          function wordCounting(topicsArray){
            if(topicsArray){
              kwords = {
              //   title:{
              //     kw: /(morning)|(glory)|(oasis)/gi,
              //   },
                songs:{
                  kw:/song/gi,
                },
                bands:{
                  kw:/band/gi,
                },
                singers:{
                  kw:/singer/gi,
                },
                songwriters:{
                  kw:/songwriter/gi,
                },
                musicians:{
                  kw:/musician/gi,
                },
                albums:{
                  kw:/album/gi,
                }
              };

              var counted = [];
              for(var ddg in topicsArray){

                 topicid = ddg;
                 counted[topicid] = {"topicid": topicid,score: 0};

                for(var word in kwords){

                  try{
                    counted[topicid].score = counted[topicid].score + topicsArray[ddg].Text.match(kwords[word].kw).length;
                    Logger.info([word,string.Topics[ddg].Text.match(kwords[word].kw).length]);
                  }catch(err){
                    Logger.info(err.message);
                  }


                }
              //   Logger.info([topicid, string.Topics[ddg].Text]);

              }

              return counted;
            }else{
              Logger.debug("wordCounting: ",topicsArray);
              return false;
            }
          }

          function chooseRelevantTopic(results,scores){
            if(results && scores){
              var max = 0;
              var topicid = null;
              for(var index in scores){
                if( scores[index].score > max){
                  max =  scores[index].score;
                  topicid = scores[index].topicid;
                }
              }
              return results[topicid];
            }else{
              Logger.info([results,scores]);
            }
          }

          function findImage(topicsArray,data){
            if(topicsArray){
              if(topicsArray.Icon.URL){
               return topicsArray.Icon.URL;
              }else{
                return fallBack(data);
              }
            }else{
              return fallBack(data);
            }
            
            function fallBack(data){ 
              if(data.Image){
               return data.Image;
              }else{
                return false;
              }
            }
          
          }
      },
      googleImg:function(q,callback){ callback(false); },
      yahooImg:function(q,callback){ callback(false); },
      bingImg:function(q,callback){ callback(false); }
    }; //imgFallbacks end

    init(); //initialize

    return local;
  }; //femto function end

atto = new femto();
// });
var volumeSlider,
seekSlider,
audio,
playlist,
tracks,
playcontrols,
current,
title,
cover,
play,
pause,
mute,
muted,
close,
duration,
defaultVolume,
currTime,
link,
shufflestate,
repeatstate,
queueWrapper,
removeTrackBtn,
removePlaylistBtn,
queueTracksLength,
saveInput,
queueSave;
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
      saveInput = $('#saveinput');
      queueSave = $('#queue_save');
      queueTracksLength = 0;
      current = 0;
      femto.player();
      femto.playlist.events();
      femto.tracks.events();

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
            if(current == len){
              current = 0;
              link = playlist.find('.track');
              console.log('Femto => current: '+ current);
            }else{
            (shufflestate)? current : current++;
              link = playlist.find('.track')[current];
            }
            console.log("Femto => Next track: "+current + ", Total tracks: " + len);

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
            if(current == len){
              current = 0;
              link = playlist.find('.track');
            }else{
              link = playlist.find('.track')[current];
            }
            console.log("Femto => Next track: "+current + ", Total tracks: " + len);
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

      //QUEUE SAVE

        queueSave.on('click',function(){
          
          var name = saveInput.val()
          var trackIds =[];

          $.each($('#queue_sortable').find('li a.track'),function(key,value){
            
            trackIds[key]= $(this).attr('track-id');

          });
          
          if(name && trackIds){

            data = {'target': '#'+$(this).parent('div').attr('id'), 'name':name, 'trackids':trackIds};
            console.log("Femto => Saving queue.");
            $(audio).trigger('playlistSave',data);

          }else{
            //notify user for error
          }

        });
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

    playlist: {
      set:function (playlistid){

        playlistid = typeof playlistid !== 'undefined' ? playlistid : '#playlist';
        console.log("Femto => Playlist: "+playlistid);


        playlist = $(playlistid);
        tracks = playlist.find('li > a.track, li a.control-play');
        shufflestate = false;
        repeat = false;
        removeTrackBtn = playlist.find('.track-remove');
        len = Math.abs(tracks.length/2 - 1);

        $(audio).trigger('trackRemove');

          // PLAYLIST CONTROLS

        tracks.click(function(event){
          link = $(this);
          detectPlaylistChange(link);
          if(link.hasClass('control-play') && link.children('i.icon-control-play').is(':visible')){
            // if overlay play button is visible player is going to get the src url for $this track to play it, which exists in a:first-child in $this li item
            parentli =link.parents('li');
            if(audio.paused && parentli.hasClass('active')){
              // if track is paused and our parent li is the active one then the player should not get the url of the track (already loaded) instead the player should just play the track is already loaded
              audio.play();
              return true
            }
            $(audio).trigger("songchanged");
            $('body').data('currentTrackOverlayBtnObj',link); // saving the current link object into .data() in order to use it in eventListeners (toggle play/pause buttons in overlays and the player as well)
            link = parentli.children('a.track'); // will be used to get song url

          }else if(link.hasClass('control-play') && link.children('i.icon-control-pause').is(':visible') && link.parents('li').hasClass('active') ){
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
              console.log("Femto => Playlist changed to id: "+playlistid);
            }
          }

      },

      events: function(){
        $(audio).on('playlistSave',function(event, data){
          console.log("Femto => Event: Save.");
          femto.playlist.actions.save(data);
        });

        $(audio).on('playlistStats',function(event, id, stats){

          console.log(id,stats);
        });

        $(audio).on('playlistDelete',function(event, id,target){
          femto.playlist.actions.delete({'id':id,'target':target});
          console.log(id);
        });

        $(audio).on('playlistLoadTracks',function(event, target, id){
          femto.playlist.actions.loadTracks({'target':target,'id':id});
          console.log('Femto => playlist loaded with id: '+id);
        });

        $(audio).on('playlistsLoadAll',function(event,target){
          femto.playlist.actions.loadAll({'target':target});
          console.log('Femto => loading all playlists.');
        });



        // $(audio).trigger('playlistsLoadAll');

      },

      actions: {
        save: function(data){
          $.post("rq.php",{'action':"playlist",'type':"save","data":data},function(response){
              if(response){
                response.target = data.target;
                console.log("Femto => Saving playlist response: ",response);
                femto.noty(response);
              }
          },'json');
        },
        delete: function(data){
          target = data.target;
          delete data.target;
          $.post("rq.php",{'action':"playlist",'type':"delete","data":data},function(response){
              if(response){
                console.log(response);
                if(response.type === 'success'){
                  target.fadeOut(1000,function(){
                    target.remove();
                  });
                }
                femto.noty(response);
              }
          },'json');
        },
        rename: function(data){
          //data = id, newName
          $.post("rq.php",{'action':"playlist",'type':"rename","data":data},function(response){
              if(response){
                console.log(response);
              }
          },'json');
        },
        stats: function(data){
          //data = id, stats
          $.post("rq.php",{'action':"playlist",'type':"stats","data":data},function(response){
              if(response){
                console.log(response);
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
                femto.playlist.set(data.target);
              }
          },'json');
        },
        loadAll:function(data){
          
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

            }else{
              console.log("Femto => empty response.");
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

                var playlistid = $(this).attr('playlist-id');
                // get playlist tracks & on success put them in ul
                $(audio).trigger('playlistLoadTracks',[playlistTracksList,playlistid]);

                playlistTracksListContainer.show("slide",{direction:"left"});
            });

            //DELETE PLAYLIST
            $(data.target).find('a.playlist-remove').on('click',function(event){
              var playlistid = $(this).siblings('a.playlist-link').attr('playlist-id');
              $(audio).trigger('playlistDelete',[playlistid,$(this).parents('li')]);
            });

              
          },'json');
        }
      },
    },

    tracks:{
        events: function(){
          $(audio).on('trackRemove',function(){
              // REMOVE TRACK FROM PLAYLIST
              removeTrackBtn.on('click',function(event){
                $(this).parents('li').remove();
                var trackId = $(this).attr('track-id');
                queue.children('span.badge').text($('#queue_sortable').find('li').length);
              });
          });

          $(audio).on('loadArtists',function(event,target){
              femto.tracks.actions.loadArtists({'target':target});
          });
          $(audio).on('loadGenres',function(event,target){
              femto.tracks.actions.loadGenres({'target':target});
          });

          $(audio).on('loadByArtist',function(event,target, id){
              femto.tracks.actions.loadByArtist({'target':target,'id':id});
          });

          $(audio).on('loadByGenre',function(event,target, id){
              femto.tracks.actions.loadByGenre({'target':target,'id':id});
          });

          $(audio).on('trackDelete',function(id){
            console.log('Femto => trackDelete');
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
                  $(audio).trigger('loadByArtist',[playlistTracksList,playlistid]);

                  playlistTracksListContainer.show("slide",{direction:"left"});
                });

              }else{
                console.log("Femto => empty response.");
              }
            },'json');
          },
          loadByArtist: function(data){

            $.post("rq.php",{'action':"tracks",'type':"loadbyartist","data":data},function(response){
                if(response){
                  // console.log(response);
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
                  

                  $(data.target).html(out);

                  femto.playlist.set(data.target);
                }
            },'json');

          },
          loadGenres: function(data){

            $.post("rq.php",{'action':"tracks",'type':"loadgenres"},function(response){
                if(response){
                  // console.log(response);
                  out = [];
                  $.each(response,function(key, item){
                    var li = $('<li class="playlist-item list-group-item">');
                    var tracklink = $('<a href="#" class="playlist-link" genre-id="'+item.name+'">'+item.name+'</a>');
                    var sharefb = $('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on facebook"><i class="fa fa-facebook"></i></a>');
                    var sharetw = $('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on twitter"><i class="fa fa-twitter"></i></a>');
                    out [key] = li.append(tracklink,sharefb,sharetw);
                  });
                  

                  $(data.target).html(out);


                  // LOAD PLAYLIST TRACKS, binding on playlist links after they're rendered
                  var playlistLinks = $(data.target).find('a.playlist-link');
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
                      $(audio).trigger('loadByGenre',[playlistTracksList,playlistid]);

                      playlistTracksListContainer.show("slide",{direction:"left"});
                  });

                }
            },'json');

          },
          loadByGenre: function(data){
            var target = data.target;
            delete data.target;
            $.post("rq.php",{'action':"tracks",'type':"loadbygenre","data":data},function(response){
                if(response){
                  // console.log(response);
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
                  

                  $(target).html(out);

                  femto.playlist.set(data.target);
                }
            },'json');

          }
        }
      },

    share: function(){

    },

    noty: function(data){
      var output;
      if(!data.element){data.element = 'fixed';}
      if(data.type && data.element === 'label'){
        output = $('<span class="label label-'+data.type+' notification">'+data.msg+'</span>');
        removeExistingNotification(data.target);
        $(data.target).append(output);
        setTimeout(function(){$('.notification').remove();},3000);
        console.log("Femto => Notification displayed.");

      }else if(data.type && data.element === 'fixed'){
        output = ('<div class="alert alert-'+data.type+' alert-dismissible alert-fixed notification" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+data.msg+'</div>');
        removeExistingNotification('body');
        $('body').append(output);
        setTimeout(function(){$('.notification').remove();},3000);

        console.log("Femto => Notification displayed.");
      }else{
        console.log('Femto => notification type not defined');
        return false;
      }

      function removeExistingNotification(target){
        existingNotification = $(target).find('.notification');
        if (existingNotification) {
          existingNotification.fadeOut(1000,function(){
            $(this).remove();
          });
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
femto.playlist.set();
});
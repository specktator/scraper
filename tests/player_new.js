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
    audio.play();
    
  
//   PLAYER CONTROLS 
    // PLAY/PAUSE CONTROL
    play.on('click',function(event){
      event.preventDefault();
      $(this).children('i').toggle();
      (audio.paused == false) ? audio.pause() : audio.play();
    });
    
//   MUTE/UNMUTE CONTROL
    volume.on('click',function(event){
      event.preventDefault();
      $(this).children('i#volume_mute, i#volume_low').toggle();
      (audio.muted == false)? audio.muted = true : audio.muted = false;
    });


    

    
// time controls
    
    //start time
    //duration
    timeToDate = function (time){
      minutes = Math.floor(time/60);
      seconds = Math.round(time % 60);
      return String(((String(minutes).length==1)? "0"+minutes : minutes) + ":"+ ((String(seconds).length ==1)? "0"+seconds : seconds)) ;
    }
    
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
//       console.log(currentTime*100/duration);
    });
    
    
// seek slider
    seekSliderChange = function(){
//       audio.currentTime = seekSlider.getValue();
      console.log('currentTime: '+audio.currentTime);
      console.log('sliderValue: '+seekSlider.getValue());
      console.log('songSeek: '+duration*seekSlider.getValue()/100);
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
    
    playlist.find('a').click(function(event){
      link = $(this);
      if(link.hasClass('control-play')){
        link = $(this).parent().parent().parent().children('a');
      }
      current = link.parent().index();
      run(link, audio);
      updatetitle($(link),title,instantAnswerobj);
      event.preventDefault();
      return false;
    });
    
    audio.addEventListener('ended',function(e){
      current++;
      if(current == len){
        current = 0;
        link = playlist.find('.track');
      }else{
        link = playlist.find('.track')[current];
      }
      playlist.animate({scrollTop:$(link).parent().position().top - $('#playlist li:first-child').position().top},"slow");
      run($(link),audio);
      updatetitle($(link),title,instantAnswerobj);
    });
  }
  
  function run(link, player){
    player.src = link.attr('href');
    par = link.parent();
    par.addClass('active').siblings().removeClass('active');
    par.find('.overlay').css('display','block');
    par.prev().find('.overlay').css('display','none');
    link.scrollTop();
    player.load();
    player.play();
//     playIcon.hide();
//     pauseIcon.show();
  }
  
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
var pickets = {
   isShift:false,
   isCtrl:false,
   isAlt:false,
    };

$(document).keyup(function (event) {
  if(event.which == 16) pickets.isShift=false;
  if(event.which == 17) pickets.isCtrl=false;
  if(event.which == 18) pickets.isAlt=false;
  stroke = null;
}).keydown(function (event) {

  if(event.which == 16) pickets.isShift=true;
  if(event.which == 17) pickets.isCtrl=true;
  if(event.which == 18) pickets.isAlt=true;
    
  for(var picket in pickets){
   if(pickets[picket]){
     stroke = picket;
   }
  }

  var strokes = function(def,shift,ctrl,alt){
    var defaultFunc = function(){
      if(loglevel === 3){
        console.log("N/A");
      }
    }

    if(typeof(shift) === "undefined") shift = defaultFunc;
    if(typeof(ctrl) === "undefined") ctrl = defaultFunc
    if(typeof(alt) === "undefined") alt = defaultFunc
    if(typeof(def) === "undefined") def = defaultFunc
   
    switch(stroke){
      case 'isShift':
        shift();
        break;
      case 'isCtrl':
        ctrl();
        break;
      case 'isAlt':
        alt();
        break;
      default:
        def();
    }
  };
   
  switch(event.key){
  case 'ArrowLeft':
     //previous
     strokes(function(){
       femto.player.actions.previous();
     });
    break;
  case 'ArrowRight':
     //next
     strokes(function(){
       femto.player.actions.next();
     });
    break;
  case 'ArrowUp':
    audio.volume = audio.volume + 0.05;
    break;
  case 'ArrowDown':
    audio.volume = audio.volume - 0.05;
    break;
  case 'Enter':
    break;
  case 'PageUp':
    break;
  case 'PageDown':
    break;
  case 'Home':
     (audio.paused == false) ? audio.pause() : audio.play();
    break;
  case 'End':
  //          (audio.muted == false)? audio.muted = true : audio.muted = false;
     volume.trigger('click');
    break;

  }
  return false;
  
});
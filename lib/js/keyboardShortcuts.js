keyboardShortcuts = function(local){
	

	var mute = function(){
		local.volumeBtn.trigger('click');
	},
	togglePlay = function(){
		(local.audio.paused == false) ? local.audio.pause() : local.audio.play();
	},
	volumeUp = function(){
	  	var volLength = local.audio.volume + 0.05;
	    local.audio.volume = ( volLength >=0 && volLength <= 1  )? volLength : local.audio.volume;
	},
	volumeDown = function(){
	  	var volLength = local.audio.volume - 0.05;
	    local.audio.volume = ( volLength >=0 && volLength <= 1  )? volLength : local.audio.volume;
	};



	$(document).bind('keydown', 'end', function(){
		local.volumeBtn.trigger('click');
	});
	$(document).bind('keydown', 'space', togglePlay());
	$(document).bind('keydown', 'up', volumeUp());
	$(document).bind('keydown', 'end', volumeDown());

	return local;

};

atto = new keyboardShortcuts(atto);
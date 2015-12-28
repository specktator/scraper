function f(){

	var sk = '123';
	this.settings = {
		volume : 0.4
	};

	this.tracklist = {
		1: "http://femto.local/rq.php?action=streaming&track-id=fa54b12f383618afd047587e59b4ccab",
		2: "http://femto.local/rq.php?action=streaming&track-id=b5eb9e4dd397594feecbe3590d07370d",
		3: "http://femto.local/rq.php?action=streaming&track-id=32e2a1f417ad373c15a1128b080514b6"
	};
  

		this.audio = new Audio();

  	playlist = {
  		action: {
  			save: function(){
  				console.log('saved');
  			}
  		},
  		f: function(){
  			console.log(playlist.action.save());
  		}
  	};

	this.play =  function(id){
		console.log(sk);
		playlist.f();
		console.log("playing "+id);
		this.audio.src = this.tracklist[id];
		this.audio.volume = this.settings.volume;
		this.audio.play();
		this.c = id;
	};
  
    this.pause = function(){
      this.audio.pause();
    };

	this.next = function(){
		this.audio.src = this.tracklist[this.c+1];
		this.audio.play();
	};

	return this;
};

var mixx = (function(){

	this.vdiff = 0.03;
	this.xfade = function(inst){
      console.log(inst.audio.volume);
		do {
			console.log(inst.audio.volume);
            vol = inst.audio.volume-this.vdiff;
			inst.audio.volume = ( vol >= 0 && vol <= 1)? Math.round(vol*100)/100 : inst.audio.volume;
            
		}while(Math.round(inst.audio.volume)===0);
	};
  
  return this;

})();





femto = new f();
pico = new f();

// atto.play(1);
// pico.play(3);





function koukou(my){
  var my;
  my.koukou = function(){
    console.log(my.audio);
  };
	my.play = function(){
		console.log('addddd');
	};
  
  return my;
};

femto = koukou(femto);
var tracks;
$(document).ready(function(){

  var tracks = $("#playlist li a");
  var input = $('#search input');
  input.on('keyup',function(event){
    tracks.hide();
    var needle = input.val();
    var regex = new RegExp(needle,"ig");
    //     console.log('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~');
    $.each(tracks, function(key,value){

      if(regex.test(value) == true){
        console.log(value);
        $(this).show();

      }

    });

  });

});

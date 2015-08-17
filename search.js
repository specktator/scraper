var tracks;
$(document).ready(function () {
  var tracks = $('#playlist li > a');
  var input = $('#search input');
  input.on('keyup', function (event) {
    tracks.parent().hide()
    var needle = input.val();
    if ( needle.length>=3 && event.which !== 17 && event.which !== 16 || needle.length == 0 ){

      var regex = new RegExp(needle, 'i');
        
      var start = new Date().getTime();
      
      // console.log('~~~~~~~~~~~~~~~~~~~~~~~~RESULTS~~~~~~~~~~~~~~~~~~~~~~~');
      $.each(tracks, function (key, value) {
        if (regex.test(value) == true) {
          // console.log("index::"+key+"::"+$(this).html());
          $(this).parent().show()
        }
      });
      
      var end = new Date().getTime();
      var dt = end - start;
      console.log("DT:"+dt+"query: "+needle);
    }
    
  });
});
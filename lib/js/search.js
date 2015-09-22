var tracks;
var tracks = $('#playlist li > a');
var input = $('#search input');
var needle;
var visiblepageid = visiblepage.attr('id');
input.on('keyup', function (event) {
  if(visiblepage){
    searchableUl = visiblepage.find('.page-row > div > ul');
    visiblepageid = visiblepage.attr('id');
  }
  searchableUl.children('li').hide(); //hide all list items to display only the matching results later
  
  if(visiblepageid === 'main'){
    needle = input.val().replace(/\s/g,'%20');
  }else{
    needle = input.val();
  }

  if ( needle.length>=3 && event.which !== 17 || event.which !== 16 || needle.length == 0 ){
    var regex = new RegExp(needle, 'i');
    var start = new Date().getTime();
    // console.log('~~~~~~~~~~~~~~~~~~~~~~~~RESULTS~~~~~~~~~~~~~~~~~~~~~~~');
    $.each(searchableUl, function(key, value){
      searchableLinks = $(value).find('li > a:first-of-type');
      $.each(searchableLinks, function (key, value) {
        if(visiblepageid != 'main'){
          if (regex.test($(this).html())) {
            $(this).parent().show();
            // console.log("index::"+key+"::"+value);
            // console.log("html::"+($(this).html()));
            // console.log($(this).parent());
          }      
        }else{

          if (regex.test(value) == true || regex.test($(this).html())) {
            $(this).parent().show();
          }
        }
      });

    });
    var end = new Date().getTime();
    var dt = end - start;
    console.log("Dt: "+dt+"ms query: "+needle);
  }
  
});
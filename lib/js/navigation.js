var visiblepage;
var pages;
var pagelinks;

pagelinks = $('#sidebar ul.nav li a');

pages = $('#main-wrapper div.page');
$.each(pages,function(){
	if($(this).is(':visible')){
		visiblepage = $(this);
	}
});

pagelinks.on('click',function(event){
	event.preventDefault();
	
	$.each(pages,function(){
		if($(this).is(':visible')){
			$(this).hide("slide",{direction:"up"});
			$('.playlists-tracks > ul').sortable("refresh");
		}
	});

	console.log("page: "+$(this).attr('data-value'));
	$('#'+$(this).attr('data-value')).show("slide",{direction:"down"});
	visiblepage = $('#'+$(this).attr('data-value')); //store the visible page to use it in search
	pagelinks.removeClass('current-page');
	$(this).addClass('current-page');
});

playlistlinks = $('.playlist-link');
playlisttracks = $('#playlists-tracks');

playlistlinks.on('click',function(event){
	if(playlisttracks.is(':visible')){
		playlisttracks.hide("slide",{direction:"left"});
	}
	playlistid = $(this).attr('data-value');
	//get playlist tracks
	//on success put them in ul
	playlisttracks.show("slide",{direction:"left"});
});

//sortable
//dragable

//on click on tracks list then the current playlist is this
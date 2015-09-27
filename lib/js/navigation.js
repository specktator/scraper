var visiblepage,
pages,
pagelinks = $('#sidebar ul.nav li a'),
pageEventRoutes = {
	'main':{},
	'playlists':{
		events:['playlistsLoadAll'],
		target:'playlists'
	},
	'artists':{},
	'genres':{},
	'charts':{}
};

pages = $('#main-wrapper div.page');
$.each(pages,function(){ //get the home page
	if($(this).is(':visible')){
		visiblepage = $('#'+$(this).attr('id'));
		$('#sidebar ul.nav li a[data-value="'+$(this).attr('id')+'"').addClass('current-page');
		console.log((visiblepage));
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

	pageId = $(this).attr('data-value');
	console.log("Femto => loading page: "+pageId);

	$('#'+pageId).show("slide",{direction:"down"},function(){
		var pageEvents = eval('pageEventRoutes.'+pageId+'.events')
		if(pageEvents){
			$.each(pageEvents,function(key,value){
				console.log('Femto = > event:'+value);
				if(value){
					$(audio).trigger(value,eval('pageEventRoutes.'+pageId+'.target'));
				}
			});
		}
			// $(audio).trigger('playlistsLoadAll','playlists');

		console.log("Femto => animation complete");
	});

	visiblepage = $('#'+pageId); //store the visible page to use it in search
	pagelinks.removeClass('current-page'); //removes class from all navigation menu links
	$(this).addClass('current-page'); // add class to the current navigation menu link
});

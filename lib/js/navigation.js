/*

Copyright 2015 Christos Dimas <specktator@totallynoob.com>

This file is part of femto.

femto is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

femto is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with femto.  If not, see <http://www.gnu.org/licenses/>.
Source: https://github.com/specktator/scraper

*/

navigation = function (local){
	var 
	pages,
	pagelinks = $('#sidebar ul.nav li a'),
	pageEventRoutes = {
		'main':{},
		'playlists':{
			events:['playlistsLoadAll'],
			target:'ul#playlists'
		},
		'artists':{
			events:['loadArtists'],
			target:'ul#artists'
		},
		'genres':{
			events:['loadGenres'],
			target:'ul#genres'
		},
		'charts':{
			events:['loadTrackCharts'],
			target:'ul#trackCharts'
		}
	};

	pages = $('#main-wrapper div.page');
	$.each(pages,function(){ //get the home page

		if( $(this).is(':visible') ){
			local.visiblepage = $('#'+$(this).attr('id'));
			$('#sidebar ul.nav li a[data-value="'+$(this).attr('id')+'"').addClass('current-page');
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
					console.log('Femto => event:'+value);
					if(value){
						$(local.audio).trigger(value,eval('pageEventRoutes.'+pageId+'.target'));
					}
				});
			}
				// $(audio).trigger('playlistsLoadAll','playlists');

			console.log("Femto => animation complete");
		});

		local.visiblepage = $('#'+pageId); //store the visible page to use it in search
		pagelinks.removeClass('current-page'); //removes class from all navigation menu links
		$(this).addClass('current-page'); // add class to the current navigation menu link
	});
	
	
	return local;
};
atto = new navigation(atto);
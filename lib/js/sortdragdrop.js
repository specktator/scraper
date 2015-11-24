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

// playlist a should have a div.title inline

var 
songsList = $('div.songs-list');

  $('.playlists-tracks > ul').sortable({
    // containment:'.playlists-tracks',
    items:"li",
    helper: "clone",
    update:function(event,ui){
      // removeTrackBtn.push(ui.item.find('.track-remove')[0]);
      // $(audio).trigger('trackRemove');
      femto.playlist.set('#'+$(this).attr('id'));
      queue.children('span.badge').text($('#queue_sortable').find('li').length);
      console.log('Femto => sortable updated');
    }


  }).disableSelection();
 
$('#queue_clear').on('click',function(){
  $('#queue_sortable').html('');
  queue.children('span.badge').text($('#queue_sortable').find('li').length);
});

$('#playlist li').draggable({
  // containment: 'a.track',
  connectToSortable: '#queue_sortable',
  // appendTo: '.playlists-tracks > ul',
  scroll:false,
  delay:1,
  // scope: "playlisttracks",
  helper: 'clone',
  // drag:enableQueue,
  start:function(event,ui){
        // enableQueue();
        $('#queue_sortable').css('border','1px solid #CB8B1E');
        ui.helper.addClass('playlist-item');
        ui.helper.attr('style','position:absolute;width:415px;min-height:80px;height:80px;background-color:#333;color:black;border:none;border:1px solid white;');
        ui.helper.find('a').attr('style','color:white;');
        ui.helper.removeClass('col-lg-2 col-md-2 col-sm-6 col-xs-6 active');
        ui.helper.addClass('list-group-item');
        img = ui.helper.find('img');
        img.addClass('img-thumbnail inline-img');
        img.prependTo(ui.helper);
        ui.helper.children("div.albumart").remove();
        ui.helper.children('a.track').before('<a href="#" class="control-play"><i class="icon-control-play"></i><i class="icon-control-pause" style="display:none;"></i></a>');
        ui.helper.children('a.track').html(' '+ui.helper.find("div.title").html());
        ui.helper.append('<a href="#" class="track-remove inline-controls"><i class="fa fa-remove"></i></a>');
        ui.helper.append('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on facebook"><i class="fa fa-facebook"></i></a>');
        ui.helper.append('<a href="#" class="track-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on twitter"><i class="fa fa-twitter"></i></a>');
        ui.helper.append('<a href="#" class="playlist-reorder inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Drag to change order"><i class="fa fa-reorder"></i></a>');
        $('body').append(ui.helper);
        // console.log(ui.helper.html());
    },
    stop:function(event,ui){
      ui.helper.attr('style','');
      $('#queue_sortable').css('border','');
    }
});





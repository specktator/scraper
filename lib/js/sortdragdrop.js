// remove inline style
// add classes inline-img img-thumbunail
//remove img from div and place as first:child of the li
// add i icon after img
// playlist a should have a div.title inline
// add rest of the links (inline-controls) floated etc etc
//highlight drop area

var 
songsList = $('div.songs-list');

  $('.playlists-tracks > ul').sortable({
    // containment:'.playlists-tracks',
    items:"li",
    helper: "clone",
    update:function(event,ui){
      removebtn.push(ui.item.find('.playlist-remove')[0]);
      removebtn.on('click',function(event){
        $(this).parents('li').remove();
      });
      femto.playlist('#queue_sortable');
    }


  }).disableSelection();
 

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
        ui.helper.attr('style','position:absolute;width:auto;height:auto;background-color:white;');
        ui.helper.removeClass('col-lg-2 col-md-2 col-sm-6 col-xs-6 active');
        ui.helper.addClass('list-group-item');
        img = ui.helper.find('img')
        img.addClass('img-thumbnail inline-img');
        img.prependTo(ui.helper);
        ui.helper.children("div.albumart").remove();
        ui.helper.children('a.track').before('<a href="#" class="control-play"><i class="icon-control-play"></i><i class="icon-control-pause" style="display:none;"></i></a>');
        ui.helper.children('a.track').html(' '+ui.helper.find("div.title").html());
        ui.helper.append('<a href="#" class="playlist-remove inline-controls"><i class="fa fa-remove"></i></a>');
        ui.helper.append('<a href="#" class="playlist-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on facebook"><i class="fa fa-facebook"></i></a>');
        ui.helper.append('<a href="#" class="playlist-share inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Share on twitter"><i class="fa fa-twitter"></i></a>');
        ui.helper.append('<a href="#" class="playlist-reorder inline-controls" data-toggle="tooltip" data-placement="top" title="" data-original-title="Drag to change order"><i class="fa fa-reorder"></i></a>');
    },
    stop:function(event,ui){
      ui.helper.attr('style','');
      $('#queue_sortable').css('border','');
    }
});





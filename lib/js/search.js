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

function search(local){
  var tracks = $('#playlist li > a');
  var input = $('#search input');
  var needle;
  var visiblepageid;

  input.on('keyup', function (event) {

    if(local.visiblepage){
      searchableUl = local.visiblepage.find('.page-row > div > ul');
      visiblepageid = local.visiblepage.attr('id');
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
        searchableLinks = $(value).find('li > a.playlist-link, li > a.track');
        $.each(searchableLinks, function (key, value) {
          if(visiblepageid != 'main'){
            if (regex.test($(this).html())) {
              $(this).parent().show();
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

  return local;

}

atto = search(atto);
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
$('[data-toggle="tooltip"]').tooltip();
 
$('ul#playlist li').hover(
    function(){
        if(!$(this).hasClass('active')){
            $(this).find('.overlay').slideDown(250); //.fadeIn(250)
        }
    },
    function(){
        if(!$(this).hasClass('active')){
            $(this).find('.overlay').slideUp(250); //.fadeOut(205)
        }
    }
);
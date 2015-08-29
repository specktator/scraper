$("[rel='tooltip']").tooltip();    
 
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
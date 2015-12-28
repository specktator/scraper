
femto.screen = {
  
  enable:function(state){
    if(state){
      this.box();
    }
  },
    
  data:"Waiting data ...",
    
  box:function(){
    $("body").append('<div id="screenbox"></div>');
    $("#screenbox").css({
      "background-color":"white",
      "font-family":"Courier,Georgia,Serif",
      "font-size":"12px",
      "color":"black",
      "position":"absolute",
      "top":"40%",
      "left":"40%",
      "max-height":"60%",
      "border":"1px solid gray",
      "border-radius":"3px",
      "padding":"30px"
    });
    $('#screenbox').text(femto.screen.data);
  },
    
  update:function(data){
    var html='';
    for(var d in data){
      html += d+": "+data[d]+"\n";
    }
                 
    $("#screenbox").html("<pre>"+html+"</pre>");
  }

}

femto.screen.enable(true);

$(document).on("keydown",function(event){
  femto.screen.update({"which":event.which,"key":event.key,"keyCode":event.keyCode});
});

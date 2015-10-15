
var elements = [
  '<input class="form-control" placeholder="ddg search for img" type="text">',
  '<div id="results">'
],
q, counting, ddgResults;

$('body').append(elements);
$('input').focus();

$('input').on('keyup', function (event) {
  if (event.which == 13) {
    q = ($('input').val());
    ddg(q);
  }
});

function ddg(q) {
  $.ajax({
    type:'GET',
    url:'https://api.duckduckgo.com/',
    data: {'q': q, format:'json',skip_disambig:1},
    dataType:'jsonp',
    error: function(data){console.log("Femto => error: ",data);}
  }).done(function (data) {
    if (data) {
//       $('#results').text(JSON.stringify(data));
      ddgResults = data;
      results = findMusicTopic(data);
      scores = wordCounting( results );
      relevantTopic = chooseRelevantTopic(results,scores);
      imgURL = findImage(relevantTopic,data);
      $('body').append('<img src="'+imgURL+'">');
      console.log(scores,relevantTopic,imgURL);
    } else {
      alert('empty response');
    }
  });
}

function findMusicTopic(results){
  for(var index in results.RelatedTopics){ 
    try{ 
      if(results.RelatedTopics[index].Name === "Music"){ 
        console.log(results.RelatedTopics[index]);
        return results.RelatedTopics[index].Topics;
      }else{
        return results.RelatedTopics;
      }
    } catch(err){
//       console.log(err.message);
      return false;
    }
  }
}

function wordCounting(topicsArray){
  if(topicsArray){
    kwords = {
    //   title:{
    //     kw: /(morning)|(glory)|(oasis)/gi,
    //     count:0
    //   },
      songs:{
        kw:/song/gi,
      },
      bands:{
        kw:/band/gi,
      },
      singers:{
        kw:/singer/gi,
      },
      songwriters:{
        kw:/songwriter/gi,
      },
      musicians:{
        kw:/musician/gi,
      },
      albums:{
        kw:/album/gi,
      }
    };

    var counted = [];
    for(var ddg in topicsArray){

       topicid = ddg;
       counted[topicid] = {"topicid": topicid,score: 0};

      for(var word in kwords){

        try{
          counted[topicid].score = counted[topicid].score + topicsArray[ddg].Text.match(kwords[word].kw).length;
    //       console.log(word,string.Topics[ddg].Text.match(kwords[word].kw).length);
        }catch(err){
          console.log(err.message);
        }


      }
    //   console.log(topicid, string.Topics[ddg].Text);

    }

    return counted;
  }else{
    console.log("Femto => wordCounting: ",topicsArray);
    return false;
  }
}


function chooseRelevantTopic(results,scores){
  if(results && scores){
    var max = 0;
    var topicid = null;
    for(var index in scores){
      if( scores[index].score > max){
        max =  scores[index].score;
        topicid = scores[index].topicid;
      }
    }
    return results[topicid];
  }else{
    console.log(results,scores);
  }
}

function findImage(topicsArray,data){
  if(topicsArray){
    if(topicsArray.Icon.URL){
     return topicsArray.Icon.URL;
    }else{
      return fallBack(data);
    }
  }else{
    return fallBack(data);
  }
  
  function fallBack(data){ 
    if(data.Image){
     return data.Image;
    }else{
      return false;
    }
  }
  
}
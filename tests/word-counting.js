/*
 * This is a JavaScript Scratchpad.
 *
 * Enter some JavaScript, then Right Click or choose from the Execute Menu:
 * 1. Run to evaluate the selected text (Ctrl+R),
 * 2. Inspect to bring up an Object Inspector on the result (Ctrl+I), or,
 * 3. Display to insert the result in a comment after the selection. (Ctrl+L)
 */

stringaki = "<a href=\"https://duckduckgo.com/Morning_Glory_(Oasis_song)\">\"Morning Glory\" (Oasis song)</a>A song by the English rock band Oasis, written by Noel Gallagher, and released on the band's...";

string = {"Topics" : [
            {
               "Result" : "<a href=\"https://duckduckgo.com/Morning_Glory_(Oasis_song)\">\"Morning Glory\" (Oasis song)</a>A song by the English rock band Oasis, written by Noel Gallagher, and released on the band's...",
               "Icon" : {
                  "URL" : "https://duckduckgo.com/i/4eb2a609.jpg",
                  "Height" : "",
                  "Width" : ""
               },
               "FirstURL" : "https://duckduckgo.com/Morning_Glory_(Oasis_song)",
               "Text" : "\"Morning Glory\" (Oasis song)A song by the English rock band Oasis, written by Noel Gallagher, and released on the band's..."
            },
            {
               "Result" : "<a href=\"https://duckduckgo.com/Morning_Glory_(singles_box)\">Morning Glory (singles box)</a> A five disc gold EP consisting of the four singles from the album.",
               "Icon" : {
                  "URL" : "",
                  "Height" : "",
                  "Width" : ""
               },
               "FirstURL" : "https://duckduckgo.com/Morning_Glory_(singles_box)",
               "Text" : "Morning Glory (singles box) A five disc gold EP consisting of the four singles from the album."
            },
            {
               "Result" : "<a href=\"https://duckduckgo.com/goodbye_and_Hello\">\"Morning Glory\" (Tim Buckley song)</a>The second album by Tim Buckley, released in August 1967, recorded in Los Angeles, California in...",
               "Icon" : {
                  "URL" : "https://duckduckgo.com/i/d2889ec1.jpg",
                  "Height" : "",
                  "Width" : ""
               },
               "FirstURL" : "https://duckduckgo.com/goodbye_and_Hello",
               "Text" : "\"Morning Glory\" (Tim Buckley song)The second album by Tim Buckley, released in August 1967, recorded in Los Angeles, California in..."
            },
            {
               "Result" : "<a href=\"https://duckduckgo.com/Morning_Glory_(Tim_Buckley_album)\">Morning Glory (Tim Buckley album)</a>A compilation album by Tim Buckley.",
               "Icon" : {
                  "URL" : "https://duckduckgo.com/i/0c41a17c.jpg",
                  "Height" : "",
                  "Width" : ""
               },
               "FirstURL" : "https://duckduckgo.com/Morning_Glory_(Tim_Buckley_album)",
               "Text" : "Morning Glory (Tim Buckley album)A compilation album by Tim Buckley."
            },
            {
               "Result" : "<a href=\"https://duckduckgo.com/Morning_Glory%3A_The_Tim_Buckley_Anthology\">Morning Glory: The Tim Buckley Anthology</a> A compilation album by Tim Buckley. The two cds give an overview of Tim Buckley's career.",
               "Icon" : {
                  "URL" : "https://duckduckgo.com/i/de5ea9a8.jpg",
                  "Height" : "",
                  "Width" : ""
               },
               "FirstURL" : "https://duckduckgo.com/Morning_Glory%3A_The_Tim_Buckley_Anthology",
               "Text" : "Morning Glory: The Tim Buckley Anthology A compilation album by Tim Buckley. The two cds give an overview of Tim Buckley's career."
            },
            {
               "Result" : "<a href=\"https://duckduckgo.com/Morning_Glory_(Bonnie_Pink_song)\">\"Morning Glory\" (Bonnie Pink song)</a>\"Morning Glory\" is Bonnie Pink's second digital single.",
               "Icon" : {
                  "URL" : "https://duckduckgo.com/i/6760f93a.jpg",
                  "Height" : "",
                  "Width" : ""
               },
               "FirstURL" : "https://duckduckgo.com/Morning_Glory_(Bonnie_Pink_song)",
               "Text" : "\"Morning Glory\" (Bonnie Pink song)\"Morning Glory\" is Bonnie Pink's second digital single."
            },
            {
               "Result" : "<a href=\"https://duckduckgo.com/Morning_Glory_(band)\">Morning Glory (band)</a>An American punk band from New York.",
               "Icon" : {
                  "URL" : "https://duckduckgo.com/i/764c914b.jpg",
                  "Height" : "",
                  "Width" : ""
               },
               "FirstURL" : "https://duckduckgo.com/Morning_Glory_(band)",
               "Text" : "Morning Glory (band)An American punk band from New York."
            }
         ],
         "Name" : "Music"
      };

kwords = {
//   title:{
//     kw: /(morning)|(glory)|(oasis)/gi,
//     count:0
//   },
  songs:{
    kw:/song/gi,
    count:0
  },
  band:{
    kw:/band/gi,
    count:0
  },
  singer:{
    kw:/singer/gi,
    count:0
  }
};

for(var ddg in string.Topics){
  for(var word in kwords){
    topicid = ddg;
    console.log(word,string.Topics[ddg].Text.match(kwords[word].kw).length);
    
//  counted[topicid].topicid = topicid;
  }
  console.log(topicid, string.Topics[ddg].Text);

}


// console.log(kwords);
/*
Exception: TypeError: string.Topics[ddg].Text.match(...) is null
@Scratchpad/2:109:22
*/
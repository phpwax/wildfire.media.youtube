jQuery(document).ready(function(){

  jQuery(window).bind("media.wildfireyoutubefile.preview", function(e, row, preview_container){

    var str = "";

    var html = row.html();

    if(html.indexOf("<iframe") >= 0){
      var h = parseInt(row.find("iframe").attr("height"),10),
          w = parseInt(row.find("iframe").attr("width"),10),
          r = 200/w
          ;

      if(h && w) str += html.replace(h, Math.round(h*r)).replace('"'+w+'"',200);
    }
    else str += html;

    console.log(str);
    preview_container.html(str);

  });

});
jQuery(document).ready(function(){

  jQuery(window).bind("media.wildfireyoutubefile.preview", function(e, row, preview_container){
    var yt_iframe = jQuery("<iframe>"),
        permalink = row.find("img").data("permalink")+'?rel=0&wmode=opaque&controls=0';

    yt_iframe.attr({"src":permalink,"width":200,"height":Math.floor(200/1.778),"frameborder":0,"allowfullscreen":true});

    preview_container.html(yt_iframe);

  });

});
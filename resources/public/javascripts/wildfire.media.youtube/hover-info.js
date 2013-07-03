jQuery(document).ready(function(){

  jQuery(window).bind("media.wildfireyoutubefile.preview", function(e, row, preview_container){
    var yt_iframe = jQuery("<iframe>"),
        data = row.find("img").data(),
        html = '';

    html = yt_iframe.attr(data).wrap("<div>").parent().html();
    preview_container.html(html);

  });

});
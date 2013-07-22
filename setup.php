<?
CMSApplication::register_module("media.youtube", array("hidden"=>true, "plugin_name"=>"wildfire.media.youtube"));
CMSApplication::register_asset("wildfire", "js", "wildfire.media.youtube");
CMSApplication::register_asset("wildfire", "css", "wildfire.media.youtube");

WildfireMedia::$classes[] = 'WildfireYoutubeFile';
?>
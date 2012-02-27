<?
CMSApplication::register_module("media.youtube", array("hidden"=>true, "plugin_name"=>"wildfire.media.youtube", 'assets_for_cms'=>true));
WildfireMedia::$classes[] = 'WildfireYoutubeFile';
?>
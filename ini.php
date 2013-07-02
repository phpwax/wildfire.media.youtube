<?
AutoLoader::register_assets("javascripts/wildfire.media.youtube",__DIR__."/resources/public/javascripts/wildfire.media.youtube/", "/*.js");
AutoLoader::$plugin_array[] = array("name"=>"wildfire.media.youtube","dir"=>__DIR__);
AutoLoader::add_plugin_setup_script(__DIR__."/setup.php");
?>
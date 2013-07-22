<?
AutoLoader::register_assets("javascripts/wildfire.media.youtube",__DIR__."/resources/public/javascripts/wildfire.media.youtube", "/*.js");
AutoLoader::register_assets("stylesheets/wildfire.media.youtube",__DIR__."/resources/public/stylesheets/wildfire.media.youtube", "/*.css");
AutoLoader::register_assets("images/wildfire.media.youtube",__DIR__."/resources/public/images/wildfire.media.youtube", "/*.png");
AutoLoader::$plugin_array[] = array("name"=>"wildfire.media.youtube","dir"=>__DIR__);
AutoLoader::add_plugin_setup_script(__DIR__."/setup.php");
?>
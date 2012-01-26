<?
class WildfireYoutubeFile{

  public static $hash_length = 6;
  public static $name = "Youtube";
  public $scope = "http://gdata.youtube.com";

  public function includes(){
    set_include_path(PLUGIN_DIR."wildfire.media.youtube/ZendGdata/library/");
    require_once PLUGIN_DIR.'wildfire.media.youtube/ZendGdata/library/Zend/Loader.php';
    Zend_Loader::loadClass('Zend_Gdata_YouTube');
    Zend_Loader::loadClass('Zend_Gdata_AuthSub');
    Zend_Loader::loadClass('Zend_Gdata_App_Exception');
  }

  public function set($media_item){
    return false;
  }

  //should return a url to display the item
  public function get($media_item, $width=false, $return_obj = false){

  }

  //this will actually render the contents of the image
  public function show($media_item, $size=false){
    $data = $this->get($media_item, $size, true);
    header("Location: ".$data['source']);

  }
  //generates the tag to be displayed - return generic icon if not an image
  public function render($media_item, $size, $title="preview"){

  }


  public function sync_locations(){
    return array('1'=>array('value'=>'ALL', 'label'=>'All Videos'));
  }

  public function sync($location){
    $this->includes();
    $ids = array();
    $info = array();
    $class = get_class($this);

    $httpClient = Zend_Gdata_AuthSub::getHttpClient(Config::get('youtube/token'));
    $yt = new Zend_Gdata_YouTube($httpClient, 0, 0, Config::get('youtube/developer_key'));
    foreach($yt->getUserUploads('officialsubaruuk') as $video){
      $source = str_replace("http://gdata.youtube.com/feeds/api/videos/", "", $video->getID()->text);
      $model = new WildfireMedia;
      if($found = $model->filter("media_class", $class)->filter("source", $source)->first()) $found->update_attributes(array('status'=>1));
      else $found = $model->update_attributes(array('source'=>$video->id,
                                                'uploaded_location'=>$video->getID()->text,
                                                'status'=>1,
                                                'media_class'=>$class,
                                                'media_type'=>self::$name,
                                                'ext'=>"",
                                                'content'=>$video->getVideoDescription(),
                                                'file_type'=>"video",
                                                'title'=>$video->getTitle()->text,
                                                'hash'=> md5(time()),
                                                'sync_location'=>$location
                                                ));
      $ids[] = $found->primval;
      $info[] = $found;
      //categorisation
      foreach($video->getVideoTags() as $tag){
        $model = new WildfireCategory;
        if(($tag = trim($tag)) && $tag){
          if($cat = $model->filter("title", $tag)->first()) $found->categories = $cat;
          else $found->categories = $model->update_attributes(array('title'=>$tag));
        }
      }
    }
    $media = new WildfireMedia;
    foreach($ids as $id) $media->filter("id", $id, "!=");
    foreach($media->filter("status", 1)->filter("media_class", $class)->filter("sync_location", $location)->all() as $missing) $missing->update_attributes(array('status'=>-1));
    return $info;
  }


}
?>
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
    $httpClient = Zend_Gdata_AuthSub::getHttpClient(Config::get('youtube/token'));
    return new Zend_Gdata_YouTube($httpClient, 0, 0, Config::get('youtube/developer_key'));
  }
  //doesnt upload at the moment
  public function set($media_item){
    return false;
  }

  //should return a url to display the item
  public function get($media_item, $width=false, $return_obj = false){
    $yt = $this->includes();
    try{
      $vid = $yt->getVideoEntry($media_item->source);
    }catch(Exception $e){
      WaxLog::log("error", "[WildfireYoutubeFile] error getting video $media_item->source");
      return false;
    }
    
    $data= $media_item->row;
    foreach($vid->mediaGroup->content as $content) if($content->type === 'application/x-shockwave-flash') $data['url'] = $content->url;
    if($return_obj) return $data;
    else if(!$width) return $data['url'];
    else return "http://www.youtube-nocookie.com/embed/".$media_item->source;
  }

  //this will actually render the contents of the image
  public function show($media_item, $size=false){
    $data = $this->get($media_item, $size, true);
    header("Location: ".$data['url']);

  }
//generates the tag to be displayed - return generic icon if not an image
  public function render($media_item, $size, $title="preview", $class="attached_youtube", $min_size=200){
    if(!($url = $this->get($media_item, $size))) return;
    if($size) $w_h = " width='$size' height='".floor($size/1.778)."' ";
    else $w_h = " width='560' height='315' ";
    return '<iframe '.$w_h.'src="'.$url.'?rel=0&wmode=opaque'.(($size < $min_size) ? "&controls=0": "").'" frameborder="0" allowfullscreen></iframe>';
  }

  public function sync_locations(){
    return array('1'=>array('value'=>'ALL', 'label'=>'All Videos'));
  }

  public function sync($location){
    $yt = $this->includes();
    $start = 0;
    $videos = array();
    $total = false;
    while($start < $total || $total === false){
      $query = $yt->newVideoQuery();
      $query->maxResults = 50;
      $query->startIndex = $start+1;
      $query->setAuthor(Config::get("youtube/username"));
      $videoFeed = $yt->getVideoFeed($query);
      $total = $videoFeed->getTotalResults()->text;
      foreach($videoFeed as $video) $videos[] = $video;
      $start = count($videos);
    }

    $ids = array();
    $info = array();
    $class = get_class($this);

    foreach($videos as $video){
      $source = $video->getVideoId();
      $model = new WildfireMedia;
      if($found = $model->filter("media_class", $class)->filter("source", $source)->first()) $found->update_attributes(array('status'=>1));
      else $found = $model->update_attributes(array('source'=>$source,
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
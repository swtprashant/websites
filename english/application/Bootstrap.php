<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initDatabase(){
				
	}

	protected function _initAutoload()
    {
       	$oldurl = strtolower($_SERVER['REQUEST_URI']);
		//getOldURL($oldurl);
		if (strpos($oldurl, 'postdetail') == '')
		{
			$baseUrl = basename($oldurl);
			$mySQL = "";
			$mySQL .= "SELECT post_id, english_url";
			$mySQL .= " FROM `post` WHERE `english_url` = '".$baseUrl."'";
			$mySQL .= " LIMIT 1";
			//echo $mySQL;
			//exit;
			$rsTemp = Zend_Registry::get("db")->fetchRow($mySQL);
			
			if ($rsTemp){
				//$this->_flashMessenger->addMessage($return['error']);
				//$this->_redirect('/postdetail/index/id/'.$rsTemp['post_id'].'/'.$rsTemp['english_url']);
				header("Location: " . 'http://english.liveindia.live/postdetail/index/id/'.$rsTemp['post_id'].'/'.$rsTemp['english_url'].'/');
				exit;
			} else {
				//header("Location: http://liveindia.live/");
				//exit;
			}
		}
		
		//exit;
	    $autoloader = new Zend_Loader_Autoloader_Resource(array(
            'namespace' => 'Default',
            'basePath' => APPLICATION_PATH,
            'resourceTypes' => array(
                'form' => array(
                    'path' => 'forms',
                    'namespace' => 'Form',
                ),
                'model' => array(
                    'path' => 'models',
                    'namespace' => 'Model',
                ),
            )
        ));
		//print_r($autoloader);
		//exit;
        return $autoloader;
    }

	
	protected function _initLayout(){
		$layout = explode('/', $_SERVER['REQUEST_URI']);
		if(in_array('admin', $layout)){
			$layout_dir = 'admin';
		} else {
			$layout_dir = 'site';
		}
		$options = array('layout'   => 'layout','layoutPath' => APPLICATION_PATH . "/layouts/scripts/".$layout_dir,);
		Zend_Layout::startMvc($options);
	}

	protected function _initFrontController() {
		$front = Zend_Controller_Front::getInstance();
		$front->setControllerDirectory(
		array(
		'default'=>APPLICATION_PATH.'/modules/default/controllers',
		'admin'=>APPLICATION_PATH.'/modules/admin/controllers'
		));
		
		$response = new Zend_Controller_Response_Http;
		$response->setHeader('Content-Type', 'text/html; charset=UTF-8', true);
		$front->setResponse($response);
		
		$router = $front->getRouter();
				
	
	//-------------------add subdomain------------------------------------
	
	/*$hostnameRoute = new Zend_Controller_Router_Route_Hostname(":bts.instabus.com");
	
	$router->addDefaultRoutes();
	foreach ($router->getRoutes() as $key=>$route) {
	  $router->addRoute('hostname' . $key, $hostnameRoute->chain($route));
	}*/
		
	//----------------end subdomain ----------------------
	
		$router->addRoute(
		'sitemap',
			new Zend_Controller_Router_Route('sitemap.xml', array('controller'=>'sitemap','action'=>'xml','module'=>'default'))
		);
		
		$front->setRouter($router);
		
		$front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler());
		
		return $front;
	}
	
	protected function _initConstants()
	{
		define('PRODUCT_DIR_PATH',BASE_PATH.'/public/products');
		define('POSTIMAGE_DIR_PATH',BASE_PATH.'/public/postimages');
		define('LOGO_DIR_PATH',BASE_PATH.'/public/sitelogo');
		define('USER_DIR_PATH',BASE_PATH.'/public');
		define('GALLERY_DIR_PATH',BASE_PATH.'/public/gallery');
		define('MEDIA_DIR_PATH',BASE_PATH.'/public/mediafiles');
		define('ADS_DIR_PATH',BASE_PATH.'/public/adimages');
		
	}
	
	function getOldURL($url)
	{
		if (strpos($url, 'postdetail') == '')
		{
			$baseUrl = basename($url);
			$mySQL = "";
			$mySQL .= "SELECT post_id, english_url";
			$mySQL .= " FROM `post` WHERE `english_url` = '".$baseUrl."'";
			$mySQL .= " LIMIT 1";
			//echo $mySQL;
			//exit;
			$rsTemp = Zend_Registry::get("db")->fetchRow($mySQL);
			
			if ($rsTemp){
				//$this->_flashMessenger->addMessage($return['error']);
				$this->_redirect('/postdetail/index/id/'.$rTemp['post_id'].'/'.$rTemp['english_url']);
				exit;
			}
		}
	}
}



function GetBetween($var1="",$var2="",$pool)
{
	$temp1 = strpos($pool,$var1)+strlen($var1);
	$result = substr($pool,$temp1,strlen($pool));
	$dd=strpos($result,$var2);
	if($dd == 0){
	$dd = strlen($result);
	}
	return substr($result,0,$dd);
}

function getPrevNextRec($action, $post_id, $city, $baseUrl, $position)
{
	if($action == 'Prev'){
		$qry = ' AND post_id < '.$post_id.' ORDER BY post_id DESC';
	} else {
		$qry = ' AND post_id > '.$post_id;
	}
	$mySQL = "";
	$mySQL .= "SELECT post_id, CONCAT(SUBSTRING_INDEX(headline, ' ', 8), '...') AS headline, postType, english_url";
	$mySQL .= " FROM `post` WHERE `status` = '1'";
	//$mySQL .= " AND `city` = '".$city."'";
	$mySQL .= $qry;
	$mySQL .= " LIMIT 1";
	//echo $mySQL;
	//exit;
	$rsTemp = Zend_Registry::get("db")->fetchRow($mySQL);
	//print_r($res);die();
	
	
	if ($rsTemp){
		if($position == 'Bottom'){
			if($action == 'Prev'){
				return '<a class="more" href="'.$baseUrl.'/postdetail/index/id/'.$rsTemp['post_id'].'/'.$rsTemp['english_url'].'"><i class="fa fa-angle-double-left"></i> '. $rsTemp['headline'].'</a>';
			} else {
				return '<a class="more" href="'.$baseUrl.'/postdetail/index/id/'.$rsTemp['post_id'].'/'.$rsTemp['english_url'].'">'. $rsTemp['headline'].' <i class="fa fa-angle-double-right"></i></a>';
			}
		} elseif($position == 'OnImage1'){ 
			if($action == 'Prev'){
				return '<a class="more" href="'.$baseUrl.'/postdetail/index/id/'.$rsTemp['post_id'].'/'.$rsTemp['english_url'].'"><div class="icon_prev control"></div></a>';
			} else {
				return '<a class="more" href="'.$baseUrl.'/postdetail/index/id/'.$rsTemp['post_id'].'/'.$rsTemp['english_url'].'"><div class="icon_next control"></div></a>';
			}
		} else {
			if($action == 'Prev'){
				return '<a class="more" href="'.$baseUrl.'/postdetail/index/id/'.$rsTemp['post_id'].'/'.$rsTemp['english_url'].'" title="'.insertReplacementTag($rsTemp['headline']).'"><div id="prevOnscreen" class="icon_prev_onscreen"></div></a>';
			} else {
				return '<a class="more" href="'.$baseUrl.'/postdetail/index/id/'.$rsTemp['post_id'].'/'.$rsTemp['english_url'].'" title="'.insertReplacementTag($rsTemp['headline']).'"><div id="nextOnscreen" class="icon_next_onscreen"></div></a>';
			}
		}
	}
}

function getMetaDetail(){
	$sptid = explode("/", $_SERVER['REQUEST_URI']);
	
	if ($_SERVER['HTTP_HOST'] == 'localhost') {
		$id = $sptid[5];
	} else {
		if (count($sptid) > 4) {
			$id = $sptid[4];
		}
	}
	//echo 'ID = ' . $id . '<br>';
	
	$pageurl = strtolower($_SERVER['REQUEST_URI']);
	//echo 'PAGE URL = ' . $pageurl . '<br>';
	if ( strpos($pageurl, 'postdetail')){
		//echo 'I am here - post Detail <br>';
		$mySQL = "";
		$mySQL .= "SELECT `post_id`";
		$mySQL .= ", `headline`";
		$mySQL .= ", `user_name`";
		$mySQL .= ", `meta_title`";
		$mySQL .= ", `meta_keyword`";
		$mySQL .= ", `meta_desc`";
		$mySQL .= ", `intro`";
		$mySQL .= ", `image`";
		$mySQL .= ", `content`";
		$mySQL .= " FROM `post`";
		$mySQL .= " WHERE post_id = '".$id."'";
		//echo $mySQL;
		//exit;
		$rsTemp = Zend_Registry::get("db")->fetchRow($mySQL);
		
		if ($rsTemp){
			$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			if (!empty($rsTemp['meta_title'])){
				$title = $rsTemp['meta_title'];
			} else {
				$title = $rsTemp['headline'];
			}
		
			if (!empty($rsTemp['meta_desc'])){
				$metaDescription = $rsTemp['meta_desc'];
			} else {
				$metaDescription = substr($rsTemp['intro'], 0, 220);
			}
		
			if (!empty($rsTemp['meta_keyword'])){
				$metaKeywords = $rsTemp['meta_keyword'];
			} else {
				$metaKeywords = substr($rsTemp['intro'], 0, 200) . 'Added By ' . $rsTemp['user_name'] ;
			}
		}
	} else if ( strpos($pageurl, 'category')){
		//echo 'I am here - category <br>';
		$mySQL = "";
		$mySQL .= "SELECT `category_id`";
		$mySQL .= ", `category`";
		$mySQL .= " FROM `category`";
		$mySQL .= " WHERE category_id = '".$id."'";
		//echo $mySQL;
		//exit;
		$rsTemp = Zend_Registry::get("db")->fetchRow($mySQL);
		
		if ($rsTemp){
			$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$title = $rsTemp['category'] . " English News, Latest ". $rsTemp['category'] ." English Samachar (Khabar) from World";
			$metaDescription = "Live India - Get Live ". $rsTemp['category'] ." English News from India and Around the World, Read and share ". $rsTemp['category'] ." Latest and interesting English Samachar (Khabar) with your Social Friends, Best Selected ". $rsTemp['category'] ." English Khabar.";
			$metaKeywords = $rsTemp['category'] ." Khabar, ". $rsTemp['category'] ." English Khabar, ". $rsTemp['category'] ." Samachar, ". $rsTemp['category'] ." Latest Khabar, ". $rsTemp['category'] ." India Khabar, ". $rsTemp['category'] ." World Khabar Live, Live English News";
		}
	} else {
		//echo 'I am here - home page <br>';
		$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$title = "Live News India, English Samachar India, Latest World, Politics, Entertainment News";
		$metaDescription = "Live India- Get Live English News from India and around the world, Politics, Entertainment, Crime, Health, Society, Music, Celebs, Women, Fashion and Trending News from India, Read Live India English News and share with Friends";
		$metaKeywords = "Live News India, English Samachar India, World News Live, Politics English News, Entertainment News";
	}
	//echo 'I am here - - - ';
		
?>
	<title><?php echo $title;?></title>
    <meta name="description" content="<?php echo $metaDescription;?>" />
    <meta name="keywords" content="<?php echo $metaKeywords;?>" />
    
    <meta property="og:title" content="<?php echo $title;?>" />
    <meta property="og:description" content="<?php echo $metaDescription;?>"/>
    <meta name="twitter:description" content="<?php echo $metaDescription;?>">
        
    <meta property="og:site_name" content="Live India" >
    <meta property="og:type" content="website">
    <meta property="og:locale" content="Hindi" />
    <meta property="og:url" content="<?php echo $url;?>">
    <meta property="og:image" content="200x20 pxl fb image or logo" >
    
    <meta name="twitter:site" value="@<?php echo $_SERVER['HTTP_HOST'];?>">
    <meta name="twitter:card" content="<?php echo $metaDescription;?>">
    <meta name="twitter:url" content="<?php echo $url;?>">
    <meta name="twitter:title" content="<?php echo $title;?>">
    <meta name="twitter:description" content="<?php echo $metaDescription;?>">
    <meta name="twitter:image" content="http://<?php echo $GLOBALS['baseUrl']?>/public/images/logo-bar.png">
    <link rel="publisher" href="<?php echo $url;?>" />
    
    <meta itemprop="name" content="<?php echo $title;?>">
    <meta itemprop="description" content="<?php echo $metaDescription;?>">
    <meta itemprop="image" content="http://<?php echo $GLOBALS['baseUrl']?>/public/images/logo-bar.png">
    <meta itemprop="publisher" content="<?php echo $url;?>" />
    <meta itemprop="url" content="<?php echo $url;?>" />
    <meta itemprop="editor" content="<?php echo $_SERVER['HTTP_HOST'];?>" />
    <meta itemprop="headline" content="<?php echo $title;?>" />
    <meta itemprop="inLanguage" content="Hindi" />
    <meta itemprop="sourceOrganization" content="Live India" />
    <meta itemprop="keywords" content="<?php echo $metaKeywords;?>" />
<?php
}

function getBreakingnews(){
	$sql = "";
	$sql .= " SELECT breaking_id AS id, breakingnews, 'BN' AS source, '' AS link FROM `breaking_news` WHERE DATE(created) = '".date('Y-m-d')."'";
	$sql .= " UNION ALL ";
	$sql .= " SELECT post_id AS id, headline AS breakingnews, 'MN' AS source, CONCAT('".$GLOBALS['baseUrl']."','/postdetail/index/id/', post_id, '/', english_url) AS link FROM `post` WHERE status = 1 AND `priority` IN (1,2,3,4,5) AND DATE(created) = '".date('Y-m-d')."' ORDER BY id DESC";
	//echo $sql;
	//exit;
	$rsBreaking = Zend_Registry::get("db")->fetchAll($sql);
	
	if($rsBreaking){
?>
		<div class="" style="float:left; width:95px;"><strong>Breaking News</strong></div>
        <div class="floatright" style="width: 550px;">
        	<marquee>
<?php
				foreach($rsBreaking as $rsBreakingVal){
?>					<span style="margin:0 20px 0 20px;"><?php echo insertReplacementTag($rsBreakingVal['breakingnews']);?></span> | 
<?php
				}
?>
			</marquee>
        </div>
<?php
	}
}

function getPriority()
{
	//echo 'REQUEST_URI = ' . basename($_SERVER['REQUEST_URI']) .'<br>';
	if (basename($_SERVER['REQUEST_URI']) == 'liveindialive'
		|| basename($_SERVER['REQUEST_URI']) == 'liveindia.live'
		|| basename($_SERVER['REQUEST_URI']) == 'liveindia.press'
		|| basename($_SERVER['REQUEST_URI']) == ''
		//|| strpos(strtolower($_SERVER['REQUEST_URI']), 'liveindia.live/index/') == ''
    ){
		$mySQL = "";
		//$mySQL = "SELECT post_id, headline, postType, english_url, image, thumb_image, CONCAT(SUBSTRING_INDEX(intro, ' ', 30), '') AS intro, CONCAT(SUBSTRING_INDEX(intro, ' ', 8), '') AS intro1 FROM `post` WHERE `city` = '".$GLOBALS['city']."' AND `priority` = '".$priority."' AND `status` = '1'"; // ORDER BY editedOn DESC";
		$mySQL = "SELECT post_id, headline, postType, english_url, image, thumb_image, CONCAT(SUBSTRING_INDEX(intro, ' ', 30), '') AS intro, CONCAT(SUBSTRING_INDEX(intro, ' ', 8), '') AS intro1, videoURL FROM `post` WHERE `priority` IN (1,2,3) AND `status` = '1' ORDER BY priority";
		//echo $mySQL;
		//exit;
		$rsTemp1 = Zend_Registry::get("db")->fetchAll($mySQL);
		//print_r($res);die();
		
		if ($rsTemp1)
		{
?>
            <link type="text/css" href="<?php echo $GLOBALS['baseUrl'];?>/public/bannerslider/css/main.min.css" rel="stylesheet" media="all">
            <script type="text/javascript" src="<?php echo $GLOBALS['baseUrl'];?>/public/bannerslider/js/jquery-1.8.0.min.js"></script>
            <script type="text/javascript" src="<?php echo $GLOBALS['baseUrl'];?>/public/bannerslider/js/jquery.easing.1.3.min.js"></script>
            
            <div class="topSlides ga-block" data-ga_action="Home Scroller" id="liveINDIASlider">
                <div class="loader"><img src="<?php echo $GLOBALS['baseUrl'];?>/public/bannerslider/images/loading-anim2.gif"></div>
                <div class="wrapper blk slidesSet">
                    <div class="controls">
                        <div class="nav">
                            <a class="prv" href="javascript:;"><span>Previous</span></a>
                            <a class="nxt" href="javascript:;"><span>Next</span></a>        
                        </div>
                        <div class="slidesViews">
                            <a class="slide selected" href="javascript:;"><span>Slide View</span></a> 
                            <a class="grid" href="javascript:;"><span>Grid View</span></a>        
                        </div>
                    </div>
                    <ul class="slides" style="margin-left: -2940px; width: 7840px;"> 
                        <?php
                        $rsPVal = '';
                        $sno = 0;
                        foreach($rsTemp1 as $rsPVal)
                        {
                            $sno++;
                        ?>           
                            <li class="slide fashion current ga-entity " data-ga_label="Carousel <?php echo $sno;?>" style="width: 1100px; transition: opacity 0.5s ease 0s;" slideno="<?php echo $sno;?>">
                                <div class="byNo">
                                    <div class="from" style="display:none;"> 
                                        <span class="a">Featured</span> 
                                        <span class="b">Today</span> 
                                    </div>
                                    <div class="slideNo"> 
                                        <span class="a">viewing</span> 
                                        <span class="b">
                                            <div class="no">8</div>
                                            <div>/</div>
                                            <div class="ttl">8</div>
                                        </span> 
                                    </div>
                                </div>
                                <figure>
                                    <a class="hover-txt" href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsPVal['post_id'].'/'.$rsPVal['english_url'];?>/"></a>
                                    <a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsPVal['post_id'].'/'.$rsPVal['english_url'];?>">
                                    <?php $p1 = getcwd().'/public/products/image/'.$rsPVal['image'];?>
                                    <?php if(file_exists($p1)){?>
                                        <img class='lazy' src="<?php echo $GLOBALS['baseUrl'].'/public/products/image/'.$rsPVal['image'];?>" alt="">
                                    <?php }?>
                                    </a>                        
                                    <div class="desc">
                                        <!--<figcaption>--> 
                                        <h2 class="slidertext">
                                            <!--<span class="topic"><a href="#">Style Trends</a></span>-->
                                            <div> 
                                                <a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsPVal['post_id'].'/'.$rsPVal['english_url'];?>/"><?php echo insertReplacementTag($rsPVal['headline']);?></a>
                                            </div>
                                            <span class="by">&nbsp;</span> 
                                        </h2>
                                        <!--</figcaption>-->
                                    </div>
                                    <div class="fadedBg"></div>                        
                                </figure>
                            </li>
                        <?php
							ob_flush();
                        }
                        ?>
                        
                    </ul>
                    <ul class="thumbs">
                        <?php
                        $rsPVal = '';
                        $sno = 0;
                        foreach($rsTemp1 as $rsPVal)
                        {
                            $sno++;
                        ?>
                            <li class="current" style="transition: opacity 0.5s ease 0s;">
                                <a href="javascript:void();" onclick="ga('send', 'event', 'OnLoad Partner Stories', '<?php echo $rsPVal['post_id']?>', 'homepage', {'nonInteraction': 1});" title="<?php echo insertReplacementTag($rsPVal['headline']);?>">
                                <span></span>
                                <?php $p1 = getcwd().'/public/products/image/'.$rsPVal['image'];?>
                                <?php if(file_exists($p1)){?>
                                    <img src="<?php echo $GLOBALS['baseUrl'].'/public/products/image/thumb/'.$rsPVal['image'];?>" alt="<?php echo insertReplacementTag($rsPVal['headline']);?>">
                                <?php }?>
                                </a>
                            </li>
                        <?php
							ob_flush();
                        }
                        ?>   
                        
                    </ul>
                </div>
            </div>
            
            <script type="text/javascript">
                $(document).ready(function(){
                    var liveINDIASlidr=$("#liveINDIASlider").slider({'duration':200,'ieOpacityElems':'.byNo img .desc', 'autoPlay':true, 'autoPlayDuration':8000});
                })
            </script> 
            <script type="text/javascript" src="<?php echo $GLOBALS['baseUrl'];?>/public/bannerslider/js/liveINDIASlider.min.js"></script>
<?php
		}
	}
}

function getRightColumnVideo()
{
	$mySQL = "";
	//$mySQL = "SELECT post_id, headline, english_url, videourl, image, thumb_image, CONCAT(SUBSTRING_INDEX(intro, ' ', 20), '...') AS intro FROM `post` WHERE `city` = '".$GLOBALS['city']."' AND `status` = '1' AND videourl <> '' ORDER BY post_id DESC LIMIT 1";
	$mySQL = "SELECT post_id, headline, english_url, videourl, image, thumb_image, CONCAT(SUBSTRING_INDEX(intro, ' ', 20), '...') AS intro FROM `post` WHERE `status` = '1' AND videourl <> '' ORDER BY post_id DESC LIMIT 1";
	//echo $mySQL;
	//exit;
	$rsTemp = Zend_Registry::get("db")->fetchRow($mySQL);
	//print_r($res);die();
	
	
	if ($rsTemp)
	{
?>
        <div class="widget sidebar-featured-post">
			<div class="section-title pink-border">
				<h2><a href="#" class="textpink">Viral</a></h2>
				<div>
					<iframe style="margin-top:10px" src="<?php echo $rsTemp['videourl'];?>" webkitallowfullscreen="" mozallowfullscreen="" 	allowfullscreen="" width="100%" height="250px" frameborder="0"></iframe>
				</div>
				<div><?php echo $rsTemp['headline'];?></div>
				<div class="description" style="float:right;">
				<a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTemp['post_id'].'/'.$rsTemp['english_url'];?>" target="_blank">पूरी खबर पढ़ें →</a></div>
			</div>
		</div>
<?php
	}
}

function getRightColumnVideoOLD()
{
	$mySQL = "";
	$mySQL = "SELECT post_id, headline, english_url, videourl, image, thumb_image, CONCAT(SUBSTRING_INDEX(intro, ' ', 20), '...') AS intro FROM `post` WHERE `city` = '".$GLOBALS['city']."' AND `status` = '1' AND videourl <> '' ORDER BY post_id DESC LIMIT 1";
	//echo $mySQL;
	//exit;
	$rsTemp = Zend_Registry::get("db")->fetchRow($mySQL);
	//print_r($res);die();
	
	if ($rsTemp)
	{
?>
        <li id="text-10" class="widget widget_text">
        	<h3 class="widgettitle divborder">Viral</h3>			
            <div class="textwidget">
                <section class="gallery-block">
                    <div class="open" id="widget-zoom-video-cat-1">
                        <div class="cover">
                            <iframe src="<?php echo $rsTemp['videourl'];?>" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="" frameborder="0"></iframe>
                        </div>
                        <div><?php echo $rsTemp['headline'];?></div>
                        <div class="description" style="float:right;">
                        <a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTemp['post_id'].'/'.$rsTemp['english_url'];?>" target="_blank">पूरी खबर पढ़ें →</a></div>
                    </div>
                </section>
            </div>
        </li>
<?php
	}
}

function insertReplacementTag($strVal){
	$strVal = str_replace('##TEL##', '<i class="fa fa-phone"></i>', $strVal);
	$strVal = str_replace('##FAX##', '<i class="fa fa-fax"></i>', $strVal);
	$strVal = str_replace('##EMAIL##', '<i class="fa fa-envelope"></i>', $strVal);
	$strVal = str_replace('##WEB##', '<i class="fa fa-link"></i>', $strVal);
	$strVal = str_replace('##RS##', '<i class="fa fa-rupee"></i>', $strVal);
	return $strVal;
}

function insertADXTag($strVal){
		$repVal .= '<style> @media(min-width: 728px) { .mobile-specific {display:none; } } </style>';
		$repVal .= '<div class="mobile-specific">';
		$repVal .= '<script type="text/javascript">';
		//$repVal .= '<!--';
		$repVal .= 'google_ad_client = "ca-pub-8834194653550774";';
		$repVal .= '/* Liveindialive_WC_336*280 */';
		$repVal .= 'google_ad_slot = "2754311742";';
		$repVal .= 'google_ad_width = 336;';
		$repVal .= 'google_ad_height = 280;';
		//$repVal .= '//-->';
		$repVal .= '</script>';
		$repVal .= '<script type="text/javascript" src="//pagead2.googlesyndication.com/pagead/show_ads.js"> </script>';
		$repVal .= '</div>';
		
	$strVal = str_replace('##MC-ADS##', $repVal, $strVal);
	return $strVal;
}

//#############################################
// ADVERTISMENT
//#############################################
function getAds11111($idadcategory, $displaylimit, $position)
{
?>
	<script type="text/javascript">
	$(document).ready(function(){
		$('.bxslider_<?php echo $position;?>').bxSlider({
			mode: 'fade',
			auto:true,
			speed: 500,
			pause: 10000,
			adaptiveHeight: false,
			pager: false,
			controls: false,
			slideMargin: 5
		});
	});
</script>
<?php
	$mySQL = "";
	$mySQL = "UPDATE advertisment SET isActive = 0 WHERE DATE(enddate) < CURDATE()";	
	$rsCat = Zend_Registry::get("db")->query($mySQL);
	
	
	$mySQL = "";
	$mySQL .= "SELECT `idadvertisment`, `headline`, `image`, CONCAT('".$GLOBALS['baseUrl']."','/public/adimages/', image) AS imagepath, `englishurl`, `adscriptforheadtag`, `adscriptforbodytag`, `startdate`, `enddate`, `isActive` FROM `advertisment`";
	$mySQL .= " WHERE isActive = 1";
	//$mySQL .= " AND DATE(startdate) <= CURDATE()";
	$mySQL .= " AND DATE(enddate) >= CURDATE()";
	$mySQL .= " AND city = '".$GLOBALS['city']."'";
	$mySQL .= " AND idadcategory = '".$idadcategory."'";
	$mySQL .= " ORDER BY RAND()";
	if ($displaylimit != ""){
		$mySQL .= " LIMIT " .$displaylimit ;
	}
	//echo $mySQL;
	//exit;
	$rsTemp = Zend_Registry::get("db")->fetchAll($mySQL);
	
	if($rsTemp){
		$adsData = '<ul class="bxslider_'.$position.'">';
					
		foreach($rsTemp as $rsVal)
		{
			if ($rsVal['adscriptforbodytag'] != ''){
				echo $rsVal['adscriptforbodytag'];
			} else {
				if ($idadcategory == '1') {
						$adsData .= '<li id="TPADS650X90">';
						if($rsVal['englishurl']!=''){
							$adsData .= '<a href="'.$rsVal['englishurl'].'" target="_blank">';
						}		
						$adsData .= '<img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" />';
						if($rsVal['englishurl']!=''){
							$adsData .= '</a>';
						}
						$adsData .= '</li>';
				} elseif ($idadcategory == '2') {
						$adsData .= '<li id="RVADS300X600">';
						if($rsVal['englishurl']!=''){
							$adsData .= '<a href="'.$rsVal['englishurl'].'" target="_blank">';
						}		
						$adsData .= '<img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" />';
						if($rsVal['englishurl']!=''){
							$adsData .= '</a>';
						}
						$adsData .= '</li>';
				} elseif ($idadcategory == '3') {
						$adsData .= '<li id="FLADS650X90">';
						if($rsVal['englishurl']!=''){
							$adsData .= '<a href="'.$rsVal['englishurl'].'" target="_blank">';
						}		
						$adsData .= '<img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" />';
						if($rsVal['englishurl']!=''){
							$adsData .= '</a>';
						}
						$adsData .= '</li>';
				} elseif ($idadcategory == '4') {
						$adsData .= '<li id="FRADS300X90">';
						if($rsVal['englishurl']!=''){
							$adsData .= '<a href="'.$rsVal['englishurl'].'" target="_blank">';
						}		
						$adsData .= '<img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" />';
						if($rsVal['englishurl']!=''){
							$adsData .= '</a>';
						}
						$adsData .= '</li>';
				} elseif ($idadcategory == '5') {
						$adsData .= '<li id="THBADS1077X250">';
						if($rsVal['englishurl']!=''){
							$adsData .= '<a href="'.$rsVal['englishurl'].'" target="_blank">';
						}		
						$adsData .= '<img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" />';
						if($rsVal['englishurl']!=''){
							$adsData .= '</a>';
						}
						$adsData .= '</li>';
				} elseif ($idadcategory == '6') {
						$adsData .= '<li id="LEFTBARADS160X600">';
						if($rsVal['englishurl']!=''){
							$adsData .= '<a href="'.$rsVal['englishurl'].'" target="_blank">';
						}		
						$adsData .= '<img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" />';
						if($rsVal['englishurl']!=''){
							$adsData .= '</a>';
						}
						$adsData .= '</li>';
				} elseif ($idadcategory == '7') {
						$adsData .= '<li id="RIGHTBARADS160X600">';
						if($rsVal['englishurl']!=''){
							$adsData .= '<a href="'.$rsVal['englishurl'].'" target="_blank">';
						}		
						$adsData .= '<img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" />';
						if($rsVal['englishurl']!=''){
							$adsData .= '</a>';
						}
						$adsData .= '</li>';
				} elseif ($idadcategory == '8') {
						$adsData .= '<li id="PRIORITYBOTTOM650X90">';
						if($rsVal['englishurl']!=''){
							$adsData .= '<a href="'.$rsVal['englishurl'].'" target="_blank">';
						}		
						$adsData .= '<img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" />';
						if($rsVal['englishurl']!=''){
							$adsData .= '</a>';
						}
						$adsData .= '</li>';
				} elseif ($idadcategory == '9') {
						$adsData .= '<li id="POSTDETAILBOOTOM650X90">';
						if($rsVal['englishurl']!=''){
							$adsData .= '<a href="'.$rsVal['englishurl'].'" target="_blank">';
						}		
						$adsData .= '<img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" />';
						if($rsVal['englishurl']!=''){
							$adsData .= '</a>';
						}
						$adsData .= '</li>';
				} elseif ($idadcategory == '10') {
						$adsData .= '<li id="THBADS1077X250">';
						if($rsVal['englishurl']!=''){
							$adsData .= '<a href="'.$rsVal['englishurl'].'" target="_blank">';
						}		
						$adsData .= '<img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" />';
						if($rsVal['englishurl']!=''){
							$adsData .= '</a>';
						}
						$adsData .= '</li>';
				} 
			}
		}
		$adsData .= '</ul>';
		echo $adsData;
	}
}

function getAds($idadcategory, $displaylimit)
{
	$mySQL = "";
	$mySQL = "UPDATE advertisment SET isActive = 0 WHERE DATE(enddate) < CURDATE()";	
	$rsCat = Zend_Registry::get("db")->query($mySQL);
	
	$mySQL = "";
	$mySQL .= "SELECT `idadvertisment`, `headline`, `image`, CONCAT('".$GLOBALS['baseUrl']."','/public/adimages/', image) AS imagepath, `englishurl`, `adscriptforheadtag`, `adscriptforbodytag`, `startdate`, `enddate`, `isActive` FROM `advertisment`";
	$mySQL .= " WHERE isActive = 1";
	//$mySQL .= " AND DATE(startdate) <= CURDATE()";
	$mySQL .= " AND DATE(enddate) >= CURDATE()";
	//$mySQL .= " AND city = '".$GLOBALS['city']."'";
	$mySQL .= " AND idadcategory = '".$idadcategory."'";
	$mySQL .= " ORDER BY RAND()";
	if ($displaylimit != ""){
		$mySQL .= " LIMIT " .$displaylimit ;
	}
	//echo $mySQL;
	//exit;
	$rsTemp = Zend_Registry::get("db")->fetchAll($mySQL);
	
	if($rsTemp){
		foreach($rsTemp as $rsVal)
		{
			if ($rsVal['adscriptforbodytag'] != ''){
				echo $rsVal['adscriptforbodytag'];
			} else {
				//echo $rsVal['englishurl'];
				if($rsVal['englishurl']!=''){
					echo '<a href="'.$rsVal['englishurl'].'" target="_blank">';
				}
				if ($idadcategory == '1') {
					echo '<div id="TPADS650X90" style="margin:2px;"><img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" /></div>';		// TOP BANNER ADS 650x90
				} elseif ($idadcategory == '2') {
					echo '<div id="RVADS300X600" style="margin:2px;"><img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" /></div>';	// RIGHT VERTICLE ADS 300x600(SKY SCRAPER)
				} elseif ($idadcategory == '3') {
					echo '<div id="FLADS650X90" style="margin:2px; padding:5px; text-align:center; border-top:1px #EFEFEF solid;"><img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" /></div>';		// FOOTER LEFT ADS 650x90
				} elseif ($idadcategory == '4') {
					echo '<div id="FRADS300X90" style="margin:2px; padding:5px; text-align:center; border-top:1px #EFEFEF solid;"><img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" /></div>'; 	// FOOTER RIGHT ADS 300x90
				} elseif ($idadcategory == '5') {
					echo '<div id="THBADS1077X250" style="margin:0px; padding:0 0px 5px 0; text-align:center; border-top:0px #EFEFEF solid;"><img src="'.$rsVal['imagepath'].'" border="0" title="'.$rsVal['headline'].'" /></div>';		// TOP HEADER BANNER ADS 1077x250
				} 
				if($rsVal['englishurl']!=''){
					echo '</a>';
				}
			}
		}
	}
}
				
function getTopMenuCategoryWisePost($position){
	$mySql = "SELECT DISTINCT category_id, category FROM category WHERE position = '".$position."' ORDER BY sort";
	//echo $mySql;
	//exit;
	$rsCat= Zend_Registry::get("db")->fetchAll($mySql);
	
	if($rsCat)
	{ 
		foreach($rsCat as $rsCatVal)
		{
?>
			<li style="z-index:999;"> 
				<a href="<?php echo $GLOBALS['baseUrl'];?>/category/index/id/<?php echo $rsCatVal['category_id'].'/'.$rsCatVal['category'];?>/"><?php echo $rsCatVal['category'];?></a>
                <ul class="drop-down full-width col-5 hover-expand" style="z-index:999;">
<?php		
					$subCategory_Id = '';
					getTopMenuSubCategory($rsCatVal['category_id']);
					$subCategory_Id = getSubCategoryId($rsCatVal['category_id']);
						
					$mySql  = "SELECT P.post_id, P.headline, P.image, P.english_url FROM post P INNER JOIN post_category PC ON P.post_id = PC.post_id";
					$mySql .= " WHERE status = 1";
					$mySql .= " AND category_id IN (".$rsCatVal['category_id'].','.$subCategory_Id.")";
					//$mySql .= " AND category_id IN (".$subCategory_Id."))";
					$mySql .= " GROUP BY P.post_id ORDER BY P.post_id DESC";
					$mySql .= " LIMIT 4";
					//echo $mySql;
					//exit;
					$rsTemp = Zend_Registry::get("db")->fetchAll($mySql);
					
					if($rsTemp)
					{ 
						foreach($rsTemp as $rsTempVal)
						{
						?>
							<li>
								<?php $p1 = getcwd().'/public/products/image/'.$rsTempVal['image'];?>
								<a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>">
								<?php if(file_exists($p1)){?>
									<img src="<?php echo $GLOBALS['baseUrl'].'/public/products/image/thumb/'.$rsTempVal['image'];?>" alt="<?php echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" />
								<?php } else {?>
									<img src="<?php echo $GLOBALS['baseUrl'];?>/public/images/comingsoon.jpg" alt="<?php echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" />
								<?php }?>	
								</a>
								<h3><a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>"><?=$rsTempVal['headline'];?></a></h3>
							</li>
						<?php
							ob_flush();
						}
					}
?>
				</ul>
            </li>				
<?php
			ob_flush();
		}
	}
}

function getTopMenuSubCategory($parentId)
{						
	//blockwisenews/index/block/81/सिटी
	$sql = "SELECT category_id, category FROM `category` WHERE parentId = '".$parentId."' ORDER BY sort";
	//echo $sql;
	//exit;
	$res = Zend_Registry::get("db")->fetchAll($sql);
	
	if($res)
	{
?>
		<li>
            <ul class="sub-menu">
<?php 
			$subCategory_Id = '';
			foreach($res as $val)
			{
				if ($subCategory_Id != ''){
					$subCategory_Id = $subCategory_Id .',';
				}
				$subCategory_Id = $val['category_id'];
?>
            	<li>
                    <a href="<?php echo $GLOBALS['baseUrl'];?>/category/index/id/<?php echo $val['category_id'].'/'.$val['category'];?>/">
                    <?php echo $val['category'];?></a>
            	</li>
<?php 
			}
?>
			</ul>
        </li>
<?php
	}
}

function getSubCategoryId($parentId)
{						
	//blockwisenews/index/block/81/सिटी
	$sql = "SELECT category_id, category FROM `category` WHERE parentId = '".$parentId."' ORDER BY sort";
	//echo $sql;
	//exit;
	$rsSubCat = Zend_Registry::get("db")->fetchAll($sql);
	
	if($rsSubCat)
	{
		$subCategoryId = '';
		foreach($rsSubCat as $rsSubCatVal)
		{
			if ($subCategoryId != ''){
				$subCategoryId = $subCategoryId .',';
			}
			$subCategoryId = $subCategoryId . $rsSubCatVal['category_id'];
		}
		return $subCategoryId;
	}
}

function getMainBodyCategoryWisePost($category){
	$mySql = "SELECT DISTINCT category_id, category FROM category WHERE category = '".addslashes($category)."' ORDER BY sort";
	//echo $mySql;
	//exit;
	$rsCat= Zend_Registry::get("db")->fetchRow($mySql);
	
	if($rsCat)
	{ 
		//foreach($rsCat as $rsCatVal)
		//{
			$mySql  = "SELECT P.post_id, CONCAT(SUBSTRING_INDEX(P.headline, ' ', 8), '...') AS headline, P.image, P.english_url, P.user_name FROM post P";
		    $mySql .= " INNER JOIN post_category PC ON P.post_id = PC.post_id";
		    $mySql .= " WHERE category_id = '".$rsCat['category_id']."'";
			$mySql .= " AND P.status = 1";
			$mySql .= " AND P.sectionpriority <> ''";
			$mySql .= " ORDER BY P.sectionpriority ASC, P.post_id DESC";
			$mySql .= " LIMIT 6";
			//echo $mySql;
			//exit;
			$rsTemp = Zend_Registry::get("db")->fetchAll($mySql);
			
			if($rsTemp)
			{ 
?>
				<div class="cp-news-grid-style-4 mb20">
                    <div class="section-title pink-border">
                      <h2><a href="#" class="textpink"><?php echo $rsCat['category'];?></a></h2>
                      <small></small> </div>
                    <div class="cp-news-grid-style-3">
                        <div class="grid-holder">
                            <div class="row">
                                <ul class="cp-load-newsgrid">
                                	<?php
									foreach($rsTemp as $rsTempVal)
									{
									?>
                                      	<li class="col-md-4 col-sm-4 cp-news-post mb5">
                                        	<div class="cp-thumb">
                                            <?php $p1 = getcwd().'/public/products/image/'.$rsTempVal['image'];?>
											<a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>">
											<?php if(file_exists($p1)){?>
												<img src="<?php echo $GLOBALS['baseUrl'].'/public/products/image/thumb/'.$rsTempVal['image'];?>" alt="<?php echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" />
											<?php } else {?>
												<img src="<?php echo $GLOBALS['baseUrl'];?>/public/images/comingsoon.jpg" alt="<?php echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" />
											<?php }?>	
											</a>
                                            </div>
                                        	<div style="background-color:#EFEFEF; border:1px #CCC solid; padding:5px;">
                                            	<h3 class="h3-min-height">
                                                <a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>"><?=$rsTempVal['headline'];?></a></h3>
                                            	<div class="floatright text11"><a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>" class="textpink">more...</a></div>
                                            	<div class="text11 textblue" style="margin-top:20px; padding:5px; background-color:#CCC;">By <?php echo $rsTempVal['user_name'];?></div>
                                        	</div>
                                      	</li>
                                        <?php
										/*
                                        <!--<li class="col-md-4 col-sm-4 cp-news-post mb5">
                                        	<div class="cp-thumb">
                                            <?php //$p1 = getcwd().'/public/products/image/'.$rsTempVal['image'];?>
											<a href="<?php //echo $GLOBALS['baseUrl'].'/article/'.$rsTempVal['english_url'].'-'.$rsTempVal['post_id'].'.html';?>">
											<?php //if(file_exists($p1)){?>
												<img src="<?php //echo $GLOBALS['baseUrl'].'/public/products/image/thumb/'.$rsTempVal['image'];?>" alt="<?php //echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php //echo insertReplacementTag($rsTempVal['headline']);?>" />
											<?php //} else {?>
												<img src="<?php //echo $GLOBALS['baseUrl'];?>/public/images/comingsoon.jpg" alt="<?php //echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php //echo insertReplacementTag($rsTempVal['headline']);?>" />
											<?php //}?>	
											</a>
                                            </div>
                                        	<div style="background-color:#EFEFEF; border:1px #CCC solid; padding:5px;">
                                            	<h3 class="h3-min-height">
                                                <a href="<?php //echo $GLOBALS['baseUrl'].'/article/'.$rsTempVal['english_url'].'-'.$rsTempVal['post_id'].'.html';?>"><?//=$rsTempVal['headline'];?></a></h3>
                                            	<div class="floatright text11"><a href="<?php //echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>" class="textpink">more...</a></div>
                                            	<div class="text11 textblue" style="margin-top:20px; padding:5px; background-color:#CCC;">By <?php //echo $rsTempVal['user_name'];?></div>
                                        	</div>
                                      	</li>-->
										*/
										?>
                                   	<?php
									}
									?>
                                </ul>
                            </div>
                        </div>
                    </div>
             	</div>
<?php
			}
			ob_flush();
		//}
	}
}

function getGallery(){
	$mySql  = "SELECT G.idgallery, G.title, G.englishurl, GI.image FROM gallery G INNER JOIN galleryimage GI ON G.idgallery = GI.idgallery";
	$mySql .= " ORDER BY G.idgallery DESC";
	$mySql .= " LIMIT 1";
	//echo $mySql;
	//exit;
	$rsTemp = Zend_Registry::get("db")->fetchRow($mySql);
	
	if($rsTemp)
	{ 
?>
		<div id="hideonmobile" class="cp-news-grid-style-4 m20">
         	<div class="section-title pink-border">
            	<h2><a href="#" class="textpink">Gallery</a></h2>
              	<small></small> 
           	</div>
            <div class="cp-news-grid-style-3">
            	<div class="grid-holder">
              		<div class="row">
                    	<ul class="cp-load-newsgrid">
                          	<li class="col-md-12 col-sm-12 cp-news-post">
                            	<div class="cp-thumb">
                            		<img src="<?php echo $GLOBALS['baseUrl']?>/public/gallery/<?php echo $rsTemp['image'];?>" alt="">
                                    <div class="textcenter pad5" style="position:absolute; top:5px;">
                                    	<span class="textcenter"><a href="single-post.html" class="textpink text21"><?php echo $rsTemp['title'];?></a></span>
                               	 	</div>
                                	<div class="textcenter pad5" style="position:absolute; bottom:5px;">
                                    <a href="single-post.html"><img class="lazy" src="<?php echo $GLOBALS['baseUrl']?>/public/images/gallaryplay.png" style="width:50px;" /></a>
                                	</div>
                            	</div>
								<?php
                                /*
                                <!---<h3><a href="single-post.html">बॉलीवुड हंगामा पर हिन्दी फ़िल्म जय Gangaajal की नवीनतम फिल्म फ़ोटो </a></h3>
                                <div class="text11" style="margin-top:20px; padding:5px; background-color:#efefef;">By Live India</div>-->
                                */
                                ?>
                          	</li>
                   		</ul>
             		</div>
            	</div>
         	</div>
        </div>
<?php
	}
}

function getRightBodyCategoryWisePost($position, $category){
	$mySql = "SELECT DISTINCT category_id, category FROM category WHERE position = '".$position."' AND category = '".addslashes($category)."' ORDER BY sort";
	//echo $mySql;
	//exit;
	$rsCat= Zend_Registry::get("db")->fetchRow($mySql);
	
	if($rsCat)
	{ 
		//foreach($rsCat as $rsCatVal)
		//{
			$mySql  = "SELECT P.post_id, CONCAT(SUBSTRING_INDEX(P.headline, ' ', 8), '...') AS headline, CONCAT(SUBSTRING_INDEX(P.intro, ' ', 10), '...') AS intro, P.image, P.english_url FROM post P INNER JOIN post_category PC ON P.post_id = PC.post_id";
			$mySql .= " WHERE status = 1";
			$mySql .= " AND category_id = '".$rsCat['category_id']."'";
			$mySql .= " ORDER BY P.post_id DESC";
			if($rsCat['category'] == 'Must Read')
			{
				$mySql .= " LIMIT 10";
			} else {
				$mySql .= " LIMIT 4";
			}
			//echo $mySql;
			//exit;
			$rsTemp = Zend_Registry::get("db")->fetchAll($mySql);
			
			if($rsTemp)
			{ 
				if($rsCat['category'] == 'Must Read')
				{
?>
                	<div class="widget sidebar-featured-post">
                        <div class="section-title pink-border">
                            <h2><a href="#" class="textpink">Flicker <?php //echo $rsCat['category'];?></a></h2>
                            <small></small> 
                        </div>
                        <div class="cp-sidebar-content">
                            <div class="side-featured-slider owl-carousel owl-theme">
                                <!--<div class="item">
                                    <div class="cp-post-content">
                                        <h3><a href="#">CEOs That Are Ruling Indian-Origin CEOs That Are Ruling The World Of Technology</a></h3>
                                    </div>
                                    <img src="<?php //echo $GLOBALS['baseUrl']?>/public/images/slide1.jpg" alt="">
                                </div>-->
                                <?php
                                foreach($rsTemp as $rsTempVal)
                                {
                                ?>
                                    <div class="item">
                                        <?php $p1 = getcwd().'/public/products/image/'.$rsTempVal['image'];?>
                                        <a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>">
                                        <?php if(file_exists($p1)){?>
                                            <img src="<?php echo $GLOBALS['baseUrl'].'/public/products/image/thumb/flk_'.$rsTempVal['image'];?>" alt="<?php echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" style="width:100%; height:300px;" />
                                        <?php } else {?>
                                            <img src="<?php echo $GLOBALS['baseUrl'];?>/public/images/comingsoon.jpg" alt="<?php echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" />
                                        <?php }?>
                                        <div class="cp-post-content">
                                            <h3><a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>"><?=$rsTempVal['headline'];?></a></h3>
                                        </div>
                                    </div>
                                <?php
								//	ob_flush();
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div style="text-align:center; margin:20px 0;">
					    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <!-- MC_NEW -->
                        <ins class="adsbygoogle"
                             style="display:inline-block;width:300px;height:600px"
                             data-ad-client="ca-pub-7910693805653243"
                             data-ad-slot="5166475016"></ins>
                        <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
                    <div style="clear:both;"></div>
                <?php
				} elseif($rsCat['category'] == 'Live News') {
					$sql = "";
					$sql .= " SELECT breaking_id, breakingnews, created FROM `breaking_news` WHERE DATE(created) = '".date('Y-m-d')."' ORDER BY created DESC LIMIT 4";
					//echo $sql;
					//exit;
					$rsBreaking = Zend_Registry::get("db")->fetchAll($sql);
					if($rsBreaking){
				?>
                        <div class="widget latest-reviews">
                            <div class="section-title pink-border" style="margin-bottom:0px;">
                                <h2><a href="#" class="textpink"><?php echo $rsCat['category'];?></a></h2>
                                <small></small> 
                            </div>
                            <div class="cp-sidebar-content">
                                <ul class="reviews">
                                	<?php
                                    foreach($rsBreaking as $rsBreakingVal){
									?>
                                        <li>
                                            <h4><?php echo insertReplacementTag($rsBreakingVal['breakingnews']);?></h4>
                                            <div class="catname floatright"><a href="javascript:;" class="catname-btn btn-pink waves-effect waves-button"><i class="fa fa-clock-o"></i> <?php echo insertReplacementTag($rsBreakingVal['created']);?></a></div>
                                        </li>
                                    <?php
										ob_flush();
									}
									?>
                                </ul>
                            </div>
                        </div>
                <?php	
					}
				} elseif($rsCat['category'] == 'She Corner') {
				?>
                    <div class="widget popular-post m20" id="hideonmobile">
                        <div class="section-title pink-border">
                            <h2><a href="#" class="textpink"><?php echo $rsCat['category'];?></a></h2>
                            <small></small> 
                        </div>
                        <div class="cp-sidebar-content">
                            <ul class="small-grid">
                                <?php
                                foreach($rsTemp as $rsTempVal)
                                {
                                ?>
                                    <li>
                                        <div class="small-post">
                                            <div class="cp-thumb">
                                            <?php $p1 = getcwd().'/public/products/image/'.$rsTempVal['image'];?>
                                            <a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>">
                                            <?php if(file_exists($p1)){?>
                                                <img src="<?php echo $GLOBALS['baseUrl'].'/public/products/image/thumb/'.$rsTempVal['image'];?>" alt="<?php echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" style="width:100px; height:80px;" />
                                            <?php } else {?>
                                                <img src="<?php echo $GLOBALS['baseUrl'];?>/public/images/comingsoon.jpg" alt="<?php echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" />
                                            <?php }?>	
                                            </a>
                                            </div>
                                            <div class="cp-post-content">
                                                <h3><strong><a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>"><?=$rsTempVal['headline'];?></a></strong></h3>
                                                <h5><?=$rsTempVal['intro'];?></h5>
                                            </div>
                                        </div>
                                    </li>
                                <?php
									ob_flush();
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
				<?php
				} else {
				?>
                    <div class="widget popular-post m20">
                        <div class="section-title pink-border">
                            <h2><a href="#" class="textpink"><?php echo $rsCat['category'];?></a></h2>
                            <small></small> 
                        </div>
                        <div class="cp-sidebar-content">
                            <ul class="small-grid">
                                <?php
                                foreach($rsTemp as $rsTempVal)
                                {
                                ?>
                                    <li>
                                        <div class="small-post">
                                            <div class="cp-thumb">
                                            <?php $p1 = getcwd().'/public/products/image/'.$rsTempVal['image'];?>
                                            <a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>">
                                            <?php if(file_exists($p1)){?>
                                                <img src="<?php echo $GLOBALS['baseUrl'].'/public/products/image/thumb/thumb_'.$rsTempVal['image'];?>" alt="<?php echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" style="width:80px; height:80px;" />
                                            <?php } else {?>
                                                <img src="<?php echo $GLOBALS['baseUrl'];?>/public/images/comingsoon.jpg" alt="<?php echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" />
                                            <?php }?>	
                                            </a>
                                            </div>
                                            <div class="cp-post-content">
                                                <h3><strong><a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>"><?=$rsTempVal['headline'];?></a></strong></h3>
                                                <h5><?=$rsTempVal['intro'];?></h5>
                                            </div>
                                        </div>
                                    </li>
                                <?php
									ob_flush();
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
<?php
				}
			}
			ob_flush();
			//flush();
		//}
	}
}

function getMainBodyCategoryWisePostOnDetailPage($position, $category){
	$mySql = "SELECT DISTINCT category_id, category FROM category WHERE category = '".addslashes($category)."'";
	//$mySql .= "AND position = '".$position."'";
	$mySql .= "ORDER BY sort";
	//echo $mySql;
	//exit;
	$rsCat= Zend_Registry::get("db")->fetchRow($mySql);
	
	if($rsCat)
	{ 
		//foreach($rsCat as $rsCatVal)
		//{
			$mySql  = "SELECT P.post_id, CONCAT(SUBSTRING_INDEX(P.headline, ' ', 12), '...') AS headline, P.image, P.english_url, P.user_name FROM post P INNER JOIN post_category PC ON P.post_id = PC.post_id";
			$mySql .= " WHERE category_id = '".$rsCat['category_id']."'";
			$mySql .= " AND status = 1";
			$mySql .= " AND P.sectionpriority <> ''";
			$mySql .= " ORDER BY P.sectionpriority, P.post_id DESC";
			$mySql .= " LIMIT 3";
			//echo $mySql;
			//exit;
			$rsTemp = Zend_Registry::get("db")->fetchAll($mySql);
			
			if($rsTemp)
			{ 
?>
				<div class="cp-news-grid-style-4 mb20">
                    <div class="section-title pink-border">
                      <h2><a href="#" class="textpink"><?php echo $rsCat['category'];?></a></h2>
                      <small></small> </div>
                    <div class="cp-news-grid-style-3">
                        <div class="grid-holder">
                            <div class="row">
                                <ul class="cp-load-newsgrid">
                                	<?php
									foreach($rsTemp as $rsTempVal)
									{
									?>
                                      	<li class="col-md-4 col-sm-4 cp-news-post mb5">
                                        	<div class="cp-thumb">
                                            <?php $p1 = getcwd().'/public/products/image/'.$rsTempVal['image'];?>
											<a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>">
											<?php if(file_exists($p1)){?>
												<img src="<?php echo $GLOBALS['baseUrl'].'/public/products/image/thumb/'.$rsTempVal['image'];?>" alt="<?php echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" />
											<?php } else {?>
												<img src="<?php echo $GLOBALS['baseUrl'];?>/public/images/comingsoon.jpg" alt="<?php echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" />
											<?php }?>	
											</a>
                                            </div>
                                        	<div style="background-color:#EFEFEF; border:1px #CCC solid; padding:5px;">
                                            	<h3 class="h3-min-height">
                                                <a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>"><?=$rsTempVal['headline'];?></a></h3>
                                            	<div class="floatright text11"><a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>" class="textpink">more...</a></div>
                                            	<div class="text11 textblue" style="margin-top:20px; padding:5px; background-color:#CCC;">By <?php echo $rsTempVal['user_name'];?></div>
                                        	</div>
                                      	</li>
                                        
                                        <!--<li class="col-md-4 col-sm-4 cp-news-post mb5">
                                        	<div class="cp-thumb">
                                            <?php //$p1 = getcwd().'/public/products/image/'.$rsTempVal['image'];?>
											<a href="<?php //echo $GLOBALS['baseUrl'].'/article/'.$rsTempVal['english_url'].'-'.$rsTempVal['post_id'].'.html';?>">
											<?php //if(file_exists($p1)){?>
												<img src="<?php //echo $GLOBALS['baseUrl'].'/public/products/image/thumb/'.$rsTempVal['image'];?>" alt="<?php //echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php //echo insertReplacementTag($rsTempVal['headline']);?>" />
											<?php //} else {?>
												<img src="<?php //echo $GLOBALS['baseUrl'];?>/public/images/comingsoon.jpg" alt="<?php //echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php //echo insertReplacementTag($rsTempVal['headline']);?>" />
											<?php //}?>	
											</a>
                                            </div>
                                        	<div style="background-color:#EFEFEF; border:1px #CCC solid; padding:5px;">
                                            	<h3 class="h3-min-height">
                                                <a href="<?php //echo $GLOBALS['baseUrl'].'/article/'.$rsTempVal['english_url'].'-'.$rsTempVal['post_id'].'.html';?>"><?//=$rsTempVal['headline'];?></a></h3>
                                            	<div class="floatright text11"><a href="<?php //echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>" class="textpink">more...</a></div>
                                            	<div class="text11 textblue" style="margin-top:20px; padding:5px; background-color:#CCC;">By <?php //echo $rsTempVal['user_name'];?></div>
                                        	</div>
                                      	</li>-->
                                   	<?php
										ob_flush();
									}
									?>
                                </ul>
                            </div>
                        </div>
                    </div>
             	</div>
<?php
			}
			ob_flush();
		//}
	}
}

function getMustReadPost(){
	$mySql = "SELECT DISTINCT category_id, category FROM category WHERE `category` = 'Must Read' ORDER BY sort";
	//echo $mySql;
	//exit;
	$rsCat= Zend_Registry::get("db")->fetchRow($mySql);
	
	if($rsCat)
	{ 
		//foreach($rsCat as $rsCatVal)
		//{
			$mySql  = "SELECT P.post_id, P.headline, P.image, P.english_url FROM post P INNER JOIN post_category PC ON P.post_id = PC.post_id";
			$mySql .= " WHERE city = '".$GLOBALS['city']."'";
			$mySql .= " AND category_id = '".$rsCat['category_id']."'";
			$mySql .= " ORDER BY P.post_id DESC";
			$mySql .= " LIMIT 4";
			//echo $mySql;
			//exit;
			$rsTemp = Zend_Registry::get("db")->fetchAll($mySql);
			if($rsTemp)
			{ 
?>
				<div class="widget sidebar-featured-post">
                    <div class="section-title pink-border">
                    	<h2><a href="#" class="textpink">Must Read</a></h2>
                    	<small></small> 
                	</div>
                  	<div class="cp-sidebar-content">
                    	<div class="side-featured-slider owl-carousel owl-theme">
                      		<!--<div class="item">
                      			<div class="cp-post-content">
                          			<h3><a href="#">CEOs That Are Ruling Indian-Origin CEOs That Are Ruling The World Of Technology</a></h3>
                        		</div>
                      			<img src="<?php echo $GLOBALS['baseUrl']?>/public/images/slide1.jpg" alt="">
                      		</div>-->
                      		<?php
							foreach($rsTemp as $rsTempVal)
							{
							?>
                                <div class="item">
                                    <?php $p1 = getcwd().'/public/products/image/'.$rsTempVal['image'];?>
                                    <a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>">
                                    <?php if(file_exists($p1)){?>
                                        <img src="<?php echo $GLOBALS['baseUrl'].'/public/products/image/'.$rsTempVal['image'];?>" alt="<?php echo insertReplacementTag(rsTempVal);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" />
                                    <?php } else {?>
                                        <img src="<?php echo $GLOBALS['baseUrl'];?>/public/images/comingsoon.jpg" alt="<?php echo insertReplacementTag($rsTempVal['headline']);?>" title="<?php echo insertReplacementTag($rsTempVal['headline']);?>" />
                                    <?php }?>
                                    <div class="cp-post-content">
                                        <h3><a href="<?php echo $GLOBALS['baseUrl'].'/postdetail/index/id/'.$rsTempVal['post_id'].'/'.$rsTempVal['english_url'];?>"><?=$rsTempVal['headline'];?></a></h3>
                                    </div>
                                </div>
                            <?php
							}
							?>
                      	</div>
                  	</div>
               	</div>
<?php
			}
		//}
	}
}

function otherSiteLink()
{
	/* $sql = "SELECT domain_hindi, domain_english FROM `domain` WHERE status = 1 AND `domain_english` <> '".str_replace('www.','',$_SERVER['HTTP_HOST'])."' ORDER BY domain_english"; */
	$sql = "SELECT domain_hindi, domain_english FROM `domain` WHERE status = 1 ORDER BY domain_english";
	//echo $sql;
	$res = Zend_Registry::get("db")->fetchAll($sql);
	
	if($res)
	{ 
		foreach($res as $val)
		{
?>
            <li>
                <a href="http://<?php echo $val['domain_english'];?>" target="_blank">
				<?php echo $val['domain_hindi'];?></a>
            </li>
<?php 
		}
	}
}
function getCatName($category_id){
	$sql = "SELECT category FROM `category` WHERE category_id ='".$category_id."'";
	//echo $sql;
	//exit;
	$res = Zend_Registry::get("db")->fetchRow($sql);
	
	if($res)
	{	
		return $res['category'];
	}
}

function getBlocks()
{						
	//blockwisenews/index/block/81/सिटी
	$sql = "SELECT location_id, block_name FROM `location` WHERE isDeleted = 0 AND city ='".$GLOBALS['city']."' ORDER BY block_name";
	//echo $sql;
	//exit;
	$res = Zend_Registry::get("db")->fetchAll($sql);
	
	if($res)
	{
?>
		<ul id="content-1">
<?php 
		foreach($res as $val)
		{
?>
            <li>
                <a href="<?php echo $GLOBALS['baseUrl'];?>/search/?strSearch=<?php echo urlencode(trim($val['block_name']));?>">
				<?php echo $val['block_name'];?></a>
            </li>
<?php 
		}
?>
		</ul>
        <!-- custom scrollbar plugin (latest version) via Github with fallback to local -->
		<script src="<?php echo $GLOBALS['baseUrl'];?>/public/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script>window.mCustomScrollbar || document.write('<script src="../jquery.mCustomScrollbar.concat.min.js"><\/script>')</script>
        <script>
        (function($){
            $(window).load(function(){
                $("#content-1").mCustomScrollbar({
                    theme:"inset-1-dark",
                    axis:"yx",
                    advanced:{autoExpandHorizontalScroll:false},
                    /* change mouse-wheel axis on-the-fly */
                    callbacks:{
                        onOverflowY:function(){
                            var opt=$(this).data("mCS").opt;
                            if(opt.mouseWheel.axis!=="y") opt.mouseWheel.axis="y";
                        },
                        onOverflowX:function(){
                            var opt=$(this).data("mCS").opt;
                            if(opt.mouseWheel.axis!=="x") opt.mouseWheel.axis="x";
                        },
                    }
                });
            });
        })(jQuery);
    </script>
<?php
	}
}


function mbStringToArray ($string) {
	$strlen = mb_strlen($string);
	while ($strlen) {
		$array[] = mb_substr($string,0,1,"UTF-8");
		$string = mb_substr($string,1,$strlen,"UTF-8");
		$strlen = mb_strlen($string);
	}
	return $array;
} 

function countVowels($req){
	//I have hard coded the hex values of some characters that are vowels in Hindi
	//This does NOT include all the vowels
	//You might want to add more as per your needs from the table that I have provided before
	
	$hindi = array("\u0906","\u0908","\u093E","\u093F","\u0945","\u0946","\u0947","\u0948","\u0949","\u094A","\u094B","\u094C","\u094D");
	$vowels= array();
	$vowelcount = 0;
	for($i = 0; $i<count($hindi); $i++){
	 	//Push the decoded unicode character into the $vowels array
	 	array_push($vowels,json_decode('"'.$hindi[$i].'"')); 
	}
	
	for($j=0;$j<count($req);$j++){
	  	if(in_array($req[$j], $vowels))
			$vowelcount++;
	}
	return $vowelcount;
 }

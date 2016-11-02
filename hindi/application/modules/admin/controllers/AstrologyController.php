<?php
class Admin_AstrologyController extends Zend_Controller_Action
{
	//initialize controller
	function init(){
		//flash massenger initialize
		$this->_flashMessenger =$this->_helper->getHelper('FlashMessenger');
		//admin session object create
		$this->admin=new Zend_Session_Namespace('admin');
		//check admin login
		if(empty($this->admin->username)){
		$this->_redirect('admin/index');
		}
		//admin menu explore
		$this->view->menuExp=60;
	}
	#####################################################################################################################################
	//CONTROLOR - POST LIST PAGE 
	function indexAction(){
		$host = $_SERVER['HTTP_HOST'];
		//echo $_REQUEST['keyword'];
		//exit;
		
		//$city = GetBetween1("live",".","$host");
		if ($_SERVER['HTTP_HOST'] == 'localhost') {
			$city = 'gaya';
		} else {
			$city = GetBetween("live",".","$host");
		}
		//echo $userCity = str_replace(str_replace('live', '', $_SERVER['HTTP_HOST']), '.com', '', $_SERVER['HTTP_HOST']);
		//exit;
		$adminsession = new Zend_Session_Namespace('admin');
		$obj = new Default_Model_DbTable_User();
		$recods = $obj->getAdminByUser($adminsession->username);
		$userid = $recods['user_id'];
		$usertype = $recods['user_type'];
		$sql = "";
		$sql .= "SELECT *, @s:=@s+1 sno FROM (SELECT @s:= 0) AS s, `astrology`";

		$sql .= " WHERE isdeleted = '0'";
		
		$sql .= " ORDER BY `idastrology` DESC";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchAll($sql);
		
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($res);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);

		$this->view->perpage=25;
		$this->view->pages=$page;
		$this->view->data=$paginator;
		$this->view->messages = $this->_helper->_flashMessenger->getMessages();
		$this->render();
		
	}
	
	//CONTROLOR - POST ADD PAGE 
	function addAction(){
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$error = array();
			$aries = addslashes($formData['aries']);
			$taurus = addslashes($formData['taurus']);
			$gemini = addslashes($formData['gemini']);
			$cancer = addslashes($formData['cancer']);
			$leo 	= addslashes($formData['leo']);
			$virgo = addslashes($formData['virgo']);
			$libra = addslashes($formData['libra']);
			$scorpio = addslashes($formData['scorpio']);
			$sagittarius = addslashes($formData['sagittarius']);
			$capricorn = addslashes($formData['capricorn']);
			$aquarius = addslashes($formData['aquarius']);
			$pisces = addslashes($formData['pisces']);
			//$isSchedule = $formData['isSchedule'];
			$astrodate = $formData['astrodate'];
			
			
			//print_r($aaa);
			//die();
			
			/** category name validate*/
			if(!Zend_Validate::is($formData['aries'],'NotEmpty')){
			$error['aries']='Please Enter मेस राशी';
			}
			
			/** priority validate*/
			//if(!Zend_Validate::is($formData['priority'],'NotEmpty')){
			//$error['priority']='Please select priority';
			//}
			
			/** block validate*/
			if(!Zend_Validate::is($formData['taurus'],'NotEmpty')){
			$error['taurus']='Please Enter वृषभ राशी';
			}
			
			/** headline validate*/
			if(!Zend_Validate::is($formData['gemini'],'NotEmpty')){
			$error['gemini']='Please Enter मिथुन राशी';
			}
			
			/** english url validate*/
			if(!Zend_Validate::is($formData['cancer'],'NotEmpty')){
			$error['cancer']='Please Enter कर्क राशी';
			}
			
			/** intro validate*/
			if(!Zend_Validate::is($formData['leo'],'NotEmpty')){
			$error['leo']='Please Enter सिंह राशी';
			}
			
			if(!Zend_Validate::is($formData['virgo'],'NotEmpty')){
			$error['virgo']='Please Enter कन्या राशी';
			}
			
			if(!Zend_Validate::is($formData['libra'],'NotEmpty')){
			$error['libra']='Please Enter तुला राशी';
			}
			
			if(!Zend_Validate::is($formData['scorpio'],'NotEmpty')){
			$error['scorpio']='Please Enter वृश्चिक राशी';
			}
			
			if(!Zend_Validate::is($formData['sagittarius'],'NotEmpty')){
			$error['sagittarius']='Please Enter धनु राशी';
			}
			
			if(!Zend_Validate::is($formData['capricorn'],'NotEmpty')){
			$error['capricorn']='Please Enter मकर राशी';
			}
			
			if(!Zend_Validate::is($formData['aquarius'],'NotEmpty')){
			$error['aquarius']='Please Enter कुंभ राशी';
			}
			
			if(!Zend_Validate::is($formData['pisces'],'NotEmpty')){
			$error['pisces']='Please Enter मीन राशी';
			}
			
			if(!Zend_Validate::is($formData['astrodate'],'NotEmpty')){
			$error['astrodate']='Please Enter Date';
			}
			
			//$idReporter
			//if(!Zend_Validate::is($formData['idReporter'],'NotEmpty')){
			//$error['idReporter']='Please select reporter';
			//}
			if(!empty($astrodate)){
				$mySql="select count(0) as totalrec from astrology where astrodate='".$astrodate."'";
				$rstemp=Zend_Registry::get("db")->fetchRow($mySql);
				if($rstemp){
					if($rstemp['totalrec']>0){
						$error['recordExist']='Astrology For this date is already exist';	
					}
				}
			}
			if(count($error)== 0){
				
							
				$adminsession = new Zend_Session_Namespace('admin');
				$obj = new Default_Model_DbTable_User();
				$recods = $obj->getAdminByUser($adminsession->username);
				$userid = $recods['user_id'];
				$usertype = $recods['user_type'];
				$username = $recods['first_name']." ".$recods['last_name'];
				$created_date = date('Y-m-d h:i:s');
				
				
				//###############//
				//INSERT NEW POST//
				//###############//
				$mySql = "";
				$mySql = "INSERT INTO `astrology` (";
				$mySql = $mySql . " `aries`";
				$mySql = $mySql . ", `taurus`";
				$mySql = $mySql . ", `gemini`";
				$mySql = $mySql . ", `cancer`";
				$mySql = $mySql . ", `leo`";
				$mySql = $mySql . ", `virgo`";
				$mySql = $mySql . ", `libra`";
				$mySql = $mySql . ", `scorpio`";
				$mySql = $mySql . ", `sagittarius`";
				$mySql = $mySql . ", `capricorn`";
				$mySql = $mySql . ", `aquarius`";
				$mySql = $mySql . ", `pisces`";
				$mySql = $mySql . ", `astrodate`";
				
				$mySql = $mySql . " ) VALUES (";
				
				$mySql = $mySql . " '".$aries."'";
				$mySql = $mySql . ", '".$taurus."'";
				$mySql = $mySql . ", '".$gemini."'";
				$mySql = $mySql . ", '".$cancer."'";				
				$mySql = $mySql . ", '".$leo."'";
				$mySql = $mySql . ", '".$virgo."'";
				$mySql = $mySql . ", '".$libra."'";
				$mySql = $mySql . ", '".$scorpio."'";
				$mySql = $mySql . ", '".$sagittarius."'";
				$mySql = $mySql . ", '".$capricorn."'";
				$mySql = $mySql . ", '".$aquarius."'";
				$mySql = $mySql . ", '".$pisces."'";
				$mySql = $mySql . ", '".$astrodate."'";
				
				
				$mySql = $mySql . ")";
				//echo $mySql .'<BR>';
				//exit;
				$ss = Zend_Registry::get("db")->query($mySql);
				$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				
				//echo 'I am here...' . $lastinsertid . '<br>';
				//exit;
				
				
				if($ss){
					//assign message
					$this->_flashMessenger->addMessage('Astro added successfully');
					$this->_redirect('/admin/astrology/index');
				}else{
					$this->view->msg = 'Error , try agian.';
				}
			} else {
				//error send to view
				$this->view->error = $error;
			}
			//echo $data = serialize($_POST);
			//pr(unserialize($data));
		}
	}

	//CONTROLOR - POST EDIT PAGE 
	function editAction(){
		
			$idastrology = $this->_getParam('id');
			//echo $adminid;die();
			$sql="SELECT * FROM `astrology` WHERE `idastrology` = '".$idastrology."'";
			//echo $sql;
			//exit;
			$res = Zend_Registry::get("db")->fetchrow($sql);
			
			$this->view->data = $res;
			
			if($this->getRequest()->isPost()){
				$formData = $this->getRequest()->getPost();
				
				$error = array();
				$aries = addslashes($formData['aries']);
				$taurus = addslashes($formData['taurus']);
				$gemini = addslashes($formData['gemini']);
				$cancer = addslashes($formData['cancer']);
				$leo 	= addslashes($formData['leo']);
				$virgo = addslashes($formData['virgo']);
				$libra = addslashes($formData['libra']);
				$scorpio = addslashes($formData['scorpio']);
				$sagittarius = addslashes($formData['sagittarius']);
				$capricorn = addslashes($formData['capricorn']);
				$aquarius = addslashes($formData['aquarius']);
				$pisces = addslashes($formData['pisces']);
				//$isSchedule = $formData['isSchedule'];
				$astrodate = $formData['astrodate'];
				
				
				//print_r($aaa);die();
				/** category name validate*/
				if(!Zend_Validate::is($formData['aries'],'NotEmpty')){
					$error['aries']='Please Enter मेस राशी';
				}
				
				/** priority validate*/
				//if(!Zend_Validate::is($formData['priority'],'NotEmpty')){
				//$error['priority']='Please select priority';
				//}
				
				/** block validate*/
				if(!Zend_Validate::is($formData['taurus'],'NotEmpty')){
					$error['taurus']='Please Enter वृषभ राशी';
				}
				
				/** headline validate*/
				if(!Zend_Validate::is($formData['gemini'],'NotEmpty')){
					$error['gemini']='Please Enter मिथुन राशी';
				}
				
				/** english url validate*/
				if(!Zend_Validate::is($formData['cancer'],'NotEmpty')){
					$error['cancer']='Please Enter कर्क राशी';
				}
				
				/** intro validate*/
				if(!Zend_Validate::is($formData['leo'],'NotEmpty')){
					$error['leo']='Please Enter सिंह राशी';
				}
				
				if(!Zend_Validate::is($formData['virgo'],'NotEmpty')){
					$error['virgo']='Please Enter कन्या राशी';
				}
				
				if(!Zend_Validate::is($formData['libra'],'NotEmpty')){
					$error['libra']='Please Enter तुला राशी';
				}
				
				if(!Zend_Validate::is($formData['scorpio'],'NotEmpty')){
					$error['scorpio']='Please Enter वृश्चिक राशी';
				}
				
				if(!Zend_Validate::is($formData['sagittarius'],'NotEmpty')){
					$error['sagittarius']='Please Enter धनु राशी';
				}
				
				if(!Zend_Validate::is($formData['capricorn'],'NotEmpty')){
					$error['capricorn']='Please Enter मकर राशी';
				}
				
				if(!Zend_Validate::is($formData['aquarius'],'NotEmpty')){
					$error['aquarius']='Please Enter कुंभ राशी';
				}
				
				if(!Zend_Validate::is($formData['pisces'],'NotEmpty')){
					$error['pisces']='Please Enter मीन राशी';
				}
				
				/*if(!Zend_Validate::is($formData['astrodate'],'NotEmpty')){
				$error['astrodate']='Please Enter Date';
				}*/
				if(!empty($astrodate)){
					$mySql="select count(0) as totalrec from astrology where idastrology <> '".$idastrology."' AND astrodate='".$astrodate."'";
					//echo  $mySql;
					//exit;
					$rstemp=Zend_Registry::get("db")->fetchRow($mySql);
					if($rstemp){
						if($rstemp['totalrec']>0){
							$error['recordExist']='Astrology For this date is already exist';	
						}
					}
				}	
				if(count($error)== 0){
					$adapter  = new Zend_File_Transfer_Adapter_Http();
					$fileInfo = $adapter->getFileInfo();
					
					//echo $returnFile;
					//exit;
					
					$adminsession = new Zend_Session_Namespace('admin');
					$obj = new Default_Model_DbTable_User();
					$recods = $obj->getAdminByUser($adminsession->username);
					$userid = $recods['user_id'];
					$username = $recods['first_name']." ".$recods['last_name'];
					$usertype = $recods['user_type'];
					$created_date = date('Y-m-d h:i:s');
					
				
					//##############################//
					//UPDATE SELECTED POST ID RECORD//
					//##############################//
					$mySQL = "UPDATE `astrology` SET";
					$mySQL = $mySQL . "  aries = '".$aries."'";
					$mySQL = $mySQL . ", taurus = '".$taurus."'";
					$mySQL = $mySQL . ", gemini = '".$gemini."'";
					$mySQL = $mySQL . ", cancer = '".$cancer."'";
					$mySQL = $mySQL . ", leo = '".$leo."'";
					$mySQL = $mySQL . ", virgo = '".$virgo."'";
					$mySQL = $mySQL . ", libra = '".$libra."'";
					$mySQL = $mySQL . ", scorpio = '".$scorpio."'";
					$mySQL = $mySQL . ", sagittarius = '".$sagittarius."'";
					$mySQL = $mySQL . ", capricorn = '".$capricorn."'";
					$mySQL = $mySQL . ", aquarius = '".$aquarius."'";
					$mySQL = $mySQL . ", pisces = '".$pisces."'";
					$mySQL = $mySQL . ", astrodate = '".$astrodate."'";
					$mySQL = $mySQL . " WHERE idastrology = '".$idastrology."'";
					
					//echo $mySQL;
					//die();
					$set = Zend_Registry::get("db")->query($mySQL);
					//$lastinsertid = Zend_Registry::get("db")->lastInsertId();
					
					//echo 'I am here.!!!..';
					//die();	
					
				if($set){
					//assign message
					$this->_flashMessenger->addMessage('Astrology edited successfully');
					$this->_redirect('/admin/astrology/index');
					//echo 'I am here.!!!..';
				exit;
					}else{
						//echo 'I am here...';
				exit;
						$this->view->msg = 'Error , try agian.';
					}
				}else{
					//error send to view
					$this->view->error = $error;
				}
			//echo $data = serialize($_POST);
			//pr(unserialize($data));
			}
	}
	
	//CONTROLOR - POST DELETE PAGE 
	public function deleteAction(){
		$this->getHelper('viewRenderer')->setNoRender();
		$this->_helper->layout->disableLayout();	
		$id = $this->_getParam('id');
		//"UPDATE `user` SET `active`=abs(`active`-1) WHERE user_id='$id'";
		//$sql ="UPDATE `post` SET `status` = '0', isDeleted = '1' WHERE `post_id` = '".$id."'";
		$sql = "DELETE FROM astrology WHERE `idastrology` = '".$id."'";
		//echo $sql;die();
		$del = Zend_Registry::get("db")->query($sql);
		if($del){
			$this->_flashMessenger->addMessage('Selected record deleted');
			$this->_redirect('/admin/astrology/index');	
		}
	}
	
}//close class

function setAutoPriority($start, $city){
	if($start != ''){ 
		$editedOn = date('Y-m-d h:i:s');
		$i='';
		for($i=$start; $i<6; $i++)
		{
			$mySQL = "";
			$mySQL = $mySQL ." SELECT post_id FROM post WHERE city ='".$city."' AND `priority` = '".$i."';";
			//echo $mySQL .'<br>';
			$rsTemp = Zend_Registry::get("db")->fetchRow($mySQL);
            if($rsTemp){
				$mySQL1 = "";
				//foreach($rsTemp as $upVal){
					$pid = $rsTemp['post_id'];
					//echo '$pid = ' . $pid .'<BR>';
				//}
					
				$j = $i+1;
				
				$mySQL1 = "";
				$mySQL1 = $mySQL1 ." UPDATE `post` SET";
				if ($i == 5){
					$mySQL1 = $mySQL1 ." `priority` = ''";
				} else {
					$mySQL1 = $mySQL1 ." `priority` = '".$j."'";
				}
				$mySQL1 = $mySQL1 ." , `editedOn` = '".$editedOn."' WHERE city ='".$city."'";
				$mySQL1 = $mySQL1 ." AND `post_id` = '".$pid."'";
				$mySQL1 = $mySQL1 ." AND `priority` = '".$i."';";
				//echo $mySQL1 .'<br>';
				//echo '<hr>';
				$set = Zend_Registry::get("db")->query($mySQL1);
			}
			$pid = "";
		}
		//exit;
	}
}
function replaceDomain($imagePath, $city, $hostURL){
	$newPath='';
	//echo '$imagePath = ' .$imagePath . '<br>';
	//echo '$city = ' .$city . '<br>';
	//echo '$hostURL = ' .$hostURL . '<br>';
	if (strtolower($hostURL) == 'livedhanbad.com' && strtolower($city) != 'dhanbad') {
		$newPath = '/home/livedhanbad/public_html/live'.strtolower($city).'.com/public/products/image';
	} else {
		if (strtolower($city) == 'alwar'){
			$newPath = str_replace($hostURL, 'livealwar.com', $imagePath);
		} else if (strtolower($city) == 'basti'){
			$newPath = str_replace($hostURL, 'livebasti.com', $imagePath);
		} else if (strtolower($city) == 'begusarai'){
			$newPath = str_replace($hostURL, 'livebegusarai.com', $imagePath);
		} else if (strtolower($city) == 'dhanbad'){
			$newPath = str_replace($hostURL.'/', '', $imagePath);
		} else if (strtolower($city) == 'gaya'){
			$newPath = str_replace($hostURL, 'livegaya.com', $imagePath);
		} else if (strtolower($city) == 'giridih'){
			$newPath = str_replace($hostURL, 'livegiridih.com', $imagePath);
		} else if (strtolower($city) == 'santkabirnagar'){
			$newPath = str_replace($hostURL, 'livesantkabirnagar.com', $imagePath);
		} else if (strtolower($city) == 'siddharthnagar'){
			$newPath = str_replace($hostURL, 'livesiddharthnagar.com', $imagePath);
		} else if (strtolower($city) == 'tehri'){
			$newPath = str_replace($hostURL, 'livetehri.com', $imagePath);
		}
	}
	return $newPath;
}

function replaceJunk($str){
	$str = str_replace('font-family: helvetica, arial, sans-serif;','', $str);	
	$str = str_replace('helvetica','', $str);
	$str = str_replace(' style=""','',$str);
	$str = str_replace('class="text_exposed_show" ','',$str);
	$str = str_replace(' background-color: rgb(255, 255, 255);','',$str);
	$str = str_replace('    line-height: 21.4667px;','',$str);
	$str = str_replace(' class="MsoNormal" ','', $str);	
	$str = str_replace('class="MsoNormal"','', $str);	
	$str = str_replace('background-image: initial; ','', $str); 
	$str = str_replace('background-attachment: initial; ','', $str); 
	$str = str_replace('background-size: initial; ','', $str); 
	$str = str_replace('background-origin: initial; ','', $str);
	$str = str_replace('background-clip: initial; ','', $str);
	$str = str_replace('background-position: initial; ','', $str);
	$str = str_replace('background-repeat: initial; ','', $str);
	$str = str_replace('<font color="#222222" face="Mangal, serif">','', $str);
	$str = str_replace('<span style="font-size: 18.6667px; line-height: 21.4667px;">','', $str);
	$str = str_replace('arial','', $str);	
	$str = str_replace('Mangal','', $str);	
	$str = str_replace('serif','', $str);	
	$str = str_replace('sans-serif','', $str);
	$str = str_replace('class="MsoNormal"','', $str);
	$str = str_replace('<p  style="        ">&nbsp;</p>','', $str);
	$str = str_replace('font-size: medium;','', $str);		
	$str = str_replace('font-family','', $str);	
	$str = str_replace('font-size: 14px;','', $str);
	$str = str_replace('line-height: 19.32px;','', $str);
	$str = str_replace('margin: 6px 0px;','', $str);
	$str = str_replace('color: rgb(20, 24, 35);','', $str);
	$str = str_replace('margin: 0px 0px 6px;','', $str);
	$str = str_replace('color: rgb(52, 52, 52);','', $str);
	$str = str_replace('margin-bottom: 0.0001pt;','', $str);
	$str = str_replace('line-height: 24.4pt;','', $str);
	$str = str_replace('background-image: initial;','', $str);
	$str = str_replace('background-attachment: initial;','', $str);
	$str = str_replace('background-size: initial;','', $str);
	$str = str_replace('background-origin: initial;','', $str);
	$str = str_replace('background-clip: initial;','', $str);
	$str = str_replace('background-position: initial;','', $str);
	$str = str_replace('background-repeat: initial;','', $str);
	$str = str_replace(' style="    "','', $str);
	$str = str_replace(' style=""','', $str);
	$str = str_replace('style=": , ; "','', $str);
	$str = str_replace(' lang="HI" ','', $str);
	$str = str_replace('style="        "','', $str);
	$str = str_replace('style=": , ;        "','', $str);
	$str = str_replace('<p  >&nbsp;</p>','', $str);
	$str = str_replace('data-block="true"','', $str);
	$str = str_replace('data-offset-key="1787j-0-0"','', $str);
	$str = str_replace('class="_209g _2vxa"','', $str);
	$str = str_replace('style="direction: ltr; position: relative;    line-height: 18px; white-space: pre-wrap;"','', $str);
	$str = str_replace('line-height: 18px;','', $str);
	$str = str_replace('white-space: pre-wrap;','', $str);
	$str = str_replace('class="text_exposed_show"','', $str);
	$str = str_replace('<div>&nbsp;</div>','', $str);
	$str = str_replace('<spanstyle="line-height: 115%; : , ;">','', $str);
	//$str = str_replace('</span>','', $str);
	
	if (strpos(strtolower($str), '/userfiles/') == 0)
	{
		$hostLink = $_SERVER['HTTP_HOST'];
		$hostLink = str_replace('http://','', $hostLink);
		$hostLink = str_replace('www.','', $hostLink);
		$str = str_replace('/userfiles/', 'http://www.'. $hostLink .'/userfiles/', $str);
	}
	$str = str_replace('<div>','<p>', $str);
	$str = str_replace('</div>','</p>', $str);
	return $str;
}

function domainCity($var1="",$var2="",$pool){
	$temp1 = strpos($pool,$var1)+strlen($var1);
	$result = substr($pool,$temp1,strlen($pool));
	$dd=strpos($result,$var2);
	if($dd == 0){
		$dd = strlen($result);
	}
	return substr($result,0,$dd);
}

function getPrakhand($pid)
{
	$sql = "SELECT `block_name` FROM `post` WHERE `post_id` = '".$pid."'";
	$res = Zend_Registry::get("db")->fetchRow($sql);
	if($res){
		return $res['block_name'];
	} else {
		return;
	}
}

function getLocation($location_id)
{
	$sql = "SELECT `block_name` FROM `location` WHERE `location_id` = '".$location_id."'";
	$res = Zend_Registry::get("db")->fetchRow($sql);
	if($res){
		return $res['block_name'];
	} else {
		return;
	}
}

function getReporter($idReporter){
	if ($idReporter !=''){
		$sql = "";
		$sql = "SELECT CONCAT(first_name, ' ', last_name) AS reporterName FROM `user` WHERE user_type = 2 AND `user_id` = '".$idReporter."'";
		//echo $sql;
		//exit;
		$rsRep = Zend_Registry::get("db")->fetchRow($sql);
		if($rsRep){
			return $rsRep['reporterName'];
		} else {
			return;
		}
	} else {
		return '';
	}
}

function getCategoryName($category_id)
{
	$sql = "SELECT `category` FROM `category` WHERE `category_id` = '".$category_id."'";
	$res = Zend_Registry::get("db")->fetchRow($sql);
	if($res){
		return $res['category'];
	} else {
		return;
	}
}

function compressImage($ext,$uploadedfile,$path,$actual_image_name,$newwidth)
{
	if($ext=="jpg" || $ext=="jpeg" )
	{
		$src = imagecreatefromjpeg($uploadedfile);
	}
	else if($ext=="png")
	{
		$src = imagecreatefrompng($uploadedfile);
	}
	else if($ext=="gif")
	{
		$src = imagecreatefromgif($uploadedfile);
	}
	else
	{
		$src = imagecreatefrombmp($uploadedfile);
	}

	list($width,$height)=getimagesize($uploadedfile);
	$newheight=($height/$width)*$newwidth;
	$tmp=imagecreatetruecolor($newwidth,$newheight);
	imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
	$filename = $path.$newwidth.'_'.$actual_image_name; //PixelSize_TimeStamp.jpg
	imagejpeg($tmp,$filename,100);
	imagedestroy($tmp);
	return $filename;
}

function GetBetween1($var1="",$var2="",$pool){
	$temp1 = strpos($pool,$var1)+strlen($var1);
	$result = substr($pool,$temp1,strlen($pool));
	$dd=strpos($result,$var2);
	if($dd == 0){
	$dd = strlen($result);
	}
	return substr($result,0,$dd);
}

function getTotalNoView($post_id){
	$mySQL = "";
	$mySQL = "SELECT COUNT(*) AS totCount FROM postview WHERE post_id = '".$post_id."'";
	$rsTemp1 = Zend_Registry::get("db")->fetchRow($mySQL);	
	
	if($rsTemp1){
		return $rsTemp1['totCount'];	
	}
}

function getDomainId($city)
{
	$mySQL = "";
	$mySQL = "SELECT domain_id FROM domain WHERE city = '".$city."'";
	$rsTemp1 = Zend_Registry::get("db")->fetchRow($mySQL);	
	
	if($rsTemp1){
		return $rsTemp1['domain_id'];	
	}
}

?>
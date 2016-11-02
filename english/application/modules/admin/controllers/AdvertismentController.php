<?php
class Admin_AdvertismentController extends Zend_Controller_Action
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
		$this->view->menuExp=15;
	
	}
	#####################################################################################################################################
	//CONTROLOR - POST LIST PAGE 
	function indexAction(){
		$host = $_SERVER['HTTP_HOST'];
		//echo $host;
		
		//$city = GetBetween1("live",".","$host");
		if ($_SERVER['HTTP_HOST'] == 'localhost') {
			$city = 'gaya';
		} else {
			$city = GetBetween1("live",".","$host");
		}
		//echo $userCity = str_replace(str_replace('live', '', $_SERVER['HTTP_HOST']), '.com', '', $_SERVER['HTTP_HOST']);
		//exit;
		$adminsession = new Zend_Session_Namespace('admin');
		$obj = new Default_Model_DbTable_User();
		$recods = $obj->getAdminByUser($adminsession->username);
		$userid = $recods['user_id'];
		$usertype = $recods['user_type'];
		$sql = "";
		$sql .= "SELECT * FROM `advertisment`";
		$sql .= " WHERE city = '".$city."' AND idadvertisment <> '' ";
		if(!empty($_REQUEST['search'])){
			$keyword = trim($_REQUEST['keyword']);
			//$sql .= " AND (`city` like '%$keyword%' OR `user_name` like '%$keyword%')";
			$sql .= " AND (";
			$sql .= " title LIKE '%$keyword%'";
			$sql .= " OR createdBy LIKE '%$keyword%'";
			$sql .= ")";
		}
		$sql .= " ORDER BY `idadvertisment` DESC";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchAll($sql);
		
		$this->view->data = $res;
		//$this->view->messages = $this->_helper->_flashMessenger->getMessages();
		//$this->render();
		
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
		$host = $_SERVER['HTTP_HOST'];
		//echo $host;
		
		//$city = GetBetween1("live",".","$host");
		if ($_SERVER['HTTP_HOST'] == 'localhost') {
			$city = 'gaya';
		} else {
			$city = GetBetween1("live",".","$host");
		}
		
		$GLOBALS['idadCategory'] = $this->_getParam('id');
			
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$error = array();
			
			$idadCategory = $formData['idadCategory'];
			$headline = addslashes($formData['headline']);
			$englishurl = addslashes($formData['englishurl']);
			//if($englishurl == ''){
			//	$englishurl = $headline;	
			//}
			$englishurl = str_replace("'", "", $englishurl);
			$englishurl = str_replace(',', '', $englishurl);
			$englishurl = str_replace(' ', '-', $englishurl);
				
			$adscriptforheadtag = addslashes($formData['adscriptforheadtag']);
			$adscriptforbodytag = addslashes($formData['adscriptforbodytag']);
			$startdate = addslashes($formData['startdate']);
			$enddate = addslashes($formData['enddate']);
			$isActive = $formData['isActive']; 
			
			//print_r($aaa);
			
			/** idGallary name validate*/
			if(!Zend_Validate::is($formData['idadCategory'],'NotEmpty')){
				$error['idadCategory']='Please select title';
			}
			
			/** headline validate*/
			if(!Zend_Validate::is($formData['headline'],'NotEmpty')){
				$error['headline']='Please enter headline';
			}
			
			/** startdate validate*/
			if(!Zend_Validate::is($formData['startdate'],'NotEmpty')){
				$error['startdate']='Please fill start date';
			}
			
			/** enddate url validate*/
			if(!Zend_Validate::is($formData['enddate'],'NotEmpty')){
				$error['enddate']='Please fill end date';
			}
			
			/** isActive url validate*/
			if(!Zend_Validate::is($formData['isActive'],'NotEmpty')){
				$error['isActive']='Please select status';
			}
			
			if(count($error)== 0){
				$adapter  = new Zend_File_Transfer_Adapter_Http();
				$fileInfo = $adapter->getFileInfo();
				//$returnFile = $formData['imageOld'];
				if($fileInfo['image']['name'] != "")
				{
					$bigImagePath = ADS_DIR_PATH.'/';
					$return = $this->uploadImage($adapter, $bigImagePath,TRUE);
					//echo "<pre>";
					//print_r($return); die;
					$returnFile = $return['fileName'];
					if($return['error']!= "")
					{
						$this->_flashMessenger->addMessage($return['error']);
						$this->_redirect('/admin/advertisment/index/');
					}
				}
				
								
				$adminsession = new Zend_Session_Namespace('admin');
				$obj = new Default_Model_DbTable_User();
				$recods = $obj->getAdminByUser($adminsession->username);
				$userid = $recods['user_id'];
				$usertype = $recods['user_type'];
				$username = $recods['first_name'];
				$createdOn = date('Y-m-d h:i:s');
				$createdBy = $username;
				
				//############//
				// INSERT NEW //
				//############//
				$mySql = "";
				$mySql = "INSERT INTO `advertisment` (";
				$mySql = $mySql . " `idadCategory`";
				$mySql = $mySql . ", `headline`";
				$mySql = $mySql . ", `city`";
				$mySql = $mySql . ", `englishurl`";
				$mySql = $mySql . ", `image`";
				$mySql = $mySql . ", `adscriptforheadtag`";
				$mySql = $mySql . ", `adscriptforbodytag`";
				$mySql = $mySql . ", `startdate`";
				$mySql = $mySql . ", `enddate`";
				$mySql = $mySql . ", `isActive`";
				$mySql = $mySql . ", `createdOn`";
				$mySql = $mySql . ", `createdBy`";
				$mySql = $mySql . " ) VALUES (";
				$mySql = $mySql . " '".$idadCategory."'";
				$mySql = $mySql . ", '".$headline."'";
				$mySql = $mySql . ", '".$city."'";
				$mySql = $mySql . ", '".$englishurl."'";
				$mySql = $mySql . ", '".$returnFile."'";
				$mySql = $mySql . ", '".$adscriptforheadtag."'";
				$mySql = $mySql . ", '".$adscriptforbodytag."'";
				$mySql = $mySql . ", '".$startdate."'";
				$mySql = $mySql . ", '".$enddate."'";
				$mySql = $mySql . ", '".$isActive."'";
				$mySql = $mySql . ", '".$createdOn."'";
				$mySql = $mySql . ", '".$createdBy."'";
				$mySql = $mySql . ")";
				//echo $mySql .'<BR>';
				//exit;
				$ss = Zend_Registry::get("db")->query($mySql);
				$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				
				//echo 'I am here...' . $lastinsertid . '<br>';
				//exit;
				
				//exit;
				if($ss){
					//assign message
					$this->_flashMessenger->addMessage('Advertisment added successfully');
					$this->_redirect('/admin/advertisment/index');
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
		$host = $_SERVER['HTTP_HOST'];
		//echo $host;
		
		//$city = GetBetween1("live",".","$host");
		if ($_SERVER['HTTP_HOST'] == 'localhost') {
			$city = 'gaya';
		} else {
			$city = GetBetween1("live",".","$host");
		}
		
		$idadvertisment = $this->_getParam('id');
		//echo $adminid;die();
		$sql="SELECT * FROM `advertisment` WHERE `idadvertisment` = '".$idadvertisment."'";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchrow($sql);
		
		$this->view->data = $res;
		
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$error = array();
			
			$idadCategory = $formData['idadCategory'];
			$headline = addslashes($formData['headline']);
			$englishurl = addslashes($formData['englishurl']);
			//if($englishurl == ''){
			//	$englishurl = $headline;	
			//}
			$englishurl = str_replace("'", "", $englishurl);
			$englishurl = str_replace(',', '', $englishurl);
			$englishurl = str_replace(' ', '-', $englishurl);
				
			$adscriptforheadtag = addslashes($formData['adscriptforheadtag']);
			$adscriptforbodytag = addslashes($formData['adscriptforbodytag']);
			$startdate = addslashes($formData['startdate']);
			$enddate = addslashes($formData['enddate']);
			$isActive = $formData['isActive']; 
			
			//print_r($aaa);
			
			/** idGallary name validate*/
			if(!Zend_Validate::is($formData['idadCategory'],'NotEmpty')){
				$error['idadCategory']='Please select title';
			}
			
			/** headline validate*/
			if(!Zend_Validate::is($formData['headline'],'NotEmpty')){
				$error['headline']='Please enter headline';
			}
			
			/** startdate validate*/
			if(!Zend_Validate::is($formData['startdate'],'NotEmpty')){
				$error['startdate']='Please fill start date';
			}
			
			/** enddate url validate*/
			if(!Zend_Validate::is($formData['enddate'],'NotEmpty')){
				$error['enddate']='Please fill end date';
			}
			
			/** isActive url validate*/
			if(!Zend_Validate::is($formData['isActive'],'NotEmpty')){
				$error['isActive']='Please select status';
			}
			
			if(count($error)== 0){
			$adapter  = new Zend_File_Transfer_Adapter_Http();
			$fileInfo = $adapter->getFileInfo();
			//$returnFile = $formData['imageOld'];
			if($fileInfo['image']['name'] != "")
			{
				$bigImagePath = ADS_DIR_PATH.'/';
				$return = $this->uploadImage($adapter, $bigImagePath,TRUE);
				//echo "<pre>";
				//print_r($return); die;
				$returnFile = $return['fileName'];
				if($return['error']!= "")
				{
					$this->_flashMessenger->addMessage($return['error']);
					$this->_redirect('/admin/advertisment/index/');
				}
			} else {
				$returnFile = $formData['imageOld'];
			}
								
			$adminsession = new Zend_Session_Namespace('admin');
			$obj = new Default_Model_DbTable_User();
			$recods = $obj->getAdminByUser($adminsession->username);
			$userid = $recods['user_id'];
			$usertype = $recods['user_type'];
			$username = $recods['first_name'];
			$createdOn = date('Y-m-d h:i:s');
			$editedBy = $username;
			
			//########//
			// UPDATE //
			//########//
			$mySql = "";
			$mySql = "UPDATE `advertisment` SET";
			$mySql .= " `idadCategory` = '".$idadCategory."'";
			$mySql .= ", `headline` = '".$headline."'";
			$mySql .= ", `city` = '".$city."'";
			$mySql .= ", `englishurl` = '".$englishurl."'";
			$mySql .= ", `image` = '".$returnFile."'";
			$mySql .= ", `adscriptforheadtag` = '".$adscriptforheadtag."'";
			$mySql .= ", `adscriptforbodytag` = '".$adscriptforbodytag."'";
			$mySql .= ", `startdate` = '".$startdate."'";
			$mySql .= ", `enddate` = '".$enddate."'";
			$mySql .= ", `isActive` = '".$isActive."'";
			$mySql .= ", `editedBy` = '".$editedBy."'";
			$mySql .= " WHERE idadvertisment = '".$idadvertisment."'";
			//echo $mySql;
			//die();
			$set = Zend_Registry::get("db")->query($mySql);
			
			//$lastinsertid = Zend_Registry::get("db")->lastInsertId();
			//echo 'I am here.!!!..';
			//die();	
				
			if($set){
				//assign message
				$this->_flashMessenger->addMessage('Advertisment edited successfully');
				$this->_redirect('/admin/advertisment/index');
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
	//CONTROLOR - POST ACTIVE/INACTIVE PAGE 
	public function activeAction(){
		$this->getHelper('viewRenderer')->setNoRender();
		$this->_helper->layout->disableLayout();	
		$id = $this->_getParam('id');
		//"UPDATE `user` SET `active`=abs(`active`-1) WHERE user_id='$id'";
		$sql ="UPDATE `advertisment` SET `isActive` = abs(`isActive`-1) WHERE idadvertisment = '".$id."'";
		//echo $sql;die();
		$del = Zend_Registry::get("db")->query($sql);
		
		if($del){
			$this->_flashMessenger->addMessage('Status changed.');
			$this->_redirect('/admin/advertisment/index');	
		}
	}
	//CONTROLOR - POST DELETE PAGE 
	public function deleteAction(){
		$this->getHelper('viewRenderer')->setNoRender();
		$this->_helper->layout->disableLayout();	
		$id = $this->_getParam('id');
		//"UPDATE `user` SET `active`=abs(`active`-1) WHERE user_id='$id'";
		//$sql ="UPDATE `post` SET `status` = '0', isDeleted = '1' WHERE `post_id` = '".$id."'";
		$sql = "DELETE FROM advertisment WHERE `idadvertisment` = '".$id."'";
		//echo $sql;die();
		$del = Zend_Registry::get("db")->query($sql);
		
		if($del){
			$this->_flashMessenger->addMessage('Selected record deleted');
			$this->_redirect('/admin/advertisment/index');	
		}
	}
	
	//CONTROLOR - POST AJAXCALL SUB CAT PAGE  
	public function ajaxsubcatAction(){
		$pid = $this -> _request -> getParam('pid');
		$category_id = $this -> _request -> getParam('category_id');
		//$subcategory_id = $this -> _request -> getParam('subcategory_id');
		//echo $category_id .'<hr>';
		//$block_name = getPrakhand($pid);
		//echo $pid;
		$sql = "SELECT * FROM `category` WHERE status = 1 AND parentId <> 0 AND parentId ='".$category_id."' ORDER BY sort";
		$res = Zend_Registry::get("db")->fetchAll($sql);
		
		$option = '';
		$option .= '<select id="subcategory_id" name="subcategory_id" class="select2" style="width:420px;height:35px;">';
		$option .= '<option value="">Select</option>';
		if($res){
			foreach($res as $us){
				//if($us['category_id'] == $category_id && $us['post_id'] == $pid){
				if ($us['category_id'] == selectedSubCatgories($pid, $category_id)){
					$option .= '<option selected="selected" value="'.$us['category_id'].'">'.$us['category'].'</option>';
				} else {
					$option .= '<option value="'.$us['category_id'].'">'.$us['category'].'</option>';
				}
				//$option .= '<option value ="'.$us['block_name'].'">'.$us['block_name'].'</option>';	
			}
		}
		$option .= '</select>';
		echo $option;		
	}
	
	//CONTROLOR - POST AJAXCALL PAGE  
	public function ajaxcallAction() {
		$city = $this -> _request -> getParam('city');
		$pid = $this -> _request -> getParam('pid');
		$location_id = $this -> _request -> getParam('location_Id');
		//echo $location_id;
		$block_name = getPrakhand($pid);
		//echo $block_name;
		$sql = "SELECT location_id, `block_name` FROM `location` WHERE status = 1 AND isDeleted = 0 AND `city`= '".$city."'";
		$res = Zend_Registry::get("db")->fetchAll($sql);
		
		if($res){
			$option = '';
			//$option .= '<select id="location_id" name="location_id" class="select2">';
			$option .= '<option value=""></option>';
			foreach($res as $us){
				if($us['location_id'] == $location_id){
					$option .=  '<option selected="selected" value="'.$us['location_id'].'">'.$us['block_name'].'</option>';
				} else {
					$option .=  '<option value="'.$us['location_id'].'">'.$us['block_name'].'</option>';
				}
				//$option .= '<option value ="'.$us['block_name'].'">'.$us['block_name'].'</option>';	
			}
			//$option .= '</select>';
		}
		echo $option;
	}
	
	// IMAGE UPLOAD
	public function uploadImage($adapter, $destination, $isThumb=FALSE)
	{
		$size = new Zend_Validate_File_Size(array('max' => '2097152')); //minimum bytes filesize
		//$fileType = new Zend_Validate_File_IsImage();
		//$adapter = new Zend_File_Transfer_Adapter_Http();
		$fileInfo = $adapter->getFileInfo();
		$fileName = explode('.', $fileInfo['image']['name']);
		$fileExt  = $fileName[count($fileName) - 1];
		$fileName = 'ADS_' . date('Ymdhis') . '.' . $fileExt;
		
		$adapter->setDestination($destination);
		//validator can be more than one...
		$adapter->setValidators(array($size));
	
		$errors = '';
		if (!$adapter->isValid())
		{
			$dataError = $adapter->getMessages();
			foreach ($dataError as $key => $row)
			{
				$errors = $row;
			} //set formElementErrors
			
			return array('fileName'=>$fileName,'error'=>$errors);
		}
		else if ($fileInfo['image']['name'] != "")
		{
			$adapter->addFilter('Rename', array('target' => $destination . '/' . $fileName, 'overwrite' => true));
			$adapter->receive();
			if($isThumb)
			{
				$img = imagecreatefromjpeg( $destination . '/' . $fileName );
				
				/*if($fileExt=="jpg" || $fileExt=="jpeg" )
				{
					$img = imagecreatefromjpeg($destination . '/' . $fileName);
				}
				else if($fileExt=="png")
				{
					$img = imagecreatefrompng($destination . '/' . $fileName);
				}
				else if($fileExt=="gif")
				{
					$img = imagecreatefromgif($destination . '/' . $fileName);
				}
				else
				{
					$img = imagecreatefrombmp($destination . '/' . $fileName);
				}*/
				
				$width = imagesx( $img );
				$height = imagesy( $img );
	
				// calculate thumbnail size
				$new_width = 180;
				$new_height = floor( $height * ( 180 / $width ) );
	
				// create a new temporary image
				$tmp_img = imagecreatetruecolor($new_width, $new_height);
				
	
				// copy and resize old image into new image
				imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
	
				imagejpeg( $tmp_img, $destination . '/thumb/' . $fileName );
				
			}
			return array('fileName'=>$fileName,'error'=>$errors);
		}	
	}

}//close class

function GetBetween1($var1="",$var2="",$pool){
	$temp1 = strpos($pool,$var1)+strlen($var1);
	$result = substr($pool,$temp1,strlen($pool));
	$dd=strpos($result,$var2);
	if($dd == 0){
	$dd = strlen($result);
	}
	return substr($result,0,$dd);
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

function replaceJunk1($str){
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

/*function getLocation($location_id)
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
*/
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


?>
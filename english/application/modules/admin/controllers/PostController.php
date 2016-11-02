<?php
class Admin_PostController extends Zend_Controller_Action
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
			$city = GetBetween1("live",".","$host");
		}
		//echo $userCity = str_replace(str_replace('live', '', $_SERVER['HTTP_HOST']), '.com', '', $_SERVER['HTTP_HOST']);
		//exit;
		$keyword = $this->_getParam('keyword');
		$createdfrom = $this->_getParam('createdfrom');
		$createdto = $this->_getParam('createdto');
		$orderby = $this->_getParam('orderby');
		
		$adminsession = new Zend_Session_Namespace('admin');
		$obj = new Default_Model_DbTable_User();
		$recods = $obj->getAdminByUser($adminsession->username);
		$userid = $recods['user_id'];
		$usertype = $recods['user_type'];
		$sql = "";
		$sql .= "SELECT *, DATE(created) AS createdOn, @s:=@s+1 sno FROM (SELECT @s:= 0) AS s, `post`";
		$sql .= " WHERE isDeleted = '0'";
		if(!empty($keyword)){
			//$sql .= " AND (`city` like '%$keyword%' OR `user_name` like '%$keyword%')";
			$sql .= " AND (";
			$sql .= " headline LIKE '%$keyword%'";
			$sql .= " OR content LIKE '%$keyword%'";
			$sql .= " OR content1 LIKE '%$keyword%'";
			$sql .= " OR english_url LIKE '%$keyword%'";
			$sql .= " OR intro LIKE '%$keyword%'";
			$sql .= " OR user_name like '%$keyword%'";
			$sql .= ")";
		} //else {
		//	$sql .= " AND city = '$city'";
		//}
		if( !empty($createdfrom) && !empty($createdto) ){
			$sql .= " AND DATE(created) >= '$createdfrom'";
			$sql .= " AND DATE(created) <= '$createdto'";
		} else if(!empty($createdfrom)){
			$sql .= " AND DATE(created) = '$createdfrom'";
		} else if(!empty($createdto)){
			$sql .= " AND DATE(created) <= '$createdto'";
		}
		
		if($usertype == 2){
			$sql .= " AND `user_id` = '".$userid."'";
		}
		if(!empty($orderby)){
			$sql .= " ORDER BY '".$orderby."'";
		} else {
			$sql .= " ORDER BY `post_id` DESC";
			$sql .= " LIMIT 2000";
		}
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchAll($sql);
		
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($res);
		$paginator->setItemCountPerPage(30);
		$paginator->setCurrentPageNumber($page);

		$this->view->perpage=30;
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
			
			$headline = addslashes($formData['headline']);
			$intro = addslashes($formData['intro']);
			$category = $formData['category'];
			$content = replaceJunk(addslashes($formData['content']));
			$content1 = replaceJunk(addslashes($formData['content1']));
			$englishurl = addslashes(str_replace("'", '', $formData['englishurl']));
			$postType = $formData['postType'];
			$metatitle = addslashes($formData['metatitle']);
			$metakeyword = addslashes($formData['metakeyword']);
			$metadescription = addslashes($formData['metadescription']);
			$priority = $formData['priority'];
			$sectionpriority = $formData['sectionpriority'];
			$idReporter = $formData['idReporter'];
			$reporterName = getReporter($idReporter);
			
			//$isSchedule = $formData['isSchedule'];
			$scheduleDate = $formData['scheduleDate'];
			$scheduleTime = $formData['scheduleTime'];
			$videoURL = $formData['videoURL'];
			//print_r($aaa);
			//die();
			
			
			if(empty($englishurl)){
				$englishurl = $headline;
			}
			$englishurl = str_replace("'", '', $englishurl);
			$englishurl = str_replace("/", '-', $englishurl);
			$englishurl = str_replace("?", '', $englishurl);
			$englishurl = str_replace("#", '', $englishurl);
			$englishurl = str_replace(" ", '-', $englishurl);
			$englishurl = str_replace("@", '', $englishurl);
			$englishurl = str_replace("%", '-percent-', $englishurl);
			$englishurl = str_replace("--", '-', $englishurl);
			$englishurl = str_replace("-", '-', $englishurl);
			
			/** headline validate*/
			if(!Zend_Validate::is($formData['headline'],'NotEmpty')){
				$error['headline']='Please enter headline';
			}
			
			/** intro validate*/
			if(!Zend_Validate::is($formData['intro'],'NotEmpty')){
				$error['intro']='Please enter intro';
			}
			
			/** category name validate*/
			if(!Zend_Validate::is($formData['category'],'NotEmpty')){
				$error['category']='Please select category';
			}
			
			/** english url validate*/
			if(!Zend_Validate::is($formData['englishurl'],'NotEmpty')){
				$error['englishurl']='Please enter english url';
			}
			
			
			//$idReporter
			//print_r (mbStringToArray($formData['content']));
			$arr = mbStringToArray($formData['content']);
			$vows = countVowels($arr); //Number of vowels 
			$cons = count($arr) - $vows; //Number of consonants
			//echo $cons;
			
			if(!Zend_Validate::is($formData['content'],'NotEmpty')){
				$error['content']='Please enter content';
			} else if ($cons < '200'){
				$error['content']='Please enter upto 200 characters in content. currently using '. $cons . ' characters';
			} else if (strpos($formData['content'], '<img ') == ''){
				$error['content']='Please enter one image in content';
			}
			
			if(!Zend_Validate::is($formData['content1'],'NotEmpty')){
				$error['content1']='Please enter final content';
			}
			
			if(count($error)== 0){
				//$bigImagePath = PRODUCT_DIR_PATH.'/image/';
				//$returnFile = $this->uploadImageNew($bigImagePath);
				
				$adapter  = new Zend_File_Transfer_Adapter_Http();
				$fileInfo = $adapter->getFileInfo();
				$returnFile = $formData['imageOld'];
				if($fileInfo['image']['name'] != "")
				{
					$bigImagePath = PRODUCT_DIR_PATH.'/image';
					//if ($_SERVER['HTTP_HOST'] != 'localhost'){
					//	$bigImagePath = replaceDomain($bigImagePath, $city, str_replace('www.','',$_SERVER['HTTP_HOST'])); 
					//}
					//echo 'I am testing...<br>Please wait... <br> Image Path = ' . $bigImagePath; 
					//exit;
					$return = $this->uploadImage($adapter, $bigImagePath,TRUE);
					//echo "<pre>";
					//print_r($return); die;
					$returnFile = $return['fileName'];
					if($return['error']!= "")
					{
						$this->_flashMessenger->addMessage($return['error']);
						$this->_redirect('/admin/post/index/');
						//$this->_redirect('/admin/post/add/');
					}
				}
								
				$adminsession = new Zend_Session_Namespace('admin');
				$obj = new Default_Model_DbTable_User();
				$recods = $obj->getAdminByUser($adminsession->username);
				$userid = $recods['user_id'];
				$usertype = $recods['user_type'];
				$username = $recods['first_name']." ".$recods['last_name'];
				$created_date = date('Y-m-d h:i:s');
				
				//if($usertype == 2){
				//$sql = "INSERT INTO `post` SET `user_id`= '$userid',`user_name`= '$username',`city`= '$city', `block_name`= '$block',`headline` = '$headline', `english_url` = '$englishurl', `meta_title` = '$metatitle', `meta_keyword` = '$metakeyword', `meta_desc` = '$metadescription',`intro` = '$intro',`image`= '$returnFile',`thumb_image`= '$returnFile', `content` = '$content', `status`= 0, `created`= '$created_date'";
				//}else{
				//$sql = "INSERT INTO `post` SET `user_id`= '$userid',`user_name`= '$username',`city`= '$city', `block_name`= '$block',`headline` = '$headline', `english_url` = '$englishurl', `meta_title` = '$metatitle', `meta_keyword` = '$metakeyword', `meta_desc` = '$metadescription',`intro` = '$intro',`image`= '$returnFile',`thumb_image`= '$returnFile', `content` = '$content', `status`= 1, `created`= '$created_date'";
				//}
				
				//##########################################################//
				//IF PRIORITY = 1 THEN EXISTING PRIORITY = '' RECORD IS NULL//
				//##########################################################//
								
				if($scheduleDate == '' && $scheduleTime == ''){
					setAutoPriority($priority);
				}
				
				//$imagePath = 'http://live'.strtolower($city).'.com/public/products/image/';
				//$thumbImagePath = 'http://live'.strtolower($city).'.com/public/products/image/thumb/';
				//###############//
				//INSERT NEW POST//
				//###############//
				$mySql = "";
				$mySql = "INSERT INTO `post` (";
				$mySql = $mySql . " `user_id`";
				$mySql = $mySql . ", `user_name`";
				$mySql = $mySql . ", `idReporter`";
				$mySql = $mySql . ", `ReporterName`";
				//$mySql = $mySql . ", `city`";
				//$mySql = $mySql . ", `location_id`";
				//$mySql = $mySql . ", `block_name`";
				$mySql = $mySql . ", `headline`";
				$mySql = $mySql . ", `postType`";
				$mySql = $mySql . ", `english_url`";
				$mySql = $mySql . ", `meta_title`";
				$mySql = $mySql . ", `meta_keyword`";
				$mySql = $mySql . ", `meta_desc`";
				$mySql = $mySql . ", `intro`";
				$mySql = $mySql . ", `videoURL`";
				$mySql = $mySql . ", `image`";
				//$mySql = $mySql . ", `imagePath`";
				$mySql = $mySql . ", `thumb_image`";
				//$mySql = $mySql . ", `thumbImagePath`";
				$mySql = $mySql . ", `content`";
				$mySql = $mySql . ", `content1`";
				$mySql = $mySql . ", `status`";
				$mySql = $mySql . ", `scheduleDate`";
				$mySql = $mySql . ", `scheduleTime`";
				$mySql = $mySql . ", `priority`";
				$mySql = $mySql . ", `sectionpriority`";
				$mySql = $mySql . ", `created`";
				$mySql = $mySql . ", `editedOn`";
				
				$mySql = $mySql . " ) VALUES (";
				
				$mySql = $mySql . " '".$userid."'";
				$mySql = $mySql . ", '".$username."'";
				$mySql = $mySql . ", '".$idReporter."'";
				$mySql = $mySql . ", '".$reporterName."'";				
				//$mySql = $mySql . ", '".$city."'";
				//$mySql = $mySql . ", '".$location_id."'";
				//$mySql = $mySql . ", '".$block_name."'";
				$mySql = $mySql . ", '".$headline."'";
				$mySql = $mySql . ", '".$postType."'";
				$mySql = $mySql . ", '".str_replace(' ', '-', strtolower($englishurl))."'";
				$mySql = $mySql . ", '".$metatitle."'";
				$mySql = $mySql . ", '".$metakeyword."'";
				$mySql = $mySql . ", '".$metadescription."'";
				$mySql = $mySql . ", '".$intro."'";
				$mySql = $mySql . ", '".$videoURL."'";
				$mySql = $mySql . ", '".$returnFile."'";
				//$mySql = $mySql . ", '".$imagePath.$returnFile."'";
				$mySql = $mySql . ", '".$returnFile."'";
				//$mySql = $mySql . ", '".$thumbImagePath.$returnFile."'";
				$mySql = $mySql . ", '".$content."'";
				$mySql = $mySql . ", '".$content1."'";
				if($scheduleDate != '' && $scheduleTime != ''){
					$mySql = $mySql . ", '0'";
					$mySql = $mySql . ", '".$scheduleDate."'";
					$mySql = $mySql . ", '".$scheduleTime."'";
					$mySql = $mySql . ", ''";
				
				} else {
					if($usertype == 2){
						$mySql = $mySql . ", '1'";
					} else {
						$mySql = $mySql . ", '1'";
					}
					$mySql = $mySql . ", ''";
					$mySql = $mySql . ", ''";
					$mySql = $mySql . ", '".$priority."'";
					$mySql = $mySql . ", '".$sectionpriority."'";
				}
				$mySql = $mySql . ", '".$created_date."'";
				$mySql = $mySql . ", '".$created_date."'";
				$mySql = $mySql . ")";
				//echo $mySql .'<BR>';
				//exit;
				$ss = Zend_Registry::get("db")->query($mySql);
				$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				
	
				//echo 'I am here...' . $lastinsertid . '<br>';
				//exit;
				
				//return setAutoPriority($priority, $lastinsertid, $city);
				
				if($category){
					foreach($category as $v){
						$sql = "";
						$sql = "INSERT INTO `post_category` SET `post_id`= '".$lastinsertid."',`category_id`= '".$v."',`category_name`= '".addslashes(getCategoryName($v))."'";
						//echo $sql;
						//die();
						$rr = Zend_Registry::get("db")->query($sql);
						
					}
				}
				//exit;
				if($rr){
					//$rsTemp = null;
					
					//assign message
					$this->_flashMessenger->addMessage('Post added successfully');
					$this->_redirect('/admin/post/index');
					//$this->_redirect('/admin/post/add/');
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
		
			$postid = $this->_getParam('id');
			//echo $adminid;die();
			$sql="SELECT * FROM `post` WHERE `post_id` = '".$postid."'";
			//echo $sql;
			//exit;
			$res = Zend_Registry::get("db")->fetchrow($sql);
			
			$this->view->data = $res;
			
			if($this->getRequest()->isPost()){
				$formData = $this->getRequest()->getPost();
				
				$error = array();
				
				$headline = addslashes($formData['headline']);
				$intro = addslashes($formData['intro']);
				$category = $formData['category'];
				$content = replaceJunk(addslashes($formData['content']));
				$content1 = replaceJunk(addslashes($formData['content1']));
				$englishurl = addslashes(str_replace("'", '', $formData['englishurl']));
				$postType = $formData['postType'];
				$metatitle = addslashes($formData['metatitle']);
				$metakeyword = addslashes($formData['metakeyword']);
				$metadescription = addslashes($formData['metadescription']);
				$priority = $formData['priority'];
				$sectionpriority = $formData['sectionpriority'];
				$idReporter = $formData['idReporter'];
				$reporterName = getReporter($idReporter);
				
				//$isSchedule = $formData['isSchedule'];
				$scheduleDate = $formData['scheduleDate'];
				$scheduleTime = $formData['scheduleTime'];
				$videoURL = $formData['videoURL'];
				//print_r($aaa);
				//die();
				
				
				if($englishurl == ''){
					$englishurl = $headline;
				}
				$englishurl = str_replace("'", '', $englishurl);
				$englishurl = str_replace("/", '-', $englishurl);
				$englishurl = str_replace("?", '', $englishurl);
				$englishurl = str_replace("#", '', $englishurl);
				$englishurl = str_replace(" ", '-', $englishurl);
				$englishurl = str_replace("@", '', $englishurl);
				$englishurl = str_replace("%", '-percent-', $englishurl);
				$englishurl = str_replace("--", '-', $englishurl);
				$englishurl = str_replace("-", '-', $englishurl);
				
				//echo $englishurl;
				//exit;
				
				/** headline validate*/
				if(!Zend_Validate::is($formData['headline'],'NotEmpty')){
					$error['headline']='Please enter headline';
				}
				
				/** intro validate*/
				if(!Zend_Validate::is($formData['intro'],'NotEmpty')){
					$error['intro']='Please enter intro';
				}
				
				/** category name validate*/
				if(!Zend_Validate::is($formData['category'],'NotEmpty')){
					$error['category']='Please select category';
				}
				
				/** english url validate*/
				if(!Zend_Validate::is($englishurl,'NotEmpty')){
					$error['englishurl']='Please enter english url';
				}
								
				//$idReporter
				$arr = mbStringToArray($formData['content']);
				$vows = countVowels($arr); //Number of vowels 
				$cons = count($arr) - $vows; //Number of consonants
				//echo $cons;
				
				if(!Zend_Validate::is($formData['content'],'NotEmpty')){
					$error['content']='Please enter content';
				} else if ($cons < '200'){
					$error['content']='Please enter upto 200 characters in content. currently using '. $cons . ' characters';
				} else if (strpos($formData['content'], '<img ') == ''){
					$error['content']='Please enter one image in content';
				}
				
				if(!Zend_Validate::is($formData['content1'],'NotEmpty')){
					$error['content1']='Please enter final content';
				}
				
				if(count($error)== 0){
					$adapter  = new Zend_File_Transfer_Adapter_Http();
					$fileInfo = $adapter->getFileInfo();
					if($fileInfo['image']['name'] != "")
					//if(isset($_FILES['image']))
					{
						$bigImagePath = PRODUCT_DIR_PATH.'/image/';
						//$bigImagePath = replaceDomain($bigImagePath, $city, $_SERVER['HTTP_HOST']);
						//if ($_SERVER['HTTP_HOST'] != 'localhost'){
						//	$bigImagePath = replaceDomain($bigImagePath, $city, str_replace('www.','',$_SERVER['HTTP_HOST'])); 
						//} 
						
						//echo 'I am testing...<br>Please wait... <br> Image Path = ' . $bigImagePath; 
						//exit;
						//$returnFile = $this->uploadImageNew($bigImagePath);
						//echo '$returnFile = ' . $returnFile . '<br>';
						
						$return = $this->uploadImage($adapter, $bigImagePath,TRUE);
						//$return = $this->compressImage($ext,$uploadedfile,$path,$actual_image_name,$newwidth);
						//echo "<pre>";
						//print_r($return); die;
						$returnFile = $return['fileName'];
						if($return['error']!= "")
						{
							$this->_flashMessenger->addMessage($return['error']);
							$this->_redirect('/admin/post/index/');
							//$this->_redirect('/admin/post/add/');
						}
					}else{
						$returnFile = $formData['imageOld'];
					}
									
					$adminsession = new Zend_Session_Namespace('admin');
					$obj = new Default_Model_DbTable_User();
					$recods = $obj->getAdminByUser($adminsession->username);
					$userid = $recods['user_id'];
					$username = $recods['first_name']." ".$recods['last_name'];
					$usertype = $recods['user_type'];
					$created_date = date('Y-m-d h:i:s');
					
					//##########################################################//
					//IF PRIORITY = 1 THEN EXISTING PRIORITY = '' RECORD IS NULL//
					//##########################################################//
					//if ($userId_reprterId_Is_Same == "YES"){
					//	if($scheduleDate == '' && $scheduleTime == ''){
					//		setAutoPriority($priority, $city);
					//	}
					//}
					
					//$imagePath = 'http://live'.strtolower($city).'.com/public/products/image/';
					//$thumbImagePath = 'http://live'.strtolower($city).'.com/public/products/image/thumb/';
					//##############################//
					//UPDATE SELECTED POST ID RECORD//
					//##############################//
					$mySQL = "UPDATE `post` SET";
					$mySQL = $mySQL . "  headline = '".$headline."'";
					$mySQL = $mySQL . ", postType = '".$postType."'";
					//if ($userId_reprterId_Is_Same == "YES"){
					//	$mySQL = $mySQL . ", user_id = '".$userid."'";
					//	$mySQL = $mySQL . ", user_name = '".$username."'";
					//	$mySQL = $mySQL . ", priority = '".$priority."'";
					//}
					$mySQL = $mySQL . ", idReporter = '".$idReporter."'";
					$mySQL = $mySQL . ", reporterName = '".$reporterName."'";
					//$mySQL = $mySQL . ", city = '".$city."'";
					//$mySQL = $mySQL . ", location_id = '".$location_id."'";
					//$mySQL = $mySQL . ", block_name = '".$block_name."'";
					$mySQL = $mySQL . ", english_url = '".str_replace(' ', '-', strtolower($englishurl))."'";
					$mySQL = $mySQL . ", meta_title = '".$metatitle."'";
					$mySQL = $mySQL . ", meta_keyword = '".$metakeyword."'";
					$mySQL = $mySQL . ", meta_desc = '".$metadescription."'";
					$mySQL = $mySQL . ", intro = '".$intro."'";
					$mySQL = $mySQL . ", videoURL = '".$videoURL."'";
					$mySQL = $mySQL . ", image = '".$returnFile."'";
					//$mySQL = $mySQL . ", imagePath = '".$imagePath.$returnFile."'";
					$mySQL = $mySQL . ", thumb_image = '".$returnFile."'";
					//$mySQL = $mySQL . ", thumbImagePath = '".$thumbImagePath.$returnFile."'";
					$mySQL = $mySQL . ", content = '".$content."'";
					$mySQL = $mySQL . ", content1 = '".$content1."'";
					//$mySQL = $mySQL . ", priority = '".$priority."'";
					$mySQL = $mySQL . ", sectionpriority = '".$sectionpriority."'";
					
					if($scheduleDate != '' && $scheduleTime != ''){
						$mySQL = $mySQL . ", scheduleDate = '".$scheduleDate."'";
						$mySQL = $mySQL . ", scheduleTime = '".$scheduleTime."'";
						$mySQL = $mySQL . ", status = '0'";
					} else {
						if($usertype == 2){
							$mySQL = $mySQL . ", status = '1'";
						} else {
							$mySQL = $mySQL . ", status = '1'";
							$mySQL = $mySQL . ", edited_by = '".$username."'";
						}
					}
					$mySQL = $mySQL . ", editedOn = '".$created_date."'";
					$mySQL = $mySQL . " WHERE post_id = '".$postid."'";
					//echo $mySQL;
					//die();
					$set = Zend_Registry::get("db")->query($mySQL);
					//$lastinsertid = Zend_Registry::get("db")->lastInsertId();
					
					//echo 'I am here.!!!..';
					//die();	
					if($category){
						$sql = "";
						$sql = "DELETE FROM `post_category` WHERE `post_id` = '".$postid."'";
						//echo $res;
						//die();
						$rr = Zend_Registry::get("db")->query($sql);
						foreach($category as $val){
							$sql = "";
							$sql = "INSERT INTO `post_category` SET `post_id`= '".$postid."',`category_id`= '".$val."',`category_name`= '".addslashes(getCategoryName($val))."'";
							//echo $sql;
							//die();
							$rr = Zend_Registry::get("db")->query($sql);
							
						}
						
						//return setAutoPriority($postid, $city);
					}
				if($rr){
					//$rsTemp = null;
					
					//assign message
					$this->_flashMessenger->addMessage('Post edited successfully');
					$this->_redirect('/admin/post/index');
					//$this->_redirect('/admin/post/add/');
					//echo 'I am here.!!!..';
					//exit;
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
	
	//CONTROLOR - COPY EDIT PAGE 
	function copyAction(){
		$postid = $this->_getParam('id');
		//echo $adminid;die();
		$sql="SELECT * FROM `post` WHERE `post_id` = '".$postid."'";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchrow($sql);
		
		$this->view->data = $res;
	}
	
	//CONTROLOR - POST ACTIVE/INACTIVE PAGE 
	public function activeAction(){
		$this->getHelper('viewRenderer')->setNoRender();
		$this->_helper->layout->disableLayout();	
		$id = $this->_getParam('id');
		//"UPDATE `user` SET `active`=abs(`active`-1) WHERE user_id='$id'";
		$sql ="UPDATE `post` SET `status` = abs(`status`-1) WHERE `post_id` = '".$id."'";
		//echo $sql;die();
		$del = Zend_Registry::get("db")->query($sql);
		
		if($del){
			$this->_flashMessenger->addMessage('Status changed.');
			$this->_redirect('/admin/post/');	
		}
	}
	
	//CONTROLOR - AJAX MEDIA UPLOAD
	public function ajaxmediauploadAction()
	{
		$host = $_SERVER['HTTP_HOST'];
		//echo $host;
		
		//$city = GetBetween1("live",".","$host");
		if ($_SERVER['HTTP_HOST'] == 'localhost') {
			$city = 'gaya';
		} else {
			$city = GetBetween1("live",".","$host");
		}
		
		$valid_formats = array("wav","mp3","oga");
		if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
		{
			echo "TYPE = " . basename($_FILES["audioFile"]["name"]) ."<br>";
				
			//if ((($_FILES["audioFile"]["type"] == "audio/mp3")
			//	|| ($_FILES["audioFile"]["type"] == "audio/mp4")
			//	|| ($_FILES["audioFile"]["type"] == "audio/wav")
			//	|| ($_FILES["audioFile"]["type"] == "image/jpeg")
			//	|| ($_FILES["audioFile"]["type"] == "audio/oga"))
			//	&& ($_FILES["audioFile"]["size"] < 20971520))
			
			$name = basename($_FILES["audioFile"]["name"]);
			//$size = $_FILES["audioFile"]["size"];
			//echo 'Name = '. $name ."<br>";
			//echo 'Size = '. $size ."<br>";
			
			if(strlen($name))
			{
				list($txt, $ext) = explode(".", $name);
				
				echo 'EXT = '. $ext ."<br>";
				//print_r (array_values($valid_formats)) ."<br>";
				//print_r (in_array($ext,$valid_formats)) ."<hr>";
			
				//if(in_array($ext,$valid_formats))
				if ( $ext == "mp3"
					||$ext == "wav"
					||$ext == "oga"
					||$ext == "jpg"
					)
				{
					//if($size<(2048*2048))
					//{
						$file_loc = $_FILES["audioFile"]["tmp_name"];
						$path = MEDIA_DIR_PATH .'/';
						$fileName = 'AUD_' . date('Ymdhis') . '.' . $ext;
						//$tmp = $_FILES['audioFile']['tmp_name'];
						echo 'tmp = ' . $file_loc ."<br>";
						echo 'path = ' . $path ."<br>";
						echo 'fileName = ' . $fileName ."<br>";
						echo 'Full PATH Name = ' . $path.$fileName ."<br>";
						//exit;
						if ($_FILES["audioFile"]["error"] > 0)
						{
							echo "Return Code: " . $_FILES["audioFile"]["error"] . "<br>";
						}
						if(move_uploaded_file($file_loc, $path.$fileName))
						{
							$post_id = ""; 
							$mediaTitle = $_POST['mediaTitle'];
							$mediaType = "AUDIO";
							$mediaCity = $city;
							//$sessionId = $_POST['sessionId'];;
							$createdOn = date('Y-m-d h:i:s');
							
							$mySql = "";
							$mySql = "INSERT INTO `postmedia` (";
							$mySql = $mySql . " `post_id`";
							$mySql = $mySql . ", `mediaTitle`";
							$mySql = $mySql . ", `fileName`";
							$mySql = $mySql . ", `mediaType`";
							$mySql = $mySql . ", `mediaCity`";
							//$mySql = $mySql . ", `sessionId`";
							$mySql = $mySql . ", `createdOn`";
							$mySql = $mySql . ") VALUES (";
							$mySql = $mySql . " '".$post_id."'";
							$mySql = $mySql . ", '".$mediaTitle."'";
							$mySql = $mySql . ", '".$fileName."'";
							$mySql = $mySql . ", '".$mediaType."'";
							$mySql = $mySql . ", '".$mediaCity."'";
							//$mySql = $mySql . ", '".$sessionId."'";
							$mySql = $mySql . ", '".$createdOn."'";				
							$mySql = $mySql . ")";
							//echo $mySql .'<BR>';
							//exit;
							$ss = Zend_Registry::get("db")->query($mySql);
							
							//echo "<img src='uploads/".$actual_image_name."'  class='preview'>";
						}
						else
							echo "failed";
					//}
					//else
					//	echo "Image file size max 1 MB";					
				}
				else
					echo "Invalid file format..";	
			}
			else
				echo "Please select audio file..!";
				
			//exit;
		}
		//if(!empty($pid)){
			$sql = "";
			$sql .= "SELECT * FROM `postmedia`";
			//$sql .= " WHERE post_id = '".$pid."'";
			$sql .= " ORDER BY idMedia DESC";
			//echo $sql;
			//exit;
			$rsTemp = Zend_Registry::get("db")->fetchAll($sql);
			
			//echo $sql;
			//exit;
			$i = 0;
			if($rsTemp){
				$tableValue = '';
				$tableValue .= '<table class="table table-bordered table-scroll mb30" cellspacing="0" width="99%">';
				$tableValue .= '<thead>';
					$tableValue .= '<tr>';
						$tableValue .= '<th width="20">#</th>';
						$tableValue .= '<th width="100">Title</th>';
						$tableValue .= '<th width="10">File</th>';
						$tableValue .= '<th width="40"></th>';
					$tableValue .= '</tr>';
				$tableValue .= '</thead>';
				$tableValue .= '<tbody>';
				foreach($rsTemp as $rsVal){
					$i++;
					$tableValue .= '<input type="hidden" name="idMedia" id="idMedia" value="'.$rsVal['idMedia'].'" />';
					$tableValue .= '<tr>';
					$tableValue .= '<td style="font-size:10px;">' . $i .'</td>';
					$tableValue .= '<td style="font-size:10px;">' . $rsVal['mediaTitle'] .'</td>';
					$tableValue .= '<td style="font-size:10px;">' . $rsVal['fileName'] .'</small></td>';
					$tableValue .= '<td style="font-size:10px;">';
					$tableValue .= '<a href="javascript:void(0)" onclick="deleteMedia('.$rsVal['idMedia'].','.$rsVal['post_id'].')" id="delMedia" title="Delete This?"><i class="fa fa-trash-o textred" style="font-size:11px;"></i></a>';
					$tableValue .= '</td>';
				$tableValue .= '</tr>';
				}
				$tableValue .= '</tbody>';
				$tableValue .= '</table>';
			}
			echo $tableValue;
		//}
	}
	
	//CONTROLOR - POST DELETE PAGE 
	public function deleteAction(){
		$this->getHelper('viewRenderer')->setNoRender();
		$this->_helper->layout->disableLayout();	
		$id = $this->_getParam('id');
		//"UPDATE `user` SET `active`=abs(`active`-1) WHERE user_id='$id'";
		//$sql ="UPDATE `post` SET `status` = '0', isDeleted = '1' WHERE `post_id` = '".$id."'";
		$sql = "DELETE FROM post WHERE `post_id` = '".$id."'";
		//echo $sql;die();
		$del = Zend_Registry::get("db")->query($sql);
		
		if($del){
			$this->_flashMessenger->addMessage('Selected record deleted');
			$this->_redirect('/admin/post/index');	
		}
	}
	//CONTROLOR - POST AJAXCALL PAGE  
	public function ajaxcallAction() {
		$city = $this -> _request -> getParam('city');
		$pid = $this -> _request -> getParam('pid');
		$location_id = $this -> _request -> getParam('location_Id');
		echo $location_id;
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
	
	// IMAGE UPLOAD NEW
	public function uploadImageNew($destination)
	{
		if(isset($_FILES['image'])){
		  	$errors= array();
		  	$file_name = $_FILES['image']['name'];
		  	$file_size = $_FILES['image']['size'];
		  	$file_tmp = $_FILES['image']['tmp_name'];
		  	$file_type = $_FILES['image']['type'];
		  
		  	//echo '$file_name = ' . $file_name .'<br>';
		  	//$path = $_FILES['image']['name'];
			//$fileExt = pathinfo($path, PATHINFO_EXTENSION);

			$array = explode('.', $_FILES['image']['name']);
			$fileExt = end($array);

			//$fileName1 = explode('.', $_FILES['image']['name']);
			//print_r($fileName1);
			//$fileExt  = $fileName1[count($fileName1) - 1];
			$fileName = 'img_' . date('Ymdhis') . '.' . $fileExt;
		
			//$file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));
			
			//$expensions= array("jpeg","jpg","png");
			
			//if(in_array($file_ext,$expensions)=== false){
			//   $errors[]="extension not allowed, please choose a JPEG or PNG file.";
			//}
			
			//if($file_size > 2097152){
			//   $errors[]='File size must be excately 2 MB';
			//}
			//echo '$fileName = ' . $fileName .'<br>' . $fileExt;
			//exit;
			//if(empty($errors)==true){
			move_uploaded_file($file_tmp, $destination . $fileName);
			
			return $fileName;
			//echo "Success";
			//}else{
			//   print_r($errors);
			//}
	   }	
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
		$fileName = 'img_' . date('Ymdhis') . '.' . $fileExt;
		
		//echo '$destination = ' . $destination . '<br>';
		//echo '$destination = ' . $destination . '<br>';
		//echo '$destination = ' . $destination . '<br>';
		//exit;
		
		$adapter->setDestination($destination);
		//echo '$destination = ' . $destination . '<br>';
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
				$new_width = 222;
				$new_height = floor( $height * ( 222 / $width ) );
	
				// create a new temporary image
				$tmp_img = imagecreatetruecolor($new_width, $new_height);
			
				// copy and resize old image into new image
				imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
	
				imagejpeg( $tmp_img, $destination . '/thumb/' . $fileName );
				
				// RIGHT COLUMN THUMB
				// FLICKER IMAGE
				// calculate thumbnail size
				$new_width = 80;
				$new_height = floor( $height * ( 179 / $width ) );
	
				// create a new temporary image
				$tmp_img = imagecreatetruecolor($new_width, $new_height);
			
				// copy and resize old image into new image
				imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
	
				imagejpeg( $tmp_img, $destination . '/thumb/' . 'thumb_' . $fileName );
				
				// FLICKER IMAGE
				// calculate thumbnail size
				$new_width = 360;
				$new_height = floor( $height * ( 659 / $width ) );
	
				// create a new temporary image
				$tmp_img = imagecreatetruecolor($new_width, $new_height);
			
				// copy and resize old image into new image
				imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
	
				imagejpeg( $tmp_img, $destination . '/thumb/' . 'flk_' . $fileName );
			}
			return array('fileName'=>$fileName,'error'=>$errors);
		}	
	}

}//close class

function setAutoPriority($start){
	if($start != ''){ 
		$editedOn = date('Y-m-d h:i:s');
		$i='';
		for($i=$start; $i<4; $i++)
		{
			$mySQL = "";
			$mySQL = $mySQL ." SELECT post_id FROM post WHERE `priority` = '".$i."';";
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
				if ($i == 3){
					$mySQL1 = $mySQL1 ." `priority` = ''";
				} else {
					$mySQL1 = $mySQL1 ." `priority` = '".$j."'";
				}
				$mySQL1 = $mySQL1 ." , `editedOn` = '".$editedOn."' WHERE";
				$mySQL1 = $mySQL1 ." `post_id` = '".$pid."'";
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
	
	//if (strpos(strtolower($str), '.com/userfiles/') == 0)
	//{
	//	$hostLink = $_SERVER['HTTP_HOST'];
	//	$hostLink = str_replace('http://','', $hostLink);
	//	$hostLink = str_replace('www.','', $hostLink);
	//	$str = str_replace('/userfiles/', 'http://'. $hostLink .'/userfiles/', $str);
	//}
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
	imagejpeg($tmp,$filename,222);
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
?>
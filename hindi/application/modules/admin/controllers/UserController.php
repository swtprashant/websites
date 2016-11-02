<?php
class Admin_UserController extends Zend_Controller_Action
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
############################################################################################################################################
	//default page of this controller
	
	function indexAction(){
		$obj = new Default_Model_DbTable_User();
		if(!empty($_REQUEST['search'])){
			$keyword = $_REQUEST['keyword']; 
			$records = $obj->get_view($keyword);
		}else{
			$records = $obj->get_view();
		}
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($records);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);

		$this->view->perpage=25;
		$this->view->pages=$page;
		$this->view->data=$paginator;
		$this->view->messages = $this->_helper->_flashMessenger->getMessages();
		$this->render();
	}

	//CONTROLOR - ADD PAGE 
	function addAction(){
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$error = array();
			
			$fname = ($formData['fname']);
			//$lname = ($formData['lname']);
			$email = addslashes($formData['email']);
			$mobileno = addslashes($formData['mobileno']);
			$password = addslashes($formData['password']);
			$cpassword = addslashes($formData['cpassword']);
			//$usersign = addslashes($formData['usersign']);
			//$city = addslashes($formData['city']);
			$user_type = addslashes($formData['user_type']);
			
			/*if (isset($formData['permAdd'])) {
				$permAdd = $formData['permAdd'];
			} else {
				$permAdd = '';
			}
			if (isset($formData['permEdit'])) {
				$permEdit = $formData['permEdit'];
			} else {
				$permEdit = '';
			}
			if (isset($formData['permDelete'])) {
				$permDelete = $formData['permDelete'];
			} else {
				$permDelete = '';
			}*/
			
			/** category name validate*/
			if(!Zend_Validate::is($formData['fname'],'NotEmpty')){
				$error['fname']='Please fill name';
			}
			
			/** block validate*/
			if(!Zend_Validate::is($formData['email'],'NotEmpty')){
				$error['email']='Please valid email';
			}
			
			/** database exist*/
			$validator = new Zend_Validate_Db_RecordExists(array('table' => 'user','field' => 'email' ));
			if($validator->isValid($formData['email'])){
				$error['email']=$formData['email'] .' email id already exist.';
			}
			
			/** password validate*/
			if(!Zend_Validate::is($formData['password'],'NotEmpty')){
				$error['password']='Please enter password';
			}
			/** Confirm password validate*/
			if(!Zend_Validate::is($formData['cpassword'],'NotEmpty')){
				$error['cpassword']='Please enter confirm password';
			}
			/** password validate*/
			if($formData['password'] != $formData['cpassword'] ){
				$error['cpassword']='Confirm passwod not same.';
			}
			/** Select City validate*/
			//if(!Zend_Validate::is($formData['city'],'NotEmpty')){
			//	$error['city']='Please select city';
			//}
			/** User Type validate*/
			if(!Zend_Validate::is($formData['user_type'],'NotEmpty')){
				$error['user_type']='Please select user type.';
			}
			
			if(count($error)== 0){
				$adapter  = new Zend_File_Transfer_Adapter_Http();
				$fileInfo = $adapter->getFileInfo();
				//$returnFile = $formData['imageOld'];
				if($fileInfo['image']['name'] != "")
				{
					
					//$bigImagePath = PRODUCT_DIR_PATH.'/images';
					$bigImagePath = USER_DIR_PATH.'/userimages';
					//$bigImagePath = getcwd().'/public/userimages';
					$return = $this->uploadImage($adapter, $bigImagePath,TRUE);
					//echo "<pre>";
					//print_r($return); die;
					$returnFile = $return['fileName'];
					if($return['error']!= "")
					{
						$this->_flashMessenger->addMessage($return['error']);
						$this->_redirect('/admin/user/index/');
					}
				} else {
					$returnFile = $formData['imageOld'];
				}
				
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
				$mySql = "INSERT INTO `user` (";
				$mySql = $mySql . " `first_name`";
				//$mySql = $mySql . ", `last_name`";
				$mySql = $mySql . ", `user_type`";
				$mySql = $mySql . ", `email`";
				$mySql = $mySql . ", `password`";
				$mySql = $mySql . ", `mobile_no`";
				//$mySql = $mySql . ", `city`";
				$mySql = $mySql . ", `userPhoto`";
				//$mySql = $mySql . ", `imagePath`";
				//$mySql = $mySql . ", `permAdd`";
				//$mySql = $mySql . ", `permEdit`";
				//$mySql = $mySql . ", `permDelete`";
				//$mySql = $mySql . ", `active`";
				$mySql = $mySql . ", `created`";
				//$mySql = $mySql . ", `created_by`";
				
				$mySql = $mySql . " ) VALUES (";
				
				$mySql = $mySql . " '".$fname."'";
				//$mySql = $mySql . ", '".$lname."'";
				$mySql = $mySql . ", '".$user_type."'";
				$mySql = $mySql . ", '".$email."'";				
				$mySql = $mySql . ", '".md5($password)."'";
				$mySql = $mySql . ", '".$mobileno."'";
				//$mySql = $mySql . ", '".$city."'";
				$mySql = $mySql . ", '".$returnFile."'";
				//$mySql = $mySql . ", '".$imagePath.$returnFile."'";
				//$mySql = $mySql . ", '".$permAdd."'";
				//$mySql = $mySql . ", '".$permEdit."'";
				//$mySql = $mySql . ", '".$permDelete."'";
				//if($usertype == 2){
				//	$mySql = $mySql . ", '0'";
				//} else {
				//	$mySql = $mySql . ", '1'";
				//}
				$mySql = $mySql . ", '".$created_date."'";
				//$mySql = $mySql . ", '".$created_date."'";
				$mySql = $mySql . ")";
				//echo $mySql .'<BR>';
				//exit;
				$ss = Zend_Registry::get("db")->query($mySql);
				$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				
				//echo 'I am here...' . $lastinsertid . '<br>';
				//exit;
				
				if($ss){
					$url = 'http://'.$_SERVER['HTTP_HOST']. '/admin';
					//echo "$url";die();
				
					$headers .= "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					$headers .= "From: info@thehook.news\r\n";
					$headers .= "Reply-To: noreply@thehook.news\r\n";
					$headers .= "Return-Path: noreply@thehook.news\r\n";
			
					$email = $formData['email'];
					///send mail
					//$msg ='';
					$msg .="Hi ".$formData['fname'].", ";
					$msg .='<br>';
					$msg .="Account login details:";
					$msg .='<br>';
					$msg .= "URL:".$url;
					$msg .='<br>';
					$msg .= "Username:".$formData['email'];
					$msg .='<br>';
					$msg .= "Password:".$formData['password'];
					$msg .='<br>';
					$msg .= 'Thanks';
					$sub='Login Details';
		
					@mail($email,$sub,$msg,$headers);	
					//assign message
					$this->_flashMessenger->addMessage('User added successfully');
					$this->_redirect('/admin/user/index');
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
	
	//CONTROLOR - EDIT PAGE
	function editAction(){
		$user_id = $this->_getParam('id');
		//echo $adminid;die();
		$sql="SELECT * FROM `user` WHERE `user_id` = '$user_id'";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchrow($sql);
		
		$this->view->data = $res;
		
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$error = array();
			
			$fname = ($formData['fname']);
			//$lname = ($formData['lname']);
			$email = addslashes($formData['email']);
			$mobileno = addslashes($formData['mobileno']);
			$password = addslashes($formData['password']);
			$cpassword = addslashes($formData['cpassword']);
			//$usersign = addslashes($formData['usersign']);
			//$city = addslashes($formData['city']);
			$user_type = addslashes($formData['user_type']);
			
			/*if (isset($formData['permAdd'])) {
				$permAdd = $formData['permAdd'];
			} else {
				$permAdd = '';
			}
			if (isset($formData['permEdit'])) {
				$permEdit = $formData['permEdit'];
			} else {
				$permEdit = '';
			}
			if (isset($formData['permDelete'])) {
				$permDelete = $formData['permDelete'];
			} else {
				$permDelete = '';
			}*/
			
			/** category name validate*/
			if(!Zend_Validate::is($formData['fname'],'NotEmpty')){
				$error['fname']='Please fill name';
			}
			
			/** email validate*/
			//if(!Zend_Validate::is($formData['email'],'NotEmpty')){
			//$error['email']='Please valid email';
			//}
			
			/** database exist*/
			//$validator = new Zend_Validate_Db_RecordExists(array('table' => 'user','field' => 'email' ));
			//if($validator->isValid($formData['email'])){
			//	$error['email']=$formData['email'] .' email id already exist.';
			//}
			
			/** password validate*/
			//if(!Zend_Validate::is($formData['password'],'NotEmpty')){
			//	$error['password']='Please enter password';
			//}
			/** Confirm password validate*/
			//if(!Zend_Validate::is($formData['cpassword'],'NotEmpty')){
			//	$error['cpassword']='Please enter confirm password';
			//}
			/** password validate*/
			if($formData['password'] != $formData['cpassword'] ){
				$error['cpassword']='Confirm passwod not same.';
			}
			/** Select City validate*/
			//if(!Zend_Validate::is($formData['city'],'NotEmpty')){
			//	$error['city']='Please select city';
			//}
			/** User Type validate*/
			if(!Zend_Validate::is($formData['user_type'],'NotEmpty')){
				$error['user_type']='Please select user type.';
			}
			
			if(count($error)== 0){
				$adapter  = new Zend_File_Transfer_Adapter_Http();
				$fileInfo = $adapter->getFileInfo();
				//$returnFile = $formData['imageOld'];
				if($fileInfo['image']['name'] != "")
				{
					
					//$bigImagePath = PRODUCT_DIR_PATH.'/images';
					$bigImagePath = USER_DIR_PATH.'/userimages';
					//$bigImagePath = getcwd().'/public/userimages';
					$return = $this->uploadImage($adapter, $bigImagePath,TRUE);
					//echo "<pre>";
					//print_r($return); die;
					$returnFile = $return['fileName'];
					if($return['error']!= "")
					{
						$this->_flashMessenger->addMessage($return['error']);
						$this->_redirect('/admin/user/index/');
					}
				}else{
					$returnFile = $formData['imageOld'];
				}
							
				$adminsession = new Zend_Session_Namespace('admin');
				$obj = new Default_Model_DbTable_User();
				$recods = $obj->getAdminByUser($adminsession->username);
				$userid = $recods['user_id'];
				$usertype = $recods['user_type'];
				$username = $recods['first_name']." ".$recods['last_name'];
				$created_date = date('Y-m-d h:i:s');
				
				//###############//
				//UPDATE NEW POST//
				//###############//
				$mySql = "";
				$mySql = "UPDATE `user` SET";
				$mySql = $mySql . " `first_name` = '".$fname."'";
				//$mySql = $mySql . ", `last_name` = '".$lname."'";
				$mySql = $mySql . ", `user_type` = '".$user_type."'";
				$mySql = $mySql . ", `email` = '".$email."'";
				if(!empty($password)){	
					$mySql = $mySql . ", `password` = '".md5($password)."'";
				}
				$mySql = $mySql . ", `mobile_no` = '".$mobileno."'";
				//$mySql = $mySql . ", `city` = '".$city."'";
				$mySql = $mySql . ", `userPhoto` = '".$returnFile."'";
				//$mySql = $mySql . ", `imagePath` = '".$imagePath.$returnFile."'";
				//$mySql = $mySql . ", `permAdd` = '".$permAdd."'";
				//$mySql = $mySql . ", `permEdit` = '".$permEdit."'";
				//$mySql = $mySql . ", `permDelete` = '".$permDelete."'";
				//$mySql = $mySql . ", `active` = '1'";
				//$mySql = $mySql . ", `created` = '".$created_date."'";
				$mySql = $mySql . " WHERE user_id = '".$formData['user_id']."'";
				//echo $mySql .'<BR>';
				//exit;
				
				$ss = Zend_Registry::get("db")->query($mySql);
				$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				
				//echo 'I am here...' . $lastinsertid . '<br>';
				//exit;
				
				if($ss){
					//assign message
					$this->_flashMessenger->addMessage('User updated successfully');
					$this->_redirect('/admin/user/index');
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
	
	//CONTROLOR - POST DELETE PAGE 
	public function deleteAction(){
		$this->getHelper('viewRenderer')->setNoRender();
		$this->_helper->layout->disableLayout();	
		$id = $this->_getParam('id');
		//"UPDATE `user` SET `active`=abs(`active`-1) WHERE user_id='$id'";
		$sql ="DELETE FROM `user` WHERE `user_id` = '".$id."'";
		//echo $sql;die();
		$del = Zend_Registry::get("db")->query($sql);
		
		if($del){
			$this->_flashMessenger->addMessage('Selected record deleted');
			$this->_redirect('/admin/user/');	
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
				$new_width = 55;
				$new_height = floor( $height * ( 55 / $width ) );
	
				// create a new temporary image
				$tmp_img = imagecreatetruecolor($new_width, $new_height);
				
	
				// copy and resize old image into new image
				imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
	
				imagejpeg( $tmp_img, $destination . '/thumb/' . $fileName );
	
			}
			return array('fileName'=>$fileName,'error'=>$errors);
		}	
	}
	
	

/**
	* Activate or deactive
	*/
	
	public function activeAction(){
	$this->getHelper('viewRenderer')->setNoRender();
	$this->_helper->layout->disableLayout();	
	
	$id = $this->_getParam('id');
	$obj = new Default_Model_DbTable_User();
	$obj->statuschange($id);
	$this->_flashMessenger->addMessage('Status changed.');
	$this->_redirect('/admin/user/');
	}

}//close class
?>
<?php
class Admin_DomainController extends Zend_Controller_Action
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
			
			$obj = new Default_Model_DbTable_Domain();
			$records = $obj->get_view();
			
			$page=$this->_getParam('page',1);
			$paginator = Zend_Paginator::factory($records);
			$paginator->setItemCountPerPage(20);
			$paginator->setCurrentPageNumber($page);
	
			$this->view->perpage=20;
			$this->view->pages=$page;
			$this->view->data=$paginator;
			$this->view->messages = $this->_helper->_flashMessenger->getMessages();
			$this->render();
	}

	function addAction(){
		
		if($this->getRequest()->isPost()){
		
			$formData = $this->getRequest()->getPost();
			//print_r($formData);
			//validation
			$error = array();
			/** page name validate*/
			if(!Zend_Validate::is($formData['dhindi'],'NotEmpty')){
			$error['dhindi']='Please enter domain in hindi';
			}
			/** database exist*/
			$validator = new Zend_Validate_Db_RecordExists(array('table' => 'domain','field' => 'domain_hindi' ));
			if($validator->isValid($formData['dhindi'])){
			$error['dhindi']=$formData['dhindi'] .' domain already exist.';
			}
			/** page name validate*/
			if(!Zend_Validate::is($formData['denglish'],'NotEmpty')){
			$error['denglish']='Please enter domain in english';
			}
			/** database exist*/
			$validator = new Zend_Validate_Db_RecordExists(array('table' => 'domain','field' => 'domain_english' ));
			if($validator->isValid($formData['denglish'])){
			$error['denglish']=$formData['denglish'] .' domain already exist.';
			}
			
			/** page name validate*/
			if(!Zend_Validate::is($formData['sname'],'NotEmpty')){
			$error['sname']='Please enter state name in hindi';
			}
					
			if(count($error)== 0){
			$obj = new Default_Model_DbTable_Domain();
			//add value in database
			$re = $obj->add_domain(addslashes($formData['dhindi']),$formData['denglish'],$formData['city'],addslashes($formData['sname']));
			if($re){
			//assign message
			$this->_flashMessenger->addMessage('Domain Added successfully');
			$this->_redirect('/admin/domain/index');
			}else{
			
			$this->view->msg = 'Error , try agian.';
			}
				
			}else{
			
			//error send to view
			$this->view->error = $error;
			}
		}
	}

	function editAction(){
		$domain_id = $this->_getParam('id');
		//echo $adminid;die();
		$sql="SELECT * FROM `domain` WHERE `domain_id` = '".$domain_id."'";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchrow($sql);
		
		$this->view->data = $res;
		
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
				
			$error = array();
			$domain_hindi = $formData['domain_hindi'];
			$domain_english = ($formData['domain_english']);
			$city = addslashes($formData['city']);
			$facebookURL = addslashes($formData['facebookURL']);
			$twitterURL = addslashes($formData['twitterURL']);
			$googleAnalyticTrackingId = addslashes($formData['googleAnalyticTrackingId']);
			$googleAdSenseAccount = addslashes($formData['googleAdSenseAccount']);
			$googleSiteVerification = addslashes($formData['googleSiteVerification']);
			$Contact = $formData['Contact'];
			
			$metaDescription = $formData['metaDescription'];
			$metaKeywords = $formData['metaKeywords'];
			$metaNewsKeywords = $formData['metaNewsKeywords'];
			$metaAbstract = $formData['metaAbstract'];
			
			$status = $formData['status'];
			
			/** domain_hindi validate*/
			if(!Zend_Validate::is($formData['domain_hindi'],'NotEmpty')){
				$error['domain_hindi']='Please fill hindi name';
			}
			
			/** domain_english validate*/
			if(!Zend_Validate::is($formData['domain_english'],'NotEmpty')){
			$error['domain_english']='Please fill english name';
			}
			
			/** headline validate*/
			if(!Zend_Validate::is($formData['city'],'NotEmpty')){
			$error['city']='Please enter city';
			}
			
			if(count($error)== 0){
				$adapter  = new Zend_File_Transfer_Adapter_Http();
				$fileInfo = $adapter->getFileInfo();
				if($fileInfo['image']['name'] != "")
				{
					$bigImagePath = LOGO_DIR_PATH;
					
					//echo 'I am testing...<br>Please wait... <br> Image Path = ' . $bigImagePath; 
					//exit;
					$return = $this->uploadImage($adapter, $bigImagePath, $city, TRUE);
					//$return = $this->compressImage($ext,$uploadedfile,$path,$actual_image_name,$newwidth);
					//echo "<pre>";
					//print_r($return); die;
					$returnFile = $return['fileName'];
					if($return['error']!= "")
					{
						$this->_flashMessenger->addMessage($return['error']);
						$this->_redirect('/admin/post/index/');
					}
				}else{
					$returnFile = $formData['imageOld'];
				}
								
				$adminsession = new Zend_Session_Namespace('admin');
				$obj = new Default_Model_DbTable_User();
				$recods = $obj->getAdminByUser($adminsession->username);
				$userid = $recods['user_id'];
				$username = $recods['first_name']." ".$recods['last_name'];
				$created_date = date('Y-m-d h:i:s');
				
				//##############################//
				//UPDATE SELECTED POST ID RECORD//
				//##############################//
				$mySQL = "UPDATE `domain` SET";
				$mySQL = $mySQL . "  domain_hindi = '".$domain_hindi."'";
				$mySQL = $mySQL . ", domain_english = '".$domain_english."'";
				$mySQL = $mySQL . ", city = '".$city."'";
				$mySQL = $mySQL . ", facebookURL = '".$facebookURL."'";
				$mySQL = $mySQL . ", twitterURL = '".$twitterURL."'";
				$mySQL = $mySQL . ", googleAnalyticTrackingId = '".$googleAnalyticTrackingId."'";
				$mySQL = $mySQL . ", googleAdSenseAccount = '".$googleAdSenseAccount."'";
				$mySQL = $mySQL . ", googleSiteVerification = '".$googleSiteVerification."'";
				$mySQL = $mySQL . ", Contact = '".$Contact."'";
				$mySQL = $mySQL . ", metaDescription = '".$metaDescription."'";
				$mySQL = $mySQL . ", metaKeywords = '".$metaKeywords."'";
				$mySQL = $mySQL . ", metaNewsKeywords = '".$metaNewsKeywords."'";
				$mySQL = $mySQL . ", metaAbstract = '".$metaAbstract."'";
				$mySQL = $mySQL . ", status = ".$status."";
				$mySQL = $mySQL . ", logoImage = '".$returnFile."'";
				$mySQL = $mySQL . ", logoImagePath = '/public/sitelogo/".$returnFile."'";
				$mySQL = $mySQL . " WHERE domain_id = '".$domain_id."'";
				//echo $mySQL;
				//die();
				$set = Zend_Registry::get("db")->query($mySQL);
				//$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				
				
				//echo 'I am here.!!!..';
				//die();	
			if($set){
				//assign message
				$this->_flashMessenger->addMessage('Domain edited successfully');
				$this->_redirect('/admin/domain/index');
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

	// IMAGE UPLOAD
	public function uploadImage($adapter, $destination, $city, $isThumb=FALSE)
	{
		$size = new Zend_Validate_File_Size(array('max' => '200000')); //minimum bytes filesize
		//$fileType = new Zend_Validate_File_IsImage();
		//$adapter = new Zend_File_Transfer_Adapter_Http();
		$fileInfo = $adapter->getFileInfo();
		$fileName = explode('.', $fileInfo['image']['name']);
		$fileExt  = $fileName[count($fileName) - 1];
		$fileName = 'logo_' . $city . '.' . $fileExt;
		
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
				
				//$width = imagesx( $img );
				//$height = imagesy( $img );
	
				// calculate thumbnail size
				//$new_width = 100;
				//$new_height = floor( $height * ( 100 / $width ) );
	
				// create a new temporary image
				//$tmp_img = imagecreatetruecolor($new_width, $new_height);
				
	
				// copy and resize old image into new image
				//imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
	
				//imagejpeg( $tmp_img, $destination . '/thumb/' . $fileName );
	
			}
			return array('fileName'=>$fileName,'error'=>$errors);
		}	
	}
}//close class

?>
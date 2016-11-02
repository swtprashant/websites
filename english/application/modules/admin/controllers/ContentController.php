<?php
class Admin_ContentController extends Zend_Controller_Action
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
			//echo $userCity = str_replace(str_replace('live', '', $_SERVER['HTTP_HOST']), '.com', '', $_SERVER['HTTP_HOST']);
			//exit;
			$adminsession = new Zend_Session_Namespace('admin');
			$obj = new Default_Model_DbTable_User();
			$recods = $obj->getAdminByUser($adminsession->username);
			$userid = $recods['user_id'];
			$usertype = $recods['user_type'];
			$sql = "";
			$sql .= "SELECT * FROM `content`";
			$sql .= " WHERE isDeleted = '0'";
			//echo $sql;
			//exit;
			$res = Zend_Registry::get("db")->fetchAll($sql);
			
			$this->view->data = $res;
			$this->view->messages = $this->_helper->_flashMessenger->getMessages();
			$this->render();
		
	}
	//CONTROLOR - CONTENT ADD PAGE 
	function addAction(){
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$error = array();
			$contentHeading = $formData['contentHeading'];
			$contentDetail = addslashes($formData['contentDetail']);
			
			/** contentHeading name validate*/
			if(!Zend_Validate::is($formData['contentHeading'],'NotEmpty')){
			$error['contentHeading']='Please select heading';
			}
			
			/** contentDetail validate*/
			if(!Zend_Validate::is($formData['contentDetail'],'NotEmpty')){
			$error['contentDetail']='Please fill detail of content';
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
				//     INSERT    //
				//###############//
				$mySql = "";
				$mySql = "INSERT INTO `content` (";
				$mySql = $mySql . "  `contentHeading`";
				$mySql = $mySql . ", `contentDetail`";
				$mySql = $mySql . ", `isDeleted`";
				$mySql = $mySql . " ) VALUES (";
				$mySql = $mySql . "  '".$contentHeading."'";
				$mySql = $mySql . ", '".$contentDetail."'";
				$mySql = $mySql . ", '0'";				
				$mySql = $mySql . ")";
				//echo $mySql;
				//exit;
				$ss = Zend_Registry::get("db")->query($mySql);
				$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				
				//echo 'I am here...';
				//exit;
				if($ss){
					//assign message
					$this->_flashMessenger->addMessage('Content added successfully');
					$this->_redirect('/admin/content/index');
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

	//CONTROLOR - CONTENT EDIT PAGE 
	function editAction(){
		
		$idContent = $this->_getParam('id');
		//echo $adminid;die();
		$sql="SELECT * FROM `content` WHERE `idContent` = '".$idContent."'";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchrow($sql);
		
		$this->view->data = $res;
		
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$error = array();
			
			//$idContent = getLocation($idContent);
			$contentHeading = addslashes($formData['contentHeading']);
			$contentDetail = addslashes($formData['contentDetail']);
			//$isDeleted = $formData['isDeleted'];
			
			/** contentHeading name validate*/
			if(!Zend_Validate::is($formData['contentHeading'],'NotEmpty')){
				$error['contentHeading']='Please select heading';
			}
			
			/** contentDetail validate*/
			if(!Zend_Validate::is($formData['contentDetail'],'NotEmpty')){
			$error['contentDetail']='Please fill detail of content';
			}
			
			if(count($error)== 0){
				$adminsession = new Zend_Session_Namespace('admin');
				$obj = new Default_Model_DbTable_User();
				$recods = $obj->getAdminByUser($adminsession->username);
				$userid = $recods['user_id'];
				$username = $recods['first_name']." ".$recods['last_name'];
				$created_date = date('Y-m-d h:i:s');
				
				//##############//
				//    UPDATE    //
				//##############//
				$mySQL = "UPDATE `content` SET";
				$mySQL = $mySQL . "  contentHeading = '".$contentHeading."'";
				$mySQL = $mySQL . ", contentDetail = '".$contentDetail."'";
				//$mySQL = $mySQL . ", isDeleted = '".$isDeleted."'";
				$mySQL = $mySQL . " WHERE idContent = '".$idContent."'";
				//echo $mySQL;
				//die();
				$set = Zend_Registry::get("db")->query($mySQL);
				
				//$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				//echo 'I am here.!!!..';
				//die();	
				
				if($set){
					//assign message
					$this->_flashMessenger->addMessage('Content edited successfully');
					$this->_redirect('/admin/content/index');
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
		$sql ="UPDATE `content` SET isDeleted = '1' WHERE `idContent` = '".$id."'";
		//echo $sql;die();
		$del = Zend_Registry::get("db")->query($sql);
		
		if($del){
			$this->_flashMessenger->addMessage('Selected record deleted');
			$this->_redirect('/admin/content/index');	
		}
	}
}//close class

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
	$str = str_replace('font-size: 14px;','', $str);
	$str = str_replace('line-height: 19.32px;','', $str);
	$str = str_replace('margin: 6px 0px;','', $str);
	$str = str_replace('color: rgb(20, 24, 35);','', $str);
	$str = str_replace('margin: 0px 0px 6px;','', $str);
	$str = str_replace(' style="    "','', $str);
	$str = str_replace(' style=""','', $str);
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

?>
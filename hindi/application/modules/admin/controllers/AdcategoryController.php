<?php
class Admin_AdcategoryController extends Zend_Controller_Action
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

    	$obj = new Admin_Model_DbTable_Adscategory();
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
			
			$error = array();
			
			$adcategory = addslashes($formData['adcategory']);
			$isActive = $formData['isActive'];
			
			/** category name validate*/
			if(!Zend_Validate::is($formData['adcategory'],'NotEmpty')){
				$error['adcategory']='Please enter category';
			}
			
			/** status validate*/
			if(!Zend_Validate::is($formData['isActive'],'NotEmpty')){
				$error['isActive']='Please select status';
			}
			
			if(count($error)== 0){
				$adminsession = new Zend_Session_Namespace('admin');
				$obj = new Default_Model_DbTable_User();
				$recods = $obj->getAdminByUser($adminsession->username);
				$userid = $recods['user_id'];
				$username = $recods['first_name'];
				$createdOn = date('Y-m-d h:i:s');
				
				//##############################//
				//UPDATE SELECTED POST ID RECORD//
				//##############################//
				$mySQL = "INSERT INTO `adcategory` (";
				$mySQL .= "adcategory";
				$mySQL .= ", isActive";
				$mySQL .= ", createdOn";
				$mySQL .= ", createdBy";
				$mySQL .= ") VALUES (";
				$mySQL .= " '".$adcategory."'";
				$mySQL .= ", '".$isActive."'";
				$mySQL .= ", '".$createdOn."'";
				$mySQL .= ", '".$username."'";
				$mySQL .= ")";
				
				//echo $mySQL;
				//die();
				$set = Zend_Registry::get("db")->query($mySQL);
				
				//$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				//echo 'I am here.!!!..';
				//die();	
				
				if($set){
					//assign message
					$this->_flashMessenger->addMessage('Category added successfully');
					$this->_redirect('/admin/adcategory/index');
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

	function editAction(){
		$idadCategory = $this->_getParam('id');
		//echo $adminid;die();
		$sql="SELECT * FROM `adcategory` WHERE `idadCategory` = '".$idadCategory."'";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchrow($sql);
		
		$this->view->data = $res;
		
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$error = array();
			
			$adcategory = addslashes($formData['adcategory']);
			$isActive = $formData['isActive'];
			
			/** category name validate*/
			if(!Zend_Validate::is($formData['adcategory'],'NotEmpty')){
				$error['adcategory']='Please enter category';
			}
			
			/** status validate*/
			if(!Zend_Validate::is($formData['isActive'],'NotEmpty')){
				$error['isActive']='Please select status';
			}
			
			if(count($error)== 0){
				$adminsession = new Zend_Session_Namespace('admin');
				$obj = new Default_Model_DbTable_User();
				$recods = $obj->getAdminByUser($adminsession->username);
				$userid = $recods['user_id'];
				$username = $recods['first_name'];
				$createdOn = date('Y-m-d h:i:s');
				
				//##############################//
				//UPDATE SELECTED POST ID RECORD//
				//##############################//
				$mySQL = "UPDATE `adcategory` SET";
				$mySQL = $mySQL . "  adcategory = '".$adcategory."'";
				$mySQL = $mySQL . ", isActive = '".$isActive."'";
				$mySQL = $mySQL . " WHERE idadCategory = '".$idadCategory."'";
				//echo $mySQL;
				//die();
				$set = Zend_Registry::get("db")->query($mySQL);
				
				//$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				//echo 'I am here.!!!..';
				//die();	
				
				if($set){
					//assign message
					$this->_flashMessenger->addMessage('Category edited successfully');
					$this->_redirect('/admin/adcategory/index');
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
		$sql ="DELETE FROM `adcategory` WHERE `idadCategory` = '".$id."'";
		//echo $sql;die();
		$del = Zend_Registry::get("db")->query($sql);
		
		if($del){
			$this->_flashMessenger->addMessage('Selected record deleted');
			$this->_redirect('/admin/adcategory/index');	
		}
	}

/**
	* Activate or deactive
	*/
	
	public function activeAction(){
		$this->getHelper('viewRenderer')->setNoRender();
		$this->_helper->layout->disableLayout();	
		
		$id = $this->_getParam('id');
		$obj = new Default_Model_DbTable_Category();
		$obj->statuschange($id);
		$this->_flashMessenger->addMessage('Status changed.');
		$this->_redirect('/admin/adcategory/index');
	}

}//close class
?>
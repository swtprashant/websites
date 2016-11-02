<?php
class Admin_CategorysubController extends Zend_Controller_Action
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

    	//$obj = new Default_Model_DbTable_Category();
		//$records = $obj->get_view();
		
		$sql = "SELECT *, @s:=@s+1 sno FROM (SELECT @s:= 0) AS s, `category` WHERE parentId = 0 AND position <> '' ORDER BY position, sort";
		$records = Zend_Registry::get("db")->fetchAll($sql);
		
		
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
			
			$category_id = addslashes($formData['category_id']);
			$category = addslashes($formData['category']);
			$sort = addslashes($formData['sort']);
			
			/** category name validate*/
			if(!Zend_Validate::is($formData['category_id'],'NotEmpty')){
				$error['category_id']='Please select category';
			}
			
			/** sub category validate*/
			if(!Zend_Validate::is($formData['category'],'NotEmpty')){
				$error['category']='Please enter sub category';
			}
			
			$mySql = "";
			$mySql = "SELECT COUNT(0) AS totRec FROM `category` WHERE";
			$mySql = $mySql . " `parentId` = '".$category_id."'";
			$mySql = $mySql . " AND `category` = '".$category."'";
			//echo $mySql .'<BR>';
			//exit;
			$rsTemp = Zend_Registry::get("db")->fetchRow($mySql);
			
			if($rsTemp){
				if ($rsTemp['totRec'] > 0){
					$error['category']='Sub category name already exist under this category';	
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
				$mySql = "INSERT INTO `category` (";
				$mySql = $mySql . " `parentId`";
				$mySql = $mySql . ", `category`";
				$mySql = $mySql . ", sort";
				$mySql = $mySql . ", status";
				$mySql = $mySql . " ) VALUES (";
				
				$mySql = $mySql . " '".$category_id."'";
				$mySql = $mySql . ", '".$category."'";
				$mySql = $mySql . ", '".$sort."'";
				$mySql = $mySql . ", '1'";
				$mySql = $mySql . ")";
				//echo $mySql .'<BR>';
				//exit;
				$ss = Zend_Registry::get("db")->query($mySql);
				//$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				
				//echo 'I am here...' . $lastinsertid . '<br>';
				//exit;
				if($ss){
					//assign message
					$this->_flashMessenger->addMessage('Sub Category added successfully');
					$this->_redirect('/admin/categorysub/index');
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

	function editAction(){
		$category_id = $this->_getParam('id');
		//echo $adminid;die();
		$sql="SELECT * FROM `category` WHERE `category_id` = '".$category_id."'";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchrow($sql);
		
		$this->view->data = $res;
		
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$error = array();
			
			$parentId = addslashes($formData['parentId']);
			$category = addslashes($formData['category']);
			$sort = addslashes($formData['sort']);
			
			/** category name validate*/
			if(!Zend_Validate::is($formData['parentId'],'NotEmpty')){
				$error['parentId']='Please select category';
			}
			
			if(!Zend_Validate::is($formData['category'],'NotEmpty')){
				$error['category']='Please enter sub category';
			}
			
			if(count($error)== 0){
				$adminsession = new Zend_Session_Namespace('admin');
				$obj = new Default_Model_DbTable_User();
				$recods = $obj->getAdminByUser($adminsession->username);
				$userid = $recods['user_id'];
				$username = $recods['first_name']." ".$recods['last_name'];
				$created_date = date('Y-m-d h:i:s');
				
				//##############################//
				//UPDATE SELECTED POST ID RECORD//
				//##############################//
				$mySQL = "UPDATE `category` SET";
				$mySQL = $mySQL . "  parentId = '".$parentId."'";
				$mySQL = $mySQL . ", category = '".$category."'";
				$mySQL = $mySQL . ", sort = '".$sort."'";
				$mySQL = $mySQL . " WHERE category_id = ".$category_id;
				//echo $mySQL;
				//die();
				$set = Zend_Registry::get("db")->query($mySQL);
				//$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				
				//echo 'I am here.!!!..';
				//die();	
				
				if($set){
					//assign message
					$this->_flashMessenger->addMessage('Category edited successfully');
					$this->_redirect('/admin/categorysub/index');
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
		$sql ="DELETE FROM `category` WHERE `category_id` = '".$id."'";
		//echo $sql;die();
		$del = Zend_Registry::get("db")->query($sql);
		
		if($del){
			$this->_flashMessenger->addMessage('Selected record deleted');
			$this->_redirect('/admin/category/index');	
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
	$this->_redirect('/admin/category/index');
	}

}//close class
?>

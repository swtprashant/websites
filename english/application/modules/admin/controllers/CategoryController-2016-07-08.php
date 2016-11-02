<?php
class Admin_CategoryController extends Zend_Controller_Action
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

    	$obj = new Default_Model_DbTable_Category();
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
		
			$category = ($formData['category']);
			$position = ($formData['position']);
			$sort = ($formData['sort']);
			
			/** page name validate*/
			if(!Zend_Validate::is($formData['category'],'NotEmpty')){
				$error['category']='Please enter category';
			}
			
			if(!Zend_Validate::is($formData['position'],'NotEmpty')){
				$error['position']='Please enter position';
			}
			
			if(!Zend_Validate::is($formData['sort'],'NotEmpty')){
				$error['sort']='Please enter sort order';
			}
			
			/** database exist*/
			$validator = new Zend_Validate_Db_RecordExists(array('table' => 'category','field' => 'category' ));
				if($validator->isValid($formData['category'])){
				$error['category']=$formData['category'] .' category already exist.';
			}
		
			if(count($error)== 0){
				//$obj = new Default_Model_DbTable_Category();
				//add value in database
				//$re = $obj->add_category($this->admin->username,addslashes($formData['category']));
				
				$createdBy = $this->admin->username;
				$created = date('Y-m-d h:i:s');
				$status = 1;
				
				$mySql = "";
				$mySql .= "INSERT INTO `category` (";
				$mySql .= " `category`";
				$mySql .= ", `position`";
				$mySql .= ", `sort`";
				$mySql .= ", `createdBy`";
				$mySql .= ", `created`";
				$mySql .= ", `status`";
				$mySql .= ") VALUES (";
				$mySql .= " '".$category."'";
				$mySql .= ", '".$position."'";
				$mySql .= ", '".$sort."'";
				$mySql .= ", '".$createdBy."'";
				$mySql .= ", '".$created."'";
				$mySql .= ", '".$status."'";
				$mySql .= ")";
				//echo $mySql .'<BR>';
				//exit;
				$rsTemp = Zend_Registry::get("db")->query($mySql);
				//$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				//echo 'I am here...' . $lastinsertid . '<br>';
				//exit;
				if($rsTemp){
					//assign message
					$this->_flashMessenger->addMessage('Category Added successfully');
					$this->_redirect('/admin/category/');
				} else {
					$this->view->msg = 'Error , try agian.';
				}
			} else {
				//error send to view
				$this->view->error = $error;
			}
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
			//print_r($formData);
			//validation
			$error = array();
		
			$category = ($formData['category']);
			$position = ($formData['position']);
			$sort = ($formData['sort']);
			
			/** page name validate*/
			if(!Zend_Validate::is($formData['category'],'NotEmpty')){
				$error['category']='Please enter category';
			}
			
			if(!Zend_Validate::is($formData['position'],'NotEmpty')){
				$error['position']='Please enter position';
			}
			
			if(!Zend_Validate::is($formData['sort'],'NotEmpty')){
				$error['sort']='Please enter sort order';
			}
			
			/** database exist*/
			$validator = new Zend_Validate_Db_RecordExists(array('table' => 'category','field' => 'category' ));
				if($validator->isValid($formData['category'])){
				$error['category']=$formData['category'] .' category already exist.';
			}
		
			if(count($error)== 0){
				//$obj = new Default_Model_DbTable_Category();
				//add value in database
				//$re = $obj->add_category($this->admin->username,addslashes($formData['category']));
				
				$createdBy = $this->admin->username;
				$created = date('Y-m-d h:i:s');
				$status = 1;
				
				$mySql = "";
				$mySql .= "UPDATE `category` SET";
				$mySql .= " `category` = '".$category."'";
				$mySql .= ", `position` = '".$position."'";
				$mySql .= ", `sort` = '".$sort."'";
				//$mySql .= ", `createdBy` = '".$createdBy."'";
				//$mySql .= ", `created` = '".$created."'";
				//$mySql .= ", `status` = '".$status."'";
				$mySql .= " WHERE category_id = '".$category_id."'";
				
				//echo $mySql .'<BR>';
				//exit;
				$rsTemp = Zend_Registry::get("db")->query($mySql);
				//$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				//echo 'I am here...' . $lastinsertid . '<br>';
				//exit;
				if($rsTemp){
				//assign message
				$this->_flashMessenger->addMessage('Category updated successfully');
				$this->_redirect('/admin/category/');
			} else {
				$this->view->msg = 'Error , try agian.';
			}
		} else {
			//error send to view
			$this->view->error = $error;
		}
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
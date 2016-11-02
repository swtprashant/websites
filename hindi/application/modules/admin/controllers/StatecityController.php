<?php
class Admin_StatecityController extends Zend_Controller_Action
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
	#####################################################################################################################
	//default page of this controller
	function indexAction(){
		
			$sql="SELECT * FROM `location` WHERE isDeleted = 0 ORDER BY `state` ";
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

	function addAction(){
		if($this->getRequest()->isPost()){
		
			$formData = $this->getRequest()->getPost();
			//print_r($formData['block']);die();
			$state = $formData['state'];
			$city = $formData['city'];
			$aaa = implode(',',$formData['block']);
			//print_r($aaa);die();
			$block = explode(",",$aaa);
			//print_r($block);die();
			$sort = $formData['sort'];
			$status = $formData['status'];
			
			//validation
			$error = array();
			/** page name validate*/
			if(!Zend_Validate::is($formData['state'],'NotEmpty')){
			$error['state']='Please select state';
			}
			
			/** page name validate*/
			if(!Zend_Validate::is($formData['city'],'NotEmpty')){
			$error['city']='Please select city';
			}
			
			/** page name validate*/
			if(!Zend_Validate::is($formData['block'],'NotEmpty')){
			$error['block']='Please enter blocks';
			}
			
			/** page name validate*/
			if(!Zend_Validate::is($formData['status'],'NotEmpty')){
			$error['status']='Please select status';
			}
							
			if(count($error)== 0){
				foreach($block as $v){
					$created_date = date('Y-m-d h:i:s');
					//$blocks = implode(',',$v);
					$block_name = trim($v);
					$res = "INSERT INTO `location` SET `state`= '$state',`city`= '$city',`block_name`= '$block_name', `sort`='$sort', `status`='$status', `created`= '$created_date'";
					//echo $res .'<br>';
					$rr = Zend_Registry::get("db")->query($res);
					
				}
			
			if($rr){
			//assign message
			$this->_flashMessenger->addMessage('State,City & Blocks Added successfully');
			$this->_redirect('/admin/statecity/index');
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
		$location_id = $this->_getParam('id');
		//echo $adminid;die();
		$sql="SELECT * FROM `location` WHERE `location_id` = '".$location_id."'";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchrow($sql);
		
		$this->view->data = $res;
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			//print_r($formData['block']);die();
			$state = $formData['state'];
			$city = $formData['city'];
			$aaa = implode(',',$formData['block']);
			//print_r($aaa);die();
			$block = explode(",",$aaa);
			//print_r($block);die();
			$sort = $formData['sort'];
			$status = $formData['status'];
			
			//validation
			$error = array();
			/** page name validate*/
			if(!Zend_Validate::is($formData['state'],'NotEmpty')){
				$error['state']='Please select state';
			}
			
			/** page name validate*/
			if(!Zend_Validate::is($formData['city'],'NotEmpty')){
				$error['city']='Please select city';
			}
			
			/** page name validate*/
			if(!Zend_Validate::is($formData['block'],'NotEmpty')){
				$error['block']='Please enter blocks';
			}
			
			/** page name validate*/
			if(!Zend_Validate::is($formData['status'],'NotEmpty')){
				$error['status']='Please select status';
			}
							
			if(count($error)== 0){
				foreach($block as $v){
					$created_date = date('Y-m-d h:i:s');
					//$blocks = implode(',',$v);
					$block_name = trim($v);
					$res = "UPDATE `location` SET `state`= '$state',`city`= '$city',`block_name`= '$block_name', `sort`='$sort', `status`='$status' WHERE `location_id` = '".$location_id."'";
					//echo $res .'<br>';
					$rr = Zend_Registry::get("db")->query($res);
					
				}
			
			if($rr){
			//assign message
			$this->_flashMessenger->addMessage('State, City & Blocks update successfully');
			$this->_redirect('/admin/statecity/index');
			}else{
			
			$this->view->msg = 'Error , try agian.';
			}
				
			}else{
			
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
		$sql ="UPDATE `location` SET `status` = '0', isDeleted = '1' WHERE `location_id` = '".$id."'";
		//echo $sql;die();
		$del = Zend_Registry::get("db")->query($sql);
		
		if($del){
			$this->_flashMessenger->addMessage('Selected record deleted');
			$this->_redirect('/admin/statecity/index');	
		}
	}
}//close class

?>
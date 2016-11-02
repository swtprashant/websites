<?php
class Admin_BreakingController extends Zend_Controller_Action
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
		
    	$obj = new Default_Model_DbTable_Breaking();
		$records = $obj->get_view();
		
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($records);
		$paginator->setItemCountPerPage(30);
		$paginator->setCurrentPageNumber($page);

		$this->view->perpage=30;
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
			if(!Zend_Validate::is($formData['breaking_news'],'NotEmpty')){
				$error['breaking_news']='Please enter breaking news';
			}
			
						
			if(count($error)== 0){
			$obj = new Default_Model_DbTable_Breaking();
			//add value in database
			$re = $obj->add_news(addslashes($formData['breaking_news']), addslashes($formData['url']));
			if($re){
			//assign message
			$this->_flashMessenger->addMessage('Breaking News Added successfully');
			$this->_redirect('/admin/breaking/index');
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
	
		$id = $this->_getParam('id');
		//echo $adminid;die();
		$sql="SELECT * FROM `breaking_news` WHERE `breaking_id` = '$id'";
		
		$res = Zend_Registry::get("db")->fetchrow($sql);
		
		$this->view->data = $res;
		
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$error = array();
			if(!Zend_Validate::is($formData['breaking_news'],'NotEmpty')){
				$error['breaking_news']='Please enter breaking news';
			}
			
			if(count($error)== 0){
			$obj = new Default_Model_DbTable_Breaking();
			//add value in database
			$re = $obj->edit_news($id, addslashes($formData['breaking_news']), addslashes($formData['url']));
			//print_r($re);die();
			if($re){
			//assign message
			
			$this->_flashMessenger->addMessage('News Updated successfully');
			$this->_redirect('/admin/breaking/index');
			}else{
		
			$this->view->msg = 'Error , try agian.';
			}
				
			}else{
			
			//error send to view
			$this->view->error = $error;
			}
		}
	}
	
	function deleteAction(){
		$this->getHelper('viewRenderer')->setNoRender();
		$this->_helper->layout->disableLayout();	
		$id = $this->_getParam('id');
		//"UPDATE `user` SET `active`=abs(`active`-1) WHERE user_id='$id'";
		$sql ="DELETE FROM breaking_news WHERE `breaking_id` = '$id'";
		//echo $sql;die();
		$del = Zend_Registry::get("db")->query($sql);
		
		if($del){
			$this->_flashMessenger->addMessage('Selected record deleted');
			$this->_redirect('/admin/breaking/index');	
		}
	}

}//close class

?>
<?php
class IndexController extends Zend_Controller_Action {
//----------------------------------initilization object----------------------------------------------------------
public function init()
	{
		$this->_flashMessenger =$this->_helper->getHelper('FlashMessenger');
		$this->user=new Zend_Session_Namespace('user'); 
		//$this ->updates = new Default_Model_DbTable_Updates();
		//$this -> review = new Default_Model_DbTable_Review();
	}
//------------------------------------------------default home page ----------------------------------------------
public function indexAction()
	{
		//$obj = new Default_Model_DbTable_CmsPages();
//		$data = $obj->getContentByName("About");
//		$this->view->data = $data;
//		
//		$obj = new Default_Model_DbTable_Updates();
//		$updates = $obj->get_record();
//		$this->view->updates = $updates;
		
		//prr($updates);
		
		
		/*$obj = new Default_Model_DbTable_Associates();
		$data = $obj->get_view();
		$this->view->data = $data;
		$this -> view -> cmsdata = $data;
		$reviews = $this->review ->findByStatus();
		if(!empty($reviews))
		$this->view->review = $reviews;
		//echo '<pre>':
		exit;*/
		//echo 'I am here';
		//exit;
	}
//----------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------
}//class close
?>
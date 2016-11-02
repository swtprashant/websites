<?php
class ErrorController extends Zend_Controller_Action {
//----------------------------------initilization object----------------------------------------------------------
public function init()
	{
		$this->_flashMessenger =$this->_helper->getHelper('FlashMessenger');
		
		//$this ->associates = new Default_Model_DbTable_Associates();	prr($this->associates);
		//$this->gallery = new Default_Model_DbTable_Gallery();
		
		//$this -> cmsModel = new Default_Model_DbTable_MriCmsPages();
		//$this -> review = new Default_Model_DbTable_Review();
        //$this -> press = new Default_Model_DbTable_MriPress();
	}
//------------------------------------------------default home page ----------------------------------------------
	public function indexAction()
	{
		
	}
//----------------------------------------------------------------------------------------------------------------
	public function errorAction()
	{
		
	}
//----------------------------------------------------------------------------------------------------------------
}//class close
?>
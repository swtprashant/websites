<?php
class AuthorController extends Zend_Controller_Action {
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
		$user_id =  $this -> _request -> getParam('id');
		$query = "SELECT post_id, CONCAT(SUBSTRING_INDEX(headline, ' ', 10), '...') AS headline, image, CONCAT(SUBSTRING_INDEX(intro, ' ', 10), '...') AS intro, user_name";
		$query .= ", english_url FROM `post` WHERE user_id = '".$user_id."' AND status = '1' order by post_id desc";
		//echo 'sql = ' . $query;
		//exit;
		$res = Zend_Registry::get("db")->fetchAll($query);
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($res);
		$paginator->setItemCountPerPage(30);
		$paginator->setCurrentPageNumber($page);
		
		$this->view->perpage=30;
		$this->view->pages=$page;
		$this->view->data=$paginator;
		//$this->view->keyword=$keyword;
		$this->view->messages = $this->_helper->_flashMessenger->getMessages();
		$this->render();		
		
	}
	

//----------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------
}//class close
?>

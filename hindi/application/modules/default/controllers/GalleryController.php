<?php
class GalleryController extends Zend_Controller_Action {
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
		$host = $_SERVER['HTTP_HOST'];
		if ($_SERVER['HTTP_HOST'] == 'localhost') {
			$city = 'gaya';
			$baseurl = '/liveindia.live';
		} else {
			$city = GetBetween("live",".","$host");
			$baseurl = '';
		}
		
		//$query = "SELECT idgallery, title, englishurl FROM `gallery` ORDER BY idgallery DESC";
		$mySQL = "";
		$mySQL .= "SELECT G.idgallery, G.title, CONCAT('http://".$_SERVER['HTTP_HOST'].$baseurl."','/public/gallery/', GI.image) AS imagepath";
		$mySQL .= ", CONCAT('http://".$_SERVER['HTTP_HOST'].$baseurl."','/gallery/index/id/', G.idgallery, '/', G.englishurl) AS link";
		$mySQL .= " FROM gallery G INNER JOIN galleryimage GI ON G.idgallery = GI.idgallery GROUP BY idgallery";
		//$mySQL .= " ORDER BY G.sort";
		//echo $mySQL;
		//exit;
		$res = Zend_Registry::get("db")->fetchAll($mySQL);
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($res);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
		
		$this->view->perpage=25;
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

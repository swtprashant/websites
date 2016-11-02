<?php
class Admin_CommentController extends Zend_Controller_Action
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

	#############################################################################################################################
	//default page of this controller
	function indexAction(){
		$host = $_SERVER['HTTP_HOST'];
		//$city = GetBetween1("live",".","$host");
		if ($_SERVER['HTTP_HOST'] == 'localhost') {
			$city = 'gaya';
		} else {
			$city = GetBetween1("live",".","$host");
		}
		$adminsession = new Zend_Session_Namespace('admin');
		$obj = new Default_Model_DbTable_User();
		$recods = $obj->getAdminByUser($adminsession->username);
		$userid = $recods['user_id'];
		$usertype = $recods['user_type'];
		
		$mySQL = "";
		$mySQL = "";
		$mySQL = "SELECT PC.idComment, PC.post_id, PC.author, PC.email, PC.comment, P.headline, CONCAT(SUBSTRING_INDEX(intro, ' ', 10), '...') AS intro, image, thumb_image, english_url, DATE_FORMAT(PC.createdOn,'%d-%m-%Y') AS createdOn FROM `postcomment` PC INNER JOIN post P ON PC.post_Id = P.post_id ORDER BY PC.createdOn DESC";
		//echo $mySQL;
		//exit;
		$res = Zend_Registry::get("db")->fetchAll($mySQL);
		$this->view->data = $res;
		$this->view->messages = $this->_helper->_flashMessenger->getMessages();
		$this->render();
	}
	
	function addAction(){
	
	}
	
	function editAction(){
	
	}
	
	//CONTROLOR - DELETE ACTION 
	public function deleteAction(){
		$this->getHelper('viewRenderer')->setNoRender();
		$this->_helper->layout->disableLayout();	
		$id = $this->_getParam('id');
		$sql = "DELETE FROM `postcomment` WHERE `idComment` = '".$id."'";
		//echo $sql;die();
		$del = Zend_Registry::get("db")->query($sql);
		if($del){
			$this->_flashMessenger->addMessage('Selected record deleted');
			$this->_redirect('/admin/comment/index');	
		}
	}

}//close class

//echo $host;
function GetBetween1($var1="",$var2="",$pool){
	$temp1 = strpos($pool,$var1)+strlen($var1);
	$result = substr($pool,$temp1,strlen($pool));
	$dd=strpos($result,$var2);
	if($dd == 0){
	$dd = strlen($result);
	}
	return substr($result,0,$dd);
}
?>

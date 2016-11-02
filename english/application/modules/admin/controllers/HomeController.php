<?php
class Admin_HomeController extends Zend_Controller_Action
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
	##########################################################################################################################################
	//default page of this controller
	function indexAction(){
		$adminsession = new Zend_Session_Namespace('admin');
		$obj = new Default_Model_DbTable_User();
		$recods = $obj->getAdminByUser($adminsession->username);
		$userid = $recods['user_id'];
		$usertype = $recods['user_type'];
		$sql = "";
		$sql .= "SELECT idIP";
		$sql .= ", ipAddress";
		$sql .= ", domainURL";
		$sql .= ", COUNT(post_id) AS totalCount";
		$sql .= ", post_id";
		$sql .= ", postURL";
		$sql .= ", DATE_FORMAT(viewDate,'%d-%m-%Y') AS viewDate";
		$sql .= " FROM `postview`";
		$sql .= " WHERE domainURL LIKE '%".str_replace('www.', '', $_SERVER['HTTP_HOST'])."%'";
		//$sql .= " AND ipAddress <> '".$_SERVER['REMOTE_ADDR']."'";
		$sql .= " AND post_id IN (SELECT post_id FROM post WHERE isDeleted = 0 AND status = 1)";
		$sql .= " GROUP BY post_id HAVING COUNT(post_id) > 0 ORDER BY COUNT(post_id) DESC LIMIT 20";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchAll($sql);
		
		$this->view->data = $res;
		$this->view->messages = $this->_helper->_flashMessenger->getMessages();
		$this->render();
	}

}//close class

?>
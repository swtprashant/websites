<?php
class Admin_ContentmetaController extends Zend_Controller_Action
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
	
	//CONTROLOR - POST LIST PAGE 
	function indexAction(){
		//echo $userCity = str_replace(str_replace('live', '', $_SERVER['HTTP_HOST']), '.com', '', $_SERVER['HTTP_HOST']);
		//exit;
		$adminsession = new Zend_Session_Namespace('admin');
		$obj = new Default_Model_DbTable_User();
		$recods = $obj->getAdminByUser($adminsession->username);
		$userid = $recods['user_id'];
		$usertype = $recods['user_type'];
		$sql = "";
		$sql .= "SELECT * FROM `post`";
		$sql .= " WHERE isDeleted = '0'";
		if(!empty($_REQUEST['search'])){
			$keyword = trim($_REQUEST['keyword']);
			//$sql .= " AND (`city` like '%$keyword%' OR `user_name` like '%$keyword%')";
			$sql .= " AND (";
			$sql .= " headline LIKE '%$keyword%'";
			$sql .= " OR content LIKE '%$keyword%'";
			$sql .= " OR intro LIKE '%$keyword%'";
			$sql .= " OR city like '%$keyword%'";
			$sql .= " OR user_name like '%$keyword%'";
			$sql .= ")";
		}
		if($usertype == 2){
			$sql .= " AND `user_id` = '".$userid."'";
		}
		$sql .= " ORDER BY `post_id` DESC";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchAll($sql);
		
		$this->view->data = $res;
		
		$this->view->messages = $this->_helper->_flashMessenger->getMessages();
		$this->render(); 
		
	}
	
	//CONTROLOR - POST AJAXCALL PAGE 
	public function ajaxcallAction() {
		$post_id = $this -> _request -> getParam('post_id');
		$meta_title = $this -> _request -> getParam('meta_title');
		$meta_keyword = $this -> _request -> getParam('meta_keyword');
		$meta_desc = $this -> _request -> getParam('meta_desc');
		//echo $meta_title;
		//exit;
		//echo $block_name;
		$sql = "UPDATE post SET `meta_title` = '".$meta_title."', `meta_keyword` = '".$meta_keyword."', `meta_desc` = '".$meta_desc."' WHERE `post_id`= '".$post_id."'";
		$res = Zend_Registry::get("db")->query($sql);
		
		echo "Data Saved";		
	}
}
?>
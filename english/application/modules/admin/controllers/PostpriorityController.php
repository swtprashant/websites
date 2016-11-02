<?php
class Admin_PostpriorityController extends Zend_Controller_Action
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
	#####################################################################################################################################
	//CONTROLOR - POST LIST PAGE 
	function indexAction(){
			//echo $userCity = str_replace(str_replace('live', '', $_SERVER['HTTP_HOST']), '.com', '', $_SERVER['HTTP_HOST']);
			//exit;
			
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
			$sql = "";
			$sql .= "SELECT post_id, headline, image, imagePath, priority FROM `post`";
			$sql .= " WHERE isDeleted = '0' AND status = 1";
			//$sql .= " AND city = '$city'";
			$sql .= " AND priority IN (1,2,3)";
			//if($usertype == 2){
			//	$sql .= " AND `user_id` = '".$userid."'";
			//}
			$sql .= " ORDER BY priority";
			//echo $sql;
			//exit;
			$res = Zend_Registry::get("db")->fetchAll($sql);
			
			$this->view->data = $res;
			$this->view->messages = $this->_helper->_flashMessenger->getMessages();
			$this->render();
		
	}
	
	function ajaxcallAction(){
		$editedOn = date('Y-m-d h:i:s');
		$post_id = $this -> _request -> getParam('ids');
		//echo 'POST ID = ' . $post_id;
		//exit;
		$idArray = explode(",",$post_id);
		
		$count = 1;
		foreach ($idArray as $id){
			if ($count < 4) {			
				$mySQL = "";
				$mySQL = "UPDATE `post` SET `priority` = '".$count."', `editedOn` = '".$editedOn."' WHERE post_id = '".$id."'";
				//echo 'SQL = ' . $sql .'<br>';
				$set = Zend_Registry::get("db")->query($mySQL);
				
				$count ++;
			} else {
				$mySQL = "";
				$mySQL = "UPDATE `post` SET `priority` = '', `editedOn` = '' WHERE post_id = '".$id."'";
				//echo 'SQL = ' . $sql .'<br>';
				$set = Zend_Registry::get("db")->query($mySQL);
				
				$count ++;
			}
			//if ($count > 5){
			//	 exit;
			//}
		}
		return true;
	}
	
}//close class

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
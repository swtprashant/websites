<?php
class Default_Model_DbTable_User extends Zend_Db_Table_Abstract
{

	protected $_name="admin";	

	function add_users($user, $fname, $lname, $email, $mobileno, $password, $city, $usertype, $active = 1) {
		$created_date = date('Y-m-d h:i:s');
		$sql = "INSERT INTO `user` SET `first_name`= '$fname',`last_name`= '$lname',`user_type`= '$usertype',`email`= '$email', `password`= md5('$password'),`mobile_no`= '$mobileno',`city` = '$city',`active`= '$active',`created_by`= '$user',`created`= '$created_date'";
		//echo "$sql";die();
		//$flag = 1 -- not deleted and 0 -- deleted
		Zend_Registry::get("db")-> query($sql);
		return Zend_Registry::get("db")-> lastInsertId();

	}
	
	function edit_users($user, $user_id, $fname, $lname, $email, $mobileno, $password) {
		$created_date = date('Y-m-d h:i:s');
		$sql = "UPDATE `user` SET";
		$sql .= " `first_name`= '$fname'";
		$sql .= ", `last_name`= '$lname'";
		$sql .= ", `email`= '$email'";
		$sql .= ", `mobile_no`= '$mobileno'";
		$sql .= ", `password`= md5('$password')";
		//$sql .= ", `city` = '$city'";
		//$sql .= ", `user_type`= '$usertype'";
		$sql .= " WHERE user_id = '$user_id'";
		//echo "$sql";die();
		//$flag = 1 -- not deleted and 0 -- deleted
		Zend_Registry::get("db")-> query($sql);
		return $this;
	}
	function get_view($key = '')
 	{
		$result = array();
		$sql = '';
		$key = trim($key);
		
		//echo $key; die();
		if($key == "Reporter" || $key == "reporter"){
			$key = 2;
		}else if($key == "Sub-Editor" || $key == "sub-editor"){
			$key = 1;
		}

		if (!empty($key)) {
			$sq = "SELECT * FROM `user` WHERE (`email` like '%$key%' OR `mobile_no` like '%$key%' OR `city` like '%$key%' OR `user_type` like '%$key%')";
			//echo $sq;die();
		} else {
			$sq = "SELECT * FROM `user` WHERE 1 ";
		}
		
		if($key == '2' || $key == '1')
		{
			$sq = "SELECT * FROM `user` WHERE `user_type` = '$key'";
		}
		
		$sql .= $sq . " AND `user_type` != 0 ORDER BY user_id DESC";
		//echo $sql;
		/**execuite query*/
		$result = Zend_Registry::get("db")-> fetchAll($sql);

		return $result;
	}

	function admin_login($user,$pass){
		/***generate query*/
		$sql="SELECT * FROM `user` WHERE email='$user' AND password=md5('$pass') AND active = 1";
		/**execuite query*/
		
		$result = Zend_Registry::get("db")->fetchAll($sql);	
		
		if(0 == count($result)){
			return;
		}
		return $result;
	}

	function getUser_type($type) {
		/***generate query*/
		$sql = "SELECT * FROM `users` WHERE `user_type` ='$type'";
		/**execuite query*/
		$result = Zend_Registry::get("db")-> fetchRow($sql);
		return $result;
	}

	function getAdminByUser($user){
	
	/***generate query*/
	$sql="SELECT * FROM `user` WHERE email='$user'";
	/**execuite query*/
	
	$result = Zend_Registry::get("db")->fetchRow($sql);	
	
	
	return $result;
	
	
	}


	function getAdminById($id){
	
	/***generate query*/
	$sql="SELECT * FROM `user` WHERE user_id='$id'";
	/**execuite query*/
	
	$result = Zend_Registry::get("db")->fetchRow($sql);	
	
	
	return $result;
	
	
	}

	function getViews($id){
		/***generate query*/
		$sql="SELECT post_id, headline, intro, content, postType, ReporterName, user_name, image, created, english_url FROM `post` WHERE `post_id` = '$id'";
		/**execuite query*/
		$result = Zend_Registry::get("db")->fetchRow($sql);	
if( true == isset( $_COOKIE['debug'])){
echo '<pre>';
debug_print_backtrace();
exit;
}		
return $result;
	}

	function getBlockViews($block){
		/***generate query*/
		$sql="SELECT * FROM `post` WHERE `location_id` = '$block' AND `status` = '1' order by post_id desc";
		//echo $sql;
		/**execuite query*/
		$result = Zend_Registry::get("db")->fetchAll($sql);	
		return $result;
	}

	function delete($id){
		$sql ="DELETE FROM `user` WHERE user_id='$id'";
		return Zend_Registry::get("db")->query($sql);
		}

/**
*
*/
	function statuschange($id){
		$sql ="UPDATE `user` SET `active`=abs(`active`-1) WHERE user_id='$id'";
		return Zend_Registry::get("db")->query($sql);
	}
	
	function getPostDataList($key = '', $city, $pagelimit = 0, $perpage = 2) {
		$key = trim($key);
		
		$sql = "";
		$sql .= "SELECT *, DATE(created) AS createdOn FROM `post`";
		$sql .= " WHERE isDeleted = '0'";
		if(!empty($key)){
			$keyword = trim($key);
			//$sql .= " AND (`city` like '%$keyword%' OR `user_name` like '%$keyword%')";
			$sql .= " AND (";
			$sql .= " headline LIKE '%$keyword%'";
			$sql .= " OR content LIKE '%$keyword%'";
			$sql .= " OR intro LIKE '%$keyword%'";
			$sql .= " OR city like '%$keyword%'";
			$sql .= " OR user_name like '%$keyword%'";
			$sql .= " OR DATE(created) like '%$keyword%'";
			$sql .= ")";
		} //else {
		//	$sql .= " AND city = '$city'";
		//}
		//if($usertype == 2){
		//	$sql .= " AND `user_id` = '".$userid."'";
		//}
		$sql .= " ORDER BY `post_id` DESC";
		//$sql .= " LIMIT " . $pagelimit . " , " . $perpage . " ";
		//echo $sql;
		//exit;
		return $result = Zend_Registry::get("db")-> fetchAll($sql);

	}

}

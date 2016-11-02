<?php
class Admin_Model_DbTable_PostList extends Zend_Db_Table_Abstract {

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
	
	function getPostDataCount($usertype) {
		$sql = "SELECT count(1) as cnt FROM mri_admin_meta WHERE usertype= '" . $usertype . "' and flag =1 order by admin_metaid desc";
		return $result = Zend_Registry::get("db")-> fetchAll($sql);
		
	}
}

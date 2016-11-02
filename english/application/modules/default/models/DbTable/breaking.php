<?php
class Default_Model_DbTable_Breaking extends Zend_Db_Table_Abstract
{

	protected $_name="admin";	

	function add_news($breaking_news,$url) {
		$created_date = date('Y-m-d H:i:s');
		$sql = "INSERT INTO `breaking_news` SET `breakingnews`= '$breaking_news', `url`= '$url', `created`= '$created_date'";
		//echo "$sql";die();
		//$flag = 1 -- not deleted and 0 -- deleted
		Zend_Registry::get("db")-> query($sql);
		
		return Zend_Registry::get("db")-> lastInsertId();

	}
	
	function edit_news($id, $breaking_news, $url) {
		$created_date = date('Y-m-d H:i:s');
		$sql = "UPDATE `breaking_news` SET `breakingnews`= '$breaking_news', `url`= '$url',`created`= '$created_date' WHERE `breaking_id` = '$id'";
		//echo "$sql";die();
		//$flag = 1 -- not deleted and 0 -- deleted
		return Zend_Registry::get("db")->query($sql);
		

	}

	function get_view() {
		$result = array();
		$sql = "SELECT * FROM `breaking_news` ORDER BY `breaking_id` DESC";

		/**execuite query*/
		$result = Zend_Registry::get("db")-> fetchAll($sql);
		
		/*if(0 == count($result)){
		 return;
		 }*/
		return $result;
	}
	
	function getNewscity($city){
		
		/***generate query*/
		$sql="SELECT * FROM `breaking_news` WHERE `city` = '$city'";
		/**execuite query*/
		$result = Zend_Registry::get("db")->fetchRow($sql);	
		
		return $result;
	}
}

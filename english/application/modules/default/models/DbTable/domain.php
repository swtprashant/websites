<?php
class Default_Model_DbTable_Domain extends Zend_Db_Table_Abstract
{

protected $_name="admin";	


	function add_domain($hindi,$english,$city,$state) {
		$created_date = date('Y-m-d h:i:s');
		$sql = "INSERT INTO `domain` SET `domain_hindi`= '$hindi',`domain_english`= '$english',`city`= '$city', `state` = '$state', `created`= '$created_date'";
		//echo "$sql";die();
		//$flag = 1 -- not deleted and 0 -- deleted
		Zend_Registry::get("db")-> query($sql);
		
		return Zend_Registry::get("db")-> lastInsertId();

	}

	function get_view() {
		$result = array();
		$sql = "SELECT * FROM `domain` ORDER BY domain_id DESC";

		/**execuite query*/
		$result = Zend_Registry::get("db")-> fetchAll($sql);
		
		/*if(0 == count($result)){
		 return;
		 }*/
		return $result;
	}
	
	function getDomaincity($city){
		/***generate query*/
		$sql="SELECT * FROM `domain` WHERE `city` = '$city'";
		/**execuite query*/
		$result = Zend_Registry::get("db")->fetchRow($sql);	
		
		return $result;
	}
}
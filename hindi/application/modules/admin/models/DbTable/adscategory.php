<?php
class Admin_Model_DbTable_Adscategory extends Zend_Db_Table_Abstract
{

	protected $_name="admin";	


	function add_category($user, $category, $sort, $active = 1) {
			$created_date = date('Y-m-d h:i:s');
			$sql = "INSERT INTO `adcategory` SET `adcategory`= '$category', `isactive`= '$active',`createdBy`= '$user',`createdOn`= '$created_date'";
			//echo "$sql";die();
			//$flag = 1 -- not deleted and 0 -- deleted
			Zend_Registry::get("db")-> query($sql);
			return Zend_Registry::get("db")-> lastInsertId();
			
		}

	function get_view() {
			$result = array();
			$sql = "SELECT * FROM `adcategory`";
			//echo $sql;
			//exit;
			/**execuite query*/
			$result = Zend_Registry::get("db")-> fetchAll($sql);
			
			/*if(0 == count($result)){
			 return;
			 }*/
			return $result;
		}
	
	function delete($id){
		
			$sql ="DELETE FROM `adcategory` WHERE idadcategory='$id'";
			return Zend_Registry::get("db")->query($sql);
			
		}

	/**
	*
	*/
	
	
}
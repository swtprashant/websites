<?php
class Default_Model_DbTable_Category extends Zend_Db_Table_Abstract
{

	protected $_name="admin";	
	
	function add_category($user, $category,$active = 1) {
			$created_date = date('Y-m-d h:i:s');
			$sql = "INSERT INTO `category` SET `category`= '$category',`status`= '$active',`createdBy`= '$user',`created`= '$created_date'";
			//echo "$sql";die();
			//$flag = 1 -- not deleted and 0 -- deleted
			Zend_Registry::get("db")-> query($sql);
			
			return Zend_Registry::get("db")-> lastInsertId();
	
		}
	
	function get_view() {
			$result = array();
			$sql = "SELECT *, @s:=@s+1 sno FROM (SELECT @s:= 0) AS s, `category` WHERE parentId = 0 AND position <> '' ORDER BY position, sort";
	
			/**execuite query*/
			$result = Zend_Registry::get("db")-> fetchAll($sql);
			
			/*if(0 == count($result)){
			 return;
			 }*/
			return $result;
		}
		
	function getMetaKeyVal($pid){
				
				$sql="SELECT `category_name` FROM `post_category` WHERE `post_id`='$pid'";
				
				$res = Zend_Registry::get("db")->fetchRow($sql);
				
				if($res){
					return $res['category_name'];
					}else{
						
						return;
						}
	}
	
	function delete($id){
		
		$sql ="DELETE FROM `category` WHERE id='$id'";
		return Zend_Registry::get("db")->query($sql);
		
		}
	
	/**
	*
	*/
	function statuschange($id){
		
		$sql ="UPDATE `category` SET `status`=abs(`status`-1) WHERE category_id='$id'";
		return Zend_Registry::get("db")->query($sql);
		
		}

}
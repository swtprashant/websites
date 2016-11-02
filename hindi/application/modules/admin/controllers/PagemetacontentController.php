<?php
class Admin_PagemetacontentController extends Zend_Controller_Action {
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
	
	function indexAction (){
		
	}
	
	//CONTROLOR - POST AJAXCALL PAGE 
	public function ajaxcallAction() {
		$contentHeading = $this -> _request -> getParam('contentHeading');
		$meta_title = $this -> _request -> getParam('meta_title');
		$meta_keyword = $this -> _request -> getParam('meta_keyword');
		$meta_desc = $this -> _request -> getParam('meta_desc');
		//echo $meta_title;
		//exit;
		//echo $block_name;
		
		if (!empty($contentHeading)){
			$sql = "";
			$sql = "SELECT idContent FROM content WHERE contentHeading = '".$contentHeading."'";
			//echo $sql;
			$res1 = Zend_Registry::get("db")->fetchRow($sql);
			
			//exit;
			if($res1){
				$sql = "";
				$sql = "UPDATE content SET `meta_title` = '".trim($meta_title)."', `meta_keyword` = '".trim($meta_keyword)."', `meta_desc` = '".trim($meta_desc)."' WHERE contentHeading = '".$contentHeading."'";
				$res = Zend_Registry::get("db")->query($sql);
				
			} else {
				$sql = "";
				$sql = "INSERT INTO content (contentHeading, meta_title, meta_keyword, meta_desc) VALUES ('".$contentHeading."', '".$meta_title."', '".$meta_keyword."', '".$meta_desc."')";
				$res = Zend_Registry::get("db")->query($sql);
				
			}
			echo "Data Saved";
		}
		
		$sql = "SELECT contentHeading, meta_title, meta_keyword, meta_desc FROM `content` WHERE isDeleted = 0 AND meta_title <> '' AND meta_keyword <> '' AND meta_desc <> ''";
		$res = Zend_Registry::get("db")->fetchAll($sql);
		
		if($res){
			$tbl = '';
			//$option .= '<select id="location_id" name="location_id" class="select2">';
			$tbl .= '<table class="table table-success mb30">';
			$tbl .= '<thead>';
			$tbl .= '<tr>';
			$tbl .= '<th width="25%">';
			$tbl .= 'Page';
			$tbl .= '</th>';
			$tbl .= '<th width="25%">';
			$tbl .= 'Title';
			$tbl .= '</th>';
			$tbl .= '<th width="25%">';
			$tbl .= 'Keyword';
			$tbl .= '</th>';
			$tbl .= '<th width="25%">';
			$tbl .= 'Description';
			$tbl .= '</th>';
			$tbl .= '</tr>';
			$tbl .= '</thead>';
			foreach($res as $val){
				$tbl .= '<tbody>';
				$tbl .= '<tr>';
				$tbl .= '<td>';
				$tbl .= $val['contentHeading'];
				$tbl .= '</td>';
				$tbl .= '<td>';
				$tbl .= $val['meta_title'];
				$tbl .= '</td>';
				$tbl .= '<td>';
				$tbl .= $val['meta_keyword'];
				$tbl .= '</td>';
				$tbl .= '<td>';
				$tbl .= $val['meta_desc'];
				$tbl .= '</td>';
				$tbl .= '</tr>';
				$tbl .= '</tbody>';
			}
			$tbl .= '</table>';
			echo $tbl;	
		}
	}
}

?>
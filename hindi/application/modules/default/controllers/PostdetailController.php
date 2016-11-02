<?php
class PostdetailController extends Zend_Controller_Action {
//----------------------------------initilization object----------------------------------------------------------
public function init()
	{
		$this->_flashMessenger =$this->_helper->getHelper('FlashMessenger');
		
		//$this ->associates = new Default_Model_DbTable_Associates();	prr($this->associates);
		//$this->gallery = new Default_Model_DbTable_Gallery();
		
		//$this -> cmsModel = new Default_Model_DbTable_MriCmsPages();
		//$this -> review = new Default_Model_DbTable_Review();
        //$this -> press = new Default_Model_DbTable_MriPress();
		
		//admin session object create
		$this->admin=new Zend_Session_Namespace('admin');
	}
//------------------------------------------------default home page ----------------------------------------------
public function indexAction()
	{
		$post_id = $this -> _request -> getParam('id');
		$obj = new Default_Model_DbTable_User();
		
		$data = $obj->getViews($post_id);	
		$this->view->data = $data;
		
		//$ipAddress = $_SERVER['REMOTE_ADDR'];
		//$domainURL = $_SERVER['HTTP_HOST'];
		//$postURL = curPageURL();
		//$idSession = strtoupper(session_id());
		
		//$viewDate = date('Y-m-d h:i:s');
		/*$chkRecExist = chkRecExist($ipAddress, $idSession, $post_id);
		if ($chkRecExist == '0')
		{
			$mySQL = "";
			$mySQL = "INSERT INTO `postview` (";
			$mySQL = $mySQL . " ipAddress";
			$mySQL = $mySQL . ", domainURL";
			$mySQL = $mySQL . ", idSession";
			$mySQL = $mySQL . ", post_Id";
			$mySQL = $mySQL . ", postURL";
			$mySQL = $mySQL . ", viewDate";
			$mySQL = $mySQL . ") VALUES (";
			$mySQL = $mySQL . "  '".$ipAddress."'";
			$mySQL = $mySQL . ", '".$domainURL."'";
			$mySQL = $mySQL . ", '".$idSession."'";
			$mySQL = $mySQL . ", '".$post_id."'";
			$mySQL = $mySQL . ", '".$postURL."'";
			$mySQL = $mySQL . ", '".$viewDate."'";
			$mySQL = $mySQL . ")";
			//echo $mySQL .'<br>';
			//exit;
			$rsView = Zend_Registry::get("db")->query($mySQL);
			$rsView = null;
			
		}*/
	}
	
	

//----------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------

//CONTROLOR - POST AJAXCALL PAGE 
public function ajaxcallAction() {
	$post_Id = $this -> _request -> getParam('comment_post_ID');
	$author = $this -> _request -> getParam('author');
	$email = $this -> _request -> getParam('email');
	$comment = $this -> _request -> getParam('comment');
	$createdOn = date('Y-m-d h:i:s');
	//echo $author . ' - ' . $email . ' - ' . $comment . ' - ' . $comment_post_Id;
		
	$mySQL = "";
	$mySQL = "INSERT INTO `postcomment` (";
	$mySQL = $mySQL . " post_Id";
	$mySQL = $mySQL . ",author";
	$mySQL = $mySQL . ",email";
	$mySQL = $mySQL . ",comment";
	$mySQL = $mySQL . ",createdOn";
	$mySQL = $mySQL . ") VALUES (";
	$mySQL = $mySQL . " '".$post_Id."'";
	$mySQL = $mySQL . ",'".$author."'";
	$mySQL = $mySQL . ",'".$email."'";
	$mySQL = $mySQL . ",'".$comment."'";
	$mySQL = $mySQL . ",'".$createdOn."'";
	$mySQL = $mySQL . ")";
	//echo $mySQL .'<br>';
	$set = Zend_Registry::get("db")->query($mySQL);	
	
	echo "Your comment post successfully.";
	}
}//close class

/*function chkRecExist($ipAddress, $idSession, $post_id)
{
	if($post_id != ''){
		$sql = "SELECT COUNT(post_Id) AS totCount FROM postview WHERE ipAddress = '".$ipAddress."' AND idSession = '".$idSession."' AND post_Id = '".$post_id."'";
		$res = Zend_Registry::get("db")->fetchRow($sql);
		if($res){
			return $res['totCount'];
		} else {
			return '0';
		}
		$res = null;
		
	}
}*/

/*function curPageURL() {
 	$pageURL = 'http';
 	//if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 	$pageURL .= "://";
 	if ($_SERVER["SERVER_PORT"] != "80") {
  		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 	} else {
  		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 	}
 	return $pageURL;
}*/
?>

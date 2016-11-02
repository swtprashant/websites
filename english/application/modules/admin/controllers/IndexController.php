<?php
class Admin_IndexController extends Zend_Controller_Action
{
	protected $_flashMessenger = null;
	public function init()
	{
		$this->_flashMessage=$this->_helper->getHelper('FlashMessenger');
		$this->admin=new Zend_Session_Namespace('admin');
	}
	//------------------------------------------------------------------------------------
	//home page login
	public function indexAction()
	{
		//layout disable
		$this->_helper->layout->disablelayout();
		//-------check alredy login or not
		if(!empty($this->admin->email)){
			$this->_redirect('admin/home/');
		}
		//------------------------------------------------------------------------------------
		/*
		*		form action
		*/
		if($this->getRequest()->isPost())
		{
			/*
				Receive the value 
			*/
			$formData=$this->getRequest()->getPost();
	
			/** validate the value*/
			$error =array();
	
			if(empty($formData['login'])){
		
			$error['login']='Please enter email id';
		}
		if(empty($formData['password'])){
		
			$error['password']='Please enter password';
		}
	
		/** lo9gin user*/
		if(count($error)==0){
		
			/**create object*/
	
			//$obj = new Default_Model_DbTable_User();
		
			//$result = $obj->admin_login($formData['login'],$formData['password']);
			
			//	prr($result);
			$mySQL = "SELECT user_id, email FROM `user` WHERE email='".$formData['login']."' AND password = md5('".$formData['password']."') AND active = 1";
			$rsTemp = Zend_Registry::get("db")->fetchRow($mySQL);
			
			
			if(!empty($rsTemp)){
			
				//session 
				$adminsession = new Zend_Session_Namespace('admin');//$_SESSION['admin'];
				
				$adminsession->username = $rsTemp['email'];
				$adminsession->admin_id = $rsTemp['user_id'];
				//activity store
				
				$_SESSION['timeout'] = 60;
				
				//redirect
				if (empty($formData['parenturl'])){
					$this->_redirect('admin/home/index');
				} else {
					$this->_redirect ($formData['parenturl']);	
				}
				exit();
			
			} else {
				
				$this->view->msg='Email-id / Password not valid. Try again.';
				
			}
		
		
		
		}else{
	
			$this->view->data =$formData;
			$this->view->error=$error;
		}
		}//form action close
		$this->view->messages =$this->_helper->flashMessenger->getMessages();
	}//close the method
	//------------------------------------------------------------------------------------
	//forgot password----
	public function forgotpasswordAction(){
		$this->_helper->layout->disablelayout();
		$admin=new Admin_Model_DbTable_User();//create object
		//$gen=new Admin_Model_DbTable_Common();
		if($this->getRequest()->isPost()){
			$email=$_POST['email'];
			if($admin->checkEmail($email))
			{
				//find valid mail id update password and send to email
				$pass=$admin->createRandomPassword();
				//echo $users->updatePassword($email,$pass);
				if($admin->changePassword($email,$pass)){
					$msg="Hello ,\n\n <p>Your password is changed.</p> <p>please login with this password:</p> <p> your password is::$pass</p>
					<p>Best regards,<br/>The MRI Team<p>";
					//send to mail
					//die('<pre>'.print_r($this->mail,true).'</pre>');
					$this->mail->setBodyHtml($msg);
					$this->mail->setFrom($this->siteData['contact_email'], 'Admin');
					$this->mail->addTo($email);
					$this->mail->setSubject('Administrator password Recovery');
					if($this->mail->send())
					{
						$this->_helper->flashMessenger->addMessage("Your new password sent to your e-mail");	
						$this->_redirect('admin/index/forgotpassword');	
					}
				}
			}else{
				$this->_helper->flashMessenger->addMessage("Email Id not found. Please try again");	
				$this->_redirect('admin/index/forgotpassword');
			}
		}
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
	//------------------------------------------------------------------------------------
	//logout
	public function logoutAction(){
		//unset all admin session value
		$this->admin->unsetAll();
		//redirect page for login
		//$this->_redirect('admin/index');
		
		//echo $_REQUEST['parenturl'];
		//exit;
		if (empty($_REQUEST['parenturl'])){
			$this->_redirect('admin/home/index');
		} else {
			$this->_redirect ($_REQUEST['parenturl']);	
		}
	}
	//------------------------------------------------------------------------------------
	//change password
	public function changepasswordAction()
	{
		//check admin login
		if(empty($this->admin->email))
		{
		$this->_redirect('admin/home/');
		}
		//receive the action
		if($this->getRequest()->isPost())
		{
		//receive the form value
		$formData=$this->getRequest()->getPost();
		//check old password is valid
		$objAdmin = new Admin_Model_DbTable_User();
		if($objAdmin->adminLogin($this->admin->email,$formData['oldPass']))
		{
		//check check password and confirm password same or not  and password must be 5 or greather 
		if($formData['newPassword']==$formData['cnewPassword'] and strlen($formData['newPassword'])>=5){
		//if not found error change password
		if($objAdmin->changePassword($this->admin->email,$formData['cnewPassword'])){
		$this->view->error='Password change successfully.';
		}else{
		$this->view->error='System error. Please try some time later.';
		} 
		}else{//confirm password not match
		$this->view->error='Confirm password is not same.';
		}
		}else{//if not valid old password
		$this->view->error='Old password not match.';
		}
		}//close form action
	}//close change password
} //close controller
?>
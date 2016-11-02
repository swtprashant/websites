<?php
class Admin_GalleryController extends Zend_Controller_Action
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
		$this->view->menuExp=60;
	}
	#####################################################################################################################################
	//CONTROLOR - POST LIST PAGE 
	function indexAction(){
		$host = $_SERVER['HTTP_HOST'];
		//echo $_REQUEST['keyword'];
		//exit;
		
		//$city = GetBetween1("live",".","$host");
		if ($_SERVER['HTTP_HOST'] == 'localhost') {
			$city = 'gaya';
		} else {
			$city = GetBetween1("live",".","$host");
		}
		//echo $userCity = str_replace(str_replace('live', '', $_SERVER['HTTP_HOST']), '.com', '', $_SERVER['HTTP_HOST']);
		//exit;
		$adminsession = new Zend_Session_Namespace('admin');
		$obj = new Default_Model_DbTable_User();
		$recods = $obj->getAdminByUser($adminsession->username);
		$userid = $recods['user_id'];
		$usertype = $recods['user_type'];
		$sql = "";
		$sql .= "SELECT *, DATE(createdOn) AS createdOn, @s:=@s+1 sno FROM (SELECT @s:= 0) AS s, `gallery`";
		$sql .= " ORDER BY `idgallery` DESC";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchAll($sql);
		
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($res);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);

		$this->view->perpage=25;
		$this->view->pages=$page;
		$this->view->data=$paginator;
		$this->view->messages = $this->_helper->_flashMessenger->getMessages();
		$this->render();
		
	}
	
	//CONTROLOR - POST ADD PAGE 
	function addAction(){
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$error = array();
			
			$title = addslashes($formData['gallerytitle']);
			$englishurl = addslashes(str_replace("'", '', $formData['englishurl']));
			$metatitle = addslashes($formData['metatitle']);
			$metakeyword = addslashes($formData['metakeyword']);
			$metadescription = addslashes($formData['metadescription']);
			
			/** category name validate*/
			if(!Zend_Validate::is($formData['gallerytitle'],'NotEmpty')){
				$error['title']='Please enter title';
			}
			
			/** english url validate*/
			if(!Zend_Validate::is($formData['englishurl'],'NotEmpty')){
				$error['englishurl']='Please enter english url';
			}
			if(count($error)== 0){
				$adminsession = new Zend_Session_Namespace('admin');
				$obj = new Default_Model_DbTable_User();
				$recods = $obj->getAdminByUser($adminsession->username);
				$userid = $recods['user_id'];
				$usertype = $recods['user_type'];
				$username = $recods['first_name']." ".$recods['last_name'];
				$createdOn = date('Y-m-d h:i:s');
				
				//###############//
				//INSERT NEW POST//
				//###############//
				$mySql = "";
				$mySql = "INSERT INTO `gallery` (";
				$mySql = $mySql . " `title`";
				$mySql = $mySql . ", `englishurl`";
				$mySql = $mySql . ", `metatitle`";
				$mySql = $mySql . ", `metakeyword`";
				$mySql = $mySql . ", `metadescription`";
				$mySql = $mySql . ", `createdOn`";
				$mySql = $mySql . " ) VALUES (";
				$mySql = $mySql . "  '".$title."'";
				$mySql = $mySql . ", '".str_replace(' ', '-', strtolower($englishurl))."'";
				$mySql = $mySql . ", '".$metatitle."'";
				$mySql = $mySql . ", '".$metakeyword."'";
				$mySql = $mySql . ", '".$metadescription."'";
				$mySql = $mySql . ", '".$createdOn."'";
				$mySql = $mySql . ")";
				//echo $mySql .'<BR>';
				//exit;
				$ss = Zend_Registry::get("db")->query($mySql);
				$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				
				//echo 'I am here...' . $lastinsertid . '<br>';
				//exit;
				
				//return setAutoPriority($priority, $lastinsertid, $city);
				if($ss){
					//assign message
					$this->_flashMessenger->addMessage('Gallery title added successfully');
					$this->_redirect('/admin/gallery/edit/id/'.$lastinsertid);
				}else{
					$this->view->msg = 'Error , try agian.';
				}
			} else {
				//error send to view
				$this->view->error = $error;
			}
			//echo $data = serialize($_POST);
			//pr(unserialize($data));
		}
	}

	//CONTROLOR - POST EDIT PAGE 
	function editAction(){
		
		$idgallery = $this->_getParam('id');
		//echo $adminid;die();
		$sql="SELECT * FROM `gallery` WHERE `idgallery` = '".$idgallery."'";
		//echo $sql;
		//exit;
		$res = Zend_Registry::get("db")->fetchrow($sql);
		
		$this->view->data = $res;
		
		if($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			
			$error = array();
			
			$title = addslashes($formData['gallerytitle']);
			$englishurl = addslashes(str_replace("'", '', $formData['englishurl']));
			$metatitle = addslashes($formData['metatitle']);
			$metakeyword = addslashes($formData['metakeyword']);
			$metadescription = addslashes($formData['metadescription']);
			
			/** category name validate*/
			if(!Zend_Validate::is($formData['gallerytitle'],'NotEmpty')){
				$error['title']='Please enter title';
			}
			/** english url validate*/
			if(!Zend_Validate::is($formData['englishurl'],'NotEmpty')){
				$error['englishurl']='Please enter english url';
			}
			
			if(count($error)== 0){
				$adminsession = new Zend_Session_Namespace('admin');
				$obj = new Default_Model_DbTable_User();
				$recods = $obj->getAdminByUser($adminsession->username);
				$userid = $recods['user_id'];
				$usertype = $recods['user_type'];
				$username = $recods['first_name']." ".$recods['last_name'];
				$createdOn = date('Y-m-d h:i:s');
				
				//###############//
				//INSERT NEW POST//
				//###############//
				$mySql = "";
				$mySql = "UPDATE `gallery` SET";
				$mySql = $mySql . " `title` = '".$title."'";
				$mySql = $mySql . ", `englishurl` = '".str_replace(' ', '-', strtolower($englishurl))."'";
				$mySql = $mySql . ", `metatitle` = '".$metatitle."'";
				$mySql = $mySql . ", `metakeyword` = '".$metakeyword."'";
				$mySql = $mySql . ", `metadescription` = '".$metadescription."'";
				//$mySql = $mySql . ", `createdOn` = '".$createdOn."'";
				$mySql = $mySql . " WHERE idgallery = '".$idgallery."'";
				//echo $mySql .'<BR>';
				//exit;
				$ss = Zend_Registry::get("db")->query($mySql);
				//$lastinsertid = Zend_Registry::get("db")->lastInsertId();
				
				//echo 'I am here...' . $lastinsertid . '<br>';
				//exit;
				
				//return setAutoPriority($priority, $lastinsertid, $city);
				if($ss){
					//assign message
					$this->_flashMessenger->addMessage('Gallery detail updated successfully');
					$this->_redirect('/admin/gallery/edit/id/'.$idgallery);
				}else{
					$this->view->msg = 'Error , try agian.';
				}
			} else {
				//error send to view
				$this->view->error = $error;
			}
		//echo $data = serialize($_POST);
		//pr(unserialize($data));
		}
	}
	
	//CONTROLOR - AJAX MEDIA UPLOAD
	public function ajaxmediauploadAction()
	{
		$host = $_SERVER['HTTP_HOST'];
		//echo $host;
		
		//$city = GetBetween1("live",".","$host");
		if ($_SERVER['HTTP_HOST'] == 'localhost') {
			$city = 'gaya';
		} else {
			$city = GetBetween1("live",".","$host");
		}
		
		//if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
		if($this->getRequest()->isPost())
		{
			$idgallery = $_POST['idgallery'];
					
			$formData = $this->getRequest()->getPost();
			{
				$adapter  = new Zend_File_Transfer_Adapter_Http();
				$fileInfo = $adapter->getFileInfo();
				
				$returnFile = '';
				
				if($fileInfo['image']['name'] != "")
				{
					$bigImagePath = GALLERY_DIR_PATH.'/';
					//$bigImagePath = replaceDomain($bigImagePath, $city, $_SERVER['HTTP_HOST']);
					//if ($_SERVER['HTTP_HOST'] != 'localhost'){
					//	$bigImagePath = replaceDomain($bigImagePath, $city, str_replace('www.','',$_SERVER['HTTP_HOST'])); 
					//} 
					
					//echo 'I am testing...<br>Please wait... <br> Image Path = ' . $bigImagePath; 
					//exit;
					$return = $this->uploadImage($adapter, $bigImagePath,TRUE);
					//$return = $this->compressImage($ext,$uploadedfile,$path,$actual_image_name,$newwidth);
					//echo "<pre>";
					//print_r($return); die;
					$returnFile = $return['fileName'];
					if($return['error']!= "")
					{
						$this->_flashMessenger->addMessage($return['error']);
						$this->_redirect('/admin/gallery/');
					}
						
					$title = addslashes($_POST['imagetitle']);
					$createdOn = date('Y-m-d h:i:s');
					
					$mySql = "";
					$mySql = "INSERT INTO `galleryimage` (";
					$mySql = $mySql . " `idgallery`";
					$mySql = $mySql . ", `title`";
					$mySql = $mySql . ", `image`";
					$mySql = $mySql . ") VALUES (";
					$mySql = $mySql . " '".$idgallery."'";
					$mySql = $mySql . ", '".$title."'";
					$mySql = $mySql . ", '".$returnFile."'";
					$mySql = $mySql . ")";
					//echo $mySql .'<BR>';
					//exit;
					$ss = Zend_Registry::get("db")->query($mySql);
					
					//exit;
				} //else {
				//	$returnFile = $formData['imageOld'];
				//}
			}
		}
		
		$tableValue = '';
		
		//if(!empty($idgallery)){
			$sql = "";
			$sql .= "SELECT * FROM `galleryimage`";
			$sql .= " WHERE idgallery = '".$idgallery."'";
			$sql .= " ORDER BY idgalleryimage DESC";
			//echo $sql;
			//exit;
			$rsTemp = Zend_Registry::get("db")->fetchAll($sql);
			
			//echo $sql;
			//exit;
			$i = 0;
			if($rsTemp){
				$tableValue = '';
				$tableValue .= '<table class="table table-bordered table-scroll mb30" cellspacing="0" width="99%">';
				$tableValue .= '<thead>';
					$tableValue .= '<tr>';
						$tableValue .= '<th width="1%">#</th>';
						$tableValue .= '<th width="">Title</th>';
						$tableValue .= '<th width="10%">File</th>';
						$tableValue .= '<th width="1%"></th>';
					$tableValue .= '</tr>';
				$tableValue .= '</thead>';
				$tableValue .= '<tbody>';
				foreach($rsTemp as $rsVal){
					$i++;
					$tableValue .= '<input type="hidden" name="idMedia" id="idMedia" value="'.$rsVal['idgalleryimage'].'" />';
					$tableValue .= '<tr>';
					$tableValue .= '<td style="font-size:14px;">' . $i .'</td>';
					$tableValue .= '<td style="font-size:14px;">' . $rsVal['title'] .'</td>';
					$tableValue .= '<td style="font-size:10px;"><img src="../../../../public/gallery/thumb/'. $rsVal['image'] .'" width="50px" /></td>';
					$tableValue .= '<td style="font-size:10px;">';
					$tableValue .= '<a href="javascript:void(0)" onclick="deleteMedia('.$rsVal['idgalleryimage'].','.$rsVal['idgallery'].')" id="delMedia" title="Delete This?"><i class="fa fa-trash-o textred" style="font-size:11px;"></i></a>';
					$tableValue .= '</td>';
				$tableValue .= '</tr>';
				}
				$tableValue .= '</tbody>';
				$tableValue .= '</table>';
			}
			echo $tableValue;
		//}
	}
	
	//CONTROLOR - POST DELETE PAGE 
	/*public function deleteAction(){
		$this->getHelper('viewRenderer')->setNoRender();
		$this->_helper->layout->disableLayout();	
		$id = $this->_getParam('id');
		//"UPDATE `user` SET `active`=abs(`active`-1) WHERE user_id='$id'";
		//$sql ="UPDATE `post` SET `status` = '0', isDeleted = '1' WHERE `post_id` = '".$id."'";
		$sql = "DELETE FROM post WHERE `post_id` = '".$id."'";
		//echo $sql;die();
		$del = Zend_Registry::get("db")->query($sql);
		
		if($del){
			$this->_flashMessenger->addMessage('Selected record deleted');
			$this->_redirect('/admin/post/index');	
		}
	}*/
	
	// IMAGE UPLOAD
	public function uploadImage($adapter, $destination, $isThumb=FALSE)
	{
		$size = new Zend_Validate_File_Size(array('max' => '2097152')); //minimum bytes filesize
		//$fileType = new Zend_Validate_File_IsImage();
		//$adapter = new Zend_File_Transfer_Adapter_Http();
		$fileInfo = $adapter->getFileInfo();
		$fileName = explode('.', $fileInfo['image']['name']);
		$fileExt  = $fileName[count($fileName) - 1];
		$fileName = 'img_' . date('Ymdhis') . '.' . $fileExt;
		
		$adapter->setDestination($destination);
		//validator can be more than one...
		$adapter->setValidators(array($size));
	
		$errors = '';
		if (!$adapter->isValid())
		{
			$dataError = $adapter->getMessages();
			foreach ($dataError as $key => $row)
			{
				$errors = $row;
			} //set formElementErrors
			
			return array('fileName'=>$fileName,'error'=>$errors);
		}
		else if ($fileInfo['image']['name'] != "")
		{
			$adapter->addFilter('Rename', array('target' => $destination . '/' . $fileName, 'overwrite' => true));
			$adapter->receive();
			if($isThumb)
			{
				$img = imagecreatefromjpeg( $destination . '/' . $fileName );
				
				/*if($fileExt=="jpg" || $fileExt=="jpeg" )
				{
					$img = imagecreatefromjpeg($destination . '/' . $fileName);
				}
				else if($fileExt=="png")
				{
					$img = imagecreatefrompng($destination . '/' . $fileName);
				}
				else if($fileExt=="gif")
				{
					$img = imagecreatefromgif($destination . '/' . $fileName);
				}
				else
				{
					$img = imagecreatefrombmp($destination . '/' . $fileName);
				}*/
				
				$width = imagesx( $img );
				$height = imagesy( $img );
	
				// calculate thumbnail size
				$new_width = 150;
				$new_height = floor( $height * ( 150 / $width ) );
	
				// create a new temporary image
				$tmp_img = imagecreatetruecolor($new_width, $new_height);
				
	
				// copy and resize old image into new image
				imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
	
				imagejpeg( $tmp_img, $destination . '/thumb/' . $fileName );
	
			}
			return array('fileName'=>$fileName,'error'=>$errors);
		}	
	}

}//close class


function replaceDomain($imagePath, $city, $hostURL){
	$newPath='';
	//echo '$imagePath = ' .$imagePath . '<br>';
	//echo '$city = ' .$city . '<br>';
	//echo '$hostURL = ' .$hostURL . '<br>';
	if (strtolower($hostURL) == 'livedhanbad.com' && strtolower($city) != 'dhanbad') {
		$newPath = '/home/livedhanbad/public_html/live'.strtolower($city).'.com/public/products/image';
	} else {
		if (strtolower($city) == 'alwar'){
			$newPath = str_replace($hostURL, 'livealwar.com', $imagePath);
		} else if (strtolower($city) == 'basti'){
			$newPath = str_replace($hostURL, 'livebasti.com', $imagePath);
		} else if (strtolower($city) == 'begusarai'){
			$newPath = str_replace($hostURL, 'livebegusarai.com', $imagePath);
		} else if (strtolower($city) == 'dhanbad'){
			$newPath = str_replace($hostURL.'/', '', $imagePath);
		} else if (strtolower($city) == 'gaya'){
			$newPath = str_replace($hostURL, 'livegaya.com', $imagePath);
		} else if (strtolower($city) == 'giridih'){
			$newPath = str_replace($hostURL, 'livegiridih.com', $imagePath);
		} else if (strtolower($city) == 'santkabirnagar'){
			$newPath = str_replace($hostURL, 'livesantkabirnagar.com', $imagePath);
		} else if (strtolower($city) == 'siddharthnagar'){
			$newPath = str_replace($hostURL, 'livesiddharthnagar.com', $imagePath);
		} else if (strtolower($city) == 'tehri'){
			$newPath = str_replace($hostURL, 'livetehri.com', $imagePath);
		}
	}
	return $newPath;
}

function replaceJunk($str){
	$str = str_replace('font-family: helvetica, arial, sans-serif;','', $str);	
	$str = str_replace('helvetica','', $str);
	$str = str_replace(' style=""','',$str);
	$str = str_replace('class="text_exposed_show" ','',$str);
	$str = str_replace(' background-color: rgb(255, 255, 255);','',$str);
	$str = str_replace('    line-height: 21.4667px;','',$str);
	$str = str_replace(' class="MsoNormal" ','', $str);	
	$str = str_replace('class="MsoNormal"','', $str);	
	$str = str_replace('background-image: initial; ','', $str); 
	$str = str_replace('background-attachment: initial; ','', $str); 
	$str = str_replace('background-size: initial; ','', $str); 
	$str = str_replace('background-origin: initial; ','', $str);
	$str = str_replace('background-clip: initial; ','', $str);
	$str = str_replace('background-position: initial; ','', $str);
	$str = str_replace('background-repeat: initial; ','', $str);
	$str = str_replace('<font color="#222222" face="Mangal, serif">','', $str);
	$str = str_replace('<span style="font-size: 18.6667px; line-height: 21.4667px;">','', $str);
	$str = str_replace('arial','', $str);	
	$str = str_replace('Mangal','', $str);	
	$str = str_replace('serif','', $str);	
	$str = str_replace('sans-serif','', $str);
	$str = str_replace('class="MsoNormal"','', $str);
	$str = str_replace('<p  style="        ">&nbsp;</p>','', $str);
	$str = str_replace('font-size: medium;','', $str);		
	$str = str_replace('font-family','', $str);	
	$str = str_replace('font-size: 14px;','', $str);
	$str = str_replace('line-height: 19.32px;','', $str);
	$str = str_replace('margin: 6px 0px;','', $str);
	$str = str_replace('color: rgb(20, 24, 35);','', $str);
	$str = str_replace('margin: 0px 0px 6px;','', $str);
	$str = str_replace('color: rgb(52, 52, 52);','', $str);
	$str = str_replace('margin-bottom: 0.0001pt;','', $str);
	$str = str_replace('line-height: 24.4pt;','', $str);
	$str = str_replace('background-image: initial;','', $str);
	$str = str_replace('background-attachment: initial;','', $str);
	$str = str_replace('background-size: initial;','', $str);
	$str = str_replace('background-origin: initial;','', $str);
	$str = str_replace('background-clip: initial;','', $str);
	$str = str_replace('background-position: initial;','', $str);
	$str = str_replace('background-repeat: initial;','', $str);
	$str = str_replace(' style="    "','', $str);
	$str = str_replace(' style=""','', $str);
	$str = str_replace('style=": , ; "','', $str);
	$str = str_replace(' lang="HI" ','', $str);
	$str = str_replace('style="        "','', $str);
	$str = str_replace('style=": , ;        "','', $str);
	$str = str_replace('<p  >&nbsp;</p>','', $str);
	$str = str_replace('data-block="true"','', $str);
	$str = str_replace('data-offset-key="1787j-0-0"','', $str);
	$str = str_replace('class="_209g _2vxa"','', $str);
	$str = str_replace('style="direction: ltr; position: relative;    line-height: 18px; white-space: pre-wrap;"','', $str);
	$str = str_replace('line-height: 18px;','', $str);
	$str = str_replace('white-space: pre-wrap;','', $str);
	$str = str_replace('class="text_exposed_show"','', $str);
	$str = str_replace('<div>&nbsp;</div>','', $str);
	$str = str_replace('<spanstyle="line-height: 115%; : , ;">','', $str);
	//$str = str_replace('</span>','', $str);
	
	if (strpos(strtolower($str), '.com/userfiles/') == 0)
	{
		$hostLink = $_SERVER['HTTP_HOST'];
		$hostLink = str_replace('http://','', $hostLink);
		$hostLink = str_replace('www.','', $hostLink);
		$str = str_replace('/userfiles/', 'http://www.'. $hostLink .'/userfiles/', $str);
	}
	$str = str_replace('<div>','<p>', $str);
	$str = str_replace('</div>','</p>', $str);
	return $str;
}

function compressImage($ext,$uploadedfile,$path,$actual_image_name,$newwidth)
{
	if($ext=="jpg" || $ext=="jpeg" )
	{
		$src = imagecreatefromjpeg($uploadedfile);
	}
	else if($ext=="png")
	{
		$src = imagecreatefrompng($uploadedfile);
	}
	else if($ext=="gif")
	{
		$src = imagecreatefromgif($uploadedfile);
	}
	else
	{
		$src = imagecreatefrombmp($uploadedfile);
	}

	list($width,$height)=getimagesize($uploadedfile);
	$newheight=($height/$width)*$newwidth;
	$tmp=imagecreatetruecolor($newwidth,$newheight);
	imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
	$filename = $path.$newwidth.'_'.$actual_image_name; //PixelSize_TimeStamp.jpg
	imagejpeg($tmp,$filename,100);
	imagedestroy($tmp);
	return $filename;
}

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
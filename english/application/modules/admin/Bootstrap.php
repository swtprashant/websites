<?php
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap 
{
  
		
 	protected function _initAutoload()
	{
		   
		  include_once('application/modules/default/models/DbTable/user.php'); 
		  include_once('application/modules/default/models/DbTable/category.php');
		  include_once('application/modules/default/models/DbTable/domain.php');
		  include_once('application/modules/default/models/DbTable/breaking.php');
		  
	}
		

}

//function pr($option){ echo '<pre>'. print_r($option,true) .'</pre>';}
//function prr($data){die('<pre>'. print_r($data,true) .'</pre>');}

// Create thumbnils //
function createThumbnail($filename, $path_to_image_directory, $path_to_thumbs_directory, $final_width_of_image){ 
     
    if(preg_match('/[.](jpg)$/', $filename)) {
        $im = imagecreatefromjpeg($path_to_image_directory . $filename);
    } else if (preg_match('/[.](gif)$/', $filename)) {
        $im = imagecreatefromgif($path_to_image_directory . $filename);
    } else if (preg_match('/[.](png)$/', $filename)) {
        $im = imagecreatefrompng($path_to_image_directory . $filename);
    }
     
    $ox = imagesx($im);
    $oy = imagesy($im);
     
    $nx = $final_width_of_image;
    $ny = floor($oy * ($final_width_of_image / $ox));
     
    $nm = imagecreatetruecolor($nx, $ny);
     
    imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);   
     
    imagejpeg($nm, $path_to_thumbs_directory . $filename);
}

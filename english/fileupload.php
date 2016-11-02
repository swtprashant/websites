<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>



<?php
   if(isset($_FILES['image'])){
      $errors= array();
      $file_name = $_FILES['image']['name'];
      $file_size =$_FILES['image']['size'];
      $file_tmp =$_FILES['image']['tmp_name'];
      $file_type=$_FILES['image']['type'];
      //$file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));
      
      //$expensions= array("jpeg","jpg","png");
      
      //if(in_array($file_ext,$expensions)=== false){
      //   $errors[]="extension not allowed, please choose a JPEG or PNG file.";
      //}
      
      //if($file_size > 2097152){
      //   $errors[]='File size must be excately 2 MB';
      //}
      
      //if(empty($errors)==true){
         move_uploaded_file($file_tmp,"uploads/".$file_name);
         echo "Success";
      //}else{
      //   print_r($errors);
      //}
   }
?>
<html>
   <body>
      
      <form action="" method="POST" enctype="multipart/form-data">
         <input type="file" name="image" />
         <input type="submit"/>
      </form>
      
   </body>
</html>
</body>
</html>
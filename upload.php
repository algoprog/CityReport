<?php

include('includes/config.php');

header('Content-type: application/json');

if(isset($_FILES['img']) && !empty($_SESSION['img_id'])){
    if($_FILES['img']['size']<1024*6000){
		$ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
		$path = 'uploads/pictures/'.sha1($_SESSION['img_id']).'.'.$ext;
		move_uploaded_file($_FILES['img']['tmp_name'], $path);
		$status = 1;
	}else{
		$status = 0;
	}
}

echo json_encode(array('status'=>$status));

?>
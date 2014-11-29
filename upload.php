<?php

include('includes/config.php');

/**
 * Image resize while uploading
 * @author Resalat Haque
 * @link http://www.w3bees.com/2013/03/resize-image-while-upload-using-php.html
 */
 
/**
 * Image resize
 * @param int $width
 * @param int $height
 */
function resize(){
	/* Get original image x y*/
	list($w, $h) = getimagesize($_FILES['img']['tmp_name']);
	
	$width = 900;
	$height = 900;
	
	$ratio_orig = $w/$h;
	if($width/$height > $ratio_orig){
		$width = $height*$ratio_orig;
	}else{
		$height = $width/$ratio_orig;
	}
	
	/* new file name */
	$ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
	$path = 'uploads/pictures/'.sha1($_SESSION['img_id']).'.'.$ext;
	/* read binary data from image file */
	$imgString = file_get_contents($_FILES['img']['tmp_name']);
	/* create image from string */
	$image = imagecreatefromstring($imgString);
	$tmp = imagecreatetruecolor($width, $height);
	imagecopyresampled($tmp, $image, 0, 0, 0, 0, $width, $height, $w, $h);
	/* Save image */
	switch ($_FILES['img']['type']) {
		case 'image/jpeg':
			imagejpeg($tmp, $path, 100);
			break;
		case 'image/png':
			imagepng($tmp, $path, 0);
			break;
		case 'image/gif':
			imagegif($tmp, $path);
			break;
		default:
			exit;
			break;
	}
	return $path;
	/* cleanup memory */
	imagedestroy($image);
	imagedestroy($tmp);
}

header('Content-type: application/json');

// settings
$max_file_size = 1024*6000; // ~6MB
$valid_exts = array('jpeg', 'jpg', 'png', 'gif');

$url = '';
$status = 0;
$msg = 'not ok';
if(isset($_FILES['img']) && !empty($_SESSION['img_id'])){
	if($_FILES['img']['size'] < $max_file_size){
		//get file extension
		$ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
		if(in_array($ext, $valid_exts)){
			/* resize image */
			$url = resize();
			$status = 1;
			$msg = 'uploaded ok';
		}else{
			$msg = 'Unsupported file';
		}
	}else{
		$msg = 'Please upload image smaller than 6 MB';
	}
}else{
	$msg = 'Not image set';
}

echo json_encode(array('status'=>$status,'msg' => $msg, 'url' => $url));

?>
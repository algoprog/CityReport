<?php

error_reporting(0);

$types = array('jpeg','jpg','gif','png');

foreach($types as $ext){
	if(file_exists('uploads/pictures/'.$_GET['f'].'.'.$ext)){
		header('content-type: image/'.$ext);
		$file = 'uploads/pictures/'.$_GET['f'].'.'.$ext;
		echo file_get_contents($file);
		unlink($file);
	}
}
header('content-type: image/gif');
echo file_get_contents('images/blank.gif');

?>
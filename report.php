<?php

include('includes/config.php');
include('includes/classes/phpmailer.class.php');

header('Content-type: application/json');

function msg($str){
	echo json_encode(array('msg'=>htmlspecialchars($str)));
}

$issue_id = intval($_POST['issue_id']);

$lat = floatval($_POST['lat']);

$lng = floatval($_POST['lng']);

$address = htmlspecialchars($_POST['address']);

$message = urldecode(htmlspecialchars($_POST['message']));

$date = time();

if($_POST['pic']){
	$_SESSION['img_id'] = $date;
	$file = sha1($_SESSION['img_id']);
	$picture = "<img src=\"$host/image?f=$file\" style=\"max-width:500px; max-height:500px;\"/>";
}

$ip = $_SERVER['REMOTE_ADDR'];

$query = mysql_query("SELECT * FROM issue_types WHERE id = '$issue_id';");

if(empty($lat)||empty($lng)){
	msg('Δεν έχει οριστεί τοποθεσία.');
}
elseif($lat>90||$lat<0){
	msg('Μη έγκυρο γεωγραφικό πλάτος.');
}
elseif($lng>180||$lng<-180){
	msg('Μη έγκυρο γεωγραφικό μήκος.');
}
elseif(empty($address)||mb_strlen($address)>100){
	msg('Η διεύθυνση πρέπει να είναι 1-100 χαρακτήρες.');
}
elseif(mb_strlen($message)>500){
	msg('Tο μήνυμα δεν πρέπει να ξεπερνάει τους 500 χαρακτήρες.');
}
elseif(mysql_num_rows($query)==0){
	msg('Μη έγκυρο id προβλήματος.');
}
elseif(trim(strtolower($_POST['captcha']))!=$_SESSION['captcha']||empty($_SESSION['captcha'])){
	msg('Παρακαλώ εισάγετε το σωστό κείμενο captcha.');
}
else{
	mysql_query("INSERT INTO issues SET issue_id = '$issue_id', lat = '$lat', lng = '$lng', address = '$address', message = '$message', date = '$date', picture = '$file', ip = '$ip';");

	if($message==''){
		$message = '-';
	}
	
	$email = new PHPMailer();
	$email->From      = 'noreply@algoprog.com';
	$email->FromName  = 'CityReport';
	$email->Subject   = $data['title'].' - CityReport';
	$email->isHTML(true);
	
	$date = gmdate("Y-m-d\TH:i:s\Z", $date);
	
	$email->Body      = 
			"<h2>Ειδοποίηση από CityReport</h2>
			Ημερομηνία: $date<br/><br/>
			Τύπος προβλήματος: {$data['title']}<br/><br/>
			Σχόλια: $message<br/><br/>
			Τοποθεσία: $lat $lng - Προβολή στο <a href='http://maps.google.com/maps?&z=10&q=".$lat."+".$lng."&ll=".$lat."+".$lng."' target='blank'>Google Maps</a><br/><br/>
			Φωτογραφία: <br/><br/>$picture<br/><br/>";
	
	$query = mysql_query("SELECT email FROM issue_types WHERE id = '$issue_id';");
	$data = mysql_fetch_assoc($query);
	
	$email->AddAddress($data['email']);
	
	$email->Send();
	
	msg('ok');
}

?>
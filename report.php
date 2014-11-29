<?php

include('includes/config.php');
include('includes/phpmailer.php');

header('Content-type: application/json');

function msg($str){
	echo json_encode(array('msg'=>htmlspecialchars($str)));
}

$issue_id = intval($_POST['issue_id']);

$lat = floatval($_POST['lat']);

$lng = floatval($_POST['lng']);

$address = htmlspecialchars($_POST['address']);

$message = urldecode(htmlspecialchars($_POST['message']));

if($message==''){
	$message = '-';
}

$date = time();

$_SESSION['img_id'] = $date;

$file = sha1($_SESSION['img_id']);
$picture = "<img src=\"$host/image?f=$file\" style=\"max-width:500px; max-height:500px;\"/>";

$ip = $_SERVER['REMOTE_ADDR'];

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
elseif($issue_id>count($issues)||$issue_id<1){
	msg('Μη έγκυρο id προβλήματος.');
}
elseif(trim(strtolower($_POST['captcha']))!=$_SESSION['captcha']){
	msg('Παρακαλώ εισάγετε το σωστό κείμενο captcha.');
}
else{
	$email = new PHPMailer();
	$email->From      = 'noreply@algoprog.com';
	$email->FromName  = 'CityReport';
	$email->Subject   = $issues[$issue_id-1]['title'].' - CityReport';
	$email->isHTML(true);
	
	$date = gmdate("Y-m-d\TH:i:s\Z", $date);
	
	$email->Body      = 
	"<h2>Ειδοποίηση από CityReport</h2>
	Ημερομηνία: $date<br/><br/>
	Τύπος προβλήματος: {$issues[$issue_id-1]['title']}<br/><br/>
	Σχόλια: $message<br/><br/>
	Τοποθεσία: $lat $lng - Προβολή στο <a href='http://maps.google.com/maps?&z=10&q=".$lat."+".$lng."&ll=".$lat."+".$lng."' target='blank'>Google Maps</a><br/><br/>
	Φωτογραφία: <br/><br/>$picture<br/><br/>";
	
	$email->AddAddress($issues[$issue_id-1]['email']);
	
	$email->Send();
	
	msg('ok');
}

?>
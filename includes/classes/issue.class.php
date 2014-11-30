<?php

class issue{
	
	public $data;
	
	function _construct($id){
		if(!empty($id)){
			$id = intval($id);
			$query = mysql_query("SELECT * FROM issues JOIN issue_types ON issues.issue_id = issue_types.id WHERE id = '$id';");
			if(mysql_num_rows($query)){
				$this->data = mysql_fetch_assoc($query);
			}
		}
	}
	
	public function delete(){
		if($this->data['id']){
			mysql_query("DELETE FROM issues WHERE id = '{$this->data['id']}';");
		}
	}
	
	public function add($issue_id, $lat, $lng, $address, $message){
		$lat = floatval($lat);
		$lng = floatval($lng);
		
		$message = mysql_real_escape_string(htmlspecialchars($message));
		if($message==''){
			$message = '-';
		}
		
		//$issue_t = new issueType($issue_id);
		$query = mysql_query("SELECT * FROM issue_types WHERE id = '$issue_id';");
		
		if(mysql_num_rows($query)==0) $msg = 'Μη έγκυρο id προβλήματος.';
		elseif($lat>90||$lat<0) $msg = 'Μη έγκυρο γεωγραφικό πλάτος.';
		elseif($lng>180||$lng<-180) $msg = 'Μη έγκυρο γεωγραφικό μήκος.';
		elseif(empty($address)||mb_strlen($address)>100) $msg = 'Η διεύθυνση πρέπει να είναι 1-100 χαρακτήρες.';
		elseif(mb_strlen($message)>500) $msg = 'Tο μήνυμα δεν πρέπει να ξεπερνάει τους 500 χαρακτήρες.';
		elseif(trim(strtolower($_POST['captcha']))!=$_SESSION['captcha']) $msg = 'Παρακαλώ εισάγετε το σωστό κείμενο captcha.';
		else{
			$data = mysql_fetch_assoc($query);
		
			$msg = 'ok';
			
			$date = time();

			$_SESSION['img_id'] = $date;
			
			$file = sha1($_SESSION['img_id']);
			$picture = "<img src=\"$host/image?f=$file\" style=\"max-width:500px; max-height:500px;\"/>";
			$ip = $_SERVER['REMOTE_ADDR'];
			
			mysql_query("INSERT INTO issues SET issue_id = '$issue_id', lat = '$lat', lng = '$lng', address = '$address', message = '$message', date = '$date', picture = '$file', ip = '$ip';");
			
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
	
			$email->AddAddress($data['email']);
	
			$email->Send();
		}
		return $msg;
	}
	
	public function add_type($title, $email){
		$title = secureval($title);
		if(!checkmail($email)) $msg = 'Μη έγκυρο email.';
		elseif(strlen($email)>100) $msg = 'Το email είναι πάνω από 100 χαρακτήρες.';
		elseif(strlen($title)>100) $msg = 'Ο τίτλος είναι πάνω από 100 χαρακτήρες.';
		elseif(empty($title)||empty($email)) $msg = 'Κενός τίτλος ή email.';
		else{
			$query = mysql_query("SELECT id FROM issue_types WHERE title = '$title';");
			if(mysql_num_rows($query)) $msg = 'Ο τύπος ήδη υπάρχει στη βάση.';
			else{
				mysql_query("INSERT INTO issue_types SET title = '$title', email = '$email';");
				$msg = 'ok';
			}
		}
		return $msg;
	}
	
	public function delete_type($id){
		$id = intval($id);
		mysql_query("DELETE FROM issue_types WHERE id = '$id';");
	}
	
}

?>
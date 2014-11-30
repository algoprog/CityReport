<?php

function msg($str){
	echo '{"msg": "'.htmlspecialchars($str).'"}';
}

function secureval($str){
	return mysql_real_escape_string(htmlspecialchars($str));
}

//Check for valid email
function checkmail($email){
	return preg_match("/^[\.A-z0-9_\-\+]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $email);
}

function time_stamp($session_time){
	$time_difference = time() - $session_time;
	if($time_difference<0){
		$time_difference = $session_time - time();
	}
	$seconds = $time_difference;
	$minutes = round($time_difference / 60 );
	$hours = round($time_difference / 3600 );
	$days = round($time_difference / 86400 );
	$weeks = round($time_difference / 604800 );
	$months = round($time_difference / 2419200 );
	$years = round($time_difference / 29030400 );
	if($seconds <= 60){
		$ago = "$seconds δευτερόλεπτα πριν";
	}
	elseif($minutes <=60){
		if($minutes==1){
			$ago = "1 λεπτό πριν";
		}
		else{
			$ago = "$minutes λεπτά πριν";
		}
	}
	elseif($hours <=24){
		if($hours==1){
			$ago = "1 ώρα πριν";
		}else{
			$ago = "$hours ώρες πριν";
		}
	}
	elseif($days <=7){
		if($days==1){
			$ago = "1 μέρα πριν";
		}else{
			$ago = "$days μέρες πριν";
		}
	}
	elseif($weeks <=4){
		if($weeks==1){
			$ago = "1 βδομάδα πριν";
		}else{
			$ago = "$weeks βδομάδες πριν";
		}
	}
	elseif($months <=12){
		if($months==1){
			$ago = "1 μήνα πριν";
		}else{
			$ago = "$months μήνες πριν";
		}
	}else{
		if($years==1){
			$ago = "1 χρόνο πριν";
		}else{
			$ago = "$years χρόνια πριν";
		}
	}
	return $ago;
}

function add_type($title, $email){
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

?>
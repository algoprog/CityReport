<?php

include('includes/config.php');
include('includes/functions.php');
include('includes/classes/admin.class.php');

$admin = new admin();

if($_GET['out']==1){
	$admin->logout();
}

$logged = $admin->is_logged();

$msg = '';

if(!$logged){
	if($_POST['login_btn']){
		$logged = $admin->login($_POST['username'], $_POST['password']);
		if(!$logged) $msg = 'Λάθος username ή password.';
	}
}else{
	if($_POST['add_type_btn']){
		$msg = add_type($_POST['type_title'], $_POST['type_email']);
	}elseif($_POST['type_id']){
		$tid = intval($_POST['type_id']);
		mysql_query("DELETE FROM issue_types WHERE id = '$tid';");
		mysql_query("DELETE FROM issues WHERE issue_id = '$tid';");
	}elseif($_POST['issue_id']){
		$iid = intval($_POST['issue_id']);
		$query = mysql_query("SELECT picture FROM issues WHERE id = '$iid';");
		$pdata = mysql_fetch_assoc($query);
		if($pdata['picture']){
			$types = array('jpeg','jpg','gif','png');
			foreach($types as $ext){
				unlink("uploads/pictures/{$pdata['picture']}.".$ext);
			}
		}
		mysql_query("DELETE FROM issues WHERE id = '$iid';");
	}
}
?>
<!DOCTYPE html>
<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CityReport / Διαχειριστής</title>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<link href="http://fonts.googleapis.com/css?family=Roboto+Condensed&subset=latin,greek" rel="stylesheet" type="text/css">

<script src="js/pace.js"></script>
<script src="js/bootbox.js"></script>
<script src="js/admin.js"></script>

<link rel="stylesheet" href="css/app.css">

</head>

<body>

<nav class="navbar navbar-default navbar-fixed-top" role="navigation">

<?php if($logged) $home = 'admin'; else $home = 'index'; ?>

<a class="navbar-brand" href="<?php echo $home; ?>"><img class="img-rounded logo" alt="CityReport" src="images/app_icon.png"></a>
<a class="navbar-brand" style="width:100px;" href="<?php echo $home; ?>">CityReport</a>

</nav>

<?php
if(!$logged){
?>

<div class="pbox center panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Σύνδεση διαχειριστή</h3>
  </div>
	<div class="panel-body">
		<form class="form-group" action="admin" method="POST">
			username: <input type="text" name="username" class="form-control" placeholder="username" required/><br/><br/>
			password: <input type="password" name="password" class="form-control" placeholder="password" required/><br/>
			<p align="center"><input type="submit" class="btn btn-success" name="login_btn" value="Σύνδεση"/></p><br/>
		</form>
  	</div>
</div>

<?php
}else{
?>

<p class="center">Είστε συνδεδεμένος - <a href="admin?out=1">Αποσύνδεση</a></p><br/>
	
<div class="pbox center panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Προσθήκη τύπου προβλήματος</h3>
  </div>
	<div class="panel-body">
		<form class="form-group" action="admin" method="POST">
			Τίτλος προβλήματος: <input type="text" name="type_title" class="form-control" placeholder="τίτλος" required/><br/><br/>
			email παραλήπτη: <input type="email" name="type_email" class="form-control" placeholder="email" required/><br/>
			<p align="center"><input type="submit" class="btn btn-success" name="add_type_btn" value="Προσθήκη"/></p><br/>
		</form>
  	</div>
</div>

<br/>

<div class="pbox center panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Διαχείριση τύπων</h3>
  </div>
	<div class="panel-body">
		<div class="form-group">
			<?php
				$query = mysql_query("SELECT * FROM issue_types;");
				while($data = mysql_fetch_assoc($query)){
					echo "<p>{$data['title']} - {$data['email']} - <a class='delete_type' tid='{$data['id']}' href='javascript:void(0);'>Διαγραφή</a></p>";
				}
			?>
		</div>
  	</div>
</div>

<?php
	$query = mysql_query("SELECT * FROM issues ORDER BY id DESC;");
	$count = mysql_num_rows($query);
?>

<br/>
<div class="pbox center panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Διαχείριση προβλημάτων (<span id="pcount"><?php echo $count; ?></span>)</h3>
  </div>
	<div class="panel-body">
		<div class="form-group">
			<?php
				while($data = mysql_fetch_assoc($query)){
					$pq = mysql_query("SELECT title FROM issue_types WHERE id = '{$data['issue_id']}';");
					$idata = mysql_fetch_assoc($pq);
					if($data['picture']){
						$img = " - <a href='image?f={$data['picture']}' target='blank'>Προβολή εικόνας</a>";
					}else{
						$img = '';
					}
					if($data['message']) $dmsg = "<br/>Μήνυμα: ".$data['message']; else $msg = '';
					echo "<p>{$idata['title']} σε {$data['address']} ".time_stamp($data['date'])."<br/><a href='http://maps.google.com/maps?&z=10&q=".$data['lat']."+".$data['lng']."&ll=".$data['lat']."+".$data['lng']."' target='blank'>Προβολή χάρτη</a>$img - <a class='delete_issue' iid='{$data['id']}' href='javascript:void(0);'>Διαγραφή</a>$dmsg</p>";
				}
			?>
		</div>
  	</div>
</div>

<br/>

<p class="center">Είστε συνδεδεμένος - <a href="admin?out=1">Αποσύνδεση</a></p><br/>

<br/><br/>

<?php
}
if($msg!=''){
	echo "<script>bootbox.alert('$msg');</script>";
}
?>

<script>
$(".pbox").fadeIn(1000);
</script>

</body>
</html>
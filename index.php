<?php

include('includes/config.php');

?>
<!DOCTYPE html>
<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CityReport</title>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<script src="http://maps.google.com/maps/api/js?sensor=false&libraries=places"></script>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<link href="http://fonts.googleapis.com/css?family=Roboto+Condensed&subset=latin,greek" rel="stylesheet" type="text/css">

<script src="js/locationpicker.js"></script>
<script src="js/pace.js"></script>
<script src="js/bootbox.js"></script>
<script src="js/elastic.js"></script>
<script src="js/geoPosition.js"></script>
<script src="js/img-upload.js"></script>
<script src="js/app.js"></script>

<link rel="stylesheet" href="css/app.css">

</head>

<body>

<nav class="navbar navbar-default navbar-fixed-top" role="navigation">

<a class="navbar-brand" href="index"><img class="img-rounded logo" alt="CityReport" src="images/app_icon.png"></a>
<a class="navbar-brand" style="width:100px;" href="index">CityReport</a>
<a href="#"><img class="search" src="images/search.png"/></a>

<div class="search_box">
<div class="input-group">
    <input type="text" class="search_txt form-control">
    <span class="input-group-btn">
		<button class="btn btn-default" type="button">Εύρεση</button>
    </span>
</div>
</div>

</nav>

<div class="location_wait">
<p><img class="location img-responsive" src="images/location.png"/></p>
<p><img class="loading" src="images/throbber.gif"/><span class="location_msg">Αναμονή για τοποθεσία...</span></p>
<p></p>
</div>

<img class="loading" src="images/throbber.gif"/>

<div class="map"></div>


<div id="step1" class="pbox center panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Τύπος προβλήματος</h3>
  </div>
  <div class="panel-body">
	<div class="list-group">
		<?php
			$id = 0;
			foreach($issues as $issue){
				$id++;
				echo '<a href="#" class="list-group-item issue" iid="'.$id.'">'.$issue['title'].'</a>';
			}
		?>
	</div>
  </div>
</div>

<div id="step2" class="pbox center panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Σχόλια</h3>
  </div>
	<div class="panel-body">
		<div class="form-group">
			<textarea rows="2" class="form-control message" maxlength="500" placeholder="σχόλια σχετικά με το πρόβλημα..."></textarea>
		</div>
  	</div>
</div>

<div id="step3" class="pbox center panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Φωτογραφία</h3>
  </div>
	<div class="panel-body">
		<div class="form-group">
			<div id="h_upload" style="display:none;"></div>
			<div id="uploadbox" onClick="singleupload_input.click();" class="singleupload"></div>
			<input type="file" id="singleupload_input" style="display:none;" name="img" value="" accept="image/*" capture="camera"/>
		</div>
  	</div>
</div>

<div id="step4" class="pbox center panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Επιβεβαίωση ασφαλείας</h3>
  </div>
	<div class="panel-body">
		<div class="form-group">
			<img class="captcha_img" src="captcha"/><br/>
			<a href="#" onClick="change_captcha();">Αλλαγή κειμένου</a><br/>
			<input class="captcha_txt form-control" type="text" value="" placeholder="captcha κείμενο"/>
		</div>
  	</div>
</div>

<br/><br/><br/>

<div class="options navbar-fixed-bottom">
<div class="btn-group btn-group-justified">
	<div class="btn-group">
		<button type="button" class="btn btn-default mtext" onclick="window.location.reload();"><span class="vmiddle glyphicon glyphicon-map-marker" aria-hidden="true"></span>&nbsp; Εντοπισμός</button>
	</div>
	<div class="btn-group" id="next">
		<button type="button" id="report" class="btn btn-default mtext" onclick="show_issues();">Επόμενο <span class="vmiddle glyphicon glyphicon-chevron-right" aria-hidden="true"></span></button>
	</div>
</div>
</div>

</body>

</html>
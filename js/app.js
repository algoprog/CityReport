$(document).ready(function(){
	lat = 0; //latitude
	lng = 0; //longitute
	message = ''; //issue message
	address = ''; //street address
	issue_id = 0; //issue id
	
	enabled = 1;
	img_percent = 0;
	pic = 0;
	
	getLocation();
	
	$(".issue").click(function(){
		$(".issue").removeClass("active");
		$(this).addClass("active");
		issue_id = $(this).attr("iid");
	});
	
	$(".search").click(function(){
		show_search_form();
	});
	
	$(".search_txt").click(function(){
		$(this).val('');
	});
	
});


/*------ image upload functions ------*/

function fileSelected(){
	var count = document.getElementById('fileToUpload').files.length;
    document.getElementById('details').innerHTML = "";
    if($('#fileToUpload').val()!=''){
		//for(var index = 0; index < count; index ++){
			var file = document.getElementById('fileToUpload').files[0];
			if(file.size>6*1024*1024){
				bootbox.alert("Η εικόνα πρέπει να είναι το πολύ 6 MB.");
				$('#fileToUpload').val('');
				return;
			}
			var fileSize = 0;
			if(file.size > 1024 * 1024){
				fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
			}else{
				fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
			}
			document.getElementById('details').innerHTML += 'Name: ' + file.name + '<br>Size: ' + fileSize + '<br>Type: ' + file.type;
			document.getElementById('details').innerHTML += '<p>';
		//}
	}
 
}
 
function uploadFile(){
    var fd = new FormData();
	var count = document.getElementById('fileToUpload').files.length;
	//for(var index = 0; index < count; index ++){
		var file = document.getElementById('fileToUpload').files[0];
		fd.append('img', file);
	//}
	
    var xhr = new XMLHttpRequest();
    xhr.upload.addEventListener("progress", uploadProgress, false);
    xhr.addEventListener("load", uploadComplete, false);
    xhr.addEventListener("error", uploadFailed, false);
    xhr.addEventListener("abort", uploadCanceled, false);
    xhr.open("POST", "upload");
    xhr.send(fd);
}

function uploadProgress(evt){
    if(evt.lengthComputable){
        var percentComplete = Math.round(evt.loaded * 100 / evt.total);
		document.getElementById('progress').innerHTML = percentComplete.toString() + '%';
		img_percent = percentComplete;
		$('#slbl').html(percentComplete.toString() + '%');
	}else{
		document.getElementById('progress').innerHTML = 'άγνωστο';
	}
}
 
function uploadComplete(evt){
    var res = $.parseJSON(evt.target.responseText);
	if(parseInt(res.status)==1){
		report_success();
	}else{
		bootbox.alert('Υπήρξε πρόβλημα με το ανέβασμα της φωτογραφίας.');
	}
	enabled = 1;
	$('#slbl').html('Αναφορά');
}

function uploadFailed(evt) {
    bootbox.alert('Υπήρξε πρόβλημα με το ανέβασμα της φωτογραφίας.');
}

function uploadCanceled(evt) {
    bootbox.alert('Το ανέβασμα της φωτογραφίας ακυρώθηκε.');
}

/*------ end image upload functions ------*/



function report_success(){
	$("#step4").hide();
	$('.captcha_txt').val('');
	$('.message').val('');
	bootbox.dialog({
		title: 'Επιτυχία αποστολής',
		message: 'Η αναφορά σας στάλθηκε με επιτυχία στον αρμόδιο φορέα. Ο χρόνος που θα διαμεσολαβήσει για την επίλυσή του εξαρτάται από τη σημαντικότητα του προβλήματος.',
		buttons: {
			success: {
				label: "Τέλος",
				className: "btn-danger",
				callback: function(){
					window.location.reload();
				}
			}
		}
	});
}

function geo_success_callback(p){
    lat = p.coords.latitude;
	lng = p.coords.longitude;
	getAddress();
}

function geo_error_callback(p){
	bootbox.hideAll();
    bootbox.alert("Δυστυχώς η θέση σας δεν εντοπίστηκε. Κάντε αναζήτηση ή μετακινήστε τον δείκτη του χάρτη στην κατάλληλη θέση.");
}
	
function getLocation(){
	$(".location_wait").fadeIn(1000);
	bootbox.dialog({
		title: 'Καλώς ορίσατε',
		message: 'To CityReport είναι μια εφαρμογή αναφοράς προβλημάτων στον δήμο. Εντοπίστε τη θέση σας αυτόματα ή με αναζήτηση, και με 3 απλά βήματα στείλτε μια αναφορά στον αρμόδιο φορέα για να επιδιορθώσει το πρόβλημα.',
		buttons: {
			success: {
				label: "Έναρξη",
				className: "btn-success",
				callback: function(){
					bootbox.alert("Παρακαλώ επιτρέψτε στο CityReport να αποκτήσει πρόσβαση στη θέση σας. Αν είστε από κινητή συσκευή ενεργοποιήστε πρώτα την πρόσβαση τοποθεσίας.");
					if(geoPosition.init()){ // Geolocation Initialisation
						geoPosition.getCurrentPosition(geo_success_callback,geo_error_callback,{enableHighAccuracy:true});
					}else{
						bootbox.hideAll();
						bootbox.alert("Η συσκευή σας δεν υποστηρίζει εντοπισμό θέσης. Κάντε αναζήτηση ή μετακινήστε τον δείκτη του χάρτη στην κατάλληλη θέση.");
					}
				}
			}
		}
	});
}
	
function getAddress(){ //get near street address...
	$(".location_msg").html("Εύρεση κοντινής διεύθυνσης...");
	$.ajax({
		method: "GET",
		dataType: "json",
		url: "http://maps.google.com/maps/api/geocode/json?latlng="+lat+","+lng+"&sensor=false&region=gr",
		success: function(res){
			if(res.results.length>0){
				var city = res.results[1].address_components[4].short_name;
				if(city!='Θεσσαλονίκη' && city!='Thessaloniki'){
					geo_error_callback();
					address = 'Thessaloniki, Greece';
					lat = 40.6171048;
					lng = 22.9594983;
					show_map(10);
				}else{
					address = res.results[0].formatted_address;
					bootbox.hideAll();
					bootbox.alert("Η εντοπισμένη θέση σας είναι κοντά σε <b>"+address+"</b><br/><br/>Σε περίπτωση που δεν είναι σωστή μετακινήστε τον δείκτη του χάρτη στην κατάλληλη θέση ή κάντε αναζήτηση.");
					show_map(17);
				}
			}else{
				if(address=='') address = 'Άγνωστο';
				bootbox.hideAll();
				bootbox.alert("Η θέση σας προσδιορίστηκε.<br/><br/>Σε περίπτωση που δεν είναι σωστή μετακινήστε τον δείκτη του χάρτη στην κατάλληλη θέση ή κάντε αναζήτηση.");
				show_map(17);
			}
		},
		error: function(){
			if(address=='') address = 'Άγνωστο';
			bootbox.hideAll();
			bootbox.alert("Η θέση σας προσδιορίστηκε.<br/><br/>Σε περίπτωση που δεν είναι σωστή μετακινήστε τον δείκτη του χάρτη στην κατάλληλη θέση ή κάντε αναζήτηση.");
			show_map(17);
		}
	});
}

function show_map(map_zoom){
	$(".map").locationpicker({
		location: {latitude: lat, longitude: lng},
		locationName: address,
		radius: 0,
		zoom: map_zoom,
		scrollwheel: true,
		onchanged: function(currentLocation, radius, isMarkerDropped){
			lat = currentLocation.latitude;
			lng = currentLocation.longitude;
			var addressComponents = $(this).locationpicker('map').location.addressComponents;
			if($(this).locationpicker('map').location.formattedAddress) address = $(this).locationpicker('map').location.formattedAddress;
			else address = addressComponents.addressLine1;
		},
		inputBinding: {
			locationNameInput: $('.search_txt')
		},
		enableAutocomplete: true
	});
	$(".location_wait").hide();
	$(".options").show();
	$(".map").css({'height':$(window).height()-50}).show();
}

function show_search_form(){
	if($(".search_box").css('opacity')==0){
		$(".search_box").css({'opacity':1});
		$(".search_txt").focus();
	}else{
		$(".search_box").css({'opacity':0});
	}
}

function show_issues(){
	$(".map").hide();
	$(".search").hide();
	$("#step1").fadeIn(200);
	change_button('Επόμενο','show_msg_form();','-chevron-right');
	bootbox.alert("Παρακαλώ επιλέξτε έναν τύπο προβλήματος.");
}

function show_msg_form(){
	if(issue_id==0){
		bootbox.alert("Παρακαλώ επιλέξτε έναν τύπο προβλήματος.");
		return;
	}
	$("#step1").hide();
	$("#step2").fadeIn(200,function(){$(".message").elastic();});
	change_button('Επόμενο','show_pic_form();','-chevron-right');
	bootbox.alert("Προαιρετικά αφήστε κάποια σχόλια σχετικά με το πρόβλημα.");
}

function show_pic_form(){
	$("#step2").hide();
	$("#step3").fadeIn(200);
	change_button('Επόμενο','show_captcha_form();','-chevron-right');
	bootbox.alert("Προαιρετικά τραβήξτε μια φωτογραφία από τον τόπο του προβλήματος.");
}

function show_captcha_form(){
	$("#step3").hide();
	$("#step4").fadeIn(200);
	change_button('Αναφορά','report();','-chevron-right');
	bootbox.alert("Εισάγετε το κείμενο που βλέπετε στην εικόνα για επιβεβαίωση.");
}

function change_captcha(){
	$('.captcha_img').attr('src','captcha?'+Math.random());
	$('.captcha_txt').val('').focus();
}

function change_button(title, click_event, icon){
	$("#next").html('<button type="button" id="report" class="btn btn-default mtext" onclick="'+click_event+'"><span id="slbl">'+title+'</span> <span class="vmiddle glyphicon glyphicon'+icon+'" aria-hidden="true"></span></button>');
}

function send_image(){
	$('#slbl').html('Uploading...');
	uploadFile();
}

function report(){
	if(!enabled) return;
	if($('#fileToUpload').val()!='') pic = 1;
	enabled = 0;
	$('#slbl').html('Περιμένετε...');
	message = encodeURIComponent($('.message').val());
	$.ajax({
		method: "POST",
		dataType: "json",
		url: "report",
		data: "lat="+lat+"&lng="+lng+"&address="+encodeURIComponent(address)+"&issue_id="+issue_id+"&message="+message+"&captcha="+$('.captcha_txt').val()+"&pic="+pic,
		success: function(res){
			if($('#fileToUpload').val()!='' && res.msg=='ok') send_image();
			else if(res.msg!='ok'){
				$('#slbl').html('Αναφορά');
				bootbox.alert(res.msg);
				enabled = 1;
			}
			else report_success();
			
			if(res.msg=='Παρακαλώ εισάγετε το σωστό κείμενο captcha.'){
				change_captcha();
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			alert(textStatus+'---'+errorThrown);
		}
	});
}
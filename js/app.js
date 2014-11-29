$(document).ready(function(){
	lat = 0; //latitude
	lng = 0; //longitute
	message = ''; //issue message
	address = ''; //street address
	issue_id = 0; //issue id
	
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
	
	$('#uploadbox').singleupload({
		action: 'upload',
		inputId: 'singleupload_input',
		btnId: 'h_upload',
		thumbId: 'uploadbox',
		onError: function(status, message) {
			if(parseInt(status)!=1){
				bootbox.alert('Υπήρξε πρόβλημα με το ανέβασμα της φωτογραφίας.');
			}else{
				report_success();
			}
		},
		onSuccess: function(url, data) {
			report_success();
		}
		/*,onProgress: function(loaded, total) {} */
	});
	
});

function report_success(){
	$('.location').hide();
	bootbox.dialog({
		title: 'Επιτυχία αποστολής',
		message: 'Η αναφορά σας στάλθηκε με επιτυχία στον αρμόδιο φορέα.',
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
			if(res.results){
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
			}
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
	$("#next").html('<button type="button" id="report" class="btn btn-default mtext" onclick="'+click_event+'">'+title+' <span class="vmiddle glyphicon glyphicon'+icon+'" aria-hidden="true"></span></button>');
}

function send_image(){
	$('.location').fadeIn(500);
	h_upload.click();
}

function report(){
	message = encodeURIComponent($('.message').val());
	$.ajax({
		method: "POST",
		dataType: "json",
		url: "report",
		data: "lat="+lat+"&lng="+lng+"&address="+encodeURIComponent(address)+"&issue_id="+issue_id+"&message="+message+"&captcha="+$('.captcha_txt').val(),
		success: function(res){
			if(res.msg!='ok') bootbox.alert(res.msg);
			else if($('#singleupload_input').val()!='') send_image();
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
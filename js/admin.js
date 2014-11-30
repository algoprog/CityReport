$(document).ready(function(){

	$(".delete_type").click(function(){
		var typeid = $(this).attr('tid');
		$.post('admin',{type_id:typeid});
		$(this).parent().fadeOut(500);
	});
	
	$(".delete_issue").click(function(){
		var issueid = $(this).attr('iid');
		$.post('admin',{issue_id:issueid});
		$(this).parent().fadeOut(500);
		var count = parseInt($('#pcount').html())-1;
		$('#pcount').html(count);
	});

});
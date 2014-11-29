/*
 * singleuploadimage - jQuery plugin for upload a image, simple and elegant.
 * 
 * Copyright (c) 2014 Langwan Luo
 *
 * Licensed under the MIT license
 *
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *     https://github.com/langwan/jquery.singleuploadimage.js
 *
 * version: 1.0.3
 */

(function ( $ ) {

    $.fn.singleupload = function(options) {

        var $this = this;
        var inputfile = null;
        
        var settings = $.extend({
            action: '#',
            onSuccess: function(url, data) {},
            onError: function(status, message){},
            OnProgress: function(loaded, total) {
                var percent = Math.round(loaded * 100 / total);
                $this.html(percent + '%');
            },
            name: 'img'
        }, options);

		$('#' + settings.inputId).bind('change', function() {
			$('#' + settings.thumbId).html('');
			$('#' + settings.thumbId).css('backgroundImage', 'none');
			var file = $('#' + settings.inputId).get(0).files[0];
			if(typeof FileReader !== "undefined"){
				var container = $('#' + settings.thumbId),
			    img = document.createElement("img"),
			    reader;
				container.html(img);
				reader = new FileReader();
				reader.onload = (function (theImg){
					return function (evt) {
						theImg.src = evt.target.result;
					};
				}(img));
				reader.readAsDataURL(file);
			}
			$('#' + settings.thumbId).find("img").css({'max-width':'99px','max-height':'99px'});
		});
		
        $('#' + settings.btnId).bind('click', function() {
            var fd = new FormData();
            fd.append($('#' + settings.inputId).attr('name'), $('#' + settings.inputId).get(0).files[0]);

            var xhr = new XMLHttpRequest();
            xhr.addEventListener("load", function(ev){
                var res = eval("(" + ev.target.responseText + ")");

                if(res.status != 0){
                    settings.onError(res.status, res.msg);
                    return;
                }
                settings.onSuccess(res.url, res.data);

            },
            false);
            xhr.upload.addEventListener("progress", function(ev) {
                settings.OnProgress(ev.loaded, ev.total);
            }, false);
            
            xhr.open("POST", settings.action, true);
            xhr.send(fd);  

        });  
       
    	return this;
    }

 
}( jQuery ));
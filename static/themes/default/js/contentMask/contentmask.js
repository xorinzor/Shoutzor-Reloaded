/*
	Copyright (c) Jorin Vermeulen
	wwww.jorinvermeulen.com
*/
(function($) {
	$.fn.toggleLoadMaskOverlay = function(options) {
		var settings = $.extend({
			text: "Loading.. Please wait."
		}, options );


		if(this.find(".loadMaskOverlay").length > 0) {
			$(".loadMaskOverlay").remove();
		} else {
			this.append('<div class="loadMaskOverlay">\
							<div class="loader">\
								<img src="/static/cms/images/loader.gif" />\
								<div class="text">'+settings.text+'</div>\
							</div>\
						</div>');

			$(".loadMaskOverlay .loader").css({
				marginLeft: 0 - ($(".loadMaskOverlay .loader").width()/2),
				marginTop: 0 - ($(".loadMaskOverlay .loader").height()/2)
			});
		}

		return true;
	};
}(jQuery));
if ( !Date.prototype.toISOString ) {
  ( function() {

    function pad(number) {
      var r = String(number);
      if ( r.length === 1 ) {
        r = '0' + r;
      }
      return r;
    }

    Date.prototype.toISOString = function() {
      return this.getUTCFullYear()
        + '-' + pad( this.getUTCMonth() + 1 )
        + '-' + pad( this.getUTCDate() )
        + 'T' + pad( this.getUTCHours() )
        + ':' + pad( this.getUTCMinutes() )
        + ':' + pad( this.getUTCSeconds() )
        + '.' + String( (this.getUTCMilliseconds()/1000).toFixed(3) ).slice( 2, 5 )
        + 'Z';
    };

  }() );
}

(function ( $ ) {

	$.fn.statusUpdate = function( options ) {
		// This is the easiest way to have default options.
		var settings = $.extend({
			nowplaying: null,
			request: null,
			profileImage: "/static/themes/dashboard/images/profile-placeholder.png",
			user: null,
			userid: "AutoDJ",
			username: "AutoDJ",
			track: null,
			artist: null,
			time: ''
		}, options );

		if(settings.request != null) {
			settings.time = settings.request.get('time_requested');
			settings.track = settings.request.get('track');
			settings.user = settings.request.get('user');
			settings.userid = (settings.user == null) ? 'AutoDJ' : settings.user.get('id');
			settings.username = (settings.user == null) ? 'AutoDJ' : settings.user.get('fullname');

			if(settings.track != null) {
				if(($(this).find(".status[data-user="+settings.userid+"]").not("[data-action='nowplaying']").length > 0) && ($(this).find(".status[data-track="+settings.track.get('id')+"]").not("[data-action='nowplaying']").length > 0)) {
					//Skip it
				} else {
					settings.time = settings.time.toISOString();

					settings.artist = "";
					settings.track.get('artist').forEach(function(artist) {
						settings.artist += artist.get('name') + ", ";
					});

					settings.artist = settings.artist.substring(0, settings.artist.length - 2); //remove last comma from names

					if(settings.artist == "") {
						settings.artist = "Unkown Artist";
					}

					settings.message = "<strong>" + settings.username + "</strong> requested <strong>" + settings.track.get('title') + "</strong> by <strong>" + settings.artist + "</strong>\
										<br />\
										<span class='timeago' title='" + settings.time + "'>" + settings.time + "</span>";

					var source   = $("#statusUpdate").html();
					var template = Handlebars.compile(source);
					var output = template(settings);

					$(output).prependTo(this).css('opacity', 0).slideDown(600).animate(
						{ opacity: 1 },
						{ queue: false, duration: 600 }
					).attr("data-user", settings.userid).attr("data-track", settings.track.get('id')).attr("data-action", 'request');

					$(this).find('.status').slice(10).remove();

					$(".timeago").timeago();
				}
			}
		} else if(settings.nowplaying != null) {
			settings.time = settings.nowplaying.get('time_played');
			settings.track = settings.nowplaying.get('track');
			settings.user = settings.nowplaying.get('user');
			settings.userid = (settings.user == null) ? 'AutoDJ' : settings.user.get('id');
			settings.username = (settings.user == null) ? 'AutoDJ' : settings.user.get('fullname');

			if(settings.track != null) {
				if(($(this).find(".status[data-user="+settings.userid+"]").not("[data-action='request']").length > 0) && ($(this).find(".status[data-track="+settings.track.get('id')+"]").not("[data-action='request']").length > 0)) {
					//Skip it
				} else {
					settings.time = settings.time.toISOString();

					settings.artist = "";
					settings.track.get('artist').forEach(function(artist) {
						settings.artist += artist.get('name') + ", ";
					});

					settings.artist = settings.artist.substring(0, settings.artist.length - 2); //remove last comma from names

					if(settings.artist == "") {
						settings.artist = "Unkown Artist";
					}

					settings.message = "Now playing <strong>" + settings.track.get('title') + "</strong> by <strong>" + settings.artist + "</strong>\
										<br />\
										<span class='timeago' title='" + settings.time + "'>" + settings.time + "</span>";

					var source   = $("#statusUpdate").html();
					var template = Handlebars.compile(source);
					var output = template(settings);

					$(output).prependTo(this).css('opacity', 0).slideDown(600).animate(
						{ opacity: 1 },
						{ queue: false, duration: 600 }
					).attr("data-user", settings.userid).attr("data-track", settings.track.get('id')).attr("data-action", 'nowplaying');

					$(this).find('.status').slice(10).remove();

					$(".timeago").timeago();
				}
			}
		}
	};

}( jQuery ));
<!DOCTYPE html>
{$THEMESECTION.BEFOREHTML}
<html>
{$THEMESECTION.HTMLBEGIN}
	<head>
		{$THEMESECTION.HEADBEGIN}
		<title>{$PAGETITLE} | {$SITETITLE}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="{$SITEDESCRIPTION}">
		<meta name="author" content="MySocialSync">
		
		<!-- Styles -->
		<link rel="stylesheet" type="text/css" href="{$SITEURL}static/themes/default/css/bootstrap/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="{$SITEURL}/static/themes/dashboard/css/fontawesome/font-awesome.min.css" />
		<link rel="stylesheet/less" type="text/css" href="{$SITEURL}static/themes/dashboard/css/ui/style.less" />
		<link rel="stylesheet/less" type="text/css" href="{$SITEURL}static/themes/dashboard/css/style.less" />
		<link rel="stylesheet" type="text/css" href="{$SITEURL}static/themes/dashboard/css/slider/slider.css" />

		<script type="text/javascript">
		window.onerror = function errorHandler(errorMsg, url, lineNumber) {
			//Make sure the crashed-alert only gets triggered for shoutzor and not for extensions or other plugins generating errors
			if(url == "{$SITEURL}static/themes/dashboard/js/ember/ember.js") {
				alert("Oops! it seems shoutzor crashed.. sorry! refresh the page please!");
			}
		}
		</script>

		<!-- LESS CSS compiler - client-side -->
		<script type="text/javascript" src="{$SITEURL}static/themes/default/js/less/less.js"></script>

		<!--[if lt IE 9]>
		  <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<script src="{$SITEURL}static/cms/js/jquery/jquery-1.10.2.js"></script>

		<script src="{$SITEURL}static/themes/dashboard/js/cookie/jquery.cookie.js"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/ui/date.js"></script>

		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/application/config.js"></script>

		<!-- bootstrap -->
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/bootstrap/bootstrap.min.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/bootstrap/bootstrap-slider.js"></script>

		<!-- load overlay -->
		<script type="text/javascript" src="{$SITEURL}static/themes/default/js/contentMask/contentmask.js"></script>

		<!-- equalizer & soundmanager -->
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/sm2/soundmanager2.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/equalizer/dsp.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/equalizer/pocket.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/equalizer/pocket.api.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/equalizer/pocket.audio.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/equalizer/pixastic.custom.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/pre3d/pre3d.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/pre3d/pre3d_path_utils.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/pre3d/pre3d_shape_utils.js"></script>

		<!-- file upload -->
		<link rel="stylesheet" type="text/css" href="{$SITEURL}static/themes/dashboard/css/fileupload.css" />
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/upload/tmpl.min.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/upload/vendor/jquery.ui.widget.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/upload/jquery.fileupload.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/upload/jquery.fileupload-process.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/upload/jquery.fileupload-validate.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/upload/jquery.fileupload-ui.js"></script>

		<!-- admin control js files -->
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/ui/knobRot-0.2.3.min.js"></script>
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/ui/timeago.js"></script>

		<!-- custom JS files -->
		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/shoutzor/custom/updateStatuses.js"></script>

		<!-- emberjs files -->
		<script src="{$SITEURL}static/themes/dashboard/js/ember/handlebars.js"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/ember/ember.js"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/ember/data.js"></script>

		<!-- bootstrap for ember -->
		<script src="{$SITEURL}static/themes/dashboard/js/bootstrapforember/bs-core.min.js" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/bootstrapforember/bs-button.min.js" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/bootstrapforember/bs-modal.min.js" type="text/javascript"></script>
		{*
		<!--
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/application/shoutzor.ejs" type="text/javascript"></script>

		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/adapter/ApplicationAdapter.ejs" type="text/javascript"></script>

		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/model/User.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/model/Artist.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/model/Album.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/model/Track.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/model/History.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/model/Queue.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/model/Search.ejs" type="text/javascript"></script>

		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/router/Map.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/router/ApplicationRoute.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/router/HomeRoute.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/router/SearchRoute.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/router/ArtistRoute.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/router/QueueRoute.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/router/UploadRoute.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/router/AlbumRoute.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/router/TrackRoute.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/router/ProfileRoute.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/router/SettingsRoute.ejs" type="text/javascript"></script>
		
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/controller/ApplicationController.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/controller/HomeController.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/controller/SearchController.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/controller/QueueController.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/controller/UploadController.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/controller/RequestTrackController.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/controller/ArtistController.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/controller/AlbumController.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/controller/ProfileController.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/controller/TrackController.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/controller/HelperControllers.ejs" type="text/javascript"></script>
		
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/view/ApplicationView.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/view/UploadView.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/view/SettingsView.ejs" type="text/javascript"></script>
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/view/AlbumContentView.ejs" type="text/javascript"></script>
		-->
		*}
		<script src="{$SITEURL}static/themes/dashboard/js/shoutzor/compiled.js" type="text/javascript"></script>

		<script type="text/javascript" src="{$SITEURL}static/themes/dashboard/js/likebar/jquery.minibar.min.js"></script>

		{$THEMESECTION.HEADEND}
	</head>
	{$THEMESECTION.BODYBEFORE}
{literal}
	<body>
			<script type="text/x-handlebars" data-template-name="application">
				<div class="row">
					<section id="leftSidebar" class="col-2">
						<nav>
							<ul id="topmenu">
								<li class="active" data-toggle="tooltip" data-original-title="Home">
									{{#link-to "home"}}
										<div class="music-icon"></div>
									{{/link-to}}
								</li>
								<li data-toggle="tooltip" data-original-title="Search">
									{{#link-to "search"}}
										<div class="search-icon"></div>
									{{/link-to}}
								</li>
								<li data-toggle="tooltip" data-original-title="Queue">
									{{#link-to "queue"}}
									<div class="list-icon"></div>
									{{/link-to}}
								</li>
								{/literal}
								{*
								<li data-toggle="tooltip" data-original-title="Chat">
									{{#link-to "chat"}}
										<div class="chat-icon"></div>
									{{/link-to}}
								</li>
								*}
								{if $PERMISSIONS.upload}
									{literal}
									<li data-toggle="tooltip" data-original-title="Upload music">
										{{#link-to "upload"}}
											<div class="upload-icon"></div>
										{{/link-to}}
									</li>
									{/literal}
								{/if}
								{literal}
							</ul>
							<ul id="bottommenu">
								{/literal}
								{if $PERMISSIONS.controlshoutzor}
									{literal}
									<li data-toggle="tooltip" data-original-title="Your Profile">
										{{#link-to "profile"}}
											<div class="profile-icon"></div>
										{{/link-to}}
									</li>
									<li data-toggle="tooltip" data-original-title="Settings">
										{{#link-to "settings"}}
											<div class="settings-icon"></div>
										{{/link-to}}
									</li>
									{/literal}
								{/if}
								{literal}
							</ul>
						</nav>
					</section>

					<section id="content" class="col-7">
						{{outlet "pageContent"}}
					</section>

					<section id="rightSidebar" class="col-3 pull-right">
						{{outlet "rightSidebarOutput"}}
					</section>
				</div>
			</script>

		<script type="text/x-handlebars" data-template-name="rightSidebarOutput">
			<div id="musicWidget">
					<div id="equalizerContainer">
						<canvas id="equalizer"></canvas>
					</div>
					<div id="player-info">
						<div id="player-info-album" class="pull-left">
							{{#if currentSong.track.album.firstObject.coverImage}}
								<img {{bind-attr src=currentSong.track.album.firstObject.coverImage}} />
							{{else}}
								<img src="/static/themes/dashboard/images/album-cover-placeholder.jpg" />
							{{/if}}
						</div>
						<div id="player-info-song" class="pull-left">
							<h3 id="player-info-song-title" class="nowplaying-title">{{#link-to 'track' currentSong.track}}{{currentSong.track.title}}{{/link-to}}</h3>
							<div>
								<span class="category">Artist:</span> 
								{{render 'Artistname' currentSong.track.artist}}
							</div>
							<div>
								<span class="category">Album:</span> 
								{{render 'Albumname' currentSong.track.album}}
							</div>
							{/literal}
							{*
							<div>
								<span class="category">Rating:</span>
								<span id="likes">50%</span> <div id="bar"></div> <span id="dislikes">50%</span>
							</div>
							*}
							{literal}
						</div>
					</div>
					<div id="player-controls">
						<ul>
							<li class="play">
								<span class="play-icon"></span>
							</li>
							<li class="progressbar">
								<div class="progress">
									<div class="progress-bar progress-bar-success"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
									</div>
								</div>
							</li>
							<li class="volume">
								<span class="volume-icon">
									<div class="menu">
										<input type="text" class="slider">
									</div>
								</span>
							</li>
							<li class="settings">
								<span class="settings-icon" {{action "getsettings" target="controllers.playersettings" bubbles=false}}></span>
							</li>
							{/literal}
							{*
							<li class="like">
								<span class="like-icon"></span>
							</li>
							<li class="dislike">
								<span class="dislike-icon"></span>
							</li>
							*}
							{literal}
						</ul>
					</div>
					<div id="audio-container">
						<audio controls="true" type="audio/ogg" id="audio" src="http://www.shoutzor.nl:8000/stream"></audio>
					</div>

				</div>

				{/literal}
				{*
				<div style="color: #FFF;">
					<h3>Shoutzor settings</h3>
					<ul>
						<li>The indexer runs every 5 minutes, please be patient</li>
						<li>A track can only be requested every 40 minutes</li>
						<li>An artist can only be requested every 20 minutes</li>
						<li>An album can only be requested every 20 minutes</li>
						<li>Amount of max requests per x amount of time not implemented yet, subject to change.</li>
					</ul>
				</div>
				*}
				{literal}

				<div id="updateContainer"></div>
		</script>

		<script id="statusUpdate" type="text/x-handlebars-template">
			<div class="status">
				<div class="user">
					<div class="profileimage">
						<img src="{{profileImage}}" />
						<div class="smallarrow"></div>
					</div>
				</div>
				<div class="message">
					{{{message}}}
				</div>
				<div class="clearfix"></div>
			</div>
		</script>
{/literal}
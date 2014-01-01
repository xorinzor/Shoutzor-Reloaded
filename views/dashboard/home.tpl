{literal}
	<script type="text/x-handlebars" data-template-name="HomePageContent">
		<div class="row">
			<div class="col-12">
				<div class="section">
					<table>
						<tr>
							<td width="190px"><h3>Now Playing:</h3></td>
							<td><h3>{{#if currentSong}}{{currentSong.track.title}} - {{render 'Artistname' currentSong.track.artist}}{{else}}Loading..{{/if}}</h3></td>
						</tr>
						<tr>
							<td>Next up:</td>
							<td>{{#if nextSong}}{{nextSong.track.title}} - {{render 'Artistname' nextSong.track.artist}}{{else}}Loading..{{/if}}</td>
						</tr>
						<tr>
							<td>Previous:</td>
							<td>{{#if previousSong}}{{previousSong.track.title}} - {{render 'Artistname' previousSong.track.artist}}{{else}}Loading..{{/if}}</td>
						</tr>
					</table>
				</div>

				<div class="section">
					<h3 id="previouslyplayed">History</h3>

					<table id="songhistory" class="table table-condensed table-striped">
						<thead>
							<th width="30%">Title</th>
							<th width="20%">Artist</th>
							<th width="20%">Album</th>
							<th width="15%">Requested by</th>
							<th width="15%">Time played</th>
						</thead>
						<tbody>
							{{#each history}}
								<tr>
									<td>{{track.title}}</td>
									<td>{{render 'Artistname' track.artist}}</td>
									<td>{{render 'Albumname' track.album}}</td>
									<td>{{#if user}}{{user.fullname}}{{else}}AutoDJ{{/if}}</td>
									<td>{{print_time}}</td>
								</tr>
							{{else}}
								<tr>
									<td colspan="5">
										<div class="loading">
											<div class="circle"></div>
											<div class="circle1"></div>
											Track history loading..
										</div>
									</td>
								</tr>
							{{/each}}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</script>

	<script type="text/x-handlebars" data-template-name="SearchPageContent">
		<div class="row">
			<div class="col-12">
				<div class="section">
					<br />

					{{input type="text" value=search action="query" class="form-control" placeholder="Search for.."}}

				</div>
				<div class="section" id="searchResults">
					{{#if query}}
						{{#if searching}}
							<h3>Search Results</h3>
							<div class="loading">
								<div class="circle"></div>
								<div class="circle1"></div>
								Searching tracks..
							</div>
						{{else}}
							{{#each result in model}}
								<h3>Search Results | Showing {{result.tracks.length}} track result(s) for "{{search}}"</h3>
								<table class="table table-condensed table-striped">
									<thead>
										<th width="30%">Title</th>
										<th width="30%">Artist</th>
										<th width="25%">Album</th>
										<th width="15%">Length</th>
									</thead>
									<tbody>
										{{#each track in result.tracks}}
											<tr>
												<td><a href="#" data-type="requestsong" {{action "requestTrack" track target="controllers.requesttrack" bubbles=false}} {{bind-attr data-trackid=track.id}}>{{track.title}}</a></td>
												<td>{{render 'Artistname' track.artist}}</td>
												<td>{{render 'Albumname' track.album}}</td>
												<td>{{track.convertedLength}}</td>
											</tr>
										{{else}}
											<tr>
												<td colspan="4">No matching track titles found</td>
											</tr>
										{{/each}}
									</tbody>
								</table>
							{{/each}}
						{{/if}}
					{{else}}
						<h3>Please enter a search query</h3>
					{{/if}}
					</div>
				</div>
			</div>
		</div>
	</script>

	<script type="text/x-handlebars" data-template-name="QueuePageContent">
		<div class="row" id="queuePageRow">
			<div class="col-12">
				<div class="section">
					<h3>Queue</h3>
					<table class="table table-condensed table-striped">
						<thead>
							<th width="30%">Title</th>
							<th width="20%">Artist</th>
							<th width="20%">Album</th>
							<th width="15%">Requested by</th>
						</thead>
						<tbody>
							{{#each model}}
								{{#if track.title}}
									<tr>
										<td>{{track.title}}</td>
										<td>{{render 'Artistname' track.artist}}</td>
										<td>{{render 'Albumname' track.album}}</td>
										<td>{{#if user}}{{user.fullname}}{{else}}AutoDJ{{/if}}</td>
									</tr>
								{{/if}}
							{{else}}
								<tr>
									<td colspan="5">
										<div class="loading">
											<div class="circle"></div>
											<div class="circle1"></div>
											Track queue loading..
										</div>
									</td>
								</tr>
							{{/each}}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</script>

	<script type="text/x-handlebars" data-template-name="ChatPageContent">
	</script>

	<script type="text/x-handlebars" data-template-name="UploadPageContent">
		<div class="row" id="albumInfo">
			<div class="col-12">
				<div class="section">
					<h3>Upload tracks</h3>
					<p>To upload your music, drag and drop your audio files in this window or click the "add files" button

					<div class="well">
						<p><strong>Important:</strong> Unless the filetype is not allowed or the file is too large NO errors will be shown! so read these rules well!</p>
						<ul>
							<li>the <strong>max filesize</strong> is <strong>30MB</strong> | <strong>allowed filetypes</strong> are: <strong>mp3</strong>, <strong>ogg</strong> and <strong>flac</strong></li>
							<li>Tracks <strong>cannot be longer then 6 minutes</strong>, any uploaded tracks longer then 6 minutes will be discarded without showing an error</li>
							<li>Before uploading tracks please make sure the Title and Artist in the file properties are set correctly, otherwise your track will show up as "Untitled" by "Unknown Artist"</li>
						</ul>
					</div>

					<!-- The file upload form used as target for the file upload widget -->
					<form id="fileupload" action="http://www.shoutzor.nl/api/upload/" method="POST" enctype="multipart/form-data">

						<div id="fileupload-controls" style="margin-bottom:5px;">
							<!-- The fileinput-button span is used to style the file input field as button -->
							<span class="btn btn-success fileinput-button">
								<span>Add files...</span>
								<input type="file" name="audiofile">
							</span>
						</div>

						<!-- The global file processing state -->
						<span class="fileupload-process"></span>

						<div class="fileupload-progress fade">
							<!-- The global progress bar -->
							<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
								<div class="progress-bar progress-bar-success" style="width:0%;"></div>
							</div>
							<!-- The extended global progress state -->
							<div class="progress-extended">&nbsp;</div>
						</div>

						<!-- The table listing the files available for upload/download -->
						<table role="presentation" class="table table-striped">
							<thead>
								<tr>
									<th width="30%">Filename</th>
									<th width="10%">Filesize</th>
									<th width="60%">Progress</th>
								</tr>
							</thead>
							<tbody class="files"></tbody>
						</table>
					</form>
				</div>
			</div>
		</div>
	</script>	

	<!-- The template to display files available for upload -->
	<script id="template-upload" type="text/x-tmpl">
	{% for (var i=0, file; file=o.files[i]; i++) { %}
	    <tr class="template-upload fade">
	        <td>
	            <p class="name">{%=file.name%}</p>
	            <strong class="error text-danger"></strong>
	        </td>
	        <td>
	        	<p class="size">Processing...</p>
	        </td>
	        <td>
	            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
	        </td>
	    </tr>
	{% } %}
	</script>

	<!-- The template to display files available for download -->
	<script id="template-download" type="text/x-tmpl">
	{% for (var i=0, file; file=o.files[i]; i++) { %}
	    <tr class="template-download fade"></tr>
	{% } %}
	</script>

	<script type="text/x-handlebars" data-template-name="ProfilePageContent">
		<div class="row">
			<div class="col-12">
				<div class="section">
					<h3>Profile</h3>
					<p>Not implemented yet.</p>
				</div>
			</div>
		</div>
	</script>

	<script type="text/x-handlebars" data-template-name="SettingsPageContent">
		<div class="row" style="min-width: 1400px !important;">
			<div class="col-sm-12">
				<div class="section">
					<h3>LiquidSoap Controls</h3>

					<div class="row" id="liquidsoapStatus">
						<div class="col-sm-10">
							<div id="mainService" class="alert alert-warning">Loading Main LiquidSoap service status..</div>
							<div id="shoutzorService" class="alert alert-warning">Loading Shoutzor LiquidSoap service status..</div>
						</div>
						<div class="col-sm-2">
							<p><button type="button" id="toggleMainService" class="btn btn-info">Please wait..</button></p>
							<p><button type="button" id="toggleShoutzorService" class="btn btn-info">Please wait..</button></p>
							<p><button type="button" id="nextTrack" class="btn btn-info">Next Track</button></p>
							<p><button type="button" id="cpForward" class="btn btn-info">Correct playlist forward</button></p>
							<p><button type="button" id="cpBackward" class="btn btn-info">Correct playlist backward</button></p>
						</div>
					</div>
				</div>
				<div class="section">
					<h3>Shoutzor System Controls</h3>
					<div class="row">
						<div class="col-sm-2 text-center">
							<h4>Master Volume: <span id="currentVolume">100</span>%</h4>
							<div class="knob-container-large">
								<!-- Volume control -->
								<div class="widget-knob widget-volume">
									<input type="text" value="100"  autocomplete="off" id="volume" />
								</div>
							</div>
						</div>

						{/literal}
						{*
						<div class="col-sm-9 col-sm-offset-1 text-center">
							<h4>Finetuning</h4>

							<div class="col-sm-1">
								<div class="knob-container">
									<p>Balance</p>
									<input type="text" value="0" class="knob-values knob-swarm" />
								</div>
							</div>

							<div class="col-sm-2">
								<div class="knob-swarm-container">
									<div class="knob-container">		
										<p>Treble</p>
										<input type="text" value="0" class="knob-values knob-swarm" />
									</div>
									<div class="knob-container">		
										<p>Bass</p>
										<input type="text" value="0" class="knob-values knob-swarm" />
									</div>
									<br style="clear: both" />
								</div>	
							</div>
						</div>
						*}
						{literal}
					</div>
				</div>
			</div>
		</div>
	</script>

	<script type="text/x-handlebars" data-template-name="AlbumPageContent">
		<div class="row" id="albumInfo">
			<div class="col-12">
				<div class="section">
					<div class="album">
						<div class="albumImage pull-left">
							{{#if album.coverImage}}
								<img {{bind-attr src=album.coverImage}} class="img-rounded albumCover" />
							{{else}}
								<img src="/static/themes/dashboard/images/album-cover-placeholder.jpg" class="img-rounded albumCover" />
							{{/if}}
						</div>
						<div class="albumContent pull-left">
							<h3>Album</h3>
							<h1>{{album.title}}</h1>
							<p>By: {{#link-to 'artist' artist}}<strong>{{artist.name}}</strong>{{/link-to}}</p>

							{{render "albumContent" album}}
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</script>

	<script type="text/x-handlebars" data-template-name="ArtistPageContent">
		<div class="row" id="artistInfo">
			<div class="col-12">
				<div id="artistHeader">
					<div id="artistImage" class="pull-left">
						{{#if artist.profileImage}}
							<img {{bind-attr src=artist.profileImage}} class="img-rounded" />
						{{else}}
							<img src="/static/themes/dashboard/images/artist-placeholder.png" class="img-rounded" />
						{{/if}}
					</div>

					<div id="artistName" class="pull-left">
						<h3>Artist</h3>
						<h1>{{artist.name}}</h1>
					</div>
				</div>

				<hr />

				<div class="section">
					<h3>Populair Tracks</h3>
					<p>Unable to get track listing</p>
				</div>

				<div class="section">
					<h3>Albums</h3>
					{{#each item in album}}
						<div class="album col-lg-12">
							<div class="albumImage col-lg-2">
								{{#link-to 'album' item}}
									{{#if item.coverImage}}
										<img {{bind-attr src=item.coverImage}} class="img-rounded albumCover" />
									{{else}}
										<img src="/static/themes/dashboard/images/album-cover-placeholder.jpg" class="img-rounded albumCover" />
									{{/if}}
								{{/link-to}}
							</div>
							<div class="albumContent col-lg-9">
								<h1 class="albumTitle">{{#link-to 'album' item}}{{item.title}}{{/link-to}}</h1>
								{{render "albumContent" item}}
							</div>
							<div class="clearfix"></div>
						</div>
					{{else}}
						<p>This artist has no albums</p>
					{{/each}}
				</div>
			</div>
		</div>
	</script>

	<script type="text/x-handlebars" data-template-name="Artistname">
		{{#each artist in controller}}<span class="commaseparated">{{#link-to 'artist' artist}}{{artist.name}}{{/link-to}}</span>{{else}}Unknown Artist{{/each}}
	</script>

	<script type="text/x-handlebars" data-template-name="Artistnamenolink">
		{{#each artist in controller}}<span class="commaseparated">{{artist.name}}</span>{{else}}Unknown Artist{{/each}}
	</script>

	<script type="text/x-handlebars" data-template-name="Albumname">
		{{#each album in controller}}<span class="commaseparated">{{#link-to 'album' album}}{{album.title}}{{/link-to}}</span>{{else}}Unknown Album{{/each}}
	</script>

	<script type="text/x-handlebars" data-template-name='albumContent'>
		<table class="table table-condensed table-striped trackList">
			<thead>
				<th width="60%">Title</th>
				<th width="30%">Artist</th>
				<th width="10%">Length</th>
			</thead>
			<tbody>
				{{#each track in tracks}}
					<tr>
						<td><a href="#" data-type="requestsong" {{action "requestTrack" track target="controllers.requesttrack" bubbles=false}} {{bind-attr data-trackid=track.id}}>{{track.title}}</a></td>
						<td>{{render 'Artistname' track.artist}}</td>
						<td>{{track.convertedLength}}</td>
					</tr>
				{{else}}
					<tr>
						<td colspan="5">
							<div class="loading">
								<div class="circle"></div>
								<div class="circle1"></div>
								loading tracks..
							</div>
						</td>
					</tr>
				{{/each}}
			</tbody>
		</table>
	</script>

	<script type="text/x-handlebars" data-template-name='Requesttrack'>
		<p>Request <strong>'{{this.targetObject.track.title}}'</strong> by: <strong>{{render 'Artistnamenolink' this.targetObject.track.artist}}</strong>?</p>
	</script>

	<script type="text/x-handlebars" data-template-name='Requesttrackloading'>
		<p>Loading..</p>
	</script>

	<script type="text/x-handlebars" data-template-name='playersettings'>
		<h2>Select Visualizer</h2>
		<ul id="equalizerVisualisations">
			<li data-script="/static/themes/dashboard/js/equalizer/presets/void.js">Void</li>
			<li data-script="/static/themes/dashboard/js/equalizer/presets/tunnel.js">Tunnel</li>
			<li data-script="/static/themes/dashboard/js/equalizer/presets/points.js">Points</li>
			<li data-script="/static/themes/dashboard/js/equalizer/presets/circular.js">Circular</li>
			<li data-script="/static/themes/dashboard/js/equalizer/presets/cranked.js">Cranked</li>
			<li data-script="/static/themes/dashboard/js/equalizer/presets/colormunch.js">Color Munch</li>
			<li data-script="/static/themes/dashboard/js/equalizer/presets/threesixty.js">Three Sixty</li>
			<li data-script="/static/themes/dashboard/js/equalizer/presets/pre3d_circle.js">3D circle</li>
			<li data-script="/static/themes/dashboard/js/equalizer/presets/pre3d_cube.js">3D cube</li>
			<li data-script="/static/themes/dashboard/js/equalizer/presets/empty.js" class="empty">No visualizer</li>
		</ul>
	</script>

<script type="text/javascript">
/*
function destroyLessCache(pathToCss) { // e.g. '/css/' or '/stylesheets/'
 
  var host = window.location.host;
  var protocol = window.location.protocol;
  var keyPrefix = protocol + '//' + host + pathToCss;
  
  for (var key in window.localStorage) {
	  delete window.localStorage[key];
  }
}

destroyLessCache("/");
*/
</script>
{/literal}
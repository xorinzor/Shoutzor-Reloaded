<style type="text/css">
	@media screen and (-webkit-min-device-pixel-ratio:0) {
	    body[data-page="login"] #title {
	    	top: 0px;
	    }
	}
</style>

<div id="logo">
	<div id="title">Shoutz0r</div>
	<div id="subtitle">Reloaded</div>
</div>
<div id="loginscreen">
	<div id="result"></div>

	<form action="{$SITEURL}login" method="POST" id="loginform" class="form-horizontal" role="form">
		<fieldset>
			<legend>Have an account? Login!</legend>
			<div class="form-group">
				<label class="col-lg-2 control-label" for="username">Username</label>
				<div class="col-lg-10">
					<input class="form-control" type="text" id="username" name="username" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-2 control-label" for="password">Password</label>
				<div class="col-lg-10">
					<input class="form-control" type="password" id="password" name="password" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-2"></div>
				<div class="col-lg-4">
					<input class="form-control" type="submit" class="btn" value="Login" />
				</div>
			</div>
		</fieldset>
	</form>

	<form action="#" method="POST" id="registerform" class="form-horizontal">
		<fieldset>
			<legend>No account yet?</legend>
			<p>Ask one of the Class Crew members how you can get an account for Shoutzor</p>
		</fieldset>
	</form>
</div>
<div id="copyright">Shoutz0r Reloaded &copy; Jorin Vermeulen</div>

<script type="text/javascript">
$(function() {
	$("form").submit(function(e) {
		e.preventDefault();

		

		if($(this).is("#registerform")) {
			return false;
		} else {
			$("body").toggleLoadMaskOverlay();

			$.post("{$SITEURL}login/", {
				username: $("#loginform input[name=username]").val(),
				password: $("#loginform input[name=password]").val()
			}, function(result) {
				$("body").toggleLoadMaskOverlay();

				if(result.result === true) {
					$("#result").html('<div class="alert alert-success"><strong>Login succes!</strong> You will now be redirected</div>');
					window.location.replace("{$SITEURL}dashboard/");
				} else {
					$("#result").html('<div class="alert alert-danger"><strong>Login incorrect!</strong> Check your username/password and try again</div>');
				}
			}, "json");
		}
	});
});
</script>
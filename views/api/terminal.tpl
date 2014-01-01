<pre>{$SESSIONDATA}</pre>

<hr />

<form class="form-horizontal">
	<fieldset>
		<div id="params">
			<div class="control-group">
				<label class="control-label">Method</label>
				<div class="controls">
					<input type="text" name="method" rel="method" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Network</label>
				<div class="controls">
					<input type="text" name="network" rel="network" />
				</div>
			</div>
		</div>
		
		<div id="newfield">
			<div class="control-group">
                <div class="controls">
					<input class="span2" type="text" value="name" />
					<input class="span4" type="text" value="value" />
					<div class="btn">Add parameter</div>
                </div>
			</div>
		</div>
		
		<div class="form-actions">
			<div class="btn primary" id="submitbutton">Create API request</div>
			<button type="reset" class="btn">Reset</button>
		</div>
	</fieldset>
</form>

<hr />

<h4>Call results</h4>
<div id="result"></div>

<script type="text/javascript">
function getTime() {
	var currentTime = new Date();
	
	var minutes = currentTime.getMinutes();
	if (minutes < 10){
		minutes = "0" + minutes;
	}
	
	var seconds = currentTime.getSeconds();
	if (seconds < 10){
		seconds = "0" + seconds;
	}

	var time = "["+currentTime.getHours()+":"+minutes+":"+seconds+"]";
	
	return time;
}

$("#result").append(getTime() + " Enter the correct parameters and press the button to show the result");

$(function(){
	$("#newfield .btn").click(function(){
		var name = $("#newfield .span2").val();
		var value = $("#newfield .span4").val();
		
		if($("#params input[name='"+name+"']").length == 0) {
			$("#params").append("<div class='control-group'><label class='control-label'>"+name+"</label><div class='controls'><input type='text' name='"+name+"' value='"+value+"' /></div></div>");
			
			$("#newfield .span2").val("name");
			$("#newfield .span4").val("value");
		} else {
			$("#result").prepend(getTime() + " Given name already exists for a parameter<br />");
		}
		
		return false;
	});
});

$(function(){
	$("#submitbutton").on('click', function(event) {
		if(!$("#submitbutton").hasClass("disabled")) {
			var params = new Object();
			
			$("#submitbutton").addClass("disabled");
			$("#submitbutton").html("Loading API request..");
		
			$("#params input").each(function(){
				params[$(this).attr("name")] = $(this).val();
			});
			
			$.post("{$SITEURL}api/", params, function(data) {
				$("#result").prepend(getTime() + " "+data+ "<br />");
				$("#submitbutton").removeClass("disabled");
				$("#submitbutton").html("API request succeeded");
			})
			.error(function(){
				$("#result").prepend(getTime() + " An error occurred during the POST request<br />");
				$("#submitbutton").removeClass("disabled");
				$("#submitbutton").html("API request failed");
			});
			
			setTimeout(function(){
				$("#submitbutton").html("Create API request");
			}, 4000);
		}
		
		return false;
	});
});
</script>
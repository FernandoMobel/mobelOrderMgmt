<?php include 'includes/nav.php';?>
<script>
function submit(){
	//console.log($('#sql').val());
	myData = { mode:"run", sql:$('#sql').val()};
		$.post("testA.php",
			myData, 
			   function(data, status, jqXHR) {	
					//console.log(jqXHR['responseText']);
					$("#tb").empty();
					$("#tb").append(data);
				});	
	
}
</script>
<div class="content-fluid">
	<textarea id="sql" name="sql" rows="1" cols="200">
	</textarea>
	<button onclick="submit()">Run</button>
</div>
<div id="tb"></div>

<?php include 'includes/foot.php';?>
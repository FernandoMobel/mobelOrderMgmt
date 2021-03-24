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

function execute(){
	//console.log($('#sql').val());
	myData = { mode:"exec", sql:$('#sql').val()};
		$.post("testA.php",
			myData, 
			   function(data, status, jqXHR) {	
					console.log(jqXHR['responseText']);					
				});	
	
}
</script>
<?php 
if($_SESSION["userid"]==11){
    ?>
	<div class="content-fluid">
		<textarea id="sql" name="sql" rows="1" cols="200">
		</textarea>
		<button onclick="submit()">Run</button>
		<button onclick="execute()">Execute</button>
	</div>
	<div id="tb"></div>

<?php 
	include 'includes/foot.php';	
}else{
    include 'includes/foot.php';
    include '403.php';
    exit();
}
?>


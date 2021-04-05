<?php
/* Static script address For local environment */
$local = "";
if(strcmp($_SERVER['SERVER_NAME'],"localhost")==0 || strcmp($_SERVER['SERVER_NAME'],"192.168.16.199")==0){
	$local = "/mobelOrderMgmt";
}

echo "<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME'].$local."/js/jquery340.js\"></script>";
echo "<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME'].$local."/js/jqueryui112/jquery-ui.min.js\"></script>";
echo "<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME'].$local."/js/MDB/js/popper.min.js\"></script>";
echo "<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME'].$local."/js/bootstrap431/js/bootstrap.bundle.min.js\"></script>";
echo "<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME'].$local."/js/bootstrap431/js/bootstrap.min.js\"></script>";
echo "<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME'].$local."/js/MDB/js/addons/datatables.min.js\"></script>";
echo "<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME'].$local."/js/bootstrapselect1139/dist/js/bootstrap-select.min.js\"></script>";
echo "<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME'].$local."/js/bootstrap-select/dist/js/bootstrap-multiselect.min.js\"></script>";
echo "<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME'].$local."/js/MDB/js/mdb.min.js\"></script>";
echo "<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME'].$local."/js/Calendar/main.js\"></script>";
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

<script type="text/javascript">
	$('#logo').height("45px");
</script>

<script defer>
	$(document).ready(function () {
		$('.dataTables_length').addClass('bs-select');
		$('[data-toggle="tooltip"]').tooltip();
	});

	$('.datepicker').on('click', function(e) {
	   e.preventDefault();
	   $(this).attr("autocomplete", "off");
	});

	$("body").on('click','.toggle-password',function(){
		$(this).toggleClass("fa-eye fa-eye-slash");
		
		var input = $("#inputPassword");
		
		if (input.prop("type") === "password") {
			input.prop("type", "text");
		} else {
			input.prop("type", "password");
		}
	});
	
</script>
</body>
</html>
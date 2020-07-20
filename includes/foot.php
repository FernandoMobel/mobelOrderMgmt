
<script type="text/javascript" src="js/jquery340.js"></script>
<script type="text/javascript"  src="js/MDB/js/addons/datatables.min.js"></script>
<script type="text/javascript"  src="js/MDB/js/popper.min.js"></script>
<script type="text/javascript"  src="js/bootstrap431/js/bootstrap.min.js"></script>
<script type="text/javascript"  src="js/bootstrapselect1139/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="js/MDB/js/mdb.min.js"></script>
<script type="text/javascript"  src="js/jqueryui112/jquery-ui.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>



</body>

<script type="text/javascript">
	$('#logo').height($('#logo').height()*0.5);
</script>

<script defer>
$(document).ready(function () {
	$('table').DataTable({
		//$('#selectedColumn').DataTable({
	"order": [[ 2, "desc" ]]
	});
	$('.dataTables_length').addClass('bs-select');
	});

$('.datepicker').on('click', function(e) {
	   e.preventDefault();
	   $(this).attr("autocomplete", "off");
	});
	
	
	
</script>
</html>
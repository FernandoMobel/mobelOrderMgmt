<?php
$sql ="select * from settings";
$result = opendb($sql);
while ( $row = $result->fetch_assoc())  {
	$sql2 = "update settings set currentLeadtime = '".calculateDays($row['autoLeadDate'])."'";
	opendb2($sql2);
}
?>
<script>
function getDateStatus($date){
	myData = { mode: "getDateStatus", date: $date};
	$.post("EmployeeMenuSettings.php",
			myData, 
		       function(data, status, jqXHR) {
            		$("#calSelect").val(jqXHR["responseText"]);
		        });
}

function updateCalDay($status){
	myData = { mode: "updateCalDay", date: $("#calDate").val(), newStatus: $status};
	$.post("EmployeeMenuSettings.php",
			myData, 
		       function(data, status, jqXHR) {            		
		        });
}
</script>
<div class="navbar navbar-expand-sm bg-light navbar-light">
	<div class="col-sm-12 col-md-11 col-lg-11 mx-auto">
		<div class="row">
			<input type="hidden" id="filtersInd" name="filtersInd" value="0" >
			<?php
			opendb("select * from settings");			
			if($GLOBALS['$result']->num_rows > 0){
				foreach ($GLOBALS['$result'] as $row) {
					?>
					<div class="col-sm-6 col-md-6 col-lg-6 date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
					<label for="Mobel Lead Time">Mobel Lead Time: (yyyy-mm-dd)</label>
					<?php
					echo "<input disabled type=\"text\"  onchange=\"saveSettings();\" class=\"form-control noresize\"  value=\"". substr($row['currentLeadtime'],0,10) ."\" id=\"currentLeadtime\">";
					?>
							<br/>
							<div class="input-group-addon">
								<span class="glyphicon glyphicon-th"></span>
							</div>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-2 offset-4">
					<label for="automaticPeriod">Auto set Lead Time (business days)</label>
					<?php
					echo "<input type=\"number\"  min=\"1\" onchange=\"saveSettings(this.value);\" class=\"form-control noresize\"  value=\"" . $row['autoLeadDate']."\" id=\"automaticPeriod\">";
					echo "</div>";
				}
			}
			?>
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="row">
		<div class="col-6 mx-auto">
			<div class="card card-signin my-3">
				<div class="card-header">
					<h5>MOBEL Calendar</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-3 mx-auto">
							<?php 
							echo "<input id=\"calDate\" type=\"text\" maxlength=\"10\" data-provide=\"datepicker\" data-date-format=\"yyyy-mm-dd\" onchange=\"getDateStatus(this.value);\" class=\"form-control datepicker\"  value=\"". date("Y/m/d") ."\" id=\"dateRequired\">";
							?>
						</div>
						<div class="col-3 mx-auto">
							<select id="calSelect" onchange="updateCalDay(this.value)" class="custom-select">
								<option value="0">Disabled</option>
								<option value="1">Enabled</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include '../includes/foot.php';?>
<script>
$(document).ready(function() {
	getDateStatus(<?php echo "'".date("Y/m/d")."'"?>);
});
</script>
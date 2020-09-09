<?php include 'includes/nav.php';?>
<?php include 'includes/db.php';?>
<script>

function clearAll() {
    $('#orderCode').val("");
	$('#poTag').val("");
	$('#nameDesigner').val("");
	$('#custType').val("");
	$('#description').val("");
	$('#receivedDt').val("");
	$('#receivedByProdDt').val("");
	$('#schedComplDt').val("");
	$('#completedDt').val("");
	$('#detailed').prop('checked',false);
  }
  
function  submitJob(){
	//Getting Order values
	var orderCode = document.getElementById("orderCode").value; //Mandatory
	var poTag = document.getElementById("poTag").value;
	var nameDesigner = document.getElementById("nameDesigner").value;
	var custType = document.getElementById("custType").value;
	var description = document.getElementById("description").value;
	var receivedDt = document.getElementById("receivedDt").value; //Mandatory
	var receivedByProdDt = document.getElementById("receivedByProdDt").value;
	var schedComplDt = document.getElementById("schedComplDt").value;
	var completedDt = document.getElementById("completedDt").value;
	var detailed = document.getElementById("detailed").checked; //Checkbox
	//Getting Detail values
	var boxes = document.getElementById("boxes").value;
	var fronts = document.getElementById("fronts").value;
	var material = document.getElementById("material").value;
	var doorStyle = document.getElementById("doorStyle").value;
	var finish = document.getElementById("finish").value;
	////
	//Validation
	orderCode = orderCode.replace(/\s+/g, '');
	if (orderCode === ""){
		$('#ordReq').show(500);
	}else if (receivedDt=== ""){
		$('#rcvdDateReq').show(500);
	}else{
		//Validation passed
		$('#ordReq').hide(500);
		$('#rcvdDateReq').hide(500);
		
		//console.log(decodeURIComponent($('#formJob :input').serialize()));
		//console.log($('#detailModal :input').serialize());
		//var data = $('#formJob :input').serialize().concat('&'.concat($('#detailModal :input').serialize()));
		//var data = $('#formJob :input').serialize();
		//console.log(data);
		myData = { mode: "saveNewOrd", orderCode:orderCode, poTag:poTag, nameDesigner:nameDesigner, custType:custType, description:description, receivedDt:receivedDt, receivedByProdDt:receivedByProdDt, 
					schedComplDt:schedComplDt, completedDt:completedDt, detailed:detailed, boxes:boxes, fronts:fronts, material:material, doorStyle:doorStyle, finish:finish};
		$.ajax({
	    url: 'newSchJob.php',
	    type: 'POST',
	    data: myData,
	    success: function(data, status, jqXHR) {
    		           console.log(jqXHR);
					   $('#successModal').modal('show');
    		        }
	  	});
	}
	
}

function detailOrder(){
	$('#detailOrderID').text($('#orderCode').val());
}
</script>
<style>
</style>
<?php
//today date
$month = date('m');
$day = date('d');
$year = date('Y');
$today = $year . '-' . $month . '-' . $day;

//Getting latest SKB order
opendb("SELECT orderCode FROM mobelSch2020 WHERE orderCode like 'SKB%' order by creationDate desc LIMIT 1");
if($GLOBALS['$result']->num_rows > 0){
	foreach ($GLOBALS['$result'] as $row) {
		$lastMobel = $row['orderCode'];
	}
}else{ $lastMobel = "No data for SKB orders"; }
//Getting latest Service order
opendb("SELECT orderCode FROM mobelSch2020 WHERE orderCode like '%S#2%' order by creationDate desc LIMIT 1");
if($GLOBALS['$result']->num_rows > 0){
	foreach ($GLOBALS['$result'] as $row) {
		$lastServ = $row['orderCode'];
	}
}else{ $lastMobel = "No data for Service orders"; }
//Getting latest DIY order
opendb("SELECT orderCode FROM mobelSch2020 WHERE orderCode like 'DIY%' order by creationDate desc LIMIT 1");
if($GLOBALS['$result']->num_rows > 0){
	foreach ($GLOBALS['$result'] as $row) {
		$lastDIY = $row['orderCode'];
	}
}else{ $lastMobel = "No data for DIY orders"; }
opendb("SELECT * FROM mobelSch2020 order by receivedDt desc");
?>
<div class="container-fluid">
	<div class="row">
		<div class="col mx-auto">
			<div class="card my-3">
				<h5 class="card-header">
					Register a new Job
				  </h5>
				<div class="card-body">
					<form id="formJob" method="post">
					  <div class="form-group">
						<div class="input-group mb-3">
						  <div class="input-group-prepend">
							<span class="input-group-text">Order Name</span>
						  </div>
						  <input type="text" class="form-control" name="orderCode" id="orderCode" style="text-transform:uppercase" aria-describedby="orderCode">
						  <div class="input-group-prepend">
							<span class="input-group-text" >Latest Orders:</span>
						  </div>
						  <div class="input-group-prepend">
							<span class="input-group-text" ><?php echo $lastMobel; ?></span>
						  </div>
						  <div class="input-group-prepend">
							<span class="input-group-text" ><?php echo $lastDIY;?></span>
						  </div>
						  <div class="input-group-prepend">
							<span class="input-group-text" ><?php echo $lastServ;?></span>
						  </div>						  
						</div>
						<small id="ordReq" class="form-text text-muted alert-danger">Order name is mandatory</small>
					  </div>
					  <div class="form-group">
						<div class="row">
							<div class="col-xl-4 col-lg-6 col-md-12 input-group mb-3">
							  <div class="input-group-prepend">
								<span class="input-group-text">Po# Tag</span>
							  </div>
							  <input type="text" class="form-control" name="poTag" id="poTag" style="text-transform:uppercase" aria-describedby="poTag">
							</div>
							<div class="col-xl-4 col-lg-6 col-md-12 input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">Name / Designer</span>
								</div>
								<input type="text" class="form-control" name="nameDesigner" id="nameDesigner" style="text-transform:uppercase" aria-describedby="nameDesigner">
							</div>
							<div class="col-xl-4 col-lg-6 col-md-12 input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">Customer Type</span>
								</div>
								<input type="text" class="form-control" name="custType" id="custType" style="text-transform:uppercase" aria-describedby="custType">
							</div>
						</div>
					  </div>
					  <div class="form-group">
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text">Details</span>
							</div>
							<input type="text" class="form-control" name="description" id="description" maxlength="100" style="text-transform:uppercase" aria-describedby="description">
						</div>
					  </div>
					  <div class="form-group">
					  <small id="rcvdDateReq" class="form-text text-muted alert-danger">Received Date is mandatory</small>
					  <div class="row">
						<div class="col-xl-3 col-lg-6 col-md-12 input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text">Received Date</span>
							</div>
							<input type="date" class="form-control" name="receivedDt" id="receivedDt" value="<?php echo $today; ?>" aria-describedby="receivedDt">							
						</div>
						<div class="col-xl-3 col-lg-6 col-md-12 input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text">Received by Prod Date</span>
							</div>
							<input type="date" class="form-control" name="receivedByProdDt" id="receivedByProdDt" aria-describedby="receivedByProdDt">
						</div>
						<div class="col-xl-3 col-lg-6 col-md-12 input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text">Shipping Date</span>
							</div>
							<input type="date" class="form-control" name="schedComplDt" id="schedComplDt" aria-describedby="schedComplDt">
						</div>
						<div class="col-xl-3 col-lg-6 col-md-12 input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text">Completed Date</span>
							</div>
							<input type="date" class="form-control" name="completedDt" id="completedDt" aria-describedby="completedDt">
						</div>
					  </div>
					  </div>
					  <div class="input-group mb-3">
						<input type="checkbox" class="form-check-input" name="detailed" id="detailed">
						<label class="form-check-label" for="detailed">Ready to be produced</label>
					  </div>
					  <button type="button" class="btn btn-primary" onclick="detailOrder()" data-toggle="modal" data-target="#detailModal">Details</button>		
					  <button type="button" onclick="submitJob()" class="btn btn-primary">Submit</button>
					  <button type="button" onclick="clearAll()" class="btn btn-secondary" style="float: right;">Clear</button>
					</form>					
				</div>
			</div>
		</div>
		<div class="col-12 mx-auto">
			<div class="card mx-auto my-3">
				<div class="card-body">
					<table class="table" style="width:100%" >
						<thead class="thead-light">
							<tr>
								<th>Order Name</th>
								<th>PO# Tag</th>
								<th>Name / Designer</th>
								<th>Customer Type</th>			
								<th>Details</th>
								<th>Received Date</th>
								<th>Received by Prod Date</th>
								<th>Shipping Date</th>
								<th>Completed Date</th>
							</tr>
						</thead>
						<tfoot class="thead-light">
							<tr>
								<th>Order Name</th>
								<th>PO# Tag</th>
								<th>Name / Designer</th>
								<th>Customer Type</th>			
								<th>Details</th>
								<th>Received Date</th>
								<th>Received by Prod Date</th>
								<th>Shipping Date</th>
								<th>Completed Date</th>
							</tr>
						</tfoot>
						<tbody>
						<?php
						if($GLOBALS['$result']->num_rows > 0){
							foreach ($GLOBALS['$result'] as $row) {
								echo "<tr>";
								echo "<td>" . $row['orderCode'] . "</td>";
								echo "<td>" . $row['poTag'] . "</td>";
								echo "<td>" . $row['nameDesigner'] . "</td>";
								echo "<td>" . $row['custType'] . "</td>";
								echo "<td>" . $row['description'] . "</td>";
								echo "<td>" . $row['receivedDt'] . "</td>";
								echo "<td>" . $row['receivedByProdDt'] . "</td>";
								echo "<td>" . $row['schedComplDt'] . "</td>";
								echo "<td>" . $row['completedDt'] . "</td>";
								echo "</tr>";
							}
						}else{
						//echo "<tr>No data for that search criteria.</tr><tr>Please change your filter to see some records.</tr>";
						}
								?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Details: &nbsp</h5>
		<h5 class="modal-title" id="detailOrderID"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<!--div class="form-group"-->
			<div class="row">
			<div class="input-group col mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text">Boxes</span>
				</div>
				<input type="text" class="form-control" name="boxes" id="boxes" style="text-transform:uppercase" aria-describedby="boxes">
			</div>
			<div class="input-group col mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text">Fronts</span>
				</div>
				<input type="text" class="form-control" name="fronts" id="fronts" style="text-transform:uppercase" aria-describedby="fronts">
			</div>
			</div>
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text">Material</span>
				</div>
				<input type="text" class="form-control" name="material" id="material" style="text-transform:uppercase" aria-describedby="material">
			</div>
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text">Door Style</span>
				</div>
				<input type="text" class="form-control" name="doorStyle" id="doorStyle" style="text-transform:uppercase" aria-describedby="doorStyle">
			</div>
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text">Finish</span>
				</div>
				<input type="text" class="form-control" name="finish" id="finish" style="text-transform:uppercase" aria-describedby="finish">
			</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <!--button type="button" class="btn btn-primary">Save changes</button-->
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!--div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Details: &nbsp</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div-->
      <div class="modal-body">
			<h5>Your order was created successfully</h5>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="window.location.reload();">Close</button>
        <!--button type="button" class="btn btn-primary">Save changes</button-->
      </div>
    </div>
  </div>
</div>
<?php include 'includes/foot.php';?>
<script>

$(document).ready(function(){
	$('#ordReq').hide();
	$('#rcvdDateReq').hide();
});
</script>
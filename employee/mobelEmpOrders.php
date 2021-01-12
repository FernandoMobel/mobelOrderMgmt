<script>
function init() {
    var tf = setFilterGrid("table1");
	var ccUpd = false;
	var frontsUpd = false;
	var deliveryDateUpd = false;
	var prevState;
  }
  
function saveUser(objectID){
	$("#"+objectID).css("border-color", "#ba0000");
	myData = { mode: "updateUser", id: objectID, value: $("#"+objectID).val()};
	$.post("./OrderItem.php",
			myData, 
		       function(data, status, jqXHR) {
            		if(status == "success"){
            	    	$("#"+objectID).css("border-color", "#00b828");
            	    }
		        });
}

function saveSettings(){
	myData = { mode: "setCurrentLeadtime", automaticPeriod: $("#automaticPeriod").val() };
	$.post("../save.php",
		myData, 
	       function(data, status, jqXHR) {
        		if(status == "success"){
        			$("#automaticPeriod").css("border-color", "#00b828");            		
					$("#currentLeadtime").val(jqXHR['responseText']);
					alert('Auto lead time is now set to: ' + jqXHR['responseText']);
        	    }else{
            	    alert('Sorry, something went wrong. Did you get the old password right? Please reload the page and try again.');
        	    }
	        });
}

function saveOrder(objectID,OID){
	$("#"+objectID+OID).css("border-color", "#ba0000");
	if($("#"+objectID+OID).val()==5){
		countBoxes(OID);
		getOrderRooms(OID);
		getRequiredDate(OID);					
	}else{
		myData = { mode: "updateOrder", id: objectID, value: $("#"+objectID+OID).val(), oid: OID};
		$.post("../OrderItem.php",
				myData, 
				   function(data, status, jqXHR) {
						if(data == "success"){
							$("#"+objectID+OID).css("border-color", "#00b828");							
						}
					});
	}
}

function productionReady(){	
	//Update date
	myData = { mode: 'updateRoomDetails',  oid:$('#orderID').attr('value'), rid: 0, col:'deliveryDate', val:$('#deliveryDate').val()};
	$.post("EmployeeMenuSettings.php",myData);
	//Update status
	myData = { mode: "updateOrder", id:'state', value:5, oid: $('#orderID').attr('value')};
	$.post("../OrderItem.php",myData);
}

//getting all the order rooms for the detailing for create tabs
function countBoxes(OID){
	myData = { mode: "countBoxes",  oid: OID};
	$.post("EmployeeMenuSettings.php",
			myData, 
		       function(data, status, jqXHR) {         
			   }
	);
}

//getting all the order rooms for the detailing for create tabs
function getOrderRooms(OID){
	$('#listRooms').empty()
	var listRooms = $('#listRooms');
	myData = { mode: "getOrderRooms",  oid: OID};
	$.post("EmployeeMenuSettings.php",
			myData, 
		       function(data, status, jqXHR) {          
					$('#modalContent').empty();
					$('#modalContent').append(jqXHR["responseText"]);
					$('#orderID').attr('value',OID);
					$('#detailsModalLabel').html("Details for Order: "+OID);
					$('#detailsModal').modal('toggle');
		        });
	//Stations
	$('#selDept').multiselect({
		allSelectedText: 'All selected',
		buttonWidth: '250px',
		dropRight: true,
		onChange: function(option, checked) {
			
		}
	});
}

function loadRoomDet(rid){
	$('#cc').css("border-color", "#ced4da");
	$('#fronts').css("border-color", "#ced4da");
	$('#deliveryDate').css("border-color", "#ced4da");
	myData = { mode: "loadRoomDet",  rid: rid};
	$.post("EmployeeMenuSettings.php",
		myData, 
		function(data, status, jqXHR) { 
			var pieces = JSON.parse(jqXHR["responseText"]);
			pieces.forEach(room => {
				$('#cc').val(room["cc"]);
				$('#fronts').val(room["fronts"]);
			});
		}
	)
}

function updateDetail(rid,col,val){
	if(col=="deliveryDate" && Date.parse(val)<new Date()){
		$('#cc').css("border-color", "#ced4da");
		$('#fronts').css("border-color", "#ced4da");
		$('#deliveryDate').css("border-color", "red");
		alert("This date: "+val+" selected, either is too soon or is not valid, please select another one. For that reason, date will not be updated");
		return;
	}
	if(col=="deliveryDate"){
		$('#deliveryDate').val(val);
		countBoxesxDay(val);
	}else{
		myData = { mode: 'updateRoomDetails',  oid:$('#orderID').attr('value'), rid: rid, col:col, val:val};
		$.post("EmployeeMenuSettings.php",
		myData, 
		function(data, status, jqXHR) { 
				//console.log(jqXHR['responseText']);
				$("#"+col).css("border-color", "#00b828");
			});
	}
}

function getOrderID(OID){
	$('#searchOrderBtn').empty();
	OID = OID.replace(/\s+/g, '');
	if (OID.trim()){			
		myData = { mode: "getOrderID", id: OID.trim(), value: OID.trim()};
		$.post("EmployeeMenuSettings.php",
			myData, 
			function(data, status, jqXHR) {
				$('#searchOrderBtn').append(data);           	    
			});
	}else{
		$('#searchOrderBtn').empty();
	}
}

function saveEmployeeSettings(objectID){
	var arr = $("#"+objectID).val();//Getting all values from input multiselect options
	switch(objectID){
		case 'stateFilter'://Order states evaluation (Quoting, submitted, etc.)
			if(arr.length==0){//if 0 then all the states will be selected
				 arr = ["1","2","3","4","5","6","7","8","9","10"]; 
			}
		break;
		default:
		break;
	}
	myData = { mode: "setFilter", id: objectID, value: arr}; 
	$.post("EmployeeMenuSettings.php",
			myData, 
			   function(data, status, jqXHR) {
					//$('#orders').empty();
					//console.log(jqXHR);
					window.location.reload();
				});
	//loadOrders(arr);
}

function loadOrders(objectID){
	myData = { mode: "getOrders", id: objectID, value: objectID }; 
	$.ajax({
	    url: 'EmployeeMenuSettings.php',
	    type: 'POST',
	    data: myData,
	    success: function(data, status, jqXHR) {						
    		        
    		    }
	});	
}

function setPrevious(prev){
	prevState = prev;
}

function setPrevState(){
	$('#state'+$('#orderID').attr('value')).val(prevState);
}

function countBoxesxDay(date){
	myData = { mode: "countBoxesxDay", date: date }; 
	$.ajax({
	    url: 'EmployeeMenuSettings.php',
	    type: 'POST',
	    data: myData,
	    success: function(data, status, jqXHR) {	 
	    	$('#lblBoxes').html('<b>'+jqXHR['responseText']+'</b> boxes are scheduled for: <b>'+date+'</b>');  
    	}
	});	
}

function getRequiredDate(oid){
	myData = {mode:"getRequiredDate", oid:oid};
	$.ajax({
	    url: 'EmployeeMenuSettings.php',
	    type: 'POST',
	    data: myData,
	    success: function(data, status, jqXHR) {
	    	$('#deliveryDate').val(jqXHR['responseText']);  
    	}
	});	
}
</script> 
<style>
div.sticky {
  position: -webkit-sticky;
  position: sticky;
  top: 0;
}
</style>
<?php 	
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//Getting employee settings
opendb2("select mainMenuDefaultStateFilter as state, clFilter, servFilter from employeeSettings where mosUser = " .$_SESSION["userid"]);
if($GLOBALS['$result2']-> num_rows >0){	
	foreach ($GLOBALS['$result2'] as $row2) {
		$state = $row2['state'];
		$state_ar = explode(', ', $state);//convert string to array to create control dinamically
		if($row2['clFilter']==""){
			$clfilter = '1,2,3';
		}else{
			$clfilter = $row2['clFilter'];			
		}
		if($row2['servFilter']==""){
			$servfilter = "0";
		}else{
			$servfilter = $row2['servFilter'];			
		}
		$cl_ar = explode(', ', $clfilter);//convert string to array to create control dinamically
		$srv_ar = explode(',', $servfilter);//convert string to array to create control dinamically
		$sqlS ="";
		$sqlW = "";
		foreach ($srv_ar as &$value2) {	
			switch($value2){
				case 4:
					$sqlS = " and isPriority = 1 ";
				break;
				case 5:
					$sqlW = " and isWarranty = 1 ";
				break;
			}
		}
		$sql ="select m.oid,a.busName as 'company', m.tagName, m.po, concat(mu.firstName,' ',mu.lastName) as 'designer', email, s.name as 'status', DATE(m.dateSubmitted) dateSubmitted, m.state, isPriority, isWarranty, CLid, m.deliveryDate from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state in (".$state.") and CLid in(".$clfilter.")".$sqlS.$sqlW." order by m.dateSubmitted asc";
		//echo $sql;
		opendb($sql);
	}
}else{
	opendb2("INSERT INTO employeeSettings (mosUser) VALUES ( ".$_SESSION["userid"] .")");//new user
	opendb2("select m.*,DATE(m.dateSubmitted) dateSubmitted,s.name as 'status', a.busName as 'company', concat(mu.firstName,' ',mu.lastName) as 'designer', email, m.state, isPriority, isWarranty, CLid, m.deliveryDate from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state > 1 and m.state <> 10 order by m.dateSubmitted asc");
}
?>
<div class="container-fluid sticky-top bg-white py-2" id="orderView">
	<div class="row">
		<div class="col-sm-6 col-lg-4">
			<div class="input-group">
				<div class="input-group-prepend">
					<label class="input-group-text bg-primary text-white" for="stateFilter">State</label>
				</div>
				<select class="custom-select" id="stateFilter" onchange="saveEmployeeSettings('stateFilter')" placeholder="Status" multiple>
				<?php				
				//Getting all states
				opendb2("select * from state order by position asc");
				//Building filter
				if($GLOBALS['$result2']->num_rows > 0){			
					foreach ($GLOBALS['$result2'] as $row2) {	
						$selected = ">";
						foreach ($state_ar as &$value) {									
							if($value==$row2['id']){
								$selected = "selected>";
								break;
							}
						}
						echo "<option value=\"".$row2['id']."\" ".$selected.$row2['name']."</option>" ;
					}
				}
				?>
				</select>
			</div>
		</div>
		<div class="col-sm-6 col-lg-4">
			<div class="input-group">
				<div class="input-group-prepend">
					<a id="popOrderType" tabindex="0" role="button" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-container="body" data-html="true">
						<label class="input-group-text bg-primary text-white" for="stateFilter">Order Types</label>
					</a>
				</div>
				<select class="custom-select" id="orderTypeFilter" multiple>
					<optgroup id="clFilter" class="font-weight-bold" label="Cabinet Lines">
						<?php
						$result = opendb2("select id, CabinetLine from cabinetLine");
						while($rowf = $result->fetch_assoc()){
							echo "<option ";
							foreach ($cl_ar as &$value) {									
								if($value==$rowf['id']){
									echo "selected ";
								}
							}
							echo "value=\"".$rowf['id']."\">".$rowf['CabinetLine']."</option>";
						}
						?>
				    </optgroup>
					<optgroup id="servFilter" class="font-weight-bold" label="Services">
						<?php
						if(count($srv_ar)==2){
							echo "<option selected value=\"4\">Service</option> ";
							echo "<option selected value=\"5\">Service/warranty</option> ";
						}else{
							foreach ($srv_ar as &$value2) {	
								if($value2==4){
									echo "<option selected value=\"4\">Service</option> ";
									echo "<option value=\"5\">Service/warranty</option> ";
								}elseif($value2==5){
									echo "<option value=\"4\">Service</option> ";
									echo "<option selected value=\"5\">Service/warranty</option> ";
								}else{
									echo "<option value=\"4\">Service</option> ";
									echo "<option value=\"5\">Service/warranty</option> ";
								}
							}
						}
						?>
				    </optgroup>							    
				</select>
			</div>
		</div>
		<div class="col-sm-6 col-lg-3 d-flex justify-content-start">
			<div id="searchOrderBtn" class="col">
			</div>
			<div >					
				<input id="openOrder" class="form-control" type="number" min="1" onkeyup="getOrderID(this.value)" placeholder="Open order by ID">					
			</div>											
		</div>
		<div class="col-sm-6 col-lg-1 form-check d-flex flex-row-reverse pr-0">
			<div>
				<label class="form-check-label" for="lockStatus">
					<svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-lock text-primary" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					  <path fill-rule="evenodd" d="M11.5 8h-7a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1zm-7-1a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-7zm0-3a3.5 3.5 0 1 1 7 0v3h-1V4a2.5 2.5 0 0 0-5 0v3h-1V4z"/>
					</svg>
				</label>
			</div>
			<div>
				<input type="checkbox" class="form-check-input" id="lockStatus" checked>
			</div>
		</div>
	</div>
</div>
<div class="col-sm-12 col-md-11 col-lg-11 mx-auto">
	<div class="card card-signin my-1">
		<div class="card-body pt-0">
			<div class="table-responsive">
				<table id="example" class="table table-hover">
					<thead class="thead-light">
						<tr>
							<th>OID</th>
							<th>Company</th>
							<th>Tag Name</th>
							<th>PO</th>			
							<th>Designer</th>
							<th>UserName</th>
							<th>Status</th>
							<th data-toggle="tooltip" title="YYYY-MM-DD">Submitted Date</th>
							<th data-toggle="tooltip" title="Completition date">Scheduled Date</th>
						</tr>
					</thead>
					<tfoot class="thead-light">
						<tr>
							<th>OID</th>
							<th>Company</th>
							<th>Tag Name</th>
							<th>PO</th>			
							<th>Designer</th>
							<th>UserName</th>
							<th>Status</th>
							<th data-toggle="tooltip" title="YYYY-MM-DD">Submitted Date</th>
							<th data-toggle="tooltip" title="YYYY-MM-DD">Scheduled Date</th>
						</tr>
					</tfoot>
					<tbody id="orders">
					<?php
					if($GLOBALS['$result']->num_rows > 0){
						foreach ($GLOBALS['$result'] as $row) {
							$orderType="";
							if($row['isPriority']==1)
								$orderType="table-warning";
							if($row['isWarranty']==1)
								$orderType="table-danger";
							if($row['CLid']==3)
								$orderType="table-primary";
							if($row['CLid']==2)
								$orderType="table-info";
							echo "<tr class=\"$orderType\">";
							echo "<td><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['oid']."</b></td>";
							echo "<td><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['company']."</b></td>";
							echo "<td><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">" . $row['tagName'] . "</td>";
							echo "<td><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">" . $row['po'] . "</td>";	
							echo "<td><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['designer']."</b></td>";
							echo "<td><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['email']."</b></td>";
							echo "<td>";
							echo "<select disabled onchange=\"saveOrder('state','" . $row['oid'] . "');\" id=\"state".$row['oid']."\" onfocus=\"setPrevious(this.value)\">";
							opendb2("select * from state order by position asc");
							if($GLOBALS['$result2']->num_rows > 0){
								if(is_null($row['status'])){
									echo "<option ". "selected" ." value=\"\">" . "Error, no valid state!" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['state']){
										echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}else{
										echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}
							echo "</select>";
							echo  "</td>";	
							echo "<td  data-toggle=\"tooltip\" title=\"Submitted date\"><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['dateSubmitted']."</b></td>";	
							echo "<td  data-toggle=\"tooltip\" title=\"Completition date\"><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['deliveryDate']."</b></td>";		
							echo "</tr>";
						}
					}else{
							echo "<tr>No data for that search criteria.</tr><tr>Please change your filter to see some records.</tr>";
					}
					?>
					</tbody>
				</table>
			</div>				
			
		</div>
	</div>
</div>
<!-- Modal for Boxes, Fronts and Delivery date-->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form onsubmit="productionReady()">
				<div class="modal-header">
					<input id="orderID" hidden></input>
					<h5 class="modal-title" id="detailsModalLabel"></h5>
					<h5 id="lblBoxes" class="modal-title mx-auto"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="setPrevState();">
					  <span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body pt-0">
					<small class="form-text text-center alert-info mb-3">This date is for the order and all it's rooms</small>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<div class="input-group mb-3">					
									<div class="input-group-prepend">
										<span class="input-group-text">Order Delivery Date</span>
									</div>
									<input required id="deliveryDate" type="text" maxlength="10" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control datepicker text-center" onchange="updateDetail(1, this.id, this.value);">
								</div>		
							</div>								
						</div>
						<div class="col-md-6">
							<select id="selDept" multiple="multiple">						
								<!--option selected value="1" id="shipping">SHIPPING</option-->
								<option selected value="2" id="wrapping">WRAPPING</option>
								<option selected value="8" id="sanding">SANDING</option>
							</select>
						</div>
					</div>
					<div id="modalContent" class="container">
					</div>
					<div class="modal-footer">
						<!--button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button-->
						<button type="submit" class="btn btn-primary">Save changes</button>
				  	</div>
				</div>
			</form>
		</div>
	</div> 
</div> 
<div id="popovercontent" hidden>
	<table class="table table-sm mx-auto text-center py-2">
		<tr><td class="table-warning p-0"><small><b>Service</b></small></td></tr>
		<tr><td class="table-danger p-0"><small><b>Service w/warranty</b></small></td></tr>
		<tr><td class="p-0"><small><b>Designers</b></small></td></tr>
		<tr><td class="table-primary p-0"><small><b>Span Medical</b></small></td></tr>
		<tr><td class="table-info p-0"><small><b>Builders</b></small></td></tr>
	</table>
</div> 
<?php include '../includes/foot.php';?>  
<script>
$(document).ready(function () {
	table = $('#example').DataTable({
		"order": [[ 7, "asc" ]],
		"lengthMenu": [30, 50, 100],
		"stateSave": true
	});
	
	//Filter options
	$('#stateFilter').multiselect({
		buttonWidth: '350px',
		maxHeight: 600,
		dropRight: true
	});

	$('#orderTypeFilter').multiselect({
		allSelectedText: 'All order types are selected',
		buttonWidth: '350px',
		maxHeight: 600,
		dropRight: true,
		onChange: function(options, checked) {
				var id = options[0].parentNode['id'];//get option group id
				var arr = $('#'+id+' option:selected').map(function(){return this.value;}).get();//get all the options selected for that option group
				myData = { mode: "setFilter", id:id, value: arr}; 
				$.post("EmployeeMenuSettings.php",
						myData, 
						   function(data, status, jqXHR) {
								//console.log(jqXHR);
								window.location.reload();
							});
        }
	});
	
	$('#listRooms.nav-tabs').on('click', 'a', function() {
		$('#listRooms.nav-tabs a.active').removeClass('active');
		$(this).addClass('active');
		loadRoomDet($(this).attr("value"));
	});
	
	//Status Locked
	$( "#lockStatus" ).click(function() {
	  if(!$("#orders select").prop('disabled')){
		  $("#orders select").prop('disabled',true);		  
	  }else{
		  $("#orders select").prop('disabled',false);
	  }
	});
	
	//Department selection filter
	$('#selDept').multiselect({
		allSelectedText: 'All departments selected',
		buttonWidth: '100%',
		dropRight: true,
		onChange: function(option, checked) {
			$action = 'complete';	
			if(checked){
				$action = 'delete';				
			}
			myData = { mode: "completeJobsAuto", oid:$('#orderID').attr('value'), dept:$(option).val(), action:$action};
			$.post("EmployeeMenuSettings.php",myData);
		}
	});

	/*Tooltips and Popovers use a built-in sanitizer to sanitize options which accept HTML */
	$.fn.popover.Constructor.Default.whiteList.table = [];
    $.fn.popover.Constructor.Default.whiteList.tr = [];
    $.fn.popover.Constructor.Default.whiteList.td = [];
    $.fn.popover.Constructor.Default.whiteList.th = [];
    $.fn.popover.Constructor.Default.whiteList.div = [];
    $.fn.popover.Constructor.Default.whiteList.tbody = [];
    $.fn.popover.Constructor.Default.whiteList.thead = [];
	$("#popOrderType").popover({
    	html: true, 
		content: function() {
        	return $('#popovercontent').html();
        }
	});
});
</script>
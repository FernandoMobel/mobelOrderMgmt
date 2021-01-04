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
	}
		
	myData = { mode: 'updateRoomDetails',  oid:$('#orderID').attr('value'), rid: rid, col:col, val:val};
	//console.log(myData);
	$.post("EmployeeMenuSettings.php",
	myData, 
	function(data, status, jqXHR) { 
			//console.log(jqXHR['responseText']);
			$("#"+col).css("border-color", "#00b828");
		});
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
	var arr = $("#"+objectID).val();
	if(arr.length==0){
		 arr = ["1","2","3","4","5","6","7","8","9","10"]; 
	}
	myData = { mode: "setFilter", id: objectID, value: arr}; 
	$.post("EmployeeMenuSettings.php",
			myData, 
			   function(data, status, jqXHR) {
					//$('#orders').empty();
					//console.log(jqXHR);
					window.location.reload();
				});
	loadOrders(arr);					
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
	//console.log(date);
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
</script> 
<div class="col-sm-12 col-md-11 col-lg-11 mx-auto">
	<div class="d-flex justify-content-end">
		<a id="pop1" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-content="Here you can see and update all the order status. If you can't find something change your filters.">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-square" viewBox="0 0 16 16">
			  <path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
			  <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
			</svg>
		</a>
	</div>
	<div class="card card-signin my-1">
		<div class="card-body pt-0">
		<?php 	
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		//Getting employee settings
		opendb2("select mainMenuDefaultStateFilter as state from employeeSettings where mosUser = " .$_SESSION["userid"]);
		if($GLOBALS['$result2']-> num_rows >0){	
			foreach ($GLOBALS['$result2'] as $row2) {
				$str = $row2['state'];
				$state_ar = explode(', ', $str);//convert string to array to create control dinamically
				$sql ="select m.oid,a.busName as 'company', m.tagName, m.po, concat(mu.firstName,' ',mu.lastName) as 'designer', email, s.name as 'status', DATE(m.dateSubmitted) dateSubmitted, m.state, isPriority, isWarranty, CLid, m.deliveryDate from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state in (".$row2['state'].") order by m.dateSubmitted asc";
				opendb($sql);
			}
		}else{
			opendb2("INSERT INTO employeeSettings (mosUser) VALUES ( ".$_SESSION["userid"] .")");//new user
			opendb2("select m.*,DATE(m.dateSubmitted) dateSubmitted,s.name as 'status', a.busName as 'company', concat(mu.firstName,' ',mu.lastName) as 'designer', email, m.state, isPriority, isWarranty, CLid, m.deliveryDate from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state > 1 and m.state <> 10 order by m.dateSubmitted asc");
		}
		echo "<br/><div class=\"container-fluid\">";
			?>
			<div class="container-fluid" id="orderView">
				<div class="row">
					<div class="col-sm-6 col-lg-5 my-1">
						<div class="input-group">
							<div class="input-group-prepend">
								<label class="input-group-text bg-primary text-white" for="stateFilter">
									<svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-funnel" fill="currentColor">
										<path fill-rule="evenodd" d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5v-2zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2h-11z"/>
									</svg>
								</label>
							</div>
							<select class="custom-select" id="stateFilter" onchange="saveEmployeeSettings('stateFilter')" placeholder="Status" multiple>
							<?php						
							/*Table Filters Start*/
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
					<div class="col-sm-6 col-lg-2">
						<table class="table table-sm mx-auto text-center"><tr><td class="table-warning p-0"><small><b>Service</b></small></td></tr><tr><td class="table-danger p-0"><small><b>Service w/warranty</b></small></td></tr><tr><td class="table-primary p-0"><small><b>Span Medical</b></small></td></tr><tr><td class="table-info p-0"><small><b>Builders</b></small></td></tr></table>
					</div>
					<div class="col-sm-6 col-lg-4 d-flex justify-content-start">
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
			<hr style="height:1px;border-width:0;color:gray;background-color:gray">
			<!--/div-->
			<div class="table-responsive">
				<table id="example" class="table table-hover">
					<thead class="thead-light">
						<tr>
							
						</tr>
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
					?>
					</tbody>
				</table>
				</div>
			</div>
				
			<?php
					}else{
						//echo "<tr>No data for that search criteria.</tr><tr>Please change your filter to see some records.</tr>";
					}
			?>

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
									<input required id="deliveryDate" type="text" maxlength="10" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control datepicker text-center" value="<?php echo date('Y-m-d'); ?>" onchange="updateDetail(1, this.id, this.value);">
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
<?php include '../includes/foot.php';?>  
<script>
$(document).ready(function () {
	table = $('#example').DataTable({
		"order": [[ 7, "asc" ]],
		"lengthMenu": [30, 50, 100]
	});
	
	//Filter options
	$('#stateFilter').multiselect({
		buttonWidth: '350px',
		maxHeight: 600,
		dropRight: true
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
	
	//Department selection
	$('#selDept').multiselect({
		allSelectedText: 'All selected',
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
	$('[data-toggle="popover"]').popover({ title: "What is this page for?" });
});
</script>
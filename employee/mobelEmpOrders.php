<?php
//getting status from DB
$result = opendb("select id,name from state");
while($row = $result->fetch_assoc()) {
	$states[] = $row;
}
$states = json_encode($states);
//echo $states;
?>
<script>
<?php echo "const states = ".$states.";"?>
function init() {
    var tf = setFilterGrid("table1");
	var ccUpd = false;
	var frontsUpd = false;
	var deliveryDateUpd = false;
	var prevState;
	var states = <?php echo $states?>;
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
		getOrderRooms(OID);
		getRequiredDate(OID);
		$('#detailsModal').modal('toggle');					
	}else{
		//console.log(objectID);
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
					//window.location.reload();					
					//table.destroy();					
				});
	table.destroy();
	loadOrders();
}

function loadOrders(){
	var dataSet;
	var rowClass = "";
	var order;
	var state;
	myData = { mode: "getOrders" }; 
	$.ajax({
	    url: 'EmployeeMenuSettings.php',
	    type: 'POST',
	    data: myData,
	    success: function(data, status, jqXHR) {						
    		        dataSet =  JSON.parse(jqXHR['responseText']);
					//console.log(dataSet);
					table = $('#example').DataTable({
						order: [[ 7, "asc" ]],
						lengthMenu: [30, 50, 100],
						stateSave: true,
						retrieve: true,
						data: dataSet,
						columns : [
							{
								className: "font-weight-normal",
								data : "oid",
								render: function(data, type) {
									order = data;
									return "<!--a class=\"onlyhover\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-file-earmark-arrow-down-fill text-primary\" viewBox=\"0 0 16 16\"><path d=\"M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zm-1 4v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 11.293V7.5a.5.5 0 0 1 1 0z\"/></svg></a-->&nbsp<a class=\"onlyhover\" href=\"#\" onclick=\"viewOrder("+order+")\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-eye text-primary\" viewBox=\"0 0 16 16\"><path d=\"M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z\"/><path d=\"M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z\"/></svg></a>&nbsp<a class=\"onlyhover\" onclick=\"getOrdFiles("+order+");\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-folder2-open text-primary\" viewBox=\"0 0 16 16\"><path d=\"M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v.64c.57.265.94.876.856 1.546l-.64 5.124A2.5 2.5 0 0 1 12.733 15H3.266a2.5 2.5 0 0 1-2.481-2.19l-.64-5.124A1.5 1.5 0 0 1 1 6.14V3.5zM2 6h12v-.5a.5.5 0 0 0-.5-.5H9c-.964 0-1.71-.629-2.174-1.154C6.374 3.334 5.82 3 5.264 3H2.5a.5.5 0 0 0-.5.5V6zm-.367 1a.5.5 0 0 0-.496.562l.64 5.124A1.5 1.5 0 0 0 3.266 14h9.468a1.5 1.5 0 0 0 1.489-1.314l.64-5.124A.5.5 0 0 0 14.367 7H1.633z\"/></svg></a>&nbsp<a href=\"../Order.php?OID="+order+"\">"+data+"</a>";
								}
							},
							{
								className: "font-weight-normal",
								data : "company",
								render: function(data, type) {
									if(data){
										return "<a href=\"../Order.php?OID="+order+"\">"+data+"</a>";
									}else{
										return data;
									}
								}
							},
							{
								className: "w-25",
								data : "tagName",
								render: function(data, type) {
									if(data){
										return "<a href=\"../Order.php?OID="+order+"\">"+data+"</a>";
									}else{
										return data;
									}
								}
							},
							{
								data : "po",
								render: function(data, type) {
									if(data){
										return "<a href=\"../Order.php?OID="+order+"\">"+data+"</a>";
									}else{
										return data;
									}
								}
							},
							{
								className: "font-weight-normal",
								data : "designer",
								render: function(data, type) {
									if(data){
										return "<a href=\"../Order.php?OID="+order+"\">"+data+"</a>";
									}else{
										return data;
									}
								}
							},
							{
								className: "font-weight-normal",
								data : "email",
								render: function(data, type) {
									if(data){
										return "<a href=\"../Order.php?OID="+order+"\">"+data+"</a>";
									}else{
										return data;
									}
								}
							},
							{								
								data : "status",
								render: function(data, type ) {
									var select='<select disabled onchange="saveOrder(\'state\','+order+');" id="state'+order+'" onfocus="setPrevious(this.value);" onclick="countBoxes('+order+');">';
									states.forEach(function(obj){
											select += '<option ';
											if(obj.id == data[1])
												select += 'selected ';
											select += 'value="'+obj.id+'">'+obj.name+'</option>';
										}
									)
									select += '</select>';
									return select;									
								}
							},							
							{
								data : "dateSubmitted",
								render: function(data, type) {
									if(data){
										return "<a href=\"../Order.php?OID="+order+"\">"+data+"</a>";
									}else{
										return data;
									}
								}
							},							
							{
								data : "deliveryDate",
								render: function(data, type) {
									if(data){
										return "<a href=\"../Order.php?OID="+order+"\">"+data+"</a>";
									}else{
										return data;
									}
								}
							},
							{
								data : "dateShipped",
								render: function(data, type) {
									if(data){
										return "<a href=\"../Order.php?OID="+order+"\">"+data+"</a>";
									}else{
										return data;
									}
								}
							}
						],
						rowCallback: function( row, data ) {						    
							//Set color code for every row
							if(data['CLid']==3)
								$(row).addClass('table-primary');
							if(data['CLid']==2)
								$(row).addClass('table-info');
							if(data['isPriority']==1)
								$(row).addClass('table-warning');
							if(data['isWarranty']==1)
								$(row).addClass('table-danger');
						}
					});
    		    }
	});
	//$('#example').DataTable().ajax.reload();
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

function getOrdFiles(oid){
	$('#filesList').empty();
	myData = {mode:"getOrdFiles", oid:oid};
	$.ajax({
	    url: 'EmployeeMenuSettings.php',
	    type: 'POST',
	    data: myData,
	    success: function(data, status, jqXHR) {
	    	  	//console.log(jqXHR['responseText']);
	    	  	$('#filesList').append(jqXHR['responseText']);
	    	  	$('#fileModal').modal('toggle');
    	}
	});	
}

function viewOrder(oid){
	$('#readOnlyOrder').val(oid);
	window.open('', 'TheWindow');
  	$('#TheForm').submit();
}
</script> 
<style>
div.sticky {
  position: -webkit-sticky;
  position: sticky;
  top: 0;
}

.onlyhover{
	display: none;
}

tr:hover .onlyhover{
	display: inline-block;
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
			<?php
			if(!in_array($_SESSION["userid"],[34,35])){
			?>
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
			<?php
			}
			?>
		</div>
	</div>
</div>
<div class="col-sm-12 col-md-12 col-lg-12 mx-auto">
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
							<th data-toggle="tooltip" title="YYYY-MM-DD">Scheduled Date</th>
							<th data-toggle="tooltip" title="YYYY-MM-DD">Shipping Date</th>
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
							<th data-toggle="tooltip" title="YYYY-MM-DD">Shipping Date</th>
						</tr>
					</tfoot>	
					<tbody id='orders'>
						
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

<!-- Modal File Editor-->
<div id="fileModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-xl">

    <!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header">
    		<h4 class="modal-title">File List</h4>
        	<button type="button" class="close" data-dismiss="modal">&times;</button>        	
      	</div>
      <div class="modal-body">
      	<table id="FileList" class="text-center" style="width:100%">
		    <thead>
		          <tr>
		            <th></th>
		            <th>File Name</th>
		            <th>Room Name</th>
		            <th>Item #</th>
		            <th>Item Description</th>
		          </tr>
		    </thead>
		    <tfoot>
		      <tr>
		        <th></th>
		        <th>File Name</th>
		        <th>Room Name</th>
		        <th>Item #</th>
		        <th>Item Description</th>
		      </tr>
		    </tfoot>
		    <tbody id="filesList">
		    
		    </tbody>
		</table>
      </div>
      <div class="modal-footer">
      	<!-- <button type="button" onClick=deleteRoom(); class="btn btn-default" data-dismiss="modal">Delete Room</button> -->
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<form id="TheForm" method="post" action="../readOnlyOrder.php" target="TheWindow">
<input id="readOnlyOrder" type="hidden" name="oid"/>
</form>
<?php include '../includes/foot.php';?>  
<script>
$(document).ready(function () {	
	loadOrders();
	
	//Filter options
	$('#stateFilter').multiselect({
		buttonWidth: '350px',
		maxHeight: 600,
		dropRight: true,
		includeSelectAllOption: true
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
								//window.location.reload();
							});
				table.destroy();
				loadOrders();
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
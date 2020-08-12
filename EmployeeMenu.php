<?php 
//$current_file_path = dirname(__DIR__ );
//echo $current_file_path;
?>

<?php include 'includes/nav.php';?>
<?php include 'includes/db.php';?>


<script>
function init() {
    var tf = setFilterGrid("table1");
  }
  
function saveUser(objectID){
	$("#"+objectID).css("border-color", "#ba0000");
	myData = { mode: "updateUser", id: objectID, value: $("#"+objectID).val()};
	$.post("OrderItem.php",
			myData, 
		       function(data, status, jqXHR) {
            		if(status == "success"){
            	    	$("#"+objectID).css("border-color", "#00b828");
            	    }
		        });
}
function saveSettings(){
	myData = { mode: "setCurrentLeadtime", currentLeadtime: $("#currentLeadtime").val() };
	$.post("save.php",
		myData, 
	       function(data, status, jqXHR) {
        		if(status == "success"){
        			$("#currentLeadtime").css("border-color", "#00b828");
            		alert('Mobel lead time is now set to: ' + $("#currentLeadtime").val());
        	    	//$("#"+objectID).css("border-color", "#00b828");
        	    }else{
            	    alert('Sorry, something went wrong. Did you get the old password right? Please reload the page and try again.');
        	    }
	        });
}

function saveOrder(objectID,OID){
	$("#"+objectID+OID).css("border-color", "#ba0000");
	myData = { mode: "updateOrder", id: objectID, value: $("#"+objectID+OID).val(), oid: OID};
	$.post("OrderItem.php",
			myData, 
		       function(data, status, jqXHR) {
            		if(data == "success"){
            	    	$("#"+objectID+OID).css("border-color", "#00b828");
            	    }
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
	myData = { mode: "setFilter", id: objectID, value: arr}; //$("#"+objectID).val()};
	$.post("EmployeeMenuSettings.php",
			myData, 
			   function(data, status, jqXHR) {
				    loadOrders(arr);
				});
}

function loadOrders(objectID){
	$("#orders").empty();
	myData = { mode: "getOrders", id: objectID, value: objectID }; //$("#"+objectID).val()};
	$.ajax({
	    url: 'EmployeeMenuSettings.php',
	    type: 'POST',
	    data: myData,
	    success: function(data, status, jqXHR) {
    		           $('#orders').append(data);
    		        }
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
        echo "<input type=\"text\"  onchange=\"saveSettings();\" class=\"form-control noresize\"  value=\"". substr($row['currentLeadtime'],0,10) ."\" id=\"currentLeadtime\">";
        ?>
    	        <br/>
        	    <div class="input-group-addon">
            		<span class="glyphicon glyphicon-th"></span>
                </div>
            </div>
        <?php
    }
}
?>


</div>
</div>
</div> 

<div class="col-sm-13 col-md-11 col-lg-9 mx-auto">
<div class="card card-signin my-3">
<div class="card-body">
<?php 	
//Getting employee settings
opendb2("select mainMenuDefaultStateFilter as state from employeeSettings where mosUser = " .$_SESSION["userid"]);
if($GLOBALS['$result2']-> num_rows >0){	
	foreach ($GLOBALS['$result2'] as $row2) {
		$str = $row2['state'];
		$state_ar = explode(', ', $str);//convert string to array to create control dinamically
		//opendb("select m.*,s.name as 'status' from mosOrder m, state s where s.id = m.state and m.state in (".$row2['state'].") order by m.state desc");
		opendb("select m.*,s.name as 'status', a.busName as 'company', concat(mu.firstName,' ',mu.lastName) as 'designer' from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state in (".$row2['state'].") order by m.state desc");
	}
}else{
	opendb2("INSERT INTO `employeesettings` (`mosUser`) VALUES ( ".$_SESSION["userid"] .")");//new user
	//opendb("select m.*,s.name as 'status' from mosOrder m, state s where s.id = m.state and m.state > 1 and m.state <> 10 order by m.state desc");
	opendb2("select m.*,s.name as 'status', a.busName as 'company', concat(mu.firstName,' ',mu.lastName) as 'designer' from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state > 1 and m.state <> 10 order by m.state desc");
}
echo "<br/><div class=\"container\">";
    ?>
    <div id="orderView">
		<div class="container">
			<div class="row">
				<div class="col-5 my-1">
					<a class="btn btn-primary btn-sm" data-toggle="collapse" href="#collapse1" role="button" aria-expanded="false" aria-controls="collapse1">Filter
						<svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-funnel" fill="currentColor">
						  <path fill-rule="evenodd" d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5v-2zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2h-11z"/>
						</svg><!--Filters-->
					</a>
					<div class="collapse" id="collapse1">
						<label>Status</label>
						<?php						
						/*Table Filters Start*/
						//Getting all states
						opendb2("select * from state order by position asc");
						echo "<select id=\"stateFilter\" onchange=\"saveEmployeeSettings('stateFilter')\" placeholder=\"Status\" multiple>";
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
						echo "</select>";
						?>					
					</div>
				</div>
				<div id="searchOrderBtn" class="col">
				</div>
				<div class= "col-3 my-1">					
					<input id="openOrder" class="form-control" type="number" onkeyup="getOrderID(this.value)" placeholder="Search Order ID">					
				</div>
			</div>
		</div>
		<hr style="height:1px;border-width:0;color:gray;background-color:gray">
	</div>
	<table id="example" class="table" style="width:100%" >
	<thead class="thead-light">
		<tr>
			
		</tr>
		<tr>
			<th>OID</th>
			<th>Company</th>
			<th>Tag Name</th>
			<th>PO</th>			
			<th>Designer</th>
			<th>Status</th>
			<!--th >Update Status</th-->
		</tr>
	</thead>
	<tfoot class="thead-light">
		<tr>
			<th>OID</th>
			<th>Company</th>
			<th>Tag Name</th>
			<th>PO</th>			
			<th>Designer</th>
			<th>Status</th>
			<!--th >Update Status</th-->
		</tr>
	</tfoot>
	<tbody id="orders">
	<?php
if($GLOBALS['$result']->num_rows > 0){
		foreach ($GLOBALS['$result'] as $row) {
			echo "<tr>";
			echo "<td><b><a href=\"Order.php?OID=" . $row['oid'] . "\">".$row['oid']."</b></td>";
			echo "<td><b><a href=\"Order.php?OID=" . $row['oid'] . "\">".$row['company']."</b></td>";
			echo "<td><a href=\"Order.php?OID=" . $row['oid'] . "\">" . $row['tagName'] . "</td>";
			echo "<td><a href=\"Order.php?OID=" . $row['oid'] . "\">" . $row['po'] . "</td>";	
			echo "<td><b><a href=\"Order.php?OID=" . $row['oid'] . "\">".$row['designer']."</b></td>";
			echo "<td>";
			echo "<select onchange=\"saveOrder('state','" . $row['oid'] . "');\" id=\"state".$row['oid']."\" >";
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
			echo "</tr>";
		}
		?>
		</tbody>
		</table>
	</div>
		
    <?php
}else{
	//echo "<tr>No data for that search criteria.</tr><tr>Please change your filter to see some records.</tr>";
}

?>

</div>
</div>
</div>    

<?php include 'includes/foot.php';?>
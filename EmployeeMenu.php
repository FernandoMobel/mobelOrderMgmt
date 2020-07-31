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
				console.log(OID);
}



function saveEmployeeSettings(objectID){
	//$(".collapse").collapse('toggle');
	var arr = $("#"+objectID).val();
	//console.log(arr);
	if(arr.length==0){
		 arr = ["1","2","3","4","5","6","7","8","9","10"]; 
	}
	myData = { mode: "setFilter", id: objectID, value: arr}; //$("#"+objectID).val()};
	console.log(myData);
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
		opendb("select m.*,s.name as 'status' from mosOrder m, state s where s.id = m.state and m.state in (".$row2['state'].") order by m.state desc");
	}
}else{
	opendb2("INSERT INTO `employeesettings` (`mosUser`) VALUES ( ".$_SESSION["userid"] .")");//new user
	opendb("select m.*,s.name as 'status' from mosOrder m, state s where s.id = m.state and m.state > 1 and m.state <> 10 order by m.state desc");
}
echo "<br/><div class=\"container\">";
    ?>
    <div id="orderView">
		<p>
			<a class="btn btn-primary btn-block btn-sm" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
				Filters
			  </a>
		</p>
		<div class="collapse" id="collapseExample">
			<?php						
			/*Table Filters Start*/
			//Getting all states
			opendb2("select * from state order by position asc");
			echo "<select id=\"stateFilter\" onchange=\"saveEmployeeSettings('stateFilter')\" placeholder=\"Status\" multiple>";
			//Building filter
			if($GLOBALS['$result2']->num_rows > 0){			
				foreach ($GLOBALS['$result2'] as $row2) {	
					foreach ($state_ar as $value) {
						$selected = ">";
						if($value==$row2['id']){
							$selected = "selected>";
							break;
						}
					}
					echo "<option value=\"".$row2['id']."\" ".$selected.$row2['name']."</option>" ;
				}
			}
			echo "</select>";
			//echo $state_ar;
			?>
		</div>				
				
			</td>
		</div>
		<table id="example" class="table table-bordered table-hover table-sm" style="width:100%" >
		<thead class="thead-dark">
			<tr>
				
			</tr>
			<tr>
				<th>OID</th>
				<th>Tag Name</th>
				<th>PO</th>			
				<th>Status</th>
				<!--th >Update Status</th-->
			</tr>
		</thead>
		<tfoot class="thead-dark">
			<tr>
				<th>OID</th>
				<th>Tag Name</th>
				<th>PO</th>
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
			echo "<td>" . $row['tagName'] . "</td>";
			//echo "<td>" . $row['status'] . "</td>";
			echo "<td>" . $row['po'] . "</td>";	
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
<script>
  
 $(document).ready(function(){

 var multipleCancelButton = new Choices('#stateFilter', {
 removeItemButton: true,
 maxItemCount:10,
 searchResultLimit:5,
 renderChoiceLimit:10
 });


 });

</script>
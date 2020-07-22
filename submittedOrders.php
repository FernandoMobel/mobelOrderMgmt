<?php include 'includes/nav.php';?>
<?php include 'includes/db.php';?>
<script>
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
</script>

<div class="navbar navbar-expand-sm bg-light navbar-light">
<div class="col-sm-12 col-md-11 col-lg-11 mx-auto">
<div class="row">

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
<div class="card card-signin my-5">
<div class="card-body">
<?php 
opendb("select m.*,s.name as 'status' from mosOrder m, state s where s.id = m.state and m.state > 1 and m.state <> 10 order by m.state desc");
echo "<br/><div class=\"container\">";
if($GLOBALS['$result']->num_rows > 0){
    ?>
    <table id="example" class="display nowrap" style="width:100%">
    <thead>
          <tr>
            <th>OID</th>
            <th>Tag Name</th>
            <th>Status</th>
            <th>PO</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>OID</th>
            <th>Tag Name</th>
            <th>Status</th>
            <th>PO</th>
          </tr>
        </tfoot><tbody>
          <?php
    
    foreach ($GLOBALS['$result'] as $row) {
        echo "<tr>";
        echo "<td><b><a href=\"Order.php?OID=" . $row['oid'] . "\">".$row['oid']."</b></td>";
        echo "<td>" . $row['tagName'] . "</td>";
        echo "<td>";
        echo "<select onchange=\"saveOrder('state','" . $row['oid'] . "');\" id=\"state".$row['oid']."\" class=\"custom-select-w-75\">";
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
        echo "<td>" . $row['po'] . "</td>";
        echo "</tr>";
    }
    ?>
    </table>
    <?php
}else{
	echo "<h3>No orders yet.</h3><br/><h3>Please have some customers submit some to you.</h3>";
}
?>
</div>
</div>
</div>     
      




      
<?php include 'includes/foot.php';?>


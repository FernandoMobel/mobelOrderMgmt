
 
<div class="col-sm-12 col-md-11 col-lg-11 mx-auto">
<div class="card card-signin my-3">
<div class="card-body pt-0">
<?php 	
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//Getting employee settings
opendb2("select mainMenuDefaultStateFilter as state from employeeSettings where mosUser = " .$_SESSION["userid"]);
if($GLOBALS['$result2']-> num_rows >0){	
	foreach ($GLOBALS['$result2'] as $row2) {
		$str = $row2['state'];
		$state_ar = explode(', ', $str);//convert string to array to create control dinamically
		$sql ="select m.oid,a.busName as 'company', m.tagName, m.po, concat(mu.firstName,' ',mu.lastName) as 'designer', email, s.name as 'status', DATE(m.dateSubmitted) dateSubmitted, m.state from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state in (".$row2['state'].") order by m.dateSubmitted asc";
		opendb($sql);
	}
}else{
	opendb2("INSERT INTO employeeSettings (mosUser) VALUES ( ".$_SESSION["userid"] .")");//new user
	opendb2("select m.*,DATE(m.dateSubmitted) dateSubmitted,s.name as 'status', a.busName as 'company', concat(mu.firstName,' ',mu.lastName) as 'designer', email, m.state from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state > 1 and m.state <> 10 order by m.dateSubmitted asc");
}
echo "<br/><div class=\"container-fluid\">";
    ?>
    <!--div -->
		<div class="container-fluid" id="orderView">
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
					<input id="openOrder" class="form-control" type="number" min="1" onkeyup="getOrderID(this.value)" placeholder="Search Order ID">					
				</div>
			</div>
		</div>
		<hr style="height:1px;border-width:0;color:gray;background-color:gray">
	<!--/div-->
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
			<th>UserName</th>
			<th>Status</th>
			<th data-toggle="tooltip" title="YYYY-MM-DD">Submitted Date</th>
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
		</tr>
	</tfoot>
	<tbody id="orders">
	<?php
if($GLOBALS['$result']->num_rows > 0){
		foreach ($GLOBALS['$result'] as $row) {
			echo "<tr>";
			echo "<td><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['oid']."</b></td>";
			echo "<td><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['company']."</b></td>";
			echo "<td><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">" . $row['tagName'] . "</td>";
			echo "<td><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">" . $row['po'] . "</td>";	
			echo "<td><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['designer']."</b></td>";
			echo "<td><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['email']."</b></td>";
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
			echo "<td  data-toggle=\"tooltip\" title=\"YYYY-MM-DD\"><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['dateSubmitted']."</b></td>";			
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
<?php include '../includes/foot.php';?>  
<script>
$(document).ready(function () {
	table = $('table').DataTable({
		"order": [[ 7, "asc" ]]
	});
});
</script>
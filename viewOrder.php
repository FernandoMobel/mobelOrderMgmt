<?php include 'includes/nav.php';?>
<?php include 'includes/db.php';?>
<script>
function switchQuote(oid) {
	$("#assignedU"+oid).css("border-color", "#ba0000");
	myData = { mode: "switchUser", oid: oid, newUser: $("#assignedU"+oid).val()};
	console.log(myData);
	$.post("OrderItem.php",
			myData, 
		       function(data, status, jqXHR) {
            		if(status == "success"){
            	    	$("#assignedU"+oid).css("border-color", "#00b828");
						console.log(jqXHR);
            	    }
		        });
}
</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12 col-md-11 col-lg-9 mx-auto">
			<div class="card card-signin my-5">
				<div class="card-header">
					<h5 id="ordersub">Orders</h5>
				</div>
				<div class="card-body py-0">
				<?php
				$admin = "";
				if($_SESSION["userType"]==2){
					$admin = "or m.account = " . $_SESSION["account"];
				}
				opendb("select m.*,s.name as 'status',u.email from mosOrder m, state s, mosUser u where s.id = m.state and m.mosUser = u.id and (u.email = '" . $_SESSION["username"] . "' ". $admin ."  )");
				echo "<br/><div class=\"container\">";
				//echo  "select m.*,s.name as 'status',u.email from mosOrder m, state s, mosUser u where s.id = m.state and m.mosUser = u.id and (u.email = '" . $_SESSION["username"] . "'  )". $admin .")";
				if($GLOBALS['$result']->num_rows > 0){
					?>
					<table id="example" class="display nowrap" style="width:100%">
					<thead>
						  <tr>
							<th>OID</th>
							<th>Tag Name</th>
							<th>Status</th>
							<th>PO</th>
							<?php
							if($_SESSION["userType"]==2)
							echo "<th>Assigned</th>";
							?>
						  </tr>
						</thead>
						<tfoot>
						  <tr>
							<th>OID</th>
							<th>Tag Name</th>
							<th>Status</th>
							<th>PO</th>
							<?php
							if($_SESSION["userType"]==2)
							echo "<th>Assigned</th>";
							?>
						  </tr>
						</tfoot><tbody>
						  <?php
					
					foreach ($GLOBALS['$result'] as $row) {
						echo "<tr>";
						echo "<td><b><a title=\"".$row['email']."\" href=\"Order.php?OID=" . $row['oid'] . "\">".$row['oid']."</b></td>";
						echo "<td><a href=\"Order.php?OID=" . $row['oid'] . "\">". $row['tagName'] . "</td>";
						echo "<td>" . $row['status'] . "</td>";
						echo "<td>" . $row['po'] . "</td>";						
						if($_SESSION["userType"]==2){
							echo "<td><select ";
							if(strcmp($row['status'],"Quoting")!=0)
								echo "disabled ";
							echo "onchange=\"switchQuote('" . $row['oid'] . "');\" id=\"assignedU".$row['oid']."\">";
							$sql2 = "select id, email from mosUser m where m.account = ".$_SESSION["account"];
							$result2 = opendb2($sql2);
							while ( $row2 = $result2->fetch_assoc())  {	
								if($row2["id"] == $row['mosUser']){
									echo "<option selected value=\"" . $row['mosUser'] . "\">" . $row['email'] . "</option>";
								}else{
									echo "<option value=\"" . $row2['id'] . "\">" . $row2['email'] . "</option>";
								}
							}
							echo "</select></td>";
						}
						echo "</tr>";
					}
					?>
					</table>
					<?php
				}else{
					echo "<h3>No orders yet.</h3><br/><h3>Please create a new order using the \"New\" menu option.</h3>";
				}
				?>
				</div>
				</div>
			</div>
		</div>  
		<!--div class="col-sm-12 col-md-11 col-lg-6 mx-auto">
			<div class="card card-signin my-5">
				<div class="card-header">
					<h5 id="order">Your Team Orders</h5>
				</div>
				<div class="card-body">
				</div>     
			</div>     
		</div-->     
	</div>     
</div>     
      
<?php include 'includes/foot.php';?>
<script>
$(document).ready(function () {
	table = $('#example').DataTable({
			"order": [[ 0, "asc" ]]
	});
});
</script>
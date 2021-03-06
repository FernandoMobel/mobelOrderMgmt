<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12 col-md-11 col-lg-10 mx-auto">
			<div class="card card-signin my-3">
				<div class="card-header">
					<h5 id="ordersub">Orders</h5>
				</div>
				<div class="card-body py-0">
					<?php
					$admin = "";
					if($_SESSION["userType"]==2){
						$admin = "or m.account = " . $_SESSION["account"];
					}
					opendb("select m.oid,m.mosUser, m.po, m.tagName, DATE(dateSubmitted) as dateSubmitted, DATE(dateRequired) as dateRequired, DATE(deliveryDate) as deliveryDate, DATE(dateShipped) as dateShipped, s.name as status,u.email from mosOrder m, state s, mosUser u where s.id = m.state and m.mosUser = u.id and (u.email = '" . $_SESSION["username"] . "' ". $admin ."  )");
					echo "<br/><div class=\"container-fluid table-responsive\">";
					//echo  "select m.*,s.name as 'status',u.email from mosOrder m, state s, mosUser u where s.id = m.state and m.mosUser = u.id and (u.email = '" . $_SESSION["username"] . "'  )". $admin .")";
					if($GLOBALS['$result']->num_rows > 0){
						?>
						<table id="example" class="table table-sm table-hover">
							<thead class="thead-light">
							  <tr>
								<th>OID</th>
								<th>Tag Name</th>
								<th>Status</th>
								<th>PO</th>
								<?php
								if($_SESSION["userType"]==2)
									echo "<th>Assigned</th>";
								?>
								<th>Submitted Date</th>
								<th>Required Date</th>
								<th>Scheduled Date</th>
								<th>Delivered Date</th>
							  </tr>
							</thead>
							<tfoot class="thead-light">
							  <tr>
								<th>OID</th>
								<th>Tag Name</th>
								<th>Status</th>
								<th>PO</th>
								<?php
								if($_SESSION["userType"]==2)
								echo "<th>Assigned</th>";
								?>
								<th>Submitted Date</th>
								<th>Required Date</th>
								<th>Scheduled Date</th>
								<th>Delivered Date</th>
							  </tr>
							</tfoot><tbody>
							  <?php
						
						foreach ($GLOBALS['$result'] as $row) {
							echo "<tr>";
							echo "<td><b><a title=\"".$row['email']."\" href=\"Order.php?OID=" . $row['oid'] . "\">".$row['oid']."</a></b></td>";
							echo "<td><a href=\"Order.php?OID=" . $row['oid'] . "\">". $row['tagName'] . "</a></td>";
							echo "<td><a href=\"Order.php?OID=" . $row['oid'] . "\">" . $row['status'] . "</a></td>";
							echo "<td><a href=\"Order.php?OID=" . $row['oid'] . "\">" . $row['po'] . "</a></td>";						
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
							echo "<td><a href=\"Order.php?OID=" . $row['oid'] . "\">" . $row['dateSubmitted'] . "</a></td>";
							//if(strcmp($row['status'],"Quoting")!=0){
								echo "<td><a href=\"Order.php?OID=" . $row['oid'] . "\">" . $row['dateRequired'] . "</a></td>";	
							/*}else{
								echo "<td style='min-width:120px'><input id=\"dateReq".$row['oid']."\" style='max-width:130px' title=\"Some factors may increase your lead time. We will inform you as soon as possible once your quote is submitted.\" type=\"text\" maxlength=\"10\" data-provide=\"datepicker\" data-date-format=\"yyyy-mm-dd\" onchange=\"updateReqDate(this);\" class=\"form-control datepicker\"  value=\"". substr($row['dateRequired'],0,10) ."\"></td>";
							}*/						
							echo "<td><a href=\"Order.php?OID=" . $row['oid'] . "\">" . $row['deliveryDate'] . "</a></td>";	
							echo "<td><a href=\"Order.php?OID=" . $row['oid'] . "\">" . $row['dateShipped'] . "</a></td>";	
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
</div>     
</div>     
      
<?php include 'includes/foot.php';?>
<script>
$(document).ready(function () {
	table = $('#example').DataTable({
		"order": [[ 0, "asc" ]],
		lengthMenu: [20, 30, 50],
		stateSave: true
	});

	$('.datepicker').datepicker({
    	startDate: new Date()
	});
});
</script>
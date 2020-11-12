<?php include_once '../includes/db.php';?>
<?php
/* For local environment */
$local = "";
if(strcmp($_SERVER['SERVER_NAME'],"localhost")==0 || strcmp($_SERVER['SERVER_NAME'],"192.168.16.199")==0){
	$local = "/mobelOrderMgmt";
}
session_start();
if($_POST['mode']=="getOrders"){
	$arr = implode(', ', $_POST['value']);//getting all values from array
	$sql = "select m.oid,a.busName as 'company', m.tagName, m.po, concat(mu.firstName,' ',mu.lastName) as 'designer', email, s.name as 'status', DATE(m.dateSubmitted) dateSubmitted, m.state from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state in (".$arr.") order by m.state desc";
	$result = opendb($sql);
	$dbdata = array();
	while ( $row = $result->fetch_assoc())  {
		$dbdata[]=$row;
	}
	echo json_encode($dbdata);
	
	/*
	if($GLOBALS['$result']-> num_rows >0){	
		foreach ($GLOBALS['$result'] as $row) {
			echo "<tr>";
			echo "<td><b><a href=\"Order.php?OID=" . $row['oid'] . "\">".$row['oid']."</b></td>";
			echo "<td><b><a href=\"Order.php?OID=" . $row['oid'] . "\">".$row['company']."</b></td>";
			echo "<td><a href=\"Order.php?OID=" . $row['oid'] . "\">" . $row['tagName'] . "</td>";
			echo "<td><a href=\"Order.php?OID=" . $row['oid'] . "\">" . $row['po'] . "</td>";	
			echo "<td><b><a href=\"Order.php?OID=" . $row['oid'] . "\">".$row['designer']."</b></td>";
			echo "<td><b><a href=\"Order.php?OID=" . $row['oid'] . "\">".$row['email']."</b></td>";
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
			echo "<td  data-toggle=\"tooltip\" title=\"YYYY-MM-DD\"><b><a href=\"Order.php?OID=" . $row['oid'] . "\">".$row['dateSubmitted']."</b></td>";						
			echo "</tr>";
		}
	}else{
		echo "<tr>No data for that search criteria.</tr><tr>Please change your filter to see some records.</tr>";
	}*/
}

if($_POST['mode']=="setFilter"){
	//Checking which filter is going to be updated
	if($_POST['id']=="stateFilter"){
		$arr = implode(', ', $_POST['value']);//getting all values from array
		$sql = "update employeeSettings set mainMenuDefaultStateFilter = \"".$arr."\" where mosUser = " . $_SESSION["userid"];
		opendb2($sql);
	}
}

if($_POST['mode']=="getOrderID"){
	opendb("select 1 from mosOrder where oid =".$_POST['value']);
	if($GLOBALS['$result']-> num_rows >0){
		echo "<a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=".$_POST['value']."\" id=\"searchOrderBtn\" class=\"btn btn-outline-primary btn-sm float-right\" type=\"button\" >Open Order</a>";
	}else{
		echo "<div class=\"alert alert-warning\" role=\"alert\">";
		echo "<p>Order <b>".$_POST['value']."</b> doesn't exists</p></div>";
	}
}

if($_POST['mode']=="updateHeader"){
	//update visibility
	$sql = "update ".$_POST['table']." set visible=".$_POST['checked']." where id=".$_POST['id'];
	opendb($sql);
	/*if($_POST['checked']=="false"){
		//Update Quotes when disable header value
		$sql = "update orderRoom orr set ".$_POST['table']."=null where orr.".$_POST['table']."=".$_POST['id']." and exists(select 1 from mosOrder mo where mo.oid = orr.oid and mo.state=1)";
		opendb($sql);
	}*/
}

if($_POST['mode']=="getDateStatus"){
	$sql = "select workDayInd from calendar where calendarDay = '".$_POST['date']."'";
	$result = opendb($sql);
	while ( $row = $result->fetch_assoc())  {
		echo $row["workDayInd"];
		
	}
}

if($_POST['mode']=="updateCalDay"){
	$sql = "update calendar set workDayInd = ".$_POST["newStatus"].", uid = ".$_SESSION["userid"].", updateDate = curdate() where calendarDay = '".$_POST['date']."'";
	opendb($sql);
}
?>
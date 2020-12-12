<?php
include 'includes/db.php';
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if($_POST['mode']=="getUserList"){
	$sql = "select concat( mu.firstName,' ', mu.lastName) as name,mu.email,mu.phone,a.busName as account,mus.name as userType,clg.name as cabinetLine ";// mu.id,  a.description, mus.id as userTypeId,
	$sql .= "from mosUser mu, account a, mosUserTypes mus, cabinetLineGroup clg "; 
	$sql .= "where mu.account = a.id and mu.userType = mus.id and mu.CLGroup = clg.id";
	//echo $sql;
	$result = opendb($sql);
	$itemData = array();
	
	while($row = $result->fetch_assoc()){ 
		$item = array();
		array_push($item, $row['name'],$row['email'],$row['phone'],$row['account'],$row['userType'],$row['cabinetLine']);
		array_push($itemData, $item); 
	} 
	return json_encode($itemData);
}

if($_POST['mode']=="getUserbyId"){
	$sql = "select concat( mu.firstName,' ', mu.lastName) as name,mu.email,mu.phone,a.busName as account,mus.name as userType,clg.name as cabinetLine ";// mu.id,  a.description, mus.id as userTypeId,
	$sql .= "from mosUser mu, account a, mosUserTypes mus, cabinetLineGroup clg "; 
	$sql .= "where mu.account = a.id and mu.userType = mus.id and mu.CLGroup = clg.id and mu.id =".$_POST['uid'];
	$result = opendb($sql);
	$itemData = array();
	while($row = $result->fetch_assoc()){ 
		$item = array();
		array_push($item, $row['name'],$row['email'],$row['phone'],$row['account'],$row['userType'],$row['cabinetLine']);
		array_push($itemData, $item); 
	} 
	return json_encode($itemData);
}

if($_POST['mode']=="getOrdDet"){
	$sql = "SELECT s.id, s.name as state, count(*)as qty FROM mosOrder mo, state s where mo.state = s.id and mosUser = ".$_POST['uid']." group by s.name order by s.id"; 
	$result = opendb($sql);
	$total = 0;
	$div = "";
	while ( $row = $result->fetch_assoc())  {	
		$total += $row["qty"];
		if($row["id"]<7){
			$div .= "<div class=\"text-center my-3 p-0\"><h5 class=\"my-0\"><b>".$row["qty"]."</b></h5><small class=\"mx-auto\">".$row["state"]."</small></div>";
		}
	}
	$div .= "<div class=\"text-center my-auto\"><h4><b>".$total."</b></h4><small class=\"mx-auto\"><b>Total Orders</b></small></div>";
	echo $div;
}
?>
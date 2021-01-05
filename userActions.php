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

if($_POST['mode']=="getUsrDet"){
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

if($_POST['mode']=="updateDetail"){
	$sql = "update mosUser set ".$_POST['col']."='".$_POST['value']."' where email='".$_POST['email']."'";
	opendb($sql);
	if(strcmp($_POST['email'],$_SESSION['email'])==0)
		$_SESSION[$_POST['col']] = $_POST['value'];
	echo $sql;
}

if($_POST['mode']=="updatePassword"){
	if(strcmp($_SESSION['email'],$_POST['email']) == 0 ){
    	$sql = "update mosUser set pw = '" . $_POST['pw2'] . "' where '" . $_POST['pw'] . "' = pw and '" . $_POST['pw2'] . "' = '" . $_POST['pw3'] . "' and email = '" . $_SESSION['username'] . "'";
    }else{
    	$sql = "update mosUser set pw = '" . $_POST['pw2'] . "' where '" . $_POST['pw2'] . "' = '" . $_POST['pw3'] . "' and email = '" . $_POST['email'] . "'";
    }
    opendb($sql);
    if($GLOBALS['$conn']->affected_rows<>1){
        var_dump(http_response_code(204));
    }
}

if($_POST['mode']=="addNewUser"){
	try{
		$sql = "insert into mosUser(email,pw,firstName,lastName,phone,account,userType,CLGroup,defaultCLid) values(";
		$sql .= completeQuery();
		//echo $sql;
		opendb($sql);
	}catch (mysqli_sql_exception $e) {
		header('HTTP/1.1 500 Internal Server Booboo');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode($e));
   }
	
}

if($_POST['mode']=="addNewAccount"){
	$sql = "insert into account(busName,busDBA,phone,discount,CLgroup) values(";
	$sql .= completeQuery();
	echo $sql;
	opendb($sql);
}

if($_POST['mode']=="addNewAccountAddress"){
	$sql = "insert into accountAddress(aid,aType,contactName,contactEmail,contactPhone,country,province,city,street,unit,postalCode) values(";
	$sql .= completeQuery();
	echo $sql;
	opendb($sql);
}

if($_POST['mode']=="userExist"){
	$sql = "select count(1) exist from mosUser where email='".$_POST['email']."'";
	$result = opendb($sql);
	$row = $result->fetch_assoc();
	echo $row['exist'];
}

function completeQuery(){
	$firstRow = true;
	$sql="";
	foreach (explode('&', $_POST['data']) as $chunk) {
	    $param = explode("=", $chunk);
	    if ($param) {
	    	if($firstRow){
				if (is_numeric(urldecode($param[1]))) {
					$sql .= urldecode($param[1]);
				} else {
					$sql .= "'".urldecode($param[1])."'";
				}	
				$firstRow = false;
			}else{
				if (is_numeric(urldecode($param[1]))) {
					$sql .= ", ".urldecode($param[1]);
				} else {
					$sql .= ", '".urldecode($param[1])."'";
				}			
			}
	    }
	}
	$sql .= ")";
	return $sql;
}

?>
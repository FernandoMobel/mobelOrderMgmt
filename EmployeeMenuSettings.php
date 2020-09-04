<?php
session_start();
if(isset($_SESSION["username"])){
    if(($_SESSION["username"]=="" || $_SESSION["username"]=="invalid") && str_replace("/HelloWorldPHP/","",$_SERVER['REQUEST_URI'])!="index.php"){
        header("Location: index.php");
        //exit();
    }
}else{
    $_SESSION["username"]="invalid";
    header("Location: index.php");
}
?>
<?php include_once 'includes/db.php';?>
<?php
if($_POST['mode']=="getOrders"){
	$arr = implode(', ', $_POST['value']);//getting all values from array
	//opendb("select m.*,s.name as 'status' from mosOrder m, state s where s.id = m.state and m.state in (".$arr.") order by m.state desc");
	opendb("select m.*,s.name as 'status', a.busName as 'company', concat(mu.firstName,' ',mu.lastName) as 'designer',email from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state in (".$arr.") order by m.state desc");
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
			echo "</tr>";
		}
	}else{
		echo "<tr>No data for that search criteria.</tr><tr>Please change your filter to see some records.</tr>";
	}
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
		echo "<a href=\"Order.php?OID=".$_POST['value']."\" id=\"searchOrderBtn\" class=\"btn btn-outline-primary btn-sm float-right\" type=\"button\" >Open Order</a>";
	}else{
		echo "<div class=\"alert alert-warning\" role=\"alert\">";
		echo "<p>Order <b>".$_POST['value']."</b> doesn't exists</p></div>";
	}
}
?>
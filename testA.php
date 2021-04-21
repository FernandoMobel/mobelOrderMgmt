<?php include_once 'includes/db.php';?>
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
/* For local environment */
$local = "";
if(strcmp($_SERVER['SERVER_NAME'],"localhost")==0 || strcmp($_SERVER['SERVER_NAME'],"192.168.16.199")==0){
	$local = "/mobelOrderMgmt";
}
session_start();
if($_POST['mode']=="run"){ 
	$sql = $_POST['sql'];
	$result = opendb($sql);
	if ($result) {
		echo "<table id='tbl' class=\"table bg-white\"><tr>";
		$field=$result->fetch_fields();
		foreach ($field as $col){
			echo "<th>".$col->name."</th>";
		}
		echo "</tr>";
		
		while ( $row = $result->fetch_row()){
			echo "<tr>";

			for ($i=0;$i<$result->field_count;$i++)
			{
			echo "<td>".$row[$i]."</td>";
			}

			echo "</tr>";
		}
		echo "</table>";
	}else{
		printf("Errormessage: %s\n", $GLOBALS['$conn']->error);
	}
}

if($_POST['mode']=="exec"){
	$sql = $_POST['sql'];
	$result = opendbmulti($sql);
	if (!$result) {
		printf("Errormessage: %s\n", $GLOBALS['$conn']->error);
	 }else{
		echo $result;
	 }
}
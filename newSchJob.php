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

include_once 'includes/db.php';?>
<?php
function convertQueryStr(){
	$itemData = $_POST['data'];
	$keywords = preg_split("/[\s,=,&]+/", $itemData);
	$arr=array();
	for($i=0;$i<sizeof($keywords);$i++)	{
		$arr[$keywords[$i]] = mb_convert_encoding(urldecode($keywords[++$i]), 'UTF-8', 'UTF-8,ISO-8859-1');//encoding ascii values
	}
	return json_decode(json_encode((object)$arr),true);
}

if($_POST['mode']=="saveNewOrd"){
	$sql = "INSERT INTO mobelsch2020(orderCode, poTag, nameDesigner, custType, description, receivedDt, receivedByProdDt, schedComplDt, completedDt, detailed, boxes, fronts, material, doorStyle, finish) "; 	
	$sql .= "VALUES ('".strtoupper($_POST["orderCode"])."','".strtoupper($_POST["poTag"])."','".strtoupper($_POST["nameDesigner"])."','".strtoupper($_POST["custType"])."','".strtoupper($_POST["description"]);
	$sql .="','".$_POST["receivedDt"]."','".$_POST["receivedByProdDt"]."','".$_POST["schedComplDt"]."','".$_POST["completedDt"]."'";
	if($_POST['detailed']=="true"){
		$sql .= ",1";
	}else{
		$sql .= ",0";
	}
	$sql .= ",'".strtoupper($_POST["boxes"])."','".strtoupper($_POST["fronts"])."','".strtoupper($_POST["material"])."','".strtoupper($_POST["doorStyle"])."','".strtoupper($_POST["finish"])."')";
	//echo $sql;
	opendb($sql);
	echo $GLOBALS['$result'] ;
}
?>
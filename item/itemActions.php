<?php
include '../includes/db.php';
session_start();

function convertQueryStr(){
	$itemData = $_POST['data'];
	$keywords = preg_split("/[\s,=,&]+/", $itemData);
	$arr=array();
	for($i=0;$i<sizeof($keywords);$i++)	{
		$arr[$keywords[$i]] = mb_convert_encoding(urldecode($keywords[++$i]), 'UTF-8', 'UTF-8,ISO-8859-1');//encoding ascii values
	}
	return json_decode(json_encode((object)$arr),true);
}

if($_POST['mode']=="insertNewItem"){
	$obj = convertQueryStr();
	
	$sql = "INSERT INTO item(name, description, price, sizePrice, minSize, W, H, D, W2, H2, D2, minW, minH, minD, maxW, maxH, maxD, doorFactor, ";
	$sql .=	"speciesFactor, finishFactor, interiorFactor, sheenFactor, glazeFactor, drawers, smallDrawerFronts, largeDrawerFronts, dateCreated, pricingMethod,";
	if(count($obj)==29){
		$sql .= "isCabinet,";
	}	
	$sql .= " CLGroup) VALUES (";
	$sql .= "'".strtoupper($obj["name"])."','".strtoupper($obj["description"])."',".$obj["price"].",".$obj["sizePrice"].",".$obj["minSize"].",".$obj["W"].",".$obj["H"].",".$obj["D"].",".$obj["W2"].",".$obj["H2"].",";
	$sql .= $obj["D2"].",".$obj["minW"].",".$obj["minH"].",".$obj["minD"].",".$obj["maxW"].",".$obj["maxH"].",".$obj["maxD"].",".$obj["doorFactor"].",".$obj["speciesFactor"].",".$obj["finishFactor"].",";
	$sql .= $obj["interiorFactor"].",".$obj["sheenFactor"].",".$obj["glazeFactor"].",".$obj["drawers"].",".$obj["smallDrawerFronts"].",".$obj["largeDrawerFronts"].",CURDATE(),".$obj["pricingMethod"].",";
	if(count($obj)==29){
		$sql .= "1,";
	}
	$sql .= $obj["CLGroup"].")";
	
	opendb($sql);
	echo $GLOBALS['$result'] ;
}

if($_POST['mode']=="updateItemById"){
	$id = $_POST['id'];
	//convert to Json
	$obj = convertQueryStr();
	//Update Item
	$sql = "update item set name = '".strtoupper($obj["name"])."', description = '".strtoupper($obj["description"])."', price=".$obj["price"].", sizePrice =".$obj["sizePrice"];
	$sql .= ", minSize=".$obj["minSize"].", W =".$obj["W"].", H=".$obj["H"].", D = ".$obj["D"].", lastModified=CURDATE()";
	$sql .= ", minW=".$obj["minW"].", minH=".$obj["minH"].", minD=".$obj["minD"];
	if(count($obj)==29){
		$sql .= " , isCabinet = 1";
	}else{
		$sql .= " , isCabinet = 0";
	}
	$sql .= " where id = ".$id;
	opendb($sql);
	if($GLOBALS['$result'] > 0){
		//Update Order Items
		$sql = "UPDATE orderItem oit SET name = '".strtoupper($obj["name"])."', description = '".strtoupper($obj["description"])."', price=".$obj["price"].", sizePrice =".$obj["sizePrice"];
		$sql .= ", minSize=".$obj["minSize"]; //.", W =".$obj["W"].", H=".$obj["H"].", D = ".$obj["D"];
		$sql .= ", minW=".$obj["minW"].", minH=".$obj["minH"].", minD=".$obj["minD"];
		$sql .= " where oit.id in (SELECT oi.id FROM orderRoom orr, orderItem oi, mosOrder mo where orr.rid = oi.rid and mo.oid = orr.oid and mo.state = 1 and oi.iid = ".$id.")";
		opendb2($sql);
	}
	echo $GLOBALS['$result2'];
}

if($_POST['mode']=="getItems"){
	$filter = $_POST['str'];
	$sql = "select id,name,description from item where name like '%".$filter."%' or description like '%".$filter."%' order by description limit 150";
    //echo $sql;
    $query = opendb($sql);
	$itemData = array(); 
	if($query->num_rows > 0){ 
		while($row = $query->fetch_assoc()){ 
			$data['id'] = $row['id'];
			$data['name'] = $row['name']; 
			$data['description'] = $row['description'];
			array_push($itemData, $data); 
		} 
	} 
	 
	// Return results as json encoded array 
	echo json_encode($itemData); 
}

if($_POST['mode']=="getItemsRestricted"){
	$filter = $_POST['str'];
	$sql = "select id,name,description from item where (name like '%".$filter."%' or description like '%".$filter."%') and CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$_SESSION["defaultCLid"].") and visible is null order by description limit 150";
    //echo $sql;
    $query = opendb($sql);
	$itemData = array(); 
	if($query->num_rows > 0){ 
		while($row = $query->fetch_assoc()){ 
			$data['id'] = $row['id'];
			$data['name'] = $row['name']; 
			$data['description'] = $row['description'];
			array_push($itemData, $data); 
		} 
	} 
	 
	// Return results as json encoded array 
	echo json_encode($itemData); 
}


if($_POST['mode']=="getItemById"){
	$id = $_POST['id'];
	$sql = "select * from item where id = ".$id;
    //echo $sql;
	
    $query = opendb($sql);
	
	$itemData = array(); 
	if($query->num_rows > 0){ 
		while($row = $query->fetch_assoc()){ 
			$data['id'] = $row['id'];
			$data['name'] = $row['name']; 
			$data['description'] = $row['description'];
			$data['price'] = $row['price']; 
			$data['sizePrice'] = $row['sizePrice']; 
			$data['minSize'] = $row['minSize']; 
			/********************************************/
			$data['W'] = $row['W']; 
			$data['H'] = $row['H']; 
			$data['D'] = $row['D']; 
			$data['minW'] = $row['minW']; 
			$data['minH'] = $row['minH']; 
			$data['minD'] = $row['minD'];
			$data['maxW'] = $row['maxW']; 
			$data['maxH'] = $row['maxH']; 
			$data['maxD'] = $row['maxD'];
			/********************************************/
			$data['doorFactor'] = $row['doorFactor'];
			$data['speciesFactor'] = $row['speciesFactor'];
			$data['finishFactor'] = $row['finishFactor'];
			$data['interiorFactor'] = $row['interiorFactor'];
			$data['sheenFactor'] = $row['sheenFactor'];
			$data['glazeFactor'] = $row['glazeFactor'];
			$data['drawers'] = $row['drawers'];
			$data['smallDrawerFronts'] = $row['smallDrawerFronts'];
			$data['largeDrawerFronts'] = $row['largeDrawerFronts'];
			/********************************************/
			$data['isCabinet'] = $row['isCabinet'];
			$data['CLGroup'] = $row['CLGroup'];
			array_push($itemData, $data); 
		} 
	} 
	 
	// Return results as json encoded array 
	echo json_encode($itemData); 
}

if($_POST['mode']=="getImage"){
	$path = "../uploads/ItemImages/".substr($_POST['id'], -2)."/";
	$files = glob($path . $_POST['id'] . ".*", GLOB_ERR);
	if(count($files)>0){
		echo $files[0];
	}else{
		echo "false";
	};
}

if($_POST['mode']=="reqItemUpdate"){
	$oid = $_POST['id'];
	$user = $_SESSION["userid"];
	$status = 1;
	//$date = "CURDATE()";
	$table = "item";
	$stmt = $GLOBALS['$conn']->prepare("INSERT INTO itemRequest (iid,reqStatus,reqUser,reqDate,itemCatalog) VALUES (:iid,:reqStatus,:reqUser,CURDATE(),:itemCatalog)");
	echo $stmt -> error;
	$stmt->bind_param(":iid",$oid,PDO::PARAM_INT);
	$stmt->bind_param(":reqStatus",$status,PDO::PARAM_INT );
	$stmt->bind_param(":reqUser",$user,PDO::PARAM_STR);
	//$stmt->bindparam(":reqDate",$date);
	$stmt->bindparam(":itemCatalog",$table,PDO::PARAM_STR);
	$stmt->execute();
	$stmt->close();
	$GLOBALS['$conn']->close();
}

?>
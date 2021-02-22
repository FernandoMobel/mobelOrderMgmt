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
	//tracking
	$what = "";
	$firstRow = true;
	$sql = "insert into item(";
	$sql2 = " values(";
	foreach($obj as $key => $value){
		if($firstRow){
			$what .= $key.": ".$value;
			$sql .= $key;
			$sql2 .= "'".$value."'";
			$firstRow = false;
		}else{
			$what .= ", ".$key.": ".$value;
			$sql .= ", ".$key;		
			if (is_numeric($value)) {
				$sql2 .= ", ".$value;
			} else {
				$sql2 .= ", '".$value."'";
			}			
		}
	}
	$sql .= ")";
	$sql2 .= ")";
	$sql .=$sql2;
	$result = opendb($sql);

	//tracking
	$itemId =  $GLOBALS['$conn']->insert_id;
	$sql ="INSERT INTO `trackItemUpdate`(`itemId`, `uid`, `description`, `updateDate`) VALUES (".$itemId.",".$_SESSION["userid"].",'".$what."',NOW())";
	echo $sql;
	opendb($sql);
}

if($_POST['mode']=="updateItemById"){
	//convert to Json
	$obj = convertQueryStr();
	//tracking
	$what = "";
	//Update Item table
	$firstRow = true;
	$sql = "update item set ";
	foreach($obj as $key => $value){
		if($firstRow){
			$what .= $key.": ".$value;
			$sql .= $key."='".$value."'";
			$firstRow = false;
		}else{
			$what .= ", ".$key.": ".$value;
			if (is_numeric($value)) {
				$sql .= ", ".$key."=".$value;
			} else {
				$sql .= ", ".$key."='".$value."'";
			}			
		}
	}
	$sql .= " where id = ".$_POST['id'];
	opendb($sql);

	//tracking
	$track = "INSERT INTO `trackItemUpdate`(`itemId`, `uid`, `description`, `updateDate`) VALUES (".$_POST['id'].",".$_SESSION["userid"].",'".$what."',NOW())";
	opendb($track);
	//2sd3443443echo print_r($obj);
	
	//Update Open Quotes
	/*---------------------------------
	The following fileds will be updated for open quotes
	-Name			-Price		
	-Min&Max sizes	-Drawers		
	-Description	-Size price	
	-Factors		-Drawers(sm,lg,etc)
	-Is cabinet
	----------------------------------*/
	$sql = "update orderItem oit set ";
	$sql .= "name = '".$obj["name"]."', ";
	$sql .= "description = '".$obj["description"]."', ";
	$sql .= "price = ".$obj["price"].", ";
	$sql .= "sizePrice = ".$obj["sizePrice"].", ";
	$sql .= "minSize = ".$obj["minSize"].", ";
	$sql .= "minW = ".$obj["minW"].", ";
	$sql .= "minH = ".$obj["minH"].", ";
	$sql .= "minD = ".$obj["minD"].", ";
	$sql .= "maxW = ".$obj["maxW"].", ";
	$sql .= "maxH = ".$obj["maxH"].", ";
	$sql .= "maxD = ".$obj["maxD"].", ";
	$sql .= "doorFactor = ".$obj["doorFactor"].", ";
	$sql .= "speciesFactor = ".$obj["speciesFactor"].", ";
	$sql .= "finishFactor = ".$obj["finishFactor"].", ";
	$sql .= "interiorFactor = ".$obj["interiorFactor"].", ";
	$sql .= "sheenFactor = ".$obj["sheenFactor"].", ";
	$sql .= "glazeFactor = ".$obj["glazeFactor"].", ";
	$sql .= "drawers = ".$obj["drawers"].", ";
	$sql .= "smallDrawerFronts = ".$obj["smallDrawerFronts"].", ";
	$sql .= "largeDrawerFronts = ".$obj["largeDrawerFronts"];

	$sql .= " where oit.id in (SELECT oi.id FROM orderRoom orr, orderItem oi, mosOrder mo where orr.rid = oi.rid and mo.oid = orr.oid and mo.state = 1 and oi.iid = ".$_POST['id'].")";
	//echo $sql;
	opendb2($sql);
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
			$data['W2'] = $row['W2']; 
			$data['H'] = $row['H']; 
			$data['H2'] = $row['H2']; 
			$data['D'] = $row['D']; 
			$data['D2'] = $row['D2']; 
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
			$data['visible'] = $row['visible'];
			$data['CLGroup'] = $row['CLGroup'];
			$data['pricingMethod'] = $row['pricingMethod'];
			/********************************************/
			$data['isCabinet'] = $row['isCabinet'];
			array_push($itemData, $data); 
		} 
	} 
	 
	// Return results as json encoded array 
	echo json_encode($itemData); 
}

if($_POST['mode']=="getImage"){
	$path = "../uploads/ItemImages/".bin2hex($_POST['cat']);
	$files = glob($path.".*", GLOB_ERR);
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
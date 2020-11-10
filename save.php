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
if($_POST['mode'] == "setStyle"){
    $sql = "update orderRoom set ". $_POST['column'] . " = '" .$_POST['id'] . "' where oid = '" . $_POST['oid'] . "' and rid = '". $_POST['rid'] . "'";
	opendb($sql);
	if(strcmp($_POST['column'],"species")==0){
		$sql = "update orderRoom set frontFinish = null where oid = '" . $_POST['oid'] . "' and rid = '". $_POST['rid'] . "'";
		opendb($sql);
	}
    
    //echo $sql;
}
if($_POST['mode'] == "addRoom"){
    $sql = "insert into orderRoom (oid,name) values (".$_POST['oid'].",'newroom')";
    opendb($sql);
    
    //echo $sql;
}

if($_POST['mode'] == "updateRoom"){
    $sql = "update orderRoom set name = '" . $_POST['value'] . "' where oid = " . $_POST['oid'] . " and rid = " . $_POST['rid'];
    opendb($sql);
}

if($_POST['mode'] == "updateRoomNote"){
    $sql = "update orderRoom set note = '" . $_POST['value'] . "' where oid = " . $_POST['oid'] . " and rid = " . $_POST['rid'];
    opendb($sql);
}

if($_POST['mode'] == "deleteRoom"){
    $sql = "delete from orderItemMods where rid = " . $_POST['rid'];
    opendb($sql);
    $sql = "delete from orderItem where rid = " . $_POST['rid'];
    opendb($sql);
    $sql = "delete from orderRoom where oid = " . $_POST['oid'] . " and rid = " . $_POST['rid'];
    opendb($sql);
    
    //echo $sql;
}

if($_POST['mode'] == "deleteItem"){
    if($_POST['mod']>0){
        $sql = "delete from orderItemMods where id = " . $_POST['itemID'] . " and rid = " . $_POST['rid'];
    }else{
        $sql = "delete from orderItem where id = " . $_POST['itemID'] . " and rid = " . $_POST['rid'];
    }
    opendb($sql);
    

}
//echo $_POST['oid'];
if($_POST['mode'] == "saveEditedItem"){
    if(itemUpdateConstraintsOK("orderItem",$_POST['column'])==0){
        //header("HTTP/1.1 500 Internal Server Error");
    }else{
		$value = str_replace("'","\'",$_POST['id']);
        if(is_numeric($_POST['id'])){
            $sql = "update orderItem set ". $_POST['column'] . " = " .$value. " where id = '" . $_POST['itemID'] . "' and rid = '". $_POST['rid'] . "'";
        }else{
            $sql = "update orderItem set ". $_POST['column'] . " = '".$value."' where id = '" . $_POST['itemID'] . "' and rid = '". $_POST['rid'] . "'";
        }
        opendb($sql);
    }
}

if($_POST['mode'] == "saveEditedMod"){
    if(itemUpdateConstraintsOK("orderItemMods",$_POST['column'])==0){
        //header("HTTP/1.1 500 Internal Server Error");
    }else{
		$value = str_replace("'","\'",$_POST['id']);
        if(is_numeric($_POST['id'])){
            $sql = "update orderItemMods set ". $_POST['column'] . " = " .$value. " where id = '" . $_POST['itemID'] . "' and rid = '". $_POST['rid'] . "'";
        }else{
            $sql = "update orderItemMods set ". $_POST['column'] . " = '".$value."' where id = '" . $_POST['itemID'] . "' and rid = '". $_POST['rid'] . "'";
        }
        opendb($sql);
    }
}


function sendmail($to,$subject,$body)
{
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'Bcc: fernando@mobel.ca' . "\r\n";
    $headers .= 'From: '."orders@mobel.ca"."\r\n".
        'Reply-To: '."orders@mobel.ca"."\r\n" .
        'MIME-Version: 1.0\r\n' .
        'Content-Type: text/html; charset=UTF-8\r\n' .
        'X-Mailer: PHP/' . phpversion(). "\r\n";
    
    
    mail($to,$subject,$body,$headers);
}

if($_POST['mode'] == "setCurrentLeadtime"){
    $newDate = calculateDays($_POST['automaticPeriod']);
	$sql = "update settings set currentLeadtime = '".$newDate."', autoLeadDate=".$_POST['automaticPeriod'];
	opendb($sql);
	echo $newDate;
}

if($_POST['mode'] == "submitToMobel"){
    $CLid = 1;
    $sql = "update mosOrder set dateSubmitted = now(), state = '2', leadTime = (select currentLeadtime from settings) where oid = '" . $_POST['oid'] . "' and state = 1";
    opendb($sql);
    $sql = "select * from mosOrder o, accountAddress aA, account a, mosUser mu where o.mosUser = mu.id and o.account = a.id and o.shipAddress = aA.id and o.oid = '" . $_POST['oid'] . "'";
    opendb($sql);
    $msg = "<html><body><H1>Thank you for your order. </H1><p>We have recieved your order and will be sending back a confirmation shortly.</p>";
    if($GLOBALS['$result']->num_rows > 0){
        $msg .= "<p>";
    }
        foreach ($GLOBALS['$result'] as $row) {
            $msg .= "Order ID: " . $row['oid'] . "<br/>";
            $msg .= "Submitted by: " . $row['firstName'] . " " . $row['lastName'] . " from " . $row['busDBA'] . "<br/>";
            $msg .= "Phone Number: " . $row['phone'] . "<br/>";
            $msg .= "Date Submitted: " . substr($row['dateSubmitted'],0,10) . "<br/>";
            $msg .= "Date Required: " . substr($row['dateRequired'],0,10) . "<br/>";
            $msg .= "Tag Name: " . $row['tagName'] . "<br/>";
            //$msg .= "PO: " . $row['po'] . "<br/>";
            $msg .= "Ship To: " . $row['unit'] . " " . $row['street']. " " . $row['city']. ", ". $row['province']. ", ". $row['country']. " ". $row['postalCode']. "<br/>";
			$CLid = $row['CLid'];
        }
    
    $msg .= "<br/>";
    //$SQL = "sele * from (SELECT oi.note,                                       oi.id as item, 0 as sid, oi.qty, oi.name, oi.price, oi.sizePrice, 0 as 'parentPercent', ds.factor as 'DFactor', irf.factor as 'IFactor', ff.factor as 'FFactor', sh.factor as 'SFactor', gl.factor as 'GFactor', sp.finishedEndSizePrice as 'EFactor', (db.upcharge + dg.upcharge) as 'drawerCharge', sdf.upcharge as 'smallDrawerCharge', ldf.upcharge as 'largeDrawerCharge', oi.doorFactor as 'DApplies', oi.interiorFactor as 'IApplies', oi.finishFactor as 'FApplies', oi.sheenFactor as 'SApplies', oi.glazeFactor as 'GApplies',oi.drawers, oi.smallDrawerFronts, oi.largeDrawerFronts, oi.H, oi.W, oi.D, oi.minSize, it.pricingMethod as methodID, oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight
    $SQL = "select * from (SELECT oi.note, orr.rid as rid, orr.name as roomName, oi.id as item, 0 as sid, oi.qty, oi.name, oi.price, oi.sizePrice, 0 as 'parentPercent', ds.factor as 'DFactor', irf.factor as 'IFactor', ff.factor as 'FFactor', ff.upcharge as 'FUpcharge', sh.factor as 'SFactor', gl.factor as 'GFactor', sp.finishedEndSizePrice as 'EFactor', (db.upcharge + dg.upcharge) as 'drawerCharge', sdf.upcharge as 'smallDrawerCharge', ldf.upcharge as 'largeDrawerCharge', oi.doorFactor as 'DApplies', oi.interiorFactor as 'IApplies', oi.finishFactor as 'FApplies', oi.sheenFactor as 'SApplies', oi.glazeFactor as 'GApplies',oi.drawers, oi.smallDrawerFronts, oi.largeDrawerFronts, oi.H, oi.W, oi.D, oi.minSize, it.pricingMethod as methodID, oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight,orr.species,orr.interiorFinish,orr.door,orr.frontFinish,orr.drawerBox,orr.glaze,orr.smallDrawerFront,orr.sheen,orr.largeDrawerFront,orr.hinge,orr.drawerGlides,orr.finishedEnd 
    FROM  orderItem oi, orderRoom orr, doorSpecies ds, interiorFinish irf, item it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg, smallDrawerFront sdf, largeDrawerFront ldf, species sp
    WHERE it.id = oi.iid and oi.rid = orr.rid and orr.species = ds.sid and orr.door = ds.did and orr.interiorFinish = irf.id and orr.oid = '" .$_POST['oid']. "' and orr.species = sp.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and it.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CLid.")
    union all
                           SELECT oi.note, orr.rid as rid, orr.name as roomName, oi.pid as item, oi.id as sid, oi.qty, oi.name, oi.price, oi.sizePrice, 0 as 'parentPercent', ds.factor as 'DFactor', irf.factor as 'IFactor', ff.factor as 'FFactor', ff.upcharge as 'FUpcharge', sh.factor as 'SFactor', gl.factor as 'GFactor', sp.finishedEndSizePrice as 'EFactor', (db.upcharge + dg.upcharge) as 'drawerCharge', sdf.upcharge as 'smallDrawerCharge', ldf.upcharge as 'largeDrawerCharge', oi.doorFactor as 'DApplies', oi.interiorFactor as 'IApplies', oi.finishFactor as 'FApplies', oi.sheenFactor as 'SApplies', oi.glazeFactor as 'GApplies',oi.drawers, oi.smallDrawerFronts, oi.largeDrawerFronts, oi.H, oi.W, oi.D, oi.minSize, it.pricingMethod as methodID, oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight,orr.species,orr.interiorFinish,orr.door,orr.frontFinish,orr.drawerBox,orr.glaze,orr.smallDrawerFront,orr.sheen,orr.largeDrawerFront,orr.hinge,orr.drawerGlides,orr.finishedEnd
    FROM  orderItemMods oi, orderRoom orr, doorSpecies ds, interiorFinish irf, itemMods it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg, smallDrawerFront sdf, largeDrawerFront ldf, species sp
    WHERE it.id = oi.mid and oi.rid = orr.rid and orr.species = ds.sid and orr.door = ds.did and orr.interiorFinish = irf.id and orr.oid = '" .$_POST['oid']. "' and orr.species = sp.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and it.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CLid.")) as T1 order by roomName,item,sid";
    //SELECT orr.rid as rid, orr.name as roomName, oi.pid,oi.id as sid, oi.qty, oi.name, oi.price, oi.sizePrice, parentPercent, ds.factor as 'DFactor', irf.factor as 'IFactor', oi.doorFactor as 'DApplies', oi.interiorFactor as 'IApplies', oi.H, oi.W, oi.D, oi.minSize, it.pricingMethod as methodID,oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight,orr.species,orr.interiorFinish,orr.door,orr.frontFinish,orr.drawerBox,orr.glaze,orr.smallDrawerFront,orr.sheen,orr.largeDrawerFront,orr.hinge,orr.drawerGlides,orr.finishedEnd
    
    opendb($SQL);
    
    $roomID = -1;
    $roomchanged = 0;
 
    if($GLOBALS['$result']->num_rows > 0){
        
        foreach ($GLOBALS['$result'] as $row) {
            
            if(!($roomID == $row['rid'])){
                
                if($roomchanged > 0){
                    $msg .=  "</table><br/>";
                }
                $msg .=  "<style>td{text-align:center;}</style>";
                $msg .= "<div><b>" . $row['roomName'] . "</b><br/> " . roomTable($row['species'],$row['interiorFinish'],$row['door'],$row['frontFinish'],$row['drawerBox'],$row['glaze'],$row['smallDrawerFront'],$row['sheen'],$row['largeDrawerFront'],$row['hinge'],$row['drawerGlides'],$row['finishedEnd']) . "</div>";
                $msg .=  "<br/><table id=\"example\" class=\"table table-striped table-sm\" style=\"width:100%\">";
                $msg .=  "<thead>";
                $msg .=  "      <tr>";
                //$msg .=  "        <th class=\"font-weight-bold\">Room</th>";
                $msg .=  "        <th class=\"font-weight-bold\">Item#</th>";
                $msg .=  "        <th class=\"font-weight-bold\">Description</th>";
                $msg .=  "        <th class=\"font-weight-bold\">H</th>";
                $msg .=  "        <th class=\"font-weight-bold\">W</th>";
                $msg .=  "        <th class=\"font-weight-bold\">D</th>";
                $msg .=  "        <th class=\"font-weight-bold\">Qty</th>";
                $msg .=  "        <th class=\"font-weight-bold\">Hinged</th>";
                $msg .=  "        <th class=\"font-weight-bold\">F.E.</th>";
                if($_SESSION["userType"]>1){
                    $msg .=  "<th class=\"font-weight-bold\">Price</th>";
                }
                $msg .=  "<th></th>";
                $msg .=  "</tr>";
                $msg .=  "</thead>";
                $msg .=  "<tbody class=\"col-sm-12\">";
                $i = 1;
                $si= 0;
                opendb2("select price from item where id = (select iid from orderitem where id = " . $row['item'] . ")");
                foreach($GLOBALS['$result2'] as $row2){
                    $parentPrice = $row2['price'];
                }
                //$parentPrice = 0;
                $parentID = -1;
                $isParent = -1;
                $roomchanged = 1;
                $roomID = $row['rid'];
            }
            
            
            
            if($parentID !== $row['item']){ //new parent item
                $parentID = $row['item'];
                $isParent = 1;
                $parentPrice = 0;
                $si = 0;
            }else{
                $isParent = 0;
                $si = $si + 1;
                $i = $i - 1;
            }
            $msg .=  "";
            $tdStyle = "<td class=\"borderless\">";
            if($isParent===1){
                $msg .=  "<tr class=\"font-weight-bold\">";
                $tdStyle = "<td class=\"font-weight-bold\">";
            }else{
                $msg .=  "<tr class=\"table-sm\">";
            }
            
            //$msg .=  $tdStyle . $row['orr.rid'] . "</td>";
            $msg .=  $tdStyle . $i . "." . $si . "</td>";
            $msg .=  $tdStyle . $row['name'] . "</td>";
            $msg .=  $tdStyle . (float)$row['H'] . "</td>";
            $msg .=  $tdStyle . (float)$row['W'] . "</td>";
            $msg .=  $tdStyle . (float)$row['D'] . "</td>";
            $msg .=  $tdStyle . (float)$row['qty'] . "</td>";
            
            $hinging = "";
            if($row['hingeLeft']==1){
                $hinging = "L";
            }
            if($row['hingeRight']=="1"){
                $hinging = "R";
            }
            if($row['hingeLeft']=="1" && $row['hingeRight'] =="1"){
                $hinging = "B";
            }
            //echo $tdStyle . $hinging . "</td>";
            $finishedEnds = "";
            if($row['finishLeft']=="1"){
                $finishedEnds = "L";
            }
            if($row['finishRight']=="1"){
                $finishedEnds = "R";
            }
            if($row['finishLeft']=="1" && $row['finishRight']=="1"){
                $finishedEnds = "B";
            }
            //echo $tdStyle . $finishedEnds . "</td>";
            
            
            $msg .=  $tdStyle . $hinging . "</td>";
            $msg .=  $tdStyle . $finishedEnds . "</td>";
            $aPrice = //getPrice($row['qty'],$row['price'],$row['sizePrice'],$parentPrice,$row['parentPercent'],$row['DFactor'],$row['IFactor'],$row['DApplies'],$row['IApplies'],$row['H'],$row['W'],$row['D'],$row['minSize'],$row['methodID']);
            getPrice($row['qty'],$row['price'],$row['sizePrice'],$parentPrice,$row['parentPercent'],$row['DFactor'],$row['IFactor'],$row['FFactor'],$row['GFactor'],$row['SFactor'],$row['EFactor'],$row['drawerCharge'],$row['smallDrawerCharge'],$row['largeDrawerCharge'],  $row['DApplies'],$row['IApplies'],$row['FApplies'],$row['GApplies'],$row['SApplies'],$row['drawers'],$row['smallDrawerFronts'],$row['largeDrawerFronts'],$row['finishLeft']+$row['finishRight'], $row['H'],$row['W'],$row['D'],$row['minSize'],$row['methodID'],$row['FUpcharge']);
            if($isParent === 1){
                $parentPrice = $aPrice;
            }
            if($_SESSION["userType"]>1){
                $msg .=  $tdStyle . number_format($aPrice,2,'.','') . "</td>";            
            }
            $msg .=  "</tr>";
            $i = $i + 1;
        }
        $msg .=  "</tbody></table>";
    }
    
    $msg .= "</p><p>Thanks,</p><p>Mobel</p></body></html>";
    sendmail("fernando@mobel.ca; orders@mobel.ca", "Order Submitted", $msg);
    //sendmail("markelgers@gmail.com", "Order Submitted", $msg);
}

function roomTable($species,$interiorFinish,$door,$frontFinish,$drawerBox,$glaze,$smallDrawerFront,$sheen,$largeDrawerFront,$hinge,$drawerGlides,$finishedEnd){
    //$sql = "select name from species where id = " & $species;
    $roomdata = "";
    $roomdata .= "<table class=\"table table-striped table-sm\" style=\"width:100%\"><tr>";
    $roomdata .= "<td>";
    $roomdata .= "Species: " . getStyleName("species",$species);
    $roomdata .= "</td>";
    $roomdata .= "<td>";
    $roomdata .= "Interior Finish: " . getStyleName("interiorFinish",$interiorFinish);
    $roomdata .= "</td>";
    $roomdata .= "</tr><tr>";
    $roomdata .= "<td>";
    $roomdata .= "Door: " . getStyleName("door",$door);
    $roomdata .= "</td>";
    $roomdata .= "<td>";
    $roomdata .= "Finish: " . getStyleName("frontFinish",$frontFinish);
    $roomdata .= "</td>";
    $roomdata .= "</tr><tr>";
    $roomdata .= "<td>";
    $roomdata .= "Drawer Box: " . getStyleName("drawerBox",$drawerBox);
    $roomdata .= "</td>";
    $roomdata .= "<td>";
    $roomdata .= "Glaze: " . getStyleName("glaze",$glaze);
    $roomdata .= "</td>";
    $roomdata .= "</tr><tr>";
    $roomdata .= "<td>";
    $roomdata .= "Small Drawer Front: " . getStyleName("smallDrawerFront",$smallDrawerFront);
    $roomdata .= "</td>";
    $roomdata .= "<td>";
    $roomdata .= "Sheen: " . getStyleName("sheen",$sheen);
    $roomdata .= "</td>";
    $roomdata .= "</tr><tr>";
    $roomdata .= "<td>";
    $roomdata .= "Large Drawer Front: " . getStyleName("largeDrawerFront",$largeDrawerFront);
    $roomdata .= "</td>";
    $roomdata .= "<td>";
    $roomdata .= "Hinge: " . getStyleName("hinge",$hinge);
    $roomdata .= "</td>";
    $roomdata .= "</tr><tr>";
    $roomdata .= "<td>";
    $roomdata .= "Drawer Glides: " . getStyleName("drawerGlides",$drawerGlides);
    $roomdata .= "</td>";
    $roomdata .= "<td>";
    $roomdata .= "Finished End: " . getStyleName("finishedEnd",$finishedEnd);
    $roomdata .= "</td>";
    $roomdata .= "</tr></table>";
    return $roomdata;
}
function getStyleName($table, $id){
    $sql = "select name from " . $table . " where id = " . $id;
    opendb2($sql);
    if($GLOBALS['$result2']->num_rows > 0){
        foreach ($GLOBALS['$result2'] as $row) {
            return $row['name'];
        }
    }
}

function itemUpdateConstraintsOK($table){
    //if size or qty W H D qty
    //assumes itemID is the id of the specific item in the given table.
    //assumes id is the value we were going to set.
    $msg = "";
    $sql = "";
    if(strcmp($_POST['column'],"W")==0){
        $sql = "select case 
        when " . $_POST['id'] . " < i.minW then \"Sorry, this width is below the minimum.\" 
        when i.maxW <> 0 and " . $_POST['id'] . " > i.maxW then \"Sorry, this width is beyond the maximum.\" 
        else \"ok\" end as allowed from " . $table . " i where i.id = " . $_POST['itemID'] . ";";
    }
    if(strcmp($_POST['column'],"H")==0){
        $sql = "select case
        when " . $_POST['id'] . " < i.minH then \"Sorry, this height is below the minimum.\"
        when i.maxH <> 0 and " . $_POST['id'] . " > i.maxH then \"Sorry, this height is beyond the maximum.\"
        else \"ok\" end as allowed from " . $table . " i where i.id = " . $_POST['itemID'] . ";";
    }
    if(strcmp($_POST['column'],"D")==0){
        $sql = "select case
        when " . $_POST['id'] . " < i.minD then \"Sorry, this depth is below the minimum.\"
        when i.maxD <> 0 and " . $_POST['id'] . " > i.maxD then \"Sorry, this depth is beyond the maximum.\"
        else \"ok\" end as allowed from " . $table . " i where i.id = " . $_POST['itemID'] . ";";
    }
    if(strcmp($_POST['column'],"qty")==0 && $table!="orderItemMods"){
        $sql = "select case
        when " . $_POST['id'] . " <> 1 and isCabinet = 1 then \"Sorry, the quantity of a cabinet must be 1 and more than 0.\"
        else \"ok\" end as allowed from " . $table . " i, item ii where i.id = " . $_POST['itemID'] . " and ii.id = i.iid;";
    }
    
    if(strcmp($sql,"")==0){
        $msg = "ok";
        return 1;
    }
	
    opendb($sql);
	if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            $msg = $row['allowed'];
        }
    }
    
    if(strcmp($msg,"ok")<>0){
        if(strcmp($table, "orderItemMods")==0 and strcmp($_POST['column'],"qty")==0){
			return 1;
        }else{
            echo $msg;
            return 0;
        }
    }else{
        return 1;
    }
    
    
}

?>
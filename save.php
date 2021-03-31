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
<?php 
include_once 'includes/db.php';
//include 'orderXMLCV.php'; for xml(ordx) file
//include 'orderCreation.php'; //for ord file
?>
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
}

if($_POST['mode'] == "deleteItem"){
    if($_POST['mod']>0){
        $sql = "delete from orderItemMods where id = " . $_POST['itemID'] . " and rid = " . $_POST['rid'];
    }else{
        $sql = "delete from orderItemMods where pid = " . $_POST['itemID'] . " and rid = " . $_POST['rid'];
        opendb($sql);
        $sql = "delete from orderItem where id = " . $_POST['itemID'] . " and rid = " . $_POST['rid'];
    }
    opendb($sql);
    

}
//echo $_POST['oid'];
if($_POST['mode'] == "saveEditedItem"){
    if(itemUpdateConstraintsOK("orderItem",$_POST['column'])==0){
        //header("HTTP/1.1 500 Internal Server Error");
    }else{
        if(strcmp($_POST['column'],'position')==0){
            //set new position to the item
            opendb("update orderItem set position=".$_POST['value']." where id=".$_POST['itemID']);
            //rearrange positions for room
            $sql = "select * from orderItem oi where rid =". $_POST['rid']." and id not in(".$_POST['itemID'].") order by position";
            $result = opendb($sql);
            $i=1;
            while($row = $result->fetch_assoc()){
                if(intval($_POST['value'])==$i)
                    $i++;
                opendb("update orderItem set position=".$i." where id=".$row['id']);
                $i++;                                
            }
            //recalculate position for all mods based on their parents
            $sql2 = "update orderItemMods oi set oi.position = (select oi2.position from orderItem oi2 where oi2.id = oi.pid) where rid=".$_POST['rid'];
            opendb($sql2);            
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
    //Update status
    $sql = "update mosOrder set dateSubmitted = now(), submittedBy=".$_SESSION["userid"].", state = '2', leadTime = (select currentLeadtime from settings) where oid = '" . $_POST['oid'] . "' and state = 1";
    opendb($sql);
    //Getting address
    $sql = "select * ,(select concat(coalesce(unit,' '),' ',street,' ',city,' ',province,' ',country,' ',postalCode)  from accountAddress aA where aA.id =mo.shipAddress) shipTo from mosOrder mo, mosUser mu, account a, cabinetLine cl where mo.mosUser = mu.id and mo.account = a.id and mo.CLid = cl.id and mo.oid = '" . $_POST['oid'] . "'";
    $result = opendb($sql);
    $row = $result->fetch_assoc();
    $accountName = $row['busDBA'];
    $accountId = $row['account'];
    $mailOID = $row['oid'];
    $CLfactor = $row['factor'];
    $orderType="";
    if($row['isPriority']==1)
        $orderType="table-warning";
    if($row['isWarranty']==1)
        $orderType="table-danger";
    if($row['CLid']==3)
        $orderType="table-primary";
    if($row['CLid']==2)
        $orderType="table-info";
    $msg = "<!DOCTYPE html>
    <html lang=\"en\">
    <head>
      <meta charset=\"utf-8\">
      <title>Thank you</title>
      <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
      <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1\" crossorigin=\"anonymous\">
    </head>
    <body>
        <nav class=\"navbar navbar-expand-lg navbar-light bg-light d-flex justify-content-between\">
            <a class=\"navbar-brand mx-5\" href=\"https://mobel.ca\"><img id=\"logo\" alt=\"logo\" src=\"https://mobel.ca/wp-content/uploads/2019/01/Logo.png\"/></a>
            <h3 class=\"mx-5\">Thank you <b>".$accountName."</b> for your order.</h3>
        </nav>  
        <div style=\"background: #4470c8\" class=\"mx-3\">&nbsp</div>
        <div class=\"bg-light container-fluid\">
            <div class=\"row py-3\">
                <div class=\"col-12 d-flex justify-content-center\">
                    <h4>Hello, ". $row['firstName'] . " " . $row['lastName'] .". We have recieved your order.</h4></br>
                </div>  
                <div class=\"col-12 d-flex justify-content-center\">  
                    <h5> For updates, please visit your home page on <a href=\"https://mos.mobel.ca/viewOrder.php\">MOS</a></h5>         
                </div>
            </div>
            <table class=\"table my-0\">
                <tr class=\"text-center ".$orderType."\">
                    <td><h4>Order ID: ".$mailOID."</h4></td>
                    <td><h4>Date Submitted: ". substr($row['dateSubmitted'],0,10) ."</h4></td>
                </tr>
            </table>
            <div style=\"height: 10px\" class=\"bg-dark mx-3\">&nbsp</div>
            <div class=\"row\">
                <div class=\"col-3 mx-auto\">
                    <p>
                        <strong>Phone Number: </strong>" . $row['phone'] . "</br>
                        <strong>Date Required: </strong>" . substr($row['dateRequired'],0,10) . "</br>
                    </p>
                </div>
                <div class=\"col-3 mx-auto\">
                    <p>
                        <strong>Tag Name: </strong>" . $row['tagName'] . "</br>
                        <strong>Ship To: </strong>";
                        if(strlen($row['shipTo'])>0){
                            $msg .= $row['shipTo']. "<br/>";
                        }else{
                            $msg .= "No address selected<br/>";
                        }                   
            $msg .= "</p>
                </div>
            </div>";
            //Rooms start here
            $sql = "select orr.rid,orr.name rname,sp.name spname,irf.name irfname,dd.name ddname,ff.name ffname,db.name dbname,gl.name glname,sdf.name sdfname,sh.name shname,ldf.name ldfname,h.name hname,dg.name dgname, fe.name fename
            from orderRoom orr,species sp,interiorFinish irf,door dd,frontFinish ff,drawerBox db,glaze gl,smallDrawerFront sdf,sheen sh,largeDrawerFront ldf,hinge h,drawerGlides dg,finishedEnd fe where orr.oid=".$mailOID." and orr.species=sp.id and orr.door=dd.id and orr.frontFinish=ff.id and orr.glaze=gl.id and orr.glaze=gl.id and orr.sheen=sh.id and orr.hinge=h.id and orr.smallDrawerFront=sdf.id and orr.largeDrawerFront=ldf.id and orr.drawerGlides=dg.id and orr.drawerBox=db.id and orr.interiorFinish=irf.id and orr.finishedEnd=fe.id order by orr.name";
            $result = opendb($sql);
            $totalOrder = 0;
            while($row = $result->fetch_assoc()){
                $roomTotal = 0;
                $msg .="<table class=\"table table-sm\">
                    <tr class=\"table-secondary\">
                        <td class=\"text-end\" colspan=\"2\"><h5>Room</h5></td>
                        <td class=\"text-start\" colspan=\"2\"><h5>".$row['rname']."</h5></td>
                    </tr>
                    <tr>
                        <td class=\"text-end\">Species:</td>
                        <td class=\"text-start\">".$row['spname']."</td>
                        <td class=\"text-end\">Interior Finish:</td>
                        <td class=\"text-start\">".$row['irfname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-end\">Door:</td>
                        <td class=\"text-start\">".$row['ddname']."</td>
                        <td class=\"text-end\">Finish:</td>
                        <td class=\"text-start\">".$row['ffname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-end\">Drawer Box:</td>
                        <td class=\"text-start\">".$row['dbname']."</td>
                        <td class=\"text-end\">Glaze:</td>
                        <td class=\"text-start\">".$row['glname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-end\">Small Drawer Front:</td>
                        <td class=\"text-start\">".$row['sdfname']."</td>
                        <td class=\"text-end\">Sheen:</td>
                        <td class=\"text-start\">".$row['shname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-end\">Large Drawer Front:</td>
                        <td class=\"text-start\">".$row['ldfname']."</td>
                        <td class=\"text-end\">Hinge:</td>
                        <td class=\"text-start\">".$row['hname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-end\">Drawer Glides:</td>
                        <td class=\"text-start\">".$row['dgname']."</td>
                        <td class=\"text-end\">Finished End:</td>
                        <td class=\"text-start\">".$row['fename']."</td>
                    </tr>
                </table>
                <table class=\"table table-sm\">
                    <thead>
                         <tr class=\"font-weight-bold text-center\">
                            <th>Item</th>
                            <th>Description</th>
                            <th>W</th>
                            <th>H</th>
                            <th>D</th>
                            <th>Qty</th>
                            <th>Hinged</th>
                            <th>F.E.</th>
                            <th>Note</th>";
                if($_SESSION["userType"]>1){
                    $msg .="<th>Price</th>";
                }
                            
                $msg .="</tr>
                    </thead>
                    <tbody>";

                $sql2 = "select * from (SELECT orr.rid,it.CLGroup,oi.description, oi.note, oi.id as item, 0 as sid,oi.position, oi.qty, oi.name, oi.price, oi.sizePrice, 0 as 'parentPercent', ds.factor as 'DFactor', irf.factor as 'IFactor', ff.factor as 'FFactor', ff.upcharge as 'FUpcharge', sh.factor as 'SFactor', gl.factor as 'GFactor', sp.finishedEndSizePrice as 'EFactor', (db.upcharge + dg.upcharge) as 'drawerCharge', sdf.upcharge as 'smallDrawerCharge', ldf.upcharge as 'largeDrawerCharge', oi.doorFactor as 'DApplies', oi.speciesFactor as 'SpeciesApplies', oi.interiorFactor as 'IApplies', oi.finishFactor as 'FApplies', oi.sheenFactor as 'SApplies', oi.glazeFactor as 'GApplies',oi.drawers, oi.smallDrawerFronts, oi.largeDrawerFronts, oi.H, oi.W, oi.D, oi.W2, oi.D2, oi.minSize, it.pricingMethod as methodID, oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight
            FROM  orderItem oi, orderRoom orr, doorSpecies ds, interiorFinish irf, item it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg, smallDrawerFront sdf, largeDrawerFront ldf, species sp
            WHERE it.id = oi.iid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = " .$row['rid']." 
            union all
                SELECT orr.rid,it.CLGroup,oi.description, oi.note, oi.pid,oi.id as sid, oi.position, oi.qty, oi.name, oi.price, oi.sizePrice, parentPercent, ds.factor as 'DFactor', irf.factor as 'IFactor', ff.factor as 'FFactor', ff.upcharge as 'FUpcharge', sh.factor as 'SFactor', gl.factor as 'GFactor', sp.finishedEndSizePrice as 'EFactor', (db.upcharge + dg.upcharge) as 'drawerCharge', sdf.upcharge as 'smallDrawerCharge', ldf.upcharge as 'largeDrawerCharge', oi.doorFactor as 'DApplies', oi.speciesFactor as 'SpeciesApplies', oi.interiorFactor as 'IApplies', oi.finishFactor as 'FApplies', oi.sheenFactor as 'SApplies', oi.glazeFactor as 'GApplies',oi.drawers, oi.smallDrawerFronts, oi.largeDrawerFronts, oi.H, oi.W, oi.D, oi.W2, oi.D2, oi.minSize, it.pricingMethod as methodID, oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight
            FROM  orderItemMods oi, orderRoom orr, doorSpecies ds, interiorFinish irf, itemMods it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg,  smallDrawerFront sdf, largeDrawerFront ldf, species sp
            WHERE it.id = oi.mid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = ".$row['rid'].") as T1 order by rid,position,item,sid";

                $result2 = opendb2($sql2);
                $i = 0;
                $si= 0;
                $parentID = -1;
                $isParent = -1;
                while($row2 = $result2->fetch_assoc()) {
                    if($parentID !== $row2['item']){ //new parent item
                        $parentID = $row2['item'];
                        $isParent = 1;
                        $parentPrice = 0;
                        $si = 0;
                        $i++;
                    }else{
                        opendb2("select price from item where id = (select iid from orderItem where id = " . $row2['item'] . ")");
                        foreach($GLOBALS['$result2'] as $row1){
                            $parentPrice = $row1['price'];
                        }
                        $isParent = 0;
                        $si = $si + 1;
                        //$i = $i - 1;
                    }
                    $hinging = "";
                    if($row2['hingeLeft']==1){
                        $hinging = "L";
                    }
                    if($row2['hingeRight']=="1"){
                        $hinging = "R";
                    }
                    if($row2['hingeLeft']=="1" && $row2['hingeRight'] =="1"){
                        $hinging = "B";
                    }
                    $finishedEnds = "";
                    if($row2['finishLeft']=="1"){
                        $finishedEnds = "L";
                    }
                    if($row2['finishRight']=="1"){
                        $finishedEnds = "R";
                    }
                    if($row2['finishLeft']=="1" && $row2['finishRight']=="1"){
                        $finishedEnds = "B";
                    }
                    $mixDoorSpeciesFactor = 0;
                    if($row2['DApplies'] == 1 || $row2['SpeciesApplies']==1){
                        $mixDoorSpeciesFactor = 1;
                    }else{
                        $mixDoorSpeciesFactor = 0;
                    }
                    $b=""; 
                    $be = "";
                    if($isParent===1){
                        $b = "<b>";
                        $be = "</b>";
                    }
                    $aPrice =  getPrice($row2['qty'],$row2['price'],$row2['sizePrice'],$parentPrice,$row2['parentPercent'],$row2['DFactor'],$row2['IFactor'],$row2['FFactor'],$row2['GFactor'],$row2['SFactor'],$row2['EFactor'],$row2['drawerCharge'],$row2['smallDrawerCharge'],$row2['largeDrawerCharge'], $mixDoorSpeciesFactor,$row2['IApplies'],$row2['FApplies'],$row2['GApplies'],$row2['SApplies'],$row2['drawers'],$row2['smallDrawerFronts'],$row2['largeDrawerFronts'],$row2['finishLeft']+$row2['finishRight'], $row2['H'],$row2['W'],$row2['D'],$row2['minSize'],$row2['methodID'],$row2['FUpcharge'],$CLfactor);
                    $roomFinishUpcharge=$row2['FUpcharge'];
                    if($isParent === 1){
                        $parentPrice = $aPrice;
                    }
                    $roomTotal += $aPrice;
                    $msg .="<tr class=\"text-center\">
                            <td>".$b.$i.".".$si.$be."</td>
                            <td>".$b.$row2['name'].$be."</td>
                            <td>".$b.(float)$row2['W'].$be."</td>
                            <td>".$b.(float)$row2['H'].$be."</td>
                            <td>".$b.(float)$row2['D'].$be."</td>
                            <td>".$b.(float)$row2['qty'].$be."</td>
                            <td>".$b.$hinging.$be."</td>
                            <td>".$b.$finishedEnds.$be."</td>
                            <td style=\"max-width: 450px;\">".$row2['note']."</td>";
                    if($_SESSION["userType"]>1){
                            $msg .= "<td><span title = \"" . getPrice($row2['qty'],$row2['price'],$row2['sizePrice'],$parentPrice,$row2['parentPercent'],$row2['DFactor'],$row2['IFactor'],$row2['FFactor'],$row2['GFactor'],$row2['SFactor'],$row2['EFactor'],$row2['drawerCharge'],$row2['smallDrawerCharge'],$row2['largeDrawerCharge'], $mixDoorSpeciesFactor,$row2['IApplies'],$row2['FApplies'],$row2['GApplies'],$row2['SApplies'],$row2['drawers'],$row2['smallDrawerFronts'],$row2['largeDrawerFronts'],$row2['finishLeft']+$row2['finishRight'], $row2['H'],$row2['W'],$row2['D'],$row2['minSize'],$row2['methodID'],$row2['FUpcharge'],$CLfactor,1) . "\">".$b. number_format($aPrice,2,'.','').$be."</span></td>";
                    }
                    $msg .= "</tr>";
                }
                if($_SESSION["userType"]>1){
                    $msg .= "<tr class=\"border-top border-dark\">
                                <td class=\"text-end\" colspan=\"9\"><h5>Room Total:</h5></td>
                                <td class=\"text-center\"><h5>$".$roomTotal."</h5></td>
                            </tr>";
                }
                $msg .="</tbody></table>";
                $totalOrder += $roomTotal+$roomFinishUpcharge;
            }
            if($_SESSION["userType"]>1){
                $msg .= "<div><h4 class=\"text-center\">Order Total: $$totalOrder pre HST & pre delivery</h4></div>";
            }
                $msg .="</div>
        <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js\" integrity=\"sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW\" crossorigin=\"anonymous\"></script>
    </body>
    </html>";
    //echo $msg;
    sendmail("fernando@mobel.ca; orders@mobel.ca; ".$_SESSION['email'], "Order ".$mailOID." Submitted - ".$accountName, $msg);
    //createORDX($_POST['oid'],$accountId);//Call function to create ordx file
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
    if((strcmp($_POST['column'],"W")==0) || (strcmp($_POST['column'],"W2")==0)){
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
    if((strcmp($_POST['column'],"D")==0) || (strcmp($_POST['column'],"D2")==0)){
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
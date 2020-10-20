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
if($_POST['mode']=="getFileModal"){
    

    //echo "<button class=\"btn btn-primary\">Add Order File</button><br/>";
    $oid = $_POST['oid'];
    $rid = $_POST['rid'];
    $iid = $_POST['iid'];
    $mid = $_POST['mid'];
    if($mid == 0){
        $mid = "NULL";
    }
    if($iid == 0){
        $iid = "NULL";
    }
    if($rid == 0){
        $rid = "NULL";
    }
    
    echo "<h2><span onClick = \"refreshFiles()\">";
    if(strcmp($rid,"NULL")==0){
        echo "Upload Order Files";
    }else if(strcmp($iid,"NULL")==0){
        echo "Upload Room Files";
    }else if(strcmp($mid,"NULL")==0){
        echo "Upload Item Files";
    }else{
        echo "Upload Mod Files";
    }
    
    echo "</span><button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button></h2>";
    //action="upload.php" method="post"
    //<input type="submit" value="Upload File" id="sendFile" name="sendFile">
    ?>
    <form enctype="multipart/form-data">
    Drag or browse to the file you wish to upload for this order:
        <div class="custom-file">
        <input type="file" class="custom-file-input d-none" name="fileToUpload" id="fileToUpload">
		<label class="custom-file-label" for="fileToUpload">Choose File</label>
    	</div>
    	<input type="button" class="btn btn-primary  ml-0" value="Upload File" id="sendFile" name="sendFile">
        <progress id="fileUploadProgress" value="0"></progress>
		
        <input type="hidden" value="<?php echo $oid?>" name="oid">
        <input type="hidden" value="<?php echo $rid?>" name="rid">
        <input type="hidden" value="<?php echo $iid?>" name="iid">
        <input type="hidden" value="<?php echo $mid?>" name="mid">
    </form>
    



    <?php     
    echo "<br/><b>List of Order Files:<input type=\"button\" class=\"btn btn-light p-1 \" value=\"Refresh Listing\" onClick = \"refreshFiles()\"></b><br/>";
    ?>
   
    <table id="FileList" class="display nowrap ml-0" style="width:100%">
    <thead>
          <tr>
            
            <th>File Name</th>
            <th>Room Name</th>
            <th>Item #</th>
            <th>Item Description</th>
            <th>File Tools</th>
          </tr>
    </thead>
    <tfoot>
      <tr>
        
        <th>File Name</th>
        <th>Room Name</th>
        <th>Item #</th>
        <th>Item Description</th>
        <th>File Tools</th>
      </tr>
    </tfoot><tbody>
    
    </tbody>
    </table>
 
    <?php
       
}


if($_POST['mode']=="getFiles"){
    $oid = $_POST['oid'];
    $rid = $_POST['rid'];
    $iid = $_POST['iid'];
    $mid = $_POST['mid'];
    if($mid == 0){
        $mid = "NULL";
    }
    if($iid == 0){
        $iid = "NULL";
    }
    if($rid == 0){
        $rid = "NULL";
    }
	
	$result = opendb2("select account from mosOrder where oid =".$_POST["oid"]);
	$row2 = mysqli_fetch_assoc($result);
	
    opendb("select * from orderFiles where oid = ".$oid." and rid is null");
    
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            echo "<tr>";
            //echo "<td>" . $row['oid'] . "</td>";
            echo "<td><b><form action=\"download.php\" method=\"post\"><input name=\"OGName\" type=\"hidden\" value=\"". $row['name'] . "\"></input><input name=\"DealerFile\" type=\"hidden\" value=\"". $row2["account"]."/".$_POST["oid"]."/" . $row['id'] . "." . strtolower(pathinfo($row['name'],PATHINFO_EXTENSION)). "\" ></input><input type=\"submit\" value=\"" . $row['name'] . "\"/></form></b></td>";
            echo "<td>" . "N/A" . "</td>";
            echo "<td>" . "N/A" . "</td>";
            echo "<td>" . "N/A" . "</td>";
            echo "<td>" . "<input type=\"submit\" value=\"Delete\" onClick=\"deleteFile(". $row['id'] . ");\">" . "</td>";
            echo "</tr>";
        }
    }
    
	opendb("select O.account, F.oid as oid, F.name as fileName, R.name as roomName, F.id as id from orderFiles F, orderRoom R, mosOrder O where F.iid is null and F.rid = R.rid and F.oid = ".$oid." and O.oid = F.oid");
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            echo "<tr>";
            //echo "<td><b>" . $row['oid'] . "</b></td>";
            echo "<td><b><form action=\"download.php\" method=\"post\"><input name=\"OGName\" type=\"hidden\" value=\"". $row['fileName'] . "\"></input><input name=\"DealerFile\" type=\"hidden\" value=\"". $row2['account']."/".$_POST["oid"]."/" . $row['id'] . "." . strtolower(pathinfo($row['fileName'],PATHINFO_EXTENSION)). "\" ></input><input type=\"submit\" value=\"" . $row['fileName'] . "\"/></form></b></td>";
            echo "<td>" . $row['roomName'] . "</td>";
            echo "<td>" . "N/A" . "</td>";
            echo "<td>" . "N/A" . "</td>";
            echo "<td>" . "<input type=\"submit\" value=\"Delete\" onClick=\"deleteFile(". $row['id'] . ");\">" . "</td>";
            echo "</tr>";
        }
    }
    opendb("select F.oid as oid, F.name as fileName, R.name as roomName, F.id as id, I.description as description, I.name as name, I.id as iid, null as mid,   F.rid as rid from orderItem I, orderFiles F, orderRoom R where F.mid is null and F.iid = I.id and F.rid = R.rid and F.oid = ".$oid."
union all
select F.oid as oid, F.name as fileName, R.name as roomName, F.id as id, M.description as description, M.name as name,  I.id as iid, F.mid as mid, F.rid as rid from orderItemMods M, orderItem I, orderFiles F, orderRoom R where M.id = F.mid and F.iid = I.id and F.rid = R.rid and F.oid = ".$oid."
order by rid, iid, mid");
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            echo "<tr>";
            //echo "<td><b>" . $row['oid'] . "</b></td>";
            echo "<td><b><form action=\"download.php\" method=\"post\"><input name=\"OGName\" type=\"hidden\" value=\"". $row['fileName'] . "\"></input><input name=\"DealerFile\" type=\"hidden\" value=\"". $row2["account"]."/".$_POST["oid"]."/" . $row['id'] . "." . strtolower(pathinfo($row['fileName'],PATHINFO_EXTENSION)). "\" ></input><input type=\"submit\" value=\"" . $row['fileName'] . "\"/></form></b></td>";
            echo "<td>" . $row['roomName'] . "</td>";
            if(is_null($row['mid'])){
                echo "<td>" . getItemID($row['rid'],$row['iid'],0) . "</td>";
            }else{
                echo "<td>" . getItemID($row['rid'],$row['mid'],1) . "</td>";
            }
            echo "<td>" . $row['description'] . " Code: " .$row['name'] . "</td>";
            
            echo "<td>" . "<input type=\"submit\" value=\"Delete\" onClick=\"deleteFile(". $row['id'] . ");\">" . "</td>";
            echo "</tr>";
        }
    }
}

if($_POST['mode']=="deleteFile"){
    opendb("delete from orderFiles where id = " . $_POST['id']);
}

if($_POST['mode']=="getNewItem"){
    $strArr = explode(" ",$_POST['filter']); //words to search
    $com = $_POST['com']; //and or or
    $type = $_POST['type']; //item or mod
    $aFilter = "(description like";
    
    if($_POST['startsWith']==1){
        $aFilter = $aFilter . " '" . $strArr[0];
    }else{
        $aFilter = $aFilter . " '%" . $strArr[0];
    }
    $aFilter = $aFilter . "%' ";
    
    
    if($_POST['startsWith']==1){
        for($i = 1; $i<count($strArr); $i++){
            $aFilter = $aFilter . $com . " description like '" . $strArr[$i] . "%' ";
        }
    }else{
        for($i = 1; $i<count($strArr); $i++){
            $aFilter = $aFilter . $com . " description like '%" . $strArr[$i] . "%' ";
        }
    }
    
    
    
    $aFilter = $aFilter . ") or ";
    
    if($_POST['startsWith']==1){
        $aFilter = $aFilter ."(name like '" . $strArr[0] . "%'";
    }else{
        $aFilter = $aFilter ."(name like '%" . $strArr[0] . "%'";
    }
    
    if($_POST['startsWith']==1){
        for($i = 1; $i<count($strArr); $i++){
            $aFilter = $aFilter . $com . " name like '" . $strArr[$i] . "%' ";
        }
    }else{
        for($i = 1; $i<count($strArr); $i++){
            $aFilter = $aFilter . $com . " name like '%" . $strArr[$i] . "%' ";
        }
    }
    
    $aFilter = $aFilter . ")";
	if($_POST['cabinetLine']=="undefined"){
		$CL = 1;//$CL = $_POST['cabinetLine'];
	}else{
		$CL = $_POST['cabinetLine'];
	} 
    if($type=="mod"){
        $sql = "select id,description, ifnull(w,'no width') as 'w',ifnull(h, 'no height') as 'h',ifnull(d,'no depth') as 'd',ifnull(w2,'no width') as 'w2',ifnull(h2, 'no height') as 'h2',ifnull(d2,'no depth') as 'd2',name from itemMods where (".$aFilter.") and CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CL.") order by description limit 150";
    }else{
        $sql = "select id,description, ifnull(w,'no width') as 'w',ifnull(h, 'no height') as 'h',ifnull(d,'no depth') as 'd',ifnull(w2,'no width') as 'w2',ifnull(h2, 'no height') as 'h2',ifnull(d2,'no depth') as 'd2',name from item     where (".$aFilter.") and CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CL.") order by description limit 150";
    }
    //echo $sql;
    opendb($sql);
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row){
            //the id returned is the id from the mod or items table (not the orders table)
            echo "<option onClick=\"setSizes(".$row['w'].",".$row['h'].",".$row['d'].",".$row['w2'].",".$row['h2'].",".$row['d2'].",'".$row['name']."',".$row['id'].");\" class=\"allItemList\" w=\"" . $row['w']. "\" h=\"" .$row['h']. "\" d=\"" .$row['d']. "\" value=\"" . $row['id'] . "\">". $row['description'] . " Code: " .$row['name']. "</option>";
        }
    }
}
/*Updates order details*/
if($_POST['mode']=="updateOrder"){
    if($_POST['id'] == "dateRequired"  && $_POST['isPriority'] == 0){
        opendb("select * from settings");
        $d1 = "2020-01-01";
        $LT = "2020-01-01";
        if($GLOBALS['$result']->num_rows > 0){
            foreach ($GLOBALS['$result'] as $row){
                $d1 = $_POST['value'];
                $LT = substr($row['currentLeadtime'],10);
                //echo $d1;
                //echo $LT;
                if(strcmp($LT,$d1) > 0){
                    //http_response_code(206);
                    die("Invalid date entered. Must be after the leadtime.");
                }
            }
        }
    }
    $fixPost = $_POST['id'];
    if(strcmp($_POST['id'],"OrderNote")==0){
        $fixPost = "note";
    }
    
    
    $sql = "update mosOrder set ".$fixPost." = '" . $_POST['value'] . "' where oid = " . $_POST['oid'];
    opendb($sql);
    $sql = "insert into trackSingleChange (id, uid, tableName, fieldName, notes) values (". $_POST['oid'] . "," . $_SESSION["userid"] . ", 'mosOrder','" .$fixPost."','" . $_POST['value'] . "')";
    opendb($sql);
    //http_response_code(200);
    echo "success";
}

/*Updates user details*/
if($_POST['mode']=="updateUser"){
    $sql = "update mosUser set ".$_POST['id']." = '" . $_POST['value'] . "' where email = '" . $_SESSION['username'] . "'";
    opendb($sql);
}
/*Updates password*/
if($_POST['mode']=="updatePassword"){
    $sql = "update mosUser set pw = '" . $_POST['pw2'] . "' where '" . $_POST['pw'] . "' = pw and '" . $_POST['pw2'] . "' = '" . $_POST['pw3'] . "' and email = '" . $_SESSION['username'] . "'";
    opendb($sql);
    if($GLOBALS['$conn']->affected_rows<>1){
        var_dump(http_response_code(204));
    }
}


/* Responds with the item listing for use in the items area of the orderitem page */
if($_POST['mode']=="getItems"){
    $TotalPrice = 0.00;
    if(isset($_POST['rid'])){
        $RID = $_POST['rid'];
    }else{
        $RID = -1;
    }
    
	$sql = "select CLid from mosOrder where oid = ".$_POST['oid'];
	$result = opendb2($sql);

	$CLid = $result->fetch_assoc();
	//echo $CLid['CLid'];

    /*
    $doorfactor = 1.0;
    opendb("select factor from doorSpecies ds, orderRoom o where o.door = ds.did and o.species = ds.sid and rid = ". $RID);
    if($GLOBALS['$result'] <> ""){
        foreach ($GLOBALS['$result'] as $row) {
            $doorfactor = $row['factor'];
        }
    }
    
    $interiorfactor = 1.0;
    opendb("select factor from interiorFinish i, orderRoom o where rid = ". $RID ." and o.interiorFinish = i.id");
    if($GLOBALS['$result'] <> ""){
        foreach ($GLOBALS['$result'] as $row) {
            $interiorfactor = $row['factor'];
        }
    }
   
   
    opendb("select oi.*, case when round(price*(1.0+ (". $doorfactor . "-0.0 )*doorFactor  + ". $interiorfactor . "*interiorFactor),2) = 0 then 'NA' else qty*round( price*(1.0 + (". $doorfactor . " - 0.0)*doorFactor + ". $interiorfactor . "*interiorFactor),2) end as formattedPrice from orderItem oi where rid = '" .$RID. "' order by position,id asc");
   
    */
    
    //opendb("select oi.*, case when round(price*(1.0+ (". $doorfactor . "-0.0 )*doorFactor  + ". $interiorfactor . "*interiorFactor),2) = 0 then 'NA' else qty*round( price*(1.0 + (". $doorfactor . " - 0.0)*doorFactor + ". $interiorfactor . "*interiorFactor),2) end as formattedPrice from orderItem oi where rid = '" .$RID. "' order by position,id asc");
    $SQL = "select * from (SELECT oi.description, oi.note, oi.id as item, 0 as sid, oi.qty, oi.name, oi.price, oi.sizePrice, 0 as 'parentPercent', ds.factor as 'DFactor', irf.factor as 'IFactor', ff.factor as 'FFactor', ff.upcharge as 'FUpcharge', sh.factor as 'SFactor', gl.factor as 'GFactor', sp.finishedEndSizePrice as 'EFactor', (db.upcharge + dg.upcharge) as 'drawerCharge', sdf.upcharge as 'smallDrawerCharge', ldf.upcharge as 'largeDrawerCharge', oi.doorFactor as 'DApplies', oi.speciesFactor as 'SpeciesApplies', oi.interiorFactor as 'IApplies', oi.finishFactor as 'FApplies', oi.sheenFactor as 'SApplies', oi.glazeFactor as 'GApplies',oi.drawers, oi.smallDrawerFronts, oi.largeDrawerFronts, oi.H, oi.W, oi.D, oi.W2, oi.D2, oi.minSize, it.pricingMethod as methodID, oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight
    FROM  orderItem oi, orderRoom orr, doorSpecies ds, interiorFinish irf, item it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg, smallDrawerFront sdf, largeDrawerFront ldf, species sp
    WHERE it.id = oi.iid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = '" .$RID. "' and it.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CLid['CLid'].")
    union all
                           SELECT oi.description, oi.note, oi.pid,oi.id as sid,     oi.qty, oi.name, oi.price, oi.sizePrice, parentPercent       , ds.factor as 'DFactor', irf.factor as 'IFactor', ff.factor as 'FFactor', ff.upcharge as 'FUpcharge', sh.factor as 'SFactor', gl.factor as 'GFactor', sp.finishedEndSizePrice as 'EFactor', (db.upcharge + dg.upcharge) as 'drawerCharge', sdf.upcharge as 'smallDrawerCharge', ldf.upcharge as 'largeDrawerCharge', oi.doorFactor as 'DApplies', oi.speciesFactor as 'SpeciesApplies', oi.interiorFactor as 'IApplies', oi.finishFactor as 'FApplies', oi.sheenFactor as 'SApplies', oi.glazeFactor as 'GApplies',oi.drawers, oi.smallDrawerFronts, oi.largeDrawerFronts, oi.H, oi.W, oi.D, oi.W2, oi.D2, oi.minSize, it.pricingMethod as methodID, oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight
    FROM  orderItemMods oi, orderRoom orr, doorSpecies ds, interiorFinish irf, itemMods it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg,  smallDrawerFront sdf, largeDrawerFront ldf, species sp
    WHERE it.id = oi.mid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = '" .$RID. "' and it.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CLid['CLid'].")) as T1 order by item,sid";


	//echo $SQL;
    opendb($SQL);
    
    echo "<div class=\"col-12 container\">";
    if($GLOBALS['$result']->num_rows > 0){
		$tableRow = 0;//count for every row in order to identify every price column to be printable
        ?>
        <input id="#orderTotal" type="hidden"></input>
        <table id="itemListingTable" class="table table-striped table-sm" style="width:100%">
		<!-- display nowrap table-striped table-hover -->
        <thead >
              <tr>
                <th class="font-weight-bold">Item</th>
                <th class="font-weight-bold">Description</th>
                <th class="font-weight-bold" title="Width">W</th>
                <th class="font-weight-bold" title="Height">H</th>
                <th class="font-weight-bold" title="Depth">D</th>
                <th class="font-weight-bold" title="Quantity">Qty</th>
                <th class="font-weight-bold">Hinged</th>
                <th class="font-weight-bold" title="Finished End (B for Both, R for Right, L for Left)">F.E.</th>
                <th class="font-weight-bold">Note</th>
                <?php if($_SESSION["userType"]>1){
                	?><th id="priceCol<?php echo $tableRow ?>" class="d-print-none font-weight-bold">Price</th><?php
                }?>
                <th></th>
              </tr>
            </thead>
            <tbody class="col-sm-12">
              <?php
        $i = 1;
        $si= 0;
        $parentPrice = 0;
        $parentID = -1;
        $isParent = -1;
        $roomFinishUpcharge = 0;
        foreach ($GLOBALS['$result'] as $row) {
			$tableRow += 1;
            if($parentID !== $row['item']){ //new parent item
                $parentID = $row['item'];
                $isParent = 1;
                $parentPrice = 0;
                $si = 0;
            }else{
                opendb2("select price from item where id = (select iid from orderItem where id = " . $row['item'] . ")");
                foreach($GLOBALS['$result2'] as $row2){
                    $parentPrice = $row2['price'];
                }
                //echo $row['item'];
                //closedb2();
                $isParent = 0;
                $si = $si + 1;
                $i = $i - 1;
            }
            
            echo "";
            $tdStyle = "<td class=\"borderless\">";	
			$tdStyleNotPrint = "<td id=\"priceCol".$tableRow."\" class=\"d-print-none font-weight-bold\">";		
            if($isParent===1){
                echo "<tr class=\"font-weight-bold\">";
                $tdStyle = "<td class=\"font-weight-bold\">";
            }else{
                echo "<tr class=\"table-sm\">";
            }
            echo $tdStyle . $i . "." . $si . "</td>";
            echo $tdStyle . "<span title=\"". str_replace("\"","inch",$row['description'])."\">" . $row['name'] . "</span>" . "</td>";
			echo $tdStyle . (float)$row['W'] ;
			if ((float)$row['W2']>0)
				echo ", ".(float)$row['W2'];
			echo "</td>";
            echo $tdStyle . (float)$row['H'] . "</td>";
            echo $tdStyle . (float)$row['D'];
			if ((float)$row['D2']>0)
				echo ", ".(float)$row['D2'];
			echo "</td>";
            echo $tdStyle . (float)$row['qty'] . "</td>";
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
            echo $tdStyle . $hinging . "</td>";
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
            echo $tdStyle . $finishedEnds . "</td>";
            
            //echo $tdStyle . $row['finishRight'] . "</td>";
            
			$minWidth ="style=\"max-width:300px\"";
            echo $tdStyle;
            if(strlen(str_replace("\"","\\\"",$row['note']))>=1){
                //echo "<span title='Note: ". $row['note'] ."' onClick='alert(\"" . str_replace("\"","\\\"",$row['note']) . "\");'>Y</span>";
				echo "<p style=\"width: 200px;\" class=\"d-print-none mx-auto\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"". $row['note'] ."\">". $row['note'] ."</p>";
				echo "<h5 class=\"print\">" . $row['note']."</h5>";
            }else{
                echo str_replace("\"","\\\"",$row['note']);
            }
            echo "</td>";
			$minWidth ="";
            $mixDoorSpeciesFactor = 0;
            if($row['DApplies'] == 1 || $row['SpeciesApplies']==1){
                $mixDoorSpeciesFactor = 1;
            }else{
                $mixDoorSpeciesFactor = 0;
            }
            $aPrice = getPrice($row['qty'],$row['price'],$row['sizePrice'],$parentPrice,$row['parentPercent'],$row['DFactor'],$row['IFactor'],$row['FFactor'],$row['GFactor'],$row['SFactor'],$row['EFactor'],$row['drawerCharge'],$row['smallDrawerCharge'],$row['largeDrawerCharge'],  $mixDoorSpeciesFactor,$row['IApplies'],$row['FApplies'],$row['GApplies'],$row['SApplies'],$row['drawers'],$row['smallDrawerFronts'],$row['largeDrawerFronts'],$row['finishLeft']+$row['finishRight'], $row['H'],$row['W'],$row['D'],$row['minSize'],$row['methodID'],$row['FUpcharge']);
            $roomFinishUpcharge=$row['FUpcharge'];
//                      getPrice($qty, $base, $sizePrice, $parentPrice, $parentPercent,                           $DFactor,$IFactor,            $FFactor,$GFactor,$SFactor,                   $drawerCharge,$smallDrawerCharge,$largeDrawerCharge,                           $DApplies, $IApplies,            $FApplies, $GApplies, $SApplies, $drawers,                         $smallDrawerFronts,$largeDrawerFronts, $H, $W, $D, $minSize,  $methodID){
            if($isParent === 1){
                $parentPrice = $aPrice;
            }
            if($_SESSION["userType"]>1){
                $TotalPrice = $TotalPrice + $aPrice;
                echo $tdStyleNotPrint . number_format($aPrice,2,'.','') . "</td>";            
            }
            if($isParent === 1){
                echo "<td >" . "<button type=\"button\" onClick=editItems(".$row['item'].",0) class=\"btn btn-primary pl-3 pr-3 btn-sm editbutton\" data-toggle=\"modal\" title=\"Edit\" data-target=\"#editItemModal\"><span class=\"ui-icon ui-icon-pencil \"></span></button>" . "";
                //if($_SERVER['HTTP_REFERER']=="https://mos.mobel.ca/Order2.php?OID=1"){
                    echo "<button type=\"button\" title=\"Add Mod\" onclick=\"var promise = new Promise(function(resolve,reject){cleanEdit();resolve();}); promise.then(function(){\$('#editOrderItemPID').val(". $parentID .");$('#editItemTitle').text('Edit/Delete Mod');});\" class=\"btn btn-primary btn-sm editbutton btn-primary ml-0 pl-3 pr-3\" data-toggle=\"modal\" data-target=\"#editItemModal\"><span class=\"ui-icon ui-icon-circle-plus\"></button>";
                    
                //}else{
                //    echo "<button type=\"button\" onclick=\"allItems('allItems','modItems',". $parentID .");\" class=\"btn btn-primary btn-sm editbutton\" data-toggle=\"modal\" data-target=\"#addItemModal\"><span class=\"ui-icon ui-icon-circle-plus\"></button></td>";
                //}
                //echo "" . "<button type=\"button\" onClick=addModItems(".$row['item'].") class=\"btn btn-primary btn-sm editbutton\" data-toggle=\"modal\" title=\"Add a modification or accessory\" data-target=\"#addModModal\"><span class=\"ui-icon ui-icon-circle-plus\"></span></button>" . "</td>";
                    echo "<button class=\"btn btn-primary pl-3 pr-3 btn-sm ml-0 editbutton\" data-toggle=\"modal\" data-target=\"#fileModal\" type=\"button\" onClick=\"loadFiles(".$_POST['oid'] . ",$('a.nav-link.roomtab.active').attr('value'),".$parentID.");\"><span class=\"ui-icon  ui-icon-disk\"></span></button></td>";
                    
            }else{
                //echo "<td >" . "<button type=\"button\" onClick=editItems(".$row['item'].",". $row['sid'] .") class=\"btn btn-primary btn-sm editbutton\" data-toggle=\"modal\" title=\"Edit\" data-target=\"#editItemModal\"><span class=\"ui-icon ui-icon-pencil\"></span></button>" . "";
                //echo "<td>" . "<span class=\"td ui-icon ui-icon-pencil\"> <button type=\"button\" onClick=editItems(".$row['item'].",". $row['sid'] .") class=\"btn btn-primary btn-sm\" data-toggle=\"modal\" title=\"Edit\" data-target=\"#editItemModal\"></button></span>" . "";
                echo $tdStyle ."&nbsp;&nbsp;<span onClick=\"cleanEdit();$('#editOrderItemPID').val(". $parentID ."); editItems(".$row['item'].",". $row['sid'] .");\" data-toggle=\"modal\" title=\"Edit\" data-target=\"#editItemModal\" class=\"btn ml-0 btn-primary pl-2 pr-2 pt-1 pb-1 btn-sm\"><span class=\"ui-icon ui-icon-pencil btn-primary pl-2 pr-2\" ></span></span>";
                echo "<button class=\"btn btn-primary pl-2 pr-2 pt-1 pb-1 ml-1 btn-sm editbutton \" data-toggle=\"modal\" data-target=\"#fileModal\" type=\"button\" onClick=\"loadFiles(".$_POST['oid'] . ",$('a.nav-link.roomtab.active').attr('value'),".$parentID.",". $row['sid'] .");\"><span class=\"ui-icon ui-icon-disk\"></span></button></td>";
                
            }
            
            //function getPrice($qty, $base, $sizePrice, $parentPrice, $parentPercent,$DFactor,$IFactor, $DApplies, $IApplies, $H, $W, $D, $minDim,  $methodID){
            echo "</tr>";
            $i = $i + 1;
        }
        ?>
        <!--  <tfoot>
              <tr>
                <th>Item#</th>
                <th>Description</th>
                <th>H</th>
                <th>W</th>
                <th>D</th>
                <th>Qty</th>
                <th>Hinged</th>
                <th>F.E.</th>
                <th>Price</th>
                <th></th>
              </tr>
            </tfoot>-->
        </table>
        <?php
    }else{
    	echo "No items yet, or you may have chosen a species and door style that are not compatible with each other. Please ensure you have also chosen an Interior Finish. <br/>Please add items or remove this room.";
    }
    if($TotalPrice > 0.01){
        $TotalPrice = $TotalPrice + $roomFinishUpcharge;
        //echo "<input type=/"hidden/">Total room price: $" . number_format($TotalPrice,2,'.','') .  "</b>";
        echo "<input type=\"hidden\" id=\"TotalPrice\" value=\"" . number_format($TotalPrice,2,'.','') ."\">";
    }
}

if($_POST['mode'] == "addItem"){
    $sql = "select * from item where id = " . $_POST['id'];

    opendb($sql);
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            $sql = "insert into orderItem (iid,position,rid,name, description,qty,price,sizePrice,minSize,W,H,D,W2,H2,D2,minW,minH,minD,maxW,maxH,maxD,doorFactor,speciesFactor,finishFactor,interiorFactor,sheenFactor,glazeFactor,drawers,smallDrawerFronts,largeDrawerFronts,hingeLeft,hingeRight,finishLeft,finishRight) values
                                             (" . "".$_POST['id']."," . "0," . $_POST['rid'] . ",'" . $row['name'] . "','" . $row['description'] . "'," . "1" . "," . $row['price'] . "," . $row['sizePrice'] . ",'" . $row['minSize'] . "'," . $row['W'] . "," . $row['H'] . "," . $row['D'] . "," . $row['W2'] . "," . $row['H2'] ."," . $row['D2'] ."," . $row['minW'] ."," . $row['minH'] ."," . $row['minD'] ."," . $row['maxW'] ."," . $row['maxH'] ."," . $row['maxD'] ."," . $row['doorFactor'] ."," . $row['speciesFactor'] ."," . $row['finishFactor'] . "," . $row['interiorFactor'] ."," . $row['sheenFactor'] ."," . $row['glazeFactor'] ."," . $row['drawers'] ."," . $row['smallDrawerFronts'] ."," . $row['largeDrawerFronts'] . "," . "0" . "," . "0" . "," . "0" . "," . "0" . ")";
            //                               (" . "".$_POST['id']."," . "0," . $_POST['rid'] . ",'" . $row['name'] . "','" . $row['description'] . "'," . "1" . "," . $row['price'] . "," . $row['sizePrice'] . ",'" . $row['minSize'] . "'," . $row['W'] . "," . $row['H'] . "," . $row['D'] . "," . $row['W2'] . "," . $row['H2'] ."," . $row['D2'] ."," . $row['minW'] ."," . $row['minH'] ."," . $row['minD'] ."," . $row['maxW'] ."," . $row['maxH'] ."," . $row['maxD'] ."," . $row['doorFactor'] ."," . $row['speciesFactor'] ."," . $row['finishFactor'] . "," . $row['interiorFactor'] ."," . $row['sheenFactor'] ."," . $row['glazeFactor'] ."," . $row['drawers'] ."," . $row['smallDrawerFronts'] ."," . $row['largeDrawerFronts'] . "," . "0" . "," . "0" . "," . "0" . "," . "0" . ")";

            opendb($sql);
        }
        //echo $sql;
    }
    opendb("select max(id) as orderItemID from orderItem where rid = " . $_POST['rid']);
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            echo $row['orderItemID'];
        }
    }
}










if($_POST['mode'] == "editItem"){
    $sql = "select * from item where id = " . $_POST['id'];
    
    opendb($sql);
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            $sql = "update orderItem set iid=" . "".$_POST['id'].",position=" . "0,rid=" . $_POST['rid'] . ", name='" . $row['name'] . "', 
description='" . $row['description'] . "', qty=" . "1" . " ,price= " . $row['price'] . ",sizePrice= " . $row['sizePrice'] . ",
minSize= " . $row['minSize'] . ",W= " . $row['W'] . ",H=" . $row['H'] . " ,D=" . $row['D'] . " ,W2=" . $row['W2'] . " ,H2=" . $row['H2'] . " ,
D2=" . $row['D2'] . " ,minW= " . $row['minW'] .",minH= " . $row['minH'] .",minD= " . $row['minD'] .",maxW= " . $row['maxW'] .",maxH= " . $row['maxH'] .",
maxD= " . $row['maxD'] .",doorFactor= " . $row['doorFactor'] .",speciesFactor= ". $row['speciesFactor'] ." ,finishFactor= " . $row['finishFactor'] . ", 
interiorFactor= " . $row['interiorFactor'] .", sheenFactor=" . $row['sheenFactor'] ." ,glazeFactor= " . $row['glazeFactor'] .",
drawers=" . $row['drawers'] ." ,smallDrawerFronts=" . $row['smallDrawerFronts'] ." , largeDrawerFronts=" . $row['largeDrawerFronts'] . " ,
hingeLeft= " . "0" . ",hingeRight= " . "0" . ",finishLeft= " . "0" . ",finishRight= " . "0" . " where id = " . $_POST['myid'];
    
            //                               (" . "".$_POST['id']."," . "0," . $_POST['rid'] . ",'" . $row['name'] . "','" . $row['description'] . "'," . "1" . "," . $row['price'] . "," . $row['sizePrice'] . ",'" . $row['minSize'] . "'," . $row['W'] . "," . $row['H'] . "," . $row['D'] . "," . $row['W2'] . "," . $row['H2'] ."," . $row['D2'] ."," . $row['minW'] ."," . $row['minH'] ."," . $row['minD'] ."," . $row['maxW'] ."," . $row['maxH'] ."," . $row['maxD'] ."," . $row['doorFactor'] ."," . $row['speciesFactor'] ."," . $row['finishFactor'] . "," . $row['interiorFactor'] ."," . $row['sheenFactor'] ."," . $row['glazeFactor'] ."," . $row['drawers'] ."," . $row['smallDrawerFronts'] ."," . $row['largeDrawerFronts'] . "," . "0" . "," . "0" . "," . "0" . "," . "0" . ")";
            
            opendb($sql);
        }
        //echo $sql;
    }
}

if($_POST['mode'] == "editMod"){
    $sql = "select * from itemMods where id = " . $_POST['id'];

    opendb($sql);
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            //$sql = "update orderItemMods (pid,mid,position,rid,name, description,qty,price,sizePrice,minSize,W,H,D,W2,H2,D2,minW,minH,minD,maxW,maxH,maxD,doorFactor,speciesFactor,finishFactor,interiorFactor,sheenFactor,glazeFactor,drawers,smallDrawerFronts,largeDrawerFronts,hingeLeft,hingeRight,finishLeft,finishRight) values
              //                               (" . "".$_POST['pid']. "," . $_POST['id']."," . "0," . $_POST['rid'] . ",'" . $row['name'] . "','" . $row['description'] . "'," . "1" . "," . $row['price'] . "," . $row['sizePrice'] . ",'" . $row['minSize'] . "'," . $row['W'] . "," . $row['H'] . "," . $row['D'] . "," . $row['W2'] . "," . "0" ."," . "0" ."," . $row['minW'] ."," . "0" ."," . "0" ."," . "0" ."," . "0" ."," . "0" ."," . $row['doorFactor'] ."," . $row['speciesFactor'] ."," . $row['finishFactor'] . "," . $row['interiorFactor'] ."," . $row['sheenFactor'] ."," . $row['glazeFactor'] ."," . $row['drawers'] ."," . $row['smallDrawerFronts'] ."," . $row['largeDrawerFronts'] . "," . "0" . "," . "0" . "," . "0" . "," . "0" . ")";
            
            
            $sql = "update orderItemMods set pid = " . $_POST['pid'] . ", mid=" . "".$_POST['id'].",position=" . "0,rid=" . $_POST['rid'] . ", name='" . $row['name'] . "',
description='" . $row['description'] . "', qty=" . "1" . " ,price= " . $row['price'] . ",sizePrice= " . $row['sizePrice'] . ",
minSize= " . $row['minSize'] . ",W= " . $row['W'] . ",H=" . $row['H'] . " ,D=" . $row['D'] . " ,W2=" . $row['W2'] . " ,H2=" . $row['H2'] . " ,
D2=" . $row['D2'] . " ,minW= " . $row['minW'] .",minH= " . $row['minH'] .",minD= " . $row['minD'] .",maxW= " . $row['maxW'] .",maxH= " . $row['maxH'] .",
maxD= " . $row['maxD'] .",doorFactor= " . $row['doorFactor'] .",speciesFactor= ". $row['speciesFactor'] ." ,finishFactor= " . $row['finishFactor'] . ",
interiorFactor= " . $row['interiorFactor'] .", sheenFactor=" . $row['sheenFactor'] ." ,glazeFactor= " . $row['glazeFactor'] .",
drawers=" . $row['drawers'] ." ,smallDrawerFronts=" . $row['smallDrawerFronts'] ." , largeDrawerFronts=" . $row['largeDrawerFronts'] . " ,
hingeLeft= " . "0" . ",hingeRight= " . "0" . ",finishLeft= " . "0" . ",finishRight= " . "0" . " where id = " . $_POST['myid'];
            opendb($sql);
            
        }
    }
}









if($_POST['mode'] == "addMod"){
    $sql = "select * from itemMods where id = " . $_POST['id'];
    
    opendb($sql);
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            $sql = "insert into orderItemMods (pid,mid,position,rid,name, description,qty,price,sizePrice,minSize,W,H,D,W2,H2,D2,minW,minH,minD,maxW,maxH,maxD,doorFactor,speciesFactor,finishFactor,interiorFactor,sheenFactor,glazeFactor,drawers,smallDrawerFronts,largeDrawerFronts,hingeLeft,hingeRight,finishLeft,finishRight) values
                                             (" . "".$_POST['pid']. "," . $_POST['id']."," . "0," . $_POST['rid'] . ",'" . $row['name'] . "','" . $row['description'] . "'," . "1" . "," . $row['price'] . "," . $row['sizePrice'] . ",'" . $row['minSize'] . "'," . $row['W'] . "," . $row['H'] . "," . $row['D'] . "," . $row['W2'] . "," . "0" ."," . "0" ."," . $row['minW'] ."," . "0" ."," . "0" ."," . "0" ."," . "0" ."," . "0" ."," . $row['doorFactor'] ."," . $row['speciesFactor'] ."," . $row['finishFactor'] . "," . $row['interiorFactor'] ."," . $row['sheenFactor'] ."," . $row['glazeFactor'] ."," . $row['drawers'] ."," . $row['smallDrawerFronts'] ."," . $row['largeDrawerFronts'] . "," . "0" . "," . "0" . "," . "0" . "," . "0" . ")";
            opendb($sql);
            
        }
    }
    
    opendb("select max(id) as orderModItemID from orderItemMods where pid = " . $_POST['pid'] . " and rid = " . $_POST['rid']);
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            echo $row['orderModItemID'];
        }
    }
}

if($_POST['mode'] == "allItems"){
    echo "<option>Choose an item</option>";

    if($_POST['mid']>0){
        $sql = "select id,description, ifnull(w,'no width') as 'w',ifnull(h, 'no height') as 'h',ifnull(d,'no depth') as 'd',name from itemMods where description not like '%chicken%' order by description";
    }else{
        $sql = "select id,description, ifnull(w,'no width') as 'w',ifnull(h, 'no height') as 'h',ifnull(d,'no depth') as 'd',name from item     where description not like '%chicken%' order by description";
    }
    opendb($sql);
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            //echo "<option class=\"allItemList\" w=\"" . $row['w']. "\" h=\"" .$row['h']. "' d=\"" .$row['d']. "\" value=\"" . $row['id'] . "\">". $row['description'] . " " . (float)$row['w']. " " . ($row['h']+0.0). " " . ($row['d']+0.0) . " Name:" .$row['name']. "</option>";
            echo "<option class=\"allItemList\" w=\"" . $row['w']. "\" h=\"" .$row['h']. "' d=\"" .$row['d']. "\" value=\"" . $row['id'] . "\">". $row['description'] . " Code: " .$row['name']. "</option>";
        }
    }

}

if($_POST['mode'] == "editItemGetDetails"){
    $thisTable = "orderItem";
    $idField = "iid";
    if($_POST['mod']>0){
        $idField = "mid";
        $thisTable = "orderItemMods";
        $_POST['itemID'] = $_POST['mod'];
    }
    $sql = "select " . $idField . " as iid, id,description, qty, price, minSize, doorFactor,interiorFactor, hingeLeft, hingeRight, finishLeft, finishRight,ifnull(w,'no width') as 'w',ifnull(h, 'no height') as 'h',ifnull(d,'no depth') as 'd',ifnull(w2,'no width') as 'w2',ifnull(h2, 'no height') as 'h2',ifnull(d2,'no depth') as 'd2',name, note from ". $thisTable ." where id = " . $_POST['itemID'];
    
    //echo "<option>Choose an item</option>";
    opendb($sql);
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            echo "{\"iid\":\"" . $row['iid']. "\",\"name\":\"" . $row['name']. "\",\"note\":\"" . str_replace("\"","\\\"",$row['note']). "\",\"description\":\"" . str_replace("\"","\\\"",$row['description']) . "\",\"w\":\"" . $row['w']. "\",\"h\":\"" . $row['h']. "\",\"d\":\"" . $row['d']. "\",\"w2\":\"" . $row['w2'] ."\",\"h2\":\"" . $row['h2']."\",\"d2\":\"" . $row['d2']."\",\"qty\":\"" . $row['qty']. "\",\"minSize\":\"" . $row['minSize']. "\",\"hingeLeft\":\"" . $row['hingeLeft']. "\",\"hingeRight\":\"" . $row['hingeRight']. "\",\"finishLeft\":\"" . $row['finishLeft']. "\",\"finishRight\":\"" . $row['finishRight']. "\",\"id\":\"" . $row['id']. "\"}";
            //\"fieldname\":\"fieldvalue\",
            //echo "<option class=\"allItemList\" w=\"" . $row['w']. "\" h=\"" .$row['h']. "' d=\"" .$row['d']. "\" value=\"" . $row['id'] . "\">". $row['description'] . $row['id'] . " " . $row['w']. " " .$row['h']. " " .$row['d']. " Name:" .$row['name']. "</option>";
        }
    }
}
/*
 * Given a source oid, rid and/or id, copy it to the destination oid, or rid.
 * Options: include items, include og id (for service only - also include attached files) (always include mods)
 * If oid and no rid, copy all rooms
 * 
 * To accomodate the batch insert, the fromX is populated with a random number between 1 and 99999 followed by the original id.
 * Using this code, the newly created room or item can be found to link to the lower level table
 * 
 */
if($_POST['mode'] == "copy"){
    $insertID = mt_rand(1,99999);
    $orderFields = fieldList("mosOrder");
    $roomFields = fieldList("orderRoom");
    $itemFields = fieldList("orderItem");
    $modFields = fieldList("orderItemMods");
    
    echo $insertID;
    echo $_POST['mode'];
    echo $_POST['oid'];
    echo $_POST['rid'];
    echo $_POST['id'];
    echo $_POST['Doid'];
    echo $_POST['Drid'];
    copyItem($_POST['rid'],$_POST['id'],$_POST['Drid']);
    //get all field names from order, room, item, mod. Skip first field (id field). 
    //contruct SQL to copy from each area
}

if($_POST['mode'] == "resetOrder"){
	$sql = "update orderRoom set door=null, species=9, edge=null, frontFinish=null, glaze=13, sheen=7, hinge=1, smallDrawerFront=4, largeDrawerFront=4, drawerGlides=3, drawerBox=5, interiorFinish=15, finishedEnd=2  where oid =".$_POST['oid'];
	//echo $sql;
	opendb($sql);
}

function copyItem($Sroom, $Sitem, $Droom){
    //copies item Sitem in room Sroom to Droom.
    $myList = fieldList("orderItem");
    $sql = "insert into orderItem (". $myList .")
    select ". $myList ."
    from orderItem
    where id = " . $Sitem;
    echo $sql;
    $myList = fieldList("orderItem");
    
    $sql = "SELECT LAST_INSERT_ID()";
    echo $sql;
    echo "<br/>";
    echo $GLOBALS['$conn']->insert_id;
    
    $sql = "insert into your_table (". $myList .")
    select ". $myList ."
    from orderItem
    where id = " . Sitem;
    
    echo $sql;
    
}

function copyMod(){
    //copies 
}


/* 
 * Returns a comma seperated list of all of the fields in a given table.
 */
function fieldList($tableName){
    $Fields = "";
    opendb("SELECT COLUMN_NAME  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = 'dqnrmrwrfh' AND TABLE_NAME = '".$tableName."'");
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            $Fields = $Fields . $row['COLUMN_NAME'] . ",";
        }
    }
    return substr($Fields,0,$strlen($Fields)-1);
}




?>
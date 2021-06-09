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
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if($_POST['mode']=="getFileModal"){
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
            <th></th>
            <th>File Name</th>
            <th>Room Name</th>
            <th>Item #</th>
            <th>Item Description</th>
            <th>File Tools</th>
          </tr>
    </thead>
    <tfoot>
      <tr>
        <th></th>
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
	//Getting account for the path
	$result = opendb2("select account from mosOrder where oid =".$_POST["oid"]);
	$row2 = mysqli_fetch_assoc($result);
	
    //Creating HTML for Order Files
    opendb("select * from orderFiles where oid = ".$oid." and rid is null");
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            echo "<tr>";
                echo "<td><a href=\"#\" onclick=\"window.open('uploads/DealerFiles/".$row2['account']."/".$_POST["oid"]."/" . $row['id'] . "." . strtolower(pathinfo($row['name'],PATHINFO_EXTENSION))."', '_blank', 'fullscreen=yes'); return false;\">View</a></td>";
                echo "<td><b><form action=\"download.php\" method=\"post\"><input name=\"OGName\" type=\"hidden\" value=\"". $row['name'] . "\"></input><input name=\"DealerFile\" type=\"hidden\" value=\"". $row2["account"]."/".$_POST["oid"]."/" . $row['id'] . "." . strtolower(pathinfo($row['name'],PATHINFO_EXTENSION)). "\" ></input><input type=\"submit\" value=\"" . $row['name'] . "\"/></form></b></td>";
                echo "<td>" . "N/A" . "</td>";
                echo "<td>" . "N/A" . "</td>";
                echo "<td>" . "N/A" . "</td>";
                echo "<td>" . "<input type=\"submit\" value=\"Delete\" onClick=\"deleteFile(". $row['id'] . ");\">" . "</td>";
            echo "</tr>";
        }
    }
    
    //Creating HTML for Room Files
	opendb("select O.account, F.oid as oid, F.name as fileName, R.name as roomName, F.id as id from orderFiles F, orderRoom R, mosOrder O where F.iid is null and F.rid = R.rid and F.oid = ".$oid." and O.oid = F.oid");
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            echo "<tr>";
                echo "<td><a href=\"#\" onclick=\"window.open('uploads/DealerFiles/".$row2['account']."/".$_POST["oid"]."/" . $row['id'] . "." . strtolower(pathinfo($row['fileName'],PATHINFO_EXTENSION))."', '_blank', 'fullscreen=yes'); return false;\">View</a></td>";
                echo "<td><b><form action=\"download.php\" method=\"post\"><input name=\"OGName\" type=\"hidden\" value=\"". $row['fileName'] . "\"></input><input name=\"DealerFile\" type=\"hidden\" value=\"". $row2['account']."/".$_POST["oid"]."/" . $row['id'] . "." . strtolower(pathinfo($row['fileName'],PATHINFO_EXTENSION)). "\" ></input><input type=\"submit\" value=\"" . $row['fileName'] . "\"/></form></b></td>";
                echo "<td>" . $row['roomName'] . "</td>";
                echo "<td>" . "N/A" . "</td>";
                echo "<td>" . "N/A" . "</td>";
                echo "<td>" . "<input type=\"submit\" value=\"Delete\" onClick=\"deleteFile(". $row['id'] . ");\">" . "</td>";
            echo "</tr>";
        }
    }

    //Creating HTML for Item and Mod Files
    opendb("select O.account, F.oid as oid, F.name as fileName, R.name as roomName, F.id,F.iid,F.mid, COALESCE((SELECT oim.name FROM orderItemMods oim where oim.id = F.mid),I.name) as name, I.position from orderFiles F, orderRoom R, mosOrder O, orderItem I where I.rid = R.rid and F.iid is not null /*and F.mid is null*/ and F.iid = I.id and F.rid = R.rid and F.oid = ".$oid." and O.oid = F.oid order by roomName, iid, position asc");
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            echo "<tr>";
                echo "<td><a href=\"#\" onclick=\"window.open('uploads/DealerFiles/".$row2['account']."/".$_POST["oid"]."/" . $row['id'] . "." . strtolower(pathinfo($row['fileName'],PATHINFO_EXTENSION))."', '_blank', 'fullscreen=yes'); return false;\">View</a></td>";
                echo "<td><b><form action=\"download.php\" method=\"post\"><input name=\"OGName\" type=\"hidden\" value=\"". $row['fileName'] . "\"></input><input name=\"DealerFile\" type=\"hidden\" value=\"". $row2["account"]."/".$_POST["oid"]."/" . $row['id'] . "." . strtolower(pathinfo($row['fileName'],PATHINFO_EXTENSION)). "\" ></input><input type=\"submit\" value=\"" . $row['fileName'] . "\"/></form></b></td>";
                echo "<td>" . $row['roomName'] . "</td>";
                echo "<td>".$row['position'].".0</td>";
                echo "<td>".$row['name']."</td>";
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
		$CL = $_SESSION["defaultCLid"];//$CL = $_POST['cabinetLine'];
	}else{
		$CL = $_POST['cabinetLine'];
	} 
    if($type=="mod"){
        $sql = "select id,description, ifnull(w,'no width') as 'w',ifnull(h, 'no height') as 'h',ifnull(d,'no depth') as 'd',ifnull(w2,'no width') as 'w2',ifnull(h2, 'no height') as 'h2',ifnull(d2,'no depth') as 'd2',name from itemMods where (".$aFilter.") and CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CL.") and (visible is null or visible=1) order by description limit 150";
    }else{
        $sql = "select id,description, ifnull(w,'no width') as 'w',ifnull(h, 'no height') as 'h',ifnull(d,'no depth') as 'd',ifnull(w2,'no width') as 'w2',ifnull(h2, 'no height') as 'h2',ifnull(d2,'no depth') as 'd2',name from item     where (".$aFilter.") and CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CL.") and (visible is null or visible=1) order by description limit 150";
    }
    //echo $sql;
    opendb($sql);
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row){
            //the id returned is the id from the mod or items table (not the orders table)
            echo "<option class = \"highlight\" onClick=\"setSizes(".$row['w'].",".$row['h'].",".$row['d'].",".$row['w2'].",".$row['h2'].",".$row['d2'].",'".$row['name']."','".htmlspecialchars($row['description'])."',".$row['id'].");\" class=\"allItemList\" w=\"" . $row['w']. "\" h=\"" .$row['h']. "\" d=\"" .$row['d']. "\" value=\"" . $row['id'] . "\">". $row['description'] . " Code: " .$row['name']. "</option>";
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
    //This updates who is going to be invoiced if service. In this case set as null is not a service to revert if previously was selected.
    if(isset($_POST['isPriority']) ){
        if($_POST['isPriority']==0)  
            opendb("update mosOrder set invoiceTo=null where oid=".$_POST['oid']);
    }
    
    $sql = "update mosOrder set ".$fixPost." = '" . $_POST['value'] . "' where oid = " . $_POST['oid'];
    opendb($sql);
    if($fixPost=='state'){
        //If there is an update to the state, date is saved
        //When the order is sent back the delivery date(scheduled date) is cleared (states 1,2,3,4)
        switch($_POST['value']){
            case 1:
            case 2:
            case 3:
                $sql = "update mosOrder set deliveryDate = null where oid=" . $_POST['oid'];
                break;                
            case 4://State updated to Confirmed
                $sql = "update mosOrder set dateConfirmed = NOW(),deliveryDate = null where oid=" . $_POST['oid'];
                break;
            case 5://State updated to Detailed and production ready
                $sql = "update mosOrder set dateDetailed = NOW(), detailedBy=".$_SESSION["userid"]." where oid=" . $_POST['oid'];
                break;
            case 6://State updated to Quality Checked and Completed
                $sql = "update mosOrder set dateCompleted = NOW(), completedBy=".$_SESSION["userid"]." where oid=" . $_POST['oid'];
                break;
            case 7://State updated to Shipped
                $sql = "update mosOrder set dateShipped = NOW() where oid=" . $_POST['oid'];
                break;
            case 8://State updated to Invoiced
                $sql = "update mosOrder set dateInvoiced = NOW() where oid=" . $_POST['oid'];
                break;
            case 9://State updated to Paid
                $sql = "update mosOrder set datePaid = NOW() where oid=" . $_POST['oid'];
                break;
        }
        opendb($sql);
    }
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
    //getting order cabinet line and cabiner group to identify whether item is compatible 
	$sql = "select cl.factor, mo.CLid,clg.CLGid  from mosOrder mo, cabinetLine cl,cabinetLineGroups clg where mo.CLid = cl.id and cl.id = clg.CLid and mo.oid = ".$_POST['oid'];
	$result = opendb2($sql);
	$CLgroups = array();
    while ($row = $result->fetch_assoc()){
        array_push($CLgroups, $row['CLGid']);
        $CLid = $row['CLid'];
        $CLfactor = $row['factor'];
    }
    
	    
    /*$SQL = "select * from (SELECT oi.description, oi.note, oi.id as item, 0 as sid,oi.position, oi.qty, oi.name, oi.price, oi.sizePrice, 0 as 'parentPercent', ds.factor as 'DFactor', irf.factor as 'IFactor', ff.factor as 'FFactor', ff.upcharge as 'FUpcharge', sh.factor as 'SFactor', gl.factor as 'GFactor', sp.finishedEndSizePrice as 'EFactor', (db.upcharge + dg.upcharge) as 'drawerCharge', sdf.upcharge as 'smallDrawerCharge', ldf.upcharge as 'largeDrawerCharge', oi.doorFactor as 'DApplies', oi.speciesFactor as 'SpeciesApplies', oi.interiorFactor as 'IApplies', oi.finishFactor as 'FApplies', oi.sheenFactor as 'SApplies', oi.glazeFactor as 'GApplies',oi.drawers, oi.smallDrawerFronts, oi.largeDrawerFronts, oi.H, oi.W, oi.D, oi.W2, oi.D2, oi.minSize, it.pricingMethod as methodID, oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight
    FROM  orderItem oi, orderRoom orr, doorSpecies ds, interiorFinish irf, item it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg, smallDrawerFront sdf, largeDrawerFront ldf, species sp
    WHERE it.id = oi.iid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = '" .$RID. "' and it.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CLid['CLid'].")
    union all
                           SELECT oi.description, oi.note, oi.pid,oi.id as sid, oi.position, oi.qty, oi.name, oi.price, oi.sizePrice, parentPercent       , ds.factor as 'DFactor', irf.factor as 'IFactor', ff.factor as 'FFactor', ff.upcharge as 'FUpcharge', sh.factor as 'SFactor', gl.factor as 'GFactor', sp.finishedEndSizePrice as 'EFactor', (db.upcharge + dg.upcharge) as 'drawerCharge', sdf.upcharge as 'smallDrawerCharge', ldf.upcharge as 'largeDrawerCharge', oi.doorFactor as 'DApplies', oi.speciesFactor as 'SpeciesApplies', oi.interiorFactor as 'IApplies', oi.finishFactor as 'FApplies', oi.sheenFactor as 'SApplies', oi.glazeFactor as 'GApplies',oi.drawers, oi.smallDrawerFronts, oi.largeDrawerFronts, oi.H, oi.W, oi.D, oi.W2, oi.D2, oi.minSize, it.pricingMethod as methodID, oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight
    FROM  orderItemMods oi, orderRoom orr, doorSpecies ds, interiorFinish irf, itemMods it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg,  smallDrawerFront sdf, largeDrawerFront ldf, species sp
    WHERE it.id = oi.mid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = '" .$RID. "' and it.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CLid['CLid'].")) as T1 order by position,item,sid";
    */
    $SQL = "select * from (SELECT it.CLGroup,oi.description, oi.note, oi.id as item, 0 as sid,oi.position, oi.qty, oi.name, oi.price, oi.sizePrice, 0 as 'parentPercent', ds.factor as 'DFactor', irf.factor as 'IFactor', ff.factor as 'FFactor', ff.upcharge as 'FUpcharge', sh.factor as 'SFactor', gl.factor as 'GFactor', sp.finishedEndSizePrice as 'EFactor', (db.upcharge + dg.upcharge) as 'drawerCharge', sdf.upcharge as 'smallDrawerCharge', ldf.upcharge as 'largeDrawerCharge', oi.doorFactor as 'DApplies', oi.speciesFactor as 'SpeciesApplies', oi.interiorFactor as 'IApplies', oi.finishFactor as 'FApplies', oi.sheenFactor as 'SApplies', oi.glazeFactor as 'GApplies',oi.drawers, oi.smallDrawerFronts, oi.largeDrawerFronts, oi.H, oi.W, oi.D, oi.W2, oi.D2, oi.minSize, it.pricingMethod as methodID, oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight
    FROM  orderItem oi, orderRoom orr, doorSpecies ds, interiorFinish irf, item it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg, smallDrawerFront sdf, largeDrawerFront ldf, species sp
    WHERE it.id = oi.iid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = " .$RID. " 
    union all
        SELECT it.CLGroup,oi.description, oi.note, oi.pid,oi.id as sid, oi.position, oi.qty, oi.name, oi.price, oi.sizePrice, parentPercent       , ds.factor as 'DFactor', irf.factor as 'IFactor', ff.factor as 'FFactor', ff.upcharge as 'FUpcharge', sh.factor as 'SFactor', gl.factor as 'GFactor', sp.finishedEndSizePrice as 'EFactor', (db.upcharge + dg.upcharge) as 'drawerCharge', sdf.upcharge as 'smallDrawerCharge', ldf.upcharge as 'largeDrawerCharge', oi.doorFactor as 'DApplies', oi.speciesFactor as 'SpeciesApplies', oi.interiorFactor as 'IApplies', oi.finishFactor as 'FApplies', oi.sheenFactor as 'SApplies', oi.glazeFactor as 'GApplies',oi.drawers, oi.smallDrawerFronts, oi.largeDrawerFronts, oi.H, oi.W, oi.D, oi.W2, oi.D2, oi.minSize, it.pricingMethod as methodID, oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight
    FROM  orderItemMods oi, orderRoom orr, doorSpecies ds, interiorFinish irf, itemMods it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg,  smallDrawerFront sdf, largeDrawerFront ldf, species sp
    WHERE it.id = oi.mid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = ".$RID.") as T1 order by position,item,sid";

	//echo $SQL;
    opendb($SQL);
    
    echo "<div class=\"col-12 container\">";
    if($GLOBALS['$result']->num_rows > 0){
		$tableRow = 0;//count for every row in order to identify every price column to be printable
        ?>
        <input id="#orderTotal" type="hidden"></input>
        <div class="table-responsive">
        <table id="itemListingTable" class="table table-striped table-sm" style="width:100%">
		<!-- display nowrap table-striped table-hover -->
        <thead>
              <tr>
                <th style="width: 93px;" class="font-weight-bold">Item</th>
                <th style="width: 250px;" class="font-weight-bold">Description</th>
                <th style="width: 60px;" class="font-weight-bold" title="Width">W</th>
                <th class="font-weight-bold" title="Height">H</th>
                <th class="font-weight-bold" title="Depth">D</th>
                <th class="font-weight-bold" title="Quantity">Qty</th>
                <th class="font-weight-bold">Hinged</th>
                <th class="font-weight-bold" title="Finished End (B for Both, R for Right, L for Left)">F.E.</th>
                <th class="font-weight-bold">Note</th>
                <?php if($_SESSION["userType"]>1){
                	?><th class="d-print-none font-weight-bold priceCol">Price</th><?php
                }?>
                <th></th>
              </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $si= 0;
            $parentPrice = 0;
            $parentID = -1;
            $isParent = -1;
            $roomFinishUpcharge = 0;
            foreach ($GLOBALS['$result'] as $row) {
                /*update position new functionality*/
                if($row['position']==0)
                    updatePos($RID);
                /*-----------------------------------*/
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
                    $isParent = 0;
                    $si = $si + 1;
                    $i = $i - 1;
                }
            
                echo "";
                $tdStyle = "<td class=\"borderless\">";	
    			$tdStyleNotPrint = "<td class=\"d-print-none font-weight-bold\">";
                /*Warning for items not compatible*/
                $warning = "";
                if(!in_array($row['CLGroup'],$CLgroups))
                    $warning = " table-danger";
                
                if($isParent===1){
                    echo "<tr class=\"font-weight-bold".$warning."\">";
                    $tdStyle = "<td class=\"font-weight-bold\">";
                }else{
                    echo "<tr class=\"table-sm".$warning."\">";
                }
			    if($isParent===1){
                    echo $tdStyle . "<select id=\"Position".$i."\" class=\"form-control\" onchange=\"$('#editItemTitle').text('Edit/Delete Item');$('#editOrderItemID').val(".$row['item']."); saveEditedItem('Position".$i."','position');\">";
                    echo getSelectControl($_POST['rid'], $i);
                    echo "</select></td>";
                }else{
                    echo $tdStyle . $i . "." . $si . "</td>";
                }
			    echo $tdStyle . "<span title=\"". str_replace("\"","inch",$row['description'])."\">" . $row['name']; 
                if($_SESSION["userType"]<3)
                     echo "<label class=\"print\">".str_replace("\"","inch",$row['description'])."</label>"; 
                echo "</span>" . "</td>";
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
            
    			$minWidth ="style=\"max-width:300px\"";
                echo $tdStyle;
                if(strlen(str_replace("\"","\\\"",$row['note']))>=1){
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
                $aPrice = getPrice($row['qty'],$row['price'],$row['sizePrice'],$parentPrice,$row['parentPercent'],$row['DFactor'],$row['IFactor'],$row['FFactor'],$row['GFactor'],$row['SFactor'],$row['EFactor'],$row['drawerCharge'],$row['smallDrawerCharge'],$row['largeDrawerCharge'],  $mixDoorSpeciesFactor,$row['IApplies'],$row['FApplies'],$row['GApplies'],$row['SApplies'],$row['drawers'],$row['smallDrawerFronts'],$row['largeDrawerFronts'],$row['finishLeft']+$row['finishRight'], $row['H'],$row['W'],$row['D'],$row['minSize'],$row['methodID'],$row['FUpcharge'],$CLfactor);
                $roomFinishUpcharge=$row['FUpcharge'];

                if($isParent === 1){
                    $parentPrice = $aPrice;
                }
                if($_SESSION["userType"]>1){
                    $TotalPrice = $TotalPrice + $aPrice;
                    echo "<td class=\"d-print-none font-weight-bold priceCol\">";
                    if($_SESSION["userType"]>=3){
                        echo "<span title = \"" . getPrice($row['qty'],$row['price'],$row['sizePrice'],$parentPrice,$row['parentPercent'],$row['DFactor'],$row['IFactor'],$row['FFactor'],$row['GFactor'],$row['SFactor'],$row['EFactor'],$row['drawerCharge'],$row['smallDrawerCharge'],$row['largeDrawerCharge'],  $mixDoorSpeciesFactor,$row['IApplies'],$row['FApplies'],$row['GApplies'],$row['SApplies'],$row['drawers'],$row['smallDrawerFronts'],$row['largeDrawerFronts'],$row['finishLeft']+$row['finishRight'], $row['H'],$row['W'],$row['D'],$row['minSize'],$row['methodID'],$row['FUpcharge'],$CLfactor,1) . "\">" ;
                    }
                    if(in_array($row['CLGroup'],$CLgroups))
                        echo number_format($aPrice,2,'.','');
                    if($_SESSION["userType"]>=3){
                        echo "</span>";
                    }
                    echo "</td>";            
                }
			    echo $tdStyleNotPrint; 
                if($isParent === 1){                
    				echo "<button type=\"button\" onClick=editItems(".$row['item'].",0) class=\"btn btn-primary pl-3 pr-3 btn-sm editbutton\" data-toggle=\"modal\" title=\"Edit\" data-target=\"#editItemModal\">
                        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-pencil text-dark\" viewBox=\"0 0 16 16\">
                        <path d=\"M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z\"/>
                    </svg></button>";
    				echo "<button type=\"button\" title=\"Add Mod\" onclick=\"var promise = new Promise(function(resolve,reject){\$('#editItemTitle').text('Edit/Delete Mod');cleanEdit();resolve();}); promise.then(function(){\$('#editOrderItemPID').val(". $parentID .");});\" class=\"btn btn-primary btn-sm editbutton btn-primary ml-0 pl-3 pr-3\" data-toggle=\"modal\" data-target=\"#editItemModal\">
                        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-plus-square text-dark\" viewBox=\"0 0 16 16\">
                            <path d=\"M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z\"/>
                            <path d=\"M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z\"/>
                        </svg>
                    </button>";
    				echo "<button class=\"btn btn-primary pl-3 pr-3 btn-sm ml-0 editbutton\" data-toggle=\"modal\" title=\"Add files\" data-target=\"#fileModal\" type=\"button\" onClick=\"loadFiles(".$_POST['oid'] . ",$('a.nav-link.roomtab.active').attr('value'),".$parentID.");\">
                        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-folder-plus text-dark\" viewBox=\"0 0 16 16\">
                            <path d=\"m.5 3 .04.87a1.99 1.99 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2zm5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19c-.24 0-.47.042-.683.12L1.5 2.98a1 1 0 0 1 1-.98h3.672z\"/>
                            <path d=\"M13.5 10a.5.5 0 0 1 .5.5V12h1.5a.5.5 0 1 1 0 1H14v1.5a.5.5 0 1 1-1 0V13h-1.5a.5.5 0 0 1 0-1H13v-1.5a.5.5 0 0 1 .5-.5z\"/>
                        </svg>
                    </button>";
    				echo "<button class=\"btn btn-primary btn-sm editbutton btn-primary ml-0 pl-3 pr-3\" data-toggle=\"tooltip\" title=\"Copy item to the end\" onclick=\"copyItemRow(".$row['item'].")\">
                        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-clipboard text-dark\" viewBox=\"0 0 16 16\">
                            <path d=\"M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z\"/>
                            <path d=\"M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z\"/>
                        </svg>
                    </button>";
                    echo "<button class=\"btn btn-primary btn-sm editbutton btn-primary ml-0 pl-3 pr-3\" data-toggle=\"tooltip\" title=\"Delete item\" onclick=\"deleteItemShort(".$row['item'].")\">
                        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-trash text-dark\" viewBox=\"0 0 16 16\">
                            <path d=\"M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z\"/>
                            <path fill-rule=\"evenodd\" d=\"M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z\"/>
                        </svg>
                    </button>";
                }else{
                    echo "&nbsp;&nbsp;<span onClick=\"cleanEdit();$('#editOrderItemPID').val(". $parentID ."); editItems(".$row['item'].",". $row['sid'] .");\" data-toggle=\"modal\" title=\"Edit\" data-target=\"#editItemModal\" class=\"btn btn-primary pl-3 pr-3 btn-sm editbutton\">
                        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"13\" height=\"13\" fill=\"currentColor\" class=\"bi bi-pencil text-dark\" viewBox=\"0 0 16 16\">
                            <path d=\"M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z\"/>
                        </svg>
                    </span>";
                    echo "<button class=\"btn btn-primary pl-3 pr-3 btn-sm editbutton\" data-toggle=\"modal\" data-target=\"#fileModal\" type=\"button\" onClick=\"loadFiles(".$_POST['oid'] . ",$('a.nav-link.roomtab.active').attr('value'),".$parentID.",". $row['sid'] .");\">
                        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-folder-plus text-dark\" viewBox=\"0 0 16 16\">
                            <path d=\"m.5 3 .04.87a1.99 1.99 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2zm5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19c-.24 0-.47.042-.683.12L1.5 2.98a1 1 0 0 1 1-.98h3.672z\"/>
                            <path d=\"M13.5 10a.5.5 0 0 1 .5.5V12h1.5a.5.5 0 1 1 0 1H14v1.5a.5.5 0 1 1-1 0V13h-1.5a.5.5 0 0 1 0-1H13v-1.5a.5.5 0 0 1 .5-.5z\"/>
                        </svg>
                    </button>";
                }
    			echo "</td>";
    			echo "</tr>";
                $i = $i + 1;
            }
        ?>
        </tbody>
    </table>
    </div>
    <?php
    }else{
    	echo "No items yet, or you may have chosen a species and door style that are not compatible with each other. Please ensure you have also chosen an Interior Finish. <br/>Please add items or remove this room.";
    }
    if($TotalPrice > 0.01){
        $TotalPrice = $TotalPrice + $roomFinishUpcharge;
        echo "<input type=\"hidden\" id=\"TotalPrice\" value=\"" . number_format($TotalPrice,2,'.','') ."\">";
    }
}

if($_POST['mode'] == "addItem"){
    $sql = "select * from item where id = " . $_POST['id'];

    opendb($sql);
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            $sql = "insert into orderItem (iid,position,rid,name, description,qty,price,sizePrice,minSize,W,H,D,W2,H2,D2,minW,minH,minD,maxW,maxH,maxD,doorFactor,speciesFactor,finishFactor,interiorFactor,sheenFactor,glazeFactor,drawers,smallDrawerFronts,largeDrawerFronts,hingeLeft,hingeRight,finishLeft,finishRight) values
                                             (" . "".$_POST['id']."," . "(select max(position)+1 from orderItem oi2 where oi2.rid = ".$_POST['rid'].")," . $_POST['rid'] . ",'" . $row['name'] . "','" . $row['description'] . "'," . "1" . "," . $row['price'] . "," . $row['sizePrice'] . ",'" . $row['minSize'] . "'," . $row['W'] . "," . $row['H'] . "," . $row['D'] . "," . $row['W2'] . "," . $row['H2'] ."," . $row['D2'] ."," . $row['minW'] ."," . $row['minH'] ."," . $row['minD'] ."," . $row['maxW'] ."," . $row['maxH'] ."," . $row['maxD'] ."," . $row['doorFactor'] ."," . $row['speciesFactor'] ."," . $row['finishFactor'] . "," . $row['interiorFactor'] ."," . $row['sheenFactor'] ."," . $row['glazeFactor'] ."," . $row['drawers'] ."," . $row['smallDrawerFronts'] ."," . $row['largeDrawerFronts'] . "," . "0" . "," . "0" . "," . "0" . "," . "0" . ")";
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
            $sql = "update orderItem set iid=" . "".$_POST['id'].",rid=" . $_POST['rid'] . ", name='" . $row['name'] . "', 
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
            
            
            $sql = "update orderItemMods set pid = " . $_POST['pid'] . ", mid=" . "".$_POST['id'].",rid=" . $_POST['rid'] . ", name='" . $row['name'] . "',
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
                                             (" . "".$_POST['pid']. "," . $_POST['id']."," . "(select position from orderItem oi where id =".$_POST['pid'].")," . $_POST['rid'] . ",'" . $row['name'] . "','" . $row['description'] . "'," . "1" . "," . $row['price'] . "," . $row['sizePrice'] . ",'" . $row['minSize'] . "'," . $row['W'] . "," . $row['H'] . "," . $row['D'] . "," . $row['W2'] . "," . "0" ."," . "0" ."," . $row['minW'] ."," . "0" ."," . "0" ."," . "0" ."," . "0" ."," . "0" ."," . $row['doorFactor'] ."," . $row['speciesFactor'] ."," . $row['finishFactor'] . "," . $row['interiorFactor'] ."," . $row['sheenFactor'] ."," . $row['glazeFactor'] ."," . $row['drawers'] ."," . $row['smallDrawerFronts'] ."," . $row['largeDrawerFronts'] . "," . "0" . "," . "0" . "," . "0" . "," . "0" . ")";
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
    $sql = "select " . $idField . " as iid, id,description, qty, price, minSize, doorFactor,interiorFactor, hingeLeft, hingeRight, finishLeft, finishRight,ifnull(w,'no width') as 'w',ifnull(h, 'no height') as 'h',ifnull(d,'no depth') as 'd',ifnull(w2,'no width') as 'w2',ifnull(h2, 'no height') as 'h2',ifnull(d2,'no depth') as 'd2',name, note, position from ". $thisTable ." where id = " . $_POST['itemID'];
    
    //echo "<option>Choose an item</option>";
    opendb($sql);
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            echo "{\"iid\":\"" . $row['iid']. "\",\"name\":\"" . $row['name']. "\",\"note\":\"" . str_replace("\"","\\\"",$row['note']). "\",\"description\":\"" . str_replace("\"","\\\"",$row['description']) . "\",\"w\":\"" . $row['w']. "\",\"h\":\"" . $row['h']. "\",\"d\":\"" . $row['d']. "\",\"w2\":\"" . $row['w2'] ."\",\"h2\":\"" . $row['h2']."\",\"d2\":\"" . $row['d2']."\",\"qty\":\"" . $row['qty']. "\",\"minSize\":\"" . $row['minSize']. "\",\"hingeLeft\":\"" . $row['hingeLeft']. "\",\"hingeRight\":\"" . $row['hingeRight']. "\",\"finishLeft\":\"" . $row['finishLeft']. "\",\"finishRight\":\"" . $row['finishRight']. "\",\"id\":\"" . $row['id']. "\",\"position\":\"" . $row['position']. "\" }";
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
	$sql = "update orderRoom set door=null, species=null, frontFinish=null, interiorFinish=null where oid =".$_POST['oid'];
	//echo $sql;
	opendb($sql);
}

if ($_POST['mode'] == "switchUser"){
	$sql = "update mosOrder set mosUser= ".$_POST['newUser']." where oid =".$_POST['oid'];
	echo $sql;
	opendb($sql);
}

if($_POST['mode'] == "existOID"){
	if(strlen($_POST['oid'])>0){
		$admin = "";
		if($_SESSION["userType"]==2){
			$admin = "or m.account = " . $_SESSION["account"];
		}
		$sql = "select count(1) exist from mosOrder m, mosUser u where m.oid = ".$_POST['oid']." and m.mosUser = u.id and (u.email = '" . $_SESSION["username"] . "' ". $admin ."  )";
		$result = opendb($sql);
		$exist = $result->fetch_assoc();
		echo $exist['exist'];
	}else{
		echo "0";
	}
}

if($_POST['mode'] == "getOrderItemsforCopy"){
    $CL = 0;
    if(isset($_POST['CLid'])){
        $CL = $_POST['CLid'];
    }else{
        $CL = $_SESSION["defaultCLid"];
    }
	$sql = "select * from (SELECT orr.rid,orr.name orName, it.id as itemID, oi.id as orderItemID,0 as sid, oi.name, oi.description, oi.note, oi.W, oi.H, oi.D, oi.W2, oi.D2, if(oi.hingeLeft=0,'','L') HL,if(oi.hingeRight=0,'','R') HR,if(oi.finishLeft=0,'','L') FL,if(oi.finishRight=0,'','R') FR
    FROM  orderItem oi, orderRoom orr, item it
    WHERE it.id = oi.iid and oi.rid = orr.rid and orr.oid = '" .$_POST['oid']. "' and (it.visible = 1 or it.visible is null) and it.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CL.")
    union all
	SELECT orr.rid,orr.name, it.id, oi.pid,oi.id as sid, oi.name, oi.description, oi.note, oi.W, oi.H, oi.D, oi.W2, oi.D2, if(oi.hingeLeft=0,'','L') HL,if(oi.hingeRight=0,'','R') HR,if(oi.finishLeft=0,'','L') FL,if(oi.finishRight=0,'','R') FR
    FROM  orderItemMods oi, orderRoom orr, itemMods it
    WHERE it.id = oi.mid and oi.rid = orr.rid and orr.oid = '" .$_POST['oid']. "' and (it.visible = 1 or it.visible is null) and it.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CL.")) as T1 order by rid,orderItemID,sid";
	//echo $sql;
	$result = opendb($sql);
	$items = array(); 
	while ( $row = $result->fetch_assoc())  {
		$data['rid'] = $row['rid'];
		$data['orName'] = $row['orName'];
		$data['itemID'] = $row['itemID'];
		$data['orderItemID'] = $row['orderItemID'];
		$data['sid'] = $row['sid'];
		$data['name'] = $row['name'];
		$data['description'] = $row['description'];
		$data['note'] = $row['note'];
		$data['W'] = $row['W'];
		$data['H'] = $row['H'];
		$data['D'] = $row['D'];
		$data['W2'] = $row['W2'];
		$data['D2'] = $row['D2'];
		$data['HL'] = $row['HL'];
		$data['HR'] = $row['HR'];
		$data['FL'] = $row['FL'];
		$data['FR'] = $row['FR'];
		array_push($items, $data); 
	}
	echo json_encode($items); 
}

/*copy item inside an order*/
if($_POST['mode'] == "copyRowItem"){
    //update position for all the items after item
    //$sql = "update orderItem oi set oi.position = oi.position+1 where oi.position > (select oi2.position from orderItem oi2 where oi2.id = ".$_POST['item'].") and rid=".$_POST['rid'];
    //opendb($sql);
    //$sql = "update orderItemMods oi set oi.position = oi.position+1 where oi.position > (select oi2.position from orderItem oi2 where oi2.id = ".$_POST['item'].") and rid=".$_POST['rid'];
    //opendb($sql);
	$sql = "insert into orderItem( iid,position,rid,name,description,qty,price,sizePrice,minSize,W,H,D,W2,H2,D2,minW,minH,minD,maxW,maxH,maxD,doorFactor,speciesFactor,finishFactor,interiorFactor,sheenFactor,glazeFactor,drawers,smallDrawerFronts,largeDrawerFronts,hingeLeft,hingeRight,finishLeft,finishRight,note,fromItem) select iid,(select max(oi2.position)+1 from orderItem oi2 where oi2.rid=".$_POST['rid']."),rid,name,description,qty,price,sizePrice,minSize,W,H,D,W2,H2,D2,minW,minH,minD,maxW,maxH,maxD,doorFactor,speciesFactor,finishFactor,interiorFactor,sheenFactor,glazeFactor,drawers,smallDrawerFronts,largeDrawerFronts,hingeLeft,hingeRight,finishLeft,finishRight,note,id from orderItem where id =".$_POST['item'];
	opendb($sql);
	$lastInsert = getLastInsert();
	$sql = "insert into orderItemMods (pid,position,rid,name,description,qty,price,sizePrice,minSize,W,H,D,W2,H2,D2,minW,minH,minD,maxW,maxH,maxD,doorFactor,speciesFactor,finishFactor,interiorFactor,sheenFactor,glazeFactor,drawers,smallDrawerFronts,largeDrawerFronts,hingeLeft,hingeRight,finishLeft,finishRight,mid,note)
			select ".$lastInsert.",(select max(oi2.position) from orderItem oi2 where oi2.rid=".$_POST['rid']."),oim.rid,im.name,im.description,oim.qty,im.price,im.sizePrice,im.minSize,oim.W,oim.H,oim.D,oim.W2,oim.H2,oim.D2,im.minW,im.minH,im.minD,im.maxW,im.maxH,im.maxD,im.doorFactor,im.speciesFactor,im.finishFactor,im.interiorFactor,im.sheenFactor,im.glazeFactor,im.drawers,im.smallDrawerFronts,im.largeDrawerFronts,oim.hingeLeft,oim.hingeRight,oim.finishLeft,oim.finishRight,im.id,oim.note 
			from orderItemMods oim, itemMods im where oim.mid = im.id and oim.pid = ".$_POST['item'];
	opendb($sql);
}

if($_POST['mode'] == "copySomeItems"){
    if($_POST['headers']=="true"){
        $sql = "select door,species,frontFinish,glaze,sheen,hinge,smallDrawerFront,largeDrawerFront,drawerGlides,drawerBox,interiorFinish,finishedEnd from orderRoom orr where orr.rid = ".$_POST['headerRID'];
        //echo $sql."\n";
        $result = opendb($sql);
        $row = $result->fetch_assoc();
        $sql = "update orderRoom set door=".$row['door'].",species=".$row['species'].",frontFinish=".$row['frontFinish'].",glaze=".$row['glaze'].",sheen=".$row['sheen'].",hinge=".$row['hinge'].",smallDrawerFront=".$row['smallDrawerFront'].",largeDrawerFront=".$row['largeDrawerFront'].",drawerGlides=".$row['drawerGlides'].",drawerBox=".$row['drawerBox'].",interiorFinish=".$row['interiorFinish'].",finishedEnd=".$row['finishedEnd']." where rid = ".$_POST['rid'];
        //echo $sql."\n";
        opendb($sql);
    }
	copyItemsToRoom($_POST['items'],$_POST['rid']);
}

if($_POST['mode'] == "copyRoom"){
	//copy room
	$sql = "insert into orderRoom(name,oid,door,species,edge,frontFinish,glaze,sheen,hinge,smallDrawerFront,largeDrawerFront,drawerGlides,drawerBox,interiorFinish,finishedEnd,note,fromRoom,cc,fronts,pieces) select concat(name,'-',(select count(1)+1 from orderRoom orr2 where orr2.oid = orr.oid)),oid,door,species,edge,frontFinish,glaze,sheen,hinge,smallDrawerFront,largeDrawerFront,drawerGlides,drawerBox,interiorFinish,finishedEnd,note,rid,cc,fronts,pieces from orderRoom orr where orr.rid=".$_POST['rid'];
	opendb($sql);
	$newRID = getLastInsert();

	//copy items
	$items = array(); 
	$sql = "select id from orderItem oi where oi.rid =".$_POST['rid'];
	$result = opendb($sql);
	while($row = $result->fetch_assoc()){
		array_push($items, $row['id']); 
	}
	copyItemsToRoom($items,$newRID);
}

if($_POST['mode'] == "itemFilter"){
    $sql = "select id,name,description from item where description = '".$_POST['filter']."'";
    $result = opendb($sql);
    $items = array();
    while($row = $result->fetch_assoc()){
        $data['id'] = $row['id'];
        $data['name'] = $row['name'];
        $data['description'] = $row['description'];
        array_push($items, $data); 
    }
    echo json_encode($items);
}

if($_POST['mode'] == "isSomeRoomEmpty"){
    $CL = 0;
    if(isset($_POST['CLid'])){
        $CL = $_POST['CLid'];
    }else{
        $CL = $_SESSION["defaultCLid"];
    }
    $sql = "select orr.rid, (select count(1) from orderItem oi, item i where oi.rid = orr.rid and i.id = oi.iid and i.CLGroup in(select clg.CLGid from cabinetLineGroups clg where clg.CLid = ".$CL.") ) qty from orderRoom orr where orr.oid=".$_POST['OID'];
    $result = opendb($sql);
    echo $sql;
    $empty = 0;
    while($row = $result->fetch_assoc()){
        if(strcmp($row['qty'],"0")==0){
            $empty = 1;
            break;
        }
    }
    echo $empty;
}

if($_POST['mode']=="getImage"){
    try{
        if(strcmp($_POST['orderItem'],'true')==0){
            $sql = "select description from item where id = (select iid from orderItem where id=".$_POST['item'].")";
        }else{
            $sql = "select description from item where id=".$_POST['item'];
        }
        $result = opendb($sql);
        $row = $result->fetch_assoc();
        $path = "uploads/ItemImages/".bin2hex($row['description']);
        $files = glob($path.".*", GLOB_ERR);
        if(count($files)>0){
            echo $files[0];
        }else{
            echo "false";
        };
    }catch (Exception $ex){echo $ex;}
    
}

if($_POST['mode']=="updateFromOrder"){
    $sql = "update mosOrder set fromOrder='".$_POST['foid']."' where oid=".$_POST['curoid'];
    opendb($sql);
    echo $sql;
}

if($_POST['mode']=="updatePrinting"){
    $sql = "select *,coalesce(invoiceTo,'N/A')invoiceTo ,(select concat(coalesce(unit,' '),' ',street,' ',city,' ',province,' ',country,' ',postalCode)  from accountAddress aA where aA.id =mo.shipAddress) shipTo, coalesce((select concat(mu.firstName,' ',mu.lastName) from mosUser mu where mu.id = mo.submittedBy),'No name')whoSubmit, (select a.busDBA from account a where a.id = mo.account)busName,isPriority, isWarranty, CLid from mosOrder mo, mosUser mu, account a, cabinetLine cl where mo.mosUser = mu.id and mo.account = a.id and mo.CLid = cl.id and mo.oid = (select distinct oid from orderRoom orr1 where orr1.rid = ".$_POST['rid'].")";
    $result = opendb($sql);
    $row = $result->fetch_assoc();
    $accountName = $row['busDBA'];
    $mailOID = $row['oid'];
    $CLfactor = $row['factor'];
    $orderType="";
    $discount = floatval($row['discount']);
    $orderTypeDesc="";
    if($row['CLid']==3){
        $orderType="table-primary";
        $orderTypeDesc = "Span Medical";
    }
    if($row['CLid']==2){
        $orderType="table-info";
        $orderTypeDesc = "Builder";
    }
    if($row['isPriority']==1){
        $orderType="table-warning";
        $orderTypeDesc = "Service";
    }
    if($row['isWarranty']==1){
        $orderType="table-danger";
        $orderTypeDesc = "Service w/warranty";
    }
    $msg = "
        <div class=\"bg-white container-fluid\">
            <div class=\"row d-flex justify-content-around align-items-center\">
                <img id=\"logo\" alt=\"logo\" src=\"https://mobel.ca/wp-content/uploads/2019/01/Logo.png\"/>
                <h1>MOS: &nbsp;<b>".$mailOID."</b></h1>
            </div>
            <div class=\"row\">			
                <div class=\"col-12\">
                    <table class=\"table table-sm my-auto mx-5\">
                        <tr>
                            <td class=\"border-0 text-right\"><h5>Customer:</h5></td>
                            <td class=\"border-0 text-left\"><h5 class=\"font-weight-bold\">". $row['busName']."</h5></td>
                            <td class=\"border-0 text-right\"><h5>Submitted by:</h5></td>
                            <td class=\"border-0 text-left\"><h5>". $row['whoSubmit'] ."</h5></td>						
                        </tr>";
    if($_SESSION["userType"]==3){
        if(!empty($orderTypeDesc)){//hide row when is not Span or a Service
            $msg .= "<tr id='trPrintOrderType'>
                            <td class=\"border-0 text-right\"><h5>Order Type:</h5></td>
                            <td class=\"border-0 text-left\"><h5 class=\"font-weight-bold\">". $orderTypeDesc ."</h5></td>
                            <td class=\"border-0 text-right\"><h5>Invoice to:</h5></td>
                            <td class=\"border-0 text-left\"><h5>". $row['invoiceTo'] ."</h5></td>
                        </tr>";
        }
    }
    $msg .= "			<tr>
                            <td class=\"border-0 text-right\"><h5>Date Submitted:</h5></td>
                            <td class=\"border-0 text-left\"><h5>". substr($row['dateSubmitted'],0,10) ."</h5></td>
                            <td class=\"border-0 text-right\"><h5>Tag Name / PO:</h5></td>
                            <td class=\"border-0 text-left\"><h5 class=\"font-weight-bold\">".$row['tagName']." - ". $row['po'] ."</h5></td>
                        </tr>
                        <tr>						
                            <td class=\"border-0 text-right\"><h5>Ship to:</td>
                            <td colspan=\"5\" class=\"border-0 text-left\"><h5>". $row['shipTo'] ."</h5></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div style=\"height: 7px\" class=\"bg-dark\">&nbsp</div>";
            if(isset($row['note']))
                $msg .= "<h5 class=\"font-weight-bold\">Order notes: ".$row['note']."</h5>";
            //Rooms start here
            if($_POST['pro']=="true"){//if only current room is needed to be printed
                $sql = "select if(orr.touchUp>0,'YES','NO') touchUp,if(orr.hardware>0,'YES','NO') hardware,orr.counterTop,orr.rid,orr.note,orr.name rname,sp.name spname,irf.name irfname,dd.name ddname,ff.name ffname,db.name dbname,gl.name glname,sdf.name sdfname,sh.name shname,ldf.name ldfname,h.name hname,dg.name dgname, fe.name fename
            from orderRoom orr,species sp,interiorFinish irf,door dd,frontFinish ff,drawerBox db,glaze gl,smallDrawerFront sdf,sheen sh,largeDrawerFront ldf,hinge h,drawerGlides dg,finishedEnd fe where orr.rid=".$_POST['rid']." and orr.species=sp.id and orr.door=dd.id and orr.frontFinish=ff.id and orr.glaze=gl.id and orr.glaze=gl.id and orr.sheen=sh.id and orr.hinge=h.id and orr.smallDrawerFront=sdf.id and orr.largeDrawerFront=ldf.id and orr.drawerGlides=dg.id and orr.drawerBox=db.id and orr.interiorFinish=irf.id and orr.finishedEnd=fe.id order by orr.name";
            }else{
                $sql = "select if(orr.touchUp>0,'YES','NO') touchUp,if(orr.hardware>0,'YES','NO') hardware,orr.counterTop,orr.rid,orr.note,orr.name rname,sp.name spname,irf.name irfname,dd.name ddname,ff.name ffname,db.name dbname,gl.name glname,sdf.name sdfname,sh.name shname,ldf.name ldfname,h.name hname,dg.name dgname, fe.name fename
            from orderRoom orr,species sp,interiorFinish irf,door dd,frontFinish ff,drawerBox db,glaze gl,smallDrawerFront sdf,sheen sh,largeDrawerFront ldf,hinge h,drawerGlides dg,finishedEnd fe where orr.oid=".$mailOID." and orr.species=sp.id and orr.door=dd.id and orr.frontFinish=ff.id and orr.glaze=gl.id and orr.glaze=gl.id and orr.sheen=sh.id and orr.hinge=h.id and orr.smallDrawerFront=sdf.id and orr.largeDrawerFront=ldf.id and orr.drawerGlides=dg.id and orr.drawerBox=db.id and orr.interiorFinish=irf.id and orr.finishedEnd=fe.id order by orr.name";
            }
            
            $result = opendb($sql);
            $totalOrder = 0;
            while($row = $result->fetch_assoc()){
                $roomTotal = 0;
                $msg .="<table class=\"table table-sm mt-1 mb-0 border border-dark\">
                    <tr class=\"table-secondary\">
                        <td class=\"text-start py-1 my-auto\"><h5><b>Room: ".$row['rname']."</b></h5></td>
                        <td class=\"text-start py-1 my-auto\" colspan=\"3\"><b>Room notes: ".$row['note']."</b></td>
                    </tr>
                    <tr>
                        <td class=\"text-right py-0 font-weight-bold\">Species:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['spname']."</td>
                        <td class=\"text-right py-0 font-weight-bold\">Interior Finish:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['irfname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-right py-0 font-weight-bold\">Door:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['ddname']."</td>
                        <td class=\"text-right py-0 font-weight-bold\">Finish:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['ffname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-right py-0 font-weight-bold\">Drawer Box:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['dbname']."</td>
                        <td class=\"text-right py-0 font-weight-bold\">Glaze:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['glname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-right py-0 font-weight-bold\">Small Drawer Front:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['sdfname']."</td>
                        <td class=\"text-right py-0 font-weight-bold\">Sheen:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['shname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-right py-0 font-weight-bold\">Large Drawer Front:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['ldfname']."</td>
                        <td class=\"text-right py-0 font-weight-bold\">Hinge:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['hname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-right py-0 font-weight-bold\">Drawer Glides:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['dgname']."</td>
                        <td class=\"text-right py-0 font-weight-bold\">Finished End:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['fename']."</td>
                    </tr>
                </table>
                <table class=\"table table-sm border border-dark mb-3\">
                    <thead>
                        <tr>
                            <th class=\"font-weight-bold border text-center border-dark\">Item</th>
                            <th class=\"font-weight-bold border border-dark\">Description</th>
                            <th class=\"font-weight-bold border text-center border-dark\">W</th>
                            <th class=\"font-weight-bold border text-center border-dark\">H</th>
                            <th class=\"font-weight-bold border text-center border-dark\">D</th>
                            <th class=\"font-weight-bold border text-center border-dark\">Qty</th>
                            <th class=\"font-weight-bold border text-center border-dark\">Hinged</th>
                            <th class=\"font-weight-bold border text-center border-dark\">F.E.</th>
                            <th class=\"font-weight-bold border border-dark\">Note</th>";
                    if($_SESSION["userType"]>1){
                        $msg .="<th class=\"d-print-none font-weight-bold border border-dark priceCol\">Price</th>";
                    }
                        $msg .= "</tr>
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
                    $msg .="<tr>
                            <td class=\"border text-center border-dark\">".$b.$i.".".$si.$be."</td>
                            <td class=\"border border-dark\">".$b.$row2['name']." - ".$row2['description'].$be."</td>
                            <td class=\"border text-center border-dark\">".$b.(float)$row2['W'];
                            /*Printing second Width if exists*/
                            if((float)$row2['W2']>0)
                                $msg .= ",".(float)$row2['W2'];
                            $msg .=$be."</td>
                            <td class=\"border text-center border-dark\">".$b.(float)$row2['H'].$be."</td>
                            <td class=\"border text-center border-dark\">".$b.(float)$row2['D'];
                            /*Printing second Depth if exists*/
                            if((float)$row2['D2']>0)
                                $msg .= ",".(float)$row2['D2'];
                            $msg .= $be."</td>
                            <td class=\"border text-center border-dark\">".$b.(float)$row2['qty'].$be."</td>
                            <td class=\"border text-center border-dark\">".$b.$hinging.$be."</td>
                            <td class=\"border text-center border-dark\">".$b.$finishedEnds.$be."</td>
                            <td class=\"border border-dark\" style=\"max-width: 450px;\">".$row2['note']."</td>
                            <td class=\"d-print-none border text-center border-dark priceCol\"><span title = \"" . getPrice($row2['qty'],$row2['price'],$row2['sizePrice'],$parentPrice,$row2['parentPercent'],$row2['DFactor'],$row2['IFactor'],$row2['FFactor'],$row2['GFactor'],$row2['SFactor'],$row2['EFactor'],$row2['drawerCharge'],$row2['smallDrawerCharge'],$row2['largeDrawerCharge'], $mixDoorSpeciesFactor,$row2['IApplies'],$row2['FApplies'],$row2['GApplies'],$row2['SApplies'],$row2['drawers'],$row2['smallDrawerFronts'],$row2['largeDrawerFronts'],$row2['finishLeft']+$row2['finishRight'], $row2['H'],$row2['W'],$row2['D'],$row2['minSize'],$row2['methodID'],$row2['FUpcharge'],$CLfactor,1) . "\">".$b. number_format($aPrice,2,'.','').$be."</span></td>
                        </tr>";                        
                }                
                $msg .="<tr class=\"border border-dark\"><td class=\"font-weight-bold\" colspan=\"8\">Touch Up</td><td class=\"priceCol\"></td><td class=\"font-weight-bold\">".$row['touchUp']."</td></tr>";
                //hidden until more information
                $msg .="<tr class=\"border border-dark d-none\"><td class=\"font-weight-bold\" colspan=\"8\">Hardware</td><td class=\"priceCol\"></td><td class=\"font-weight-bold\">".$row['hardware']."</td></tr>";
                if($_SESSION["userType"]>1){
                    $msg .= "<tr class=\"d-print-none border-top border-dark priceCol\">
                                <td class=\"text-end\" colspan=\"9\"><h5 class=\"font-weight-bold\">Room Total:</h5></td>
                                <td class=\"text-center\"><h5 class=\"font-weight-bold\">$".round($roomTotal,2)."</h5></td>
                            </tr>";
                }
                $msg .="</tbody></table>";
                $totalOrder += $roomTotal+$roomFinishUpcharge;
            }
        if($_SESSION["userType"]>1 && $_POST['pro']!="true"){
            $discountPer = $discount*100;
            $disAmnt = round(($totalOrder*$discount),2);
            $subTotal = round($totalOrder*(1-$discount),2);
            $msg .= "<div class=\"d-print-none row justify-content-end priceCol\">
                        <div class=\"col-6 offset-6 align-self-end\">
                            <table  class=\"table border-0\">
                                <tr>
                                    <th><h4 class=\"font-weight-bold\">Total:</h4></th>
                                    <th><h4 class=\"font-weight-bold\">$$totalOrder</h4></th>
                                    <th>pre HST & pre delivery</th>
                                </tr>
                                <tr>
                                    <th><h4 class=\"font-weight-bold\">Discount(".$discountPer."%):</h4></th>
                                    <th><h4 class=\"font-weight-bold\">$".$disAmnt."</h4></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th><h4 class=\"font-weight-bold\">Sub Total:</h4></th>
                                    <th><h4 class=\"font-weight-bold\">$".$subTotal."</h4></th>
                                    <th>pre HST & pre delivery</th>
                                </tr>
                            </table>
                        </div>
                    </div>";
        }
        $msg .="</div>";
    echo $msg;
}

if($_POST['mode']=="updatePrinting2"){
    $sql = "select *,coalesce(invoiceTo,'N/A')invoiceTo ,(select concat(coalesce(unit,' '),' ',street,' ',city,' ',province,' ',country,' ',postalCode)  from accountAddress aA where aA.id =mo.shipAddress) shipTo, coalesce((select concat(mu.firstName,' ',mu.lastName) from mosUser mu where mu.id = mo.submittedBy),'No name')whoSubmit, (select a.busDBA from account a where a.id = mo.account)busName,isPriority, isWarranty, CLid from mosOrder mo, mosUser mu, account a, cabinetLine cl where mo.mosUser = mu.id and mo.account = a.id and mo.CLid = cl.id and mo.oid = (select distinct oid from orderRoom orr1 where orr1.rid = ".$_POST['rid'].")";
    $result = opendb($sql);
    $row = $result->fetch_assoc();
    $accountName = $row['busDBA'];
    $mailOID = $row['oid'];
    $CLfactor = $row['factor'];
    $orderType="";
    $discount = floatval($row['discount']);
    $orderTypeDesc="";
    if($row['CLid']==3){
        $orderType="table-primary";
        $orderTypeDesc = "Span Medical";
    }
    if($row['CLid']==2){
        $orderType="table-info";
        $orderTypeDesc = "Builder";
    }
    if($row['isPriority']==1){
        $orderType="table-warning";
        $orderTypeDesc = "Service";
    }
    if($row['isWarranty']==1){
        $orderType="table-danger";
        $orderTypeDesc = "Service w/warranty";
    }
    $msg = "
        <div class=\"bg-white container-fluid\">
            <div class=\"row d-flex justify-content-around align-items-center\">
                <img id=\"logo\" alt=\"logo\" src=\"https://mobel.ca/wp-content/uploads/2019/01/Logo.png\"/>
                <h1>MOS: &nbsp;<b>".$mailOID."</b></h1>
            </div>
            <div class=\"row\">			
                <div class=\"col-12\">
                    <table class=\"table table-sm my-auto mx-5\">
                        <tr>
                            <td class=\"border-0 text-right\"><h5>Customer:</h5></td>
                            <td class=\"border-0 text-left\"><h5 class=\"font-weight-bold\">". $row['busName']."</h5></td>
                            <td class=\"border-0 text-right\"><h5>Submitted by:</h5></td>
                            <td class=\"border-0 text-left\"><h5>". $row['whoSubmit'] ."</h5></td>						
                        </tr>";
    if($_SESSION["userType"]==3){
        if(!empty($orderTypeDesc)){//hide row when is not Span or a Service
            $msg .= "<tr id='trPrintOrderType'>
                            <td class=\"border-0 text-right\"><h5>Order Type:</h5></td>
                            <td class=\"border-0 text-left\"><h5 class=\"font-weight-bold\">". $orderTypeDesc ."</h5></td>
                            <td class=\"border-0 text-right\"><h5>Invoice to:</h5></td>
                            <td class=\"border-0 text-left\"><h5>". $row['invoiceTo'] ."</h5></td>
                        </tr>";
        }
    }
    $msg .= "			<tr>
                            <td class=\"border-0 text-right\"><h5>Date Submitted:</h5></td>
                            <td class=\"border-0 text-left\"><h5>". substr($row['dateSubmitted'],0,10) ."</h5></td>
                            <td class=\"border-0 text-right\"><h5>Tag Name / PO:</h5></td>
                            <td class=\"border-0 text-left\"><h5 class=\"font-weight-bold\">".$row['tagName']." - ". $row['po'] ."</h5></td>
                        </tr>
                        <tr>						
                            <td class=\"border-0 text-right\"><h5>Ship to:</td>
                            <td colspan=\"5\" class=\"border-0 text-left\"><h5>". $row['shipTo'] ."</h5></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div style=\"height: 7px\" class=\"bg-dark\">&nbsp</div>";
            if(isset($row['note']))
                $msg .= "<h5 class=\"font-weight-bold\">Order notes: ".$row['note']."</h5>";
            //Rooms start here
            if($_POST['pro']=="true"){//if only current room is needed to be printed
                $sql = "select orr.rid,orr.note,orr.name rname,sp.name spname,irf.name irfname,dd.name ddname,ff.name ffname,db.name dbname,gl.name glname,sdf.name sdfname,sh.name shname,ldf.name ldfname,h.name hname,dg.name dgname, fe.name fename
            from orderRoom orr,species sp,interiorFinish irf,door dd,frontFinish ff,drawerBox db,glaze gl,smallDrawerFront sdf,sheen sh,largeDrawerFront ldf,hinge h,drawerGlides dg,finishedEnd fe where orr.rid=".$_POST['rid']." and orr.species=sp.id and orr.door=dd.id and orr.frontFinish=ff.id and orr.glaze=gl.id and orr.glaze=gl.id and orr.sheen=sh.id and orr.hinge=h.id and orr.smallDrawerFront=sdf.id and orr.largeDrawerFront=ldf.id and orr.drawerGlides=dg.id and orr.drawerBox=db.id and orr.interiorFinish=irf.id and orr.finishedEnd=fe.id order by orr.name";
            }else{
                $sql = "select orr.rid,orr.note,orr.name rname,sp.name spname,irf.name irfname,dd.name ddname,ff.name ffname,db.name dbname,gl.name glname,sdf.name sdfname,sh.name shname,ldf.name ldfname,h.name hname,dg.name dgname, fe.name fename
            from orderRoom orr,species sp,interiorFinish irf,door dd,frontFinish ff,drawerBox db,glaze gl,smallDrawerFront sdf,sheen sh,largeDrawerFront ldf,hinge h,drawerGlides dg,finishedEnd fe where orr.oid=".$mailOID." and orr.species=sp.id and orr.door=dd.id and orr.frontFinish=ff.id and orr.glaze=gl.id and orr.glaze=gl.id and orr.sheen=sh.id and orr.hinge=h.id and orr.smallDrawerFront=sdf.id and orr.largeDrawerFront=ldf.id and orr.drawerGlides=dg.id and orr.drawerBox=db.id and orr.interiorFinish=irf.id and orr.finishedEnd=fe.id order by orr.name";
            }
            
            $result = opendb($sql);
            $totalOrder = 0;
            while($row = $result->fetch_assoc()){
                $roomTotal = 0;
                $msg .="<table class=\"table table-sm mt-1 mb-0 border border-dark\">
                    <tr class=\"table-secondary\">
                        <td class=\"text-start py-1 my-auto\"><h5><b>Room: ".$row['rname']."</b></h5></td>
                        <td class=\"text-start py-1 my-auto\" colspan=\"3\"><b>Room notes: ".$row['note']."</b></td>
                    </tr>
                    <tr>
                        <td class=\"text-right py-0 font-weight-bold\">Species:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['spname']."</td>
                        <td class=\"text-right py-0 font-weight-bold\">Interior Finish:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['irfname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-right py-0 font-weight-bold\">Door:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['ddname']."</td>
                        <td class=\"text-right py-0 font-weight-bold\">Finish:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['ffname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-right py-0 font-weight-bold\">Drawer Box:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['dbname']."</td>
                        <td class=\"text-right py-0 font-weight-bold\">Glaze:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['glname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-right py-0 font-weight-bold\">Small Drawer Front:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['sdfname']."</td>
                        <td class=\"text-right py-0 font-weight-bold\">Sheen:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['shname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-right py-0 font-weight-bold\">Large Drawer Front:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['ldfname']."</td>
                        <td class=\"text-right py-0 font-weight-bold\">Hinge:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['hname']."</td>
                    </tr>
                    <tr>
                        <td class=\"text-right py-0 font-weight-bold\">Drawer Glides:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['dgname']."</td>
                        <td class=\"text-right py-0 font-weight-bold\">Finished End:</td>
                        <td class=\"text-start py-0 font-weight-bold\">".$row['fename']."</td>
                    </tr>
                </table>
                <table class=\"table table-sm border border-dark mb-3\">
                    <thead>
                        <tr>
                            <th class=\"font-weight-bold border text-center border-dark\">Item</th>
                            <th class=\"font-weight-bold border border-dark\">Description</th>
                            <th class=\"font-weight-bold border text-center border-dark\">W</th>
                            <th class=\"font-weight-bold border text-center border-dark\">H</th>
                            <th class=\"font-weight-bold border text-center border-dark\">D</th>
                            <th class=\"font-weight-bold border text-center border-dark\">Qty</th>
                            <th class=\"font-weight-bold border text-center border-dark\">Hinged</th>
                            <th class=\"font-weight-bold border text-center border-dark\">F.E.</th>
                            <th class=\"font-weight-bold border border-dark\">Note</th>";
                    if($_SESSION["userType"]>1){
                        $msg .="<th class=\"d-print-none font-weight-bold border border-dark priceCol\">Price</th>";
                    }
                        $msg .= "</tr>
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
                    $msg .="<tr>
                            <td class=\"border text-center border-dark\">".$b.$i.".".$si.$be."</td>
                            <td class=\"border border-dark\">".$b.$row2['name']." - ".$row2['description'].$be."</td>
                            <td class=\"border text-center border-dark\">".$b.(float)$row2['W'].$be."</td>
                            <td class=\"border text-center border-dark\">".$b.(float)$row2['H'].$be."</td>
                            <td class=\"border text-center border-dark\">".$b.(float)$row2['D'].$be."</td>
                            <td class=\"border text-center border-dark\">".$b.(float)$row2['qty'].$be."</td>
                            <td class=\"border text-center border-dark\">".$b.$hinging.$be."</td>
                            <td class=\"border text-center border-dark\">".$b.$finishedEnds.$be."</td>
                            <td class=\"border border-dark\" style=\"max-width: 450px;\">".$row2['note']."</td>
                            <td class=\"d-print-none border text-center border-dark priceCol\"><span title = \"" . getPrice($row2['qty'],$row2['price'],$row2['sizePrice'],$parentPrice,$row2['parentPercent'],$row2['DFactor'],$row2['IFactor'],$row2['FFactor'],$row2['GFactor'],$row2['SFactor'],$row2['EFactor'],$row2['drawerCharge'],$row2['smallDrawerCharge'],$row2['largeDrawerCharge'], $mixDoorSpeciesFactor,$row2['IApplies'],$row2['FApplies'],$row2['GApplies'],$row2['SApplies'],$row2['drawers'],$row2['smallDrawerFronts'],$row2['largeDrawerFronts'],$row2['finishLeft']+$row2['finishRight'], $row2['H'],$row2['W'],$row2['D'],$row2['minSize'],$row2['methodID'],$row2['FUpcharge'],$CLfactor,1) . "\">".$b. number_format($aPrice,2,'.','').$be."</span></td>
                        </tr>";
                }
                if($_SESSION["userType"]>1){
                    $msg .= "<tr class=\"d-print-none border-top border-dark priceCol\">
                                <td class=\"text-end\" colspan=\"9\"><h5 class=\"font-weight-bold\">Room Total:</h5></td>
                                <td class=\"text-center\"><h5 class=\"font-weight-bold\">$".round($roomTotal,2)."</h5></td>
                            </tr>";
                }
                $msg .="</tbody></table>";
                $totalOrder += $roomTotal+$roomFinishUpcharge;
            }
        if($_SESSION["userType"]>1 && $_POST['pro']!="true"){
            $discountPer = $discount*100;
            $disAmnt = round(($totalOrder*$discount),2);
            $subTotal = round($totalOrder*(1-$discount),2);
            $msg .= "<div class=\"d-print-none row justify-content-end priceCol\">
                        <div class=\"col-6 offset-6 align-self-end\">
                            <table  class=\"table border-0\">
                                <tr>
                                    <th><h4 class=\"font-weight-bold\">Total:</h4></th>
                                    <th><h4 class=\"font-weight-bold\">$$totalOrder</h4></th>
                                    <th>pre HST & pre delivery</th>
                                </tr>
                                <tr>
                                    <th><h4 class=\"font-weight-bold\">Discount(".$discountPer."%):</h4></th>
                                    <th><h4 class=\"font-weight-bold\">$".$disAmnt."</h4></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th><h4 class=\"font-weight-bold\">Sub Total:</h4></th>
                                    <th><h4 class=\"font-weight-bold\">$".$subTotal."</h4></th>
                                    <th>pre HST & pre delivery</th>
                                </tr>
                            </table>
                        </div>
                    </div>";
        }
        $msg .="</div>";
    echo $msg;
}

//get headers when copy items
if($_POST['mode']=="copyItemHeaders"){
    $sql = "SELECT distinct orr.rid, orr.name, s.name species, d.name door, ff.name frontfinish, g.name glaze, sh.name sheen, h.name hinge, sdf.name smalldrawerfront, ldf.name largedrawerfront, db.name drawerbox, dg.name drawerglides, inf.name interiorfinish, fe.name finishedend FROM orderItem oi, orderRoom orr, species s, door d, frontFinish ff, glaze g, sheen sh, hinge h, smallDrawerFront sdf, largeDrawerFront ldf, drawerBox db, interiorFinish inf, finishedEnd fe, drawerGlides dg where orr.rid = oi.rid and s.id = orr.species and d.id = orr.door and ff.id = orr.frontFinish and g.id = orr.glaze and sh.id = orr.sheen and h.id = orr.hinge and sdf.id = orr.smallDrawerFront and ldf.id = orr.largeDrawerFront and db.id = orr.drawerGlides and inf.id = orr.interiorFinish and fe.id = orr.finishedEnd and dg.id = orr.drawerGlides and oi.id in (".implode(",", $_POST['items']).") order by orr.rid";
    $result = opendb($sql);
    $rid = 0;
    while($row = $result->fetch_assoc()){
        if($rid==0){
            $rid=$row['rid'];   
            echo "<input id=\"copyHeaderRoomID\" type=\"hidden\" value=\"".$rid."\">";         
            echo "<table class=\"table table-sm border-1\">";            
            echo "<tr class=\"bg-light\">
                    <th  colspan=\"2\" class=\"text-left\">
                        <input class=\"chkHeadClass\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Select header\" onchange=\"chkHeader(this,".$rid.");\" type='checkbox' id=\"chkH".$rid."\" checked>
                    </th>
                    <th colspan=\"2\" class=\"text-left\">
                        <h6 class=\"font-weight-bold\">".$row['name']."</h6>
                    </th>
                </tr>";
        }else{
            $rid=$row['rid'];
            echo "</table>";
            echo "<table class=\"table table-sm border-1\">";
            echo "<tr class=\"bg-light\">
                    <th colspan=\"2\" class=\"text-left\">
                        <input class=\"chkHeadClass\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Select header\" onchange=\"chkHeader(this,".$rid.");\" type='checkbox' id=\"chkH".$rid."\">
                    </th>
                    <th colspan=\"2\" class=\"text-left\">
                        <h6 class=\"font-weight-bold\">".$row['name']."</h6>
                    </th>
                </tr>";
        }
        echo buildHeaderHTML($row);        
    }
}

//get extras Touch up and headers to build a html select
if($_POST['mode']=="getExtras"){
    $sql = "select coalesce(touchUp,'0') touchUp,coalesce(hardware,'0') hardware,coalesce(counterTop,'0') counterTop from orderRoom where rid = ".$_POST['rid'];
    $result = opendb($sql);
    $row = $result->fetch_assoc();
    echo json_encode($row);
}

if($_POST['mode']=="setExtras"){
    $val = 0;
    if($_POST['val']=='true')
        $val = 1;
    $sql = "update orderRoom set ".$_POST['column']." = ".$val." where rid = ".$_POST['rid'];
    opendb($sql);
    echo $GLOBALS['$conn']->affected_rows;
}

function buildHeaderHTML($row){
    $html = "<tr>
                <th class=\"text-right\">Species</th>
                <th class=\"text-left\">".$row['species']."</th>
                <th class=\"text-right\">Interior Finish</th>
                <th class=\"text-left\">".$row['interiorfinish']."</th>
            </tr>
            <tr>
                <th class=\"text-right\">Door Style</th>
                <th class=\"text-left\">".$row['door']."</th>
                <th class=\"text-right\">Finish</th>
                <th class=\"text-left\">".$row['frontfinish']."</th>
            </tr>
            <tr>
                <th class=\"text-right\">Drawer Box</th>
                <th class=\"text-left\">".$row['drawerbox']."</th>
                <th class=\"text-right\">Glaze</th>
                <th class=\"text-left\">".$row['glaze']."</th>
            </tr>
            <tr>
                <th class=\"text-right\">Small Drawer Front</th>
                <th class=\"text-left\">".$row['smalldrawerfront']."</th>
                <th class=\"text-right\">Sheen</th>
                <th class=\"text-left\">".$row['sheen']."</th>
            </tr>
            <tr>
                <th class=\"text-right\">Large Drawer Front</th>
                <th class=\"text-left\">".$row['largedrawerfront']."</th>
                <th class=\"text-right\">Hinge</th>
                <th class=\"text-left\">".$row['hinge']."</th>
            </tr>
            <tr>
                <th class=\"text-right\">Drawer Glides</th>
                <th class=\"text-left\">".$row['drawerglides']."</th>
                <th class=\"text-right\">Finished End</th>
                <th class=\"text-left\">".$row['finishedend']."</th>
            </tr>
            ";
    return $html;
}

//copy items including mods to specific room
function copyItemsToRoom($items,$rid){
	$text = "";
	foreach($items as &$item){
		//copy item
		$sql = "insert into orderItem( iid,position,rid,name,description,qty,price,sizePrice,minSize,W,H,D,W2,H2,D2,minW,minH,minD,maxW,maxH,maxD,doorFactor,speciesFactor,finishFactor,interiorFactor,sheenFactor,glazeFactor,drawers,smallDrawerFronts,largeDrawerFronts,hingeLeft,hingeRight,finishLeft,finishRight,note,fromItem) select iid,(select max(position)+1 from orderItem oi where oi.rid=".$rid."),".$rid.",name,description,qty,price,sizePrice,minSize,W,H,D,W2,H2,D2,minW,minH,minD,maxW,maxH,maxD,doorFactor,speciesFactor,finishFactor,interiorFactor,sheenFactor,glazeFactor,drawers,smallDrawerFronts,largeDrawerFronts,hingeLeft,hingeRight,finishLeft,finishRight,note,id from orderItem where id =".$item;
		$result = opendb($sql);
		$lastItem = getLastInsert();
		//copy mod
		$sql = "insert into orderItemMods (pid,position,rid,name,description,qty,price,sizePrice,minSize,W,H,D,W2,H2,D2,minW,minH,minD,maxW,maxH,maxD,doorFactor,speciesFactor,finishFactor,interiorFactor,sheenFactor,glazeFactor,drawers,smallDrawerFronts,largeDrawerFronts,hingeLeft,hingeRight,finishLeft,finishRight,mid,note)
				select ".$lastItem.",(select position from orderItem oi where oi.id =".$lastItem."),".$rid.",im.name,im.description,oim.qty,im.price,im.sizePrice,im.minSize,oim.W,oim.H,oim.D,oim.W2,oim.H2,oim.D2,im.minW,im.minH,im.minD,im.maxW,im.maxH,im.maxD,im.doorFactor,im.speciesFactor,im.finishFactor,im.interiorFactor,im.sheenFactor,im.glazeFactor,im.drawers,im.smallDrawerFronts,im.largeDrawerFronts,oim.hingeLeft,oim.hingeRight,oim.finishLeft,oim.finishRight,im.id,oim.note 
				from orderItemMods oim, itemMods im where oim.mid = im.id and oim.pid = ".$item;
		opendb($sql);
		//echo $sql;
	}
}

function getLastInsert(){
	$sql = "SELECT LAST_INSERT_ID() last";
	$result = opendb($sql);
	$row = $result->fetch_assoc();
	return $row['last'];
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
    where id = " . $Sitem;
    
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
    return substr($Fields,0, strlen($Fields)-1);
}

/*------------------------------------------------------------
* Update position column
* This function was created to update the old orders items position since old orders positions were not created
* After some time this function can(should) be not longer called.
------------------------------------------------------------*/
function updatePos($rid){
    $sql = "select t.item, t.sid from (select oi.id as item, 0 as sid from orderItem oi where oi.rid =".$rid." union all select oim.pid, oim.id from orderItemMods oim where oim.rid =".$rid.") t order by t.item,t.sid asc";
    //echo $sql;
    
    $result = opendb($sql);
    $i = 0;
    while($row = $result->fetch_assoc()){
        //$i++;
        if($row['sid']==0){
            $table = "orderItem";
            $item = $row['item'];
            $i++;
        }else{
            $table = "orderItemMods";
            $item = $row['sid'];
        }
        $sql2 = "update ".$table." set position = ".$i." where id=".$item;
        opendb2($sql2);
    }
}

/*---------------------------------------------------------------------------------------
* Create position options for every item
 ---------------------------------------------------------------------------------------*/
function getSelectControl($rid, $id){
    $result = opendb("select count(1) itemQty from orderItem where rid =".$rid);
    $row = $result->fetch_assoc();
    $selectHTML ="";
    for($i=1; $i<=$row['itemQty']; $i++){        
        if($i == $id){
            $selected = "selected";
        }else{
            $selected = "";
        }        
        $selectHTML .= "<option ".$selected." value=\"".$i."\">".$i.".0</option>";
    }   
     return $selectHTML;  
 }
?>
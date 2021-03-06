<?php
include 'includes/nav.php';
include_once 'includes/db.php';
echo "<style>
	.print {display:none!important;}
	@media print {
	  .print {display:block!important;}	  
	  body {font-size: 1.3em !important;}
	  table td {overflow:hidden !important;font-size: .8em !important;overflow: visible !important;}
	  table th {font-size: .9em !important;overflow: visible !important;}
	}
	</style>";
//$_POST['oid'] = 179;
$sql = "select *,coalesce(invoiceTo,'N/A')invoiceTo ,(select concat(coalesce(unit,' '),' ',street,' ',city,' ',province,' ',country,' ',postalCode)  from accountAddress aA where aA.id =mo.shipAddress) shipTo, coalesce((select concat(mu.firstName,' ',mu.lastName) from mosUser mu where mu.id = mo.submittedBy),'No name')whoSubmit, (select a.busDBA from account a where a.id = mo.account)busName,isPriority, isWarranty, CLid from mosOrder mo, mosUser mu, account a, cabinetLine cl where mo.mosUser = mu.id and mo.account = a.id and mo.CLid = cl.id and mo.oid = '" . $_POST['oid'] . "'";
$result = opendb($sql);
$row = $result->fetch_assoc();
$accountName = $row['busDBA'];
$mailOID = $row['oid'];
$CLfactor = $row['factor'];
$orderType="";
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
	//$orderType="table-danger";
	$orderTypeDesc = "Service w/warranty";
}
$msg = "
<body>
	<div class=\"bg-white container-fluid pb-3\">
		<div class=\"row d-flex justify-content-around align-items-center $orderType\">
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
		if(!empty($orderTypeDesc)){
			$msg .= "<tr>
						<td class=\"border-0 text-right $orderType\"><h5>Order Type:</h5></td>
						<td class=\"border-0 text-left $orderType\"><h5 class=\"font-weight-bold\">". $orderTypeDesc ."</h5></td>
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
		<!--/div-->
		</div>
		<div style=\"height: 7px\" class=\"bg-dark\">&nbsp</div>";
		if(isset($row['note']))
			$msg .= "<h5 class=\"font-weight-bold\">Order notes: ".$row['note']."</h5>";
		//Rooms start here
		$sql = "select if(orr.touchUp>0,'YES','NO') touchUp,if(orr.hardware>0,'YES','NO') hardware,orr.counterTop,orr.rid,orr.note,orr.name rname,sp.name spname,irf.name irfname,dd.name ddname,ff.name ffname,db.name dbname,gl.name glname,sdf.name sdfname,sh.name shname,ldf.name ldfname,h.name hname,dg.name dgname, fe.name fename
		from orderRoom orr,species sp,interiorFinish irf,door dd,frontFinish ff,drawerBox db,glaze gl,smallDrawerFront sdf,sheen sh,largeDrawerFront ldf,hinge h,drawerGlides dg,finishedEnd fe where orr.oid=".$mailOID." and orr.species=sp.id and orr.door=dd.id and orr.frontFinish=ff.id and orr.glaze=gl.id and orr.glaze=gl.id and orr.sheen=sh.id and orr.hinge=h.id and orr.smallDrawerFront=sdf.id and orr.largeDrawerFront=ldf.id and orr.drawerGlides=dg.id and orr.drawerBox=db.id and orr.interiorFinish=irf.id and orr.finishedEnd=fe.id order by orr.name";
		$result = opendb($sql);
		$totalOrder = 0;
    	while($row = $result->fetch_assoc()){
    		$roomTotal = 0;
	    	$msg .="<table class=\"table table-sm mt-1 mb-0 border border-dark\">
				<tr class=\"table-secondary\">
					<td class=\"text-start py-1 my-auto\"><h5 class=\"font-weight-bold\">Room: ".$row['rname']."</h5></td>
					<td class=\"font-weight-bold text-start py-1 my-auto\" colspan=\"3\">Room notes: ".$row['note']."</td>
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
		                <th class=\"font-weight-bold border border-dark\">Note</th>
	                	<!--th class=\"font-weight-bold border border-dark\">Price</th-->
	              	</tr>
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
						<td class=\"border text-center border-dark font-weight-bold\">".$b.$i.".".$si.$be."</td>
						<td class=\"border border-dark font-weight-bold\">".$b.$row2['name']." - ".$row2['description'].$be."</td>
						<td class=\"border text-center border-dark font-weight-bold\">".$b.(float)$row2['W'];
						/*Printing second Width if exists*/
						if((float)$row2['W2']>0)
							$msg .= ", ".(float)$row2['W2'];
						$msg .=$be."</td>
						<td class=\"border text-center border-dark font-weight-bold\">".$b.(float)$row2['H'].$be."</td>
						<td class=\"border text-center border-dark font-weight-bold\">".$b.(float)$row2['D'];
						/*Printing second Depth if exists*/
						if((float)$row2['D2']>0)
							$msg .= ", ".(float)$row2['D2'];
						$msg .= $be."</td>
						<td class=\"border text-center border-dark font-weight-bold\">".$b.(float)$row2['qty'].$be."</td>
						<td class=\"border text-center border-dark font-weight-bold\">".$b.$hinging.$be."</td>
						<td class=\"border text-center border-dark font-weight-bold\">".$b.$finishedEnds.$be."</td>
						<td class=\"border border-dark font-weight-bold\" style=\"max-width: 450px;\">".$row2['note']."</td>
						<!--td><span title = \"" . getPrice($row2['qty'],$row2['price'],$row2['sizePrice'],$parentPrice,$row2['parentPercent'],$row2['DFactor'],$row2['IFactor'],$row2['FFactor'],$row2['GFactor'],$row2['SFactor'],$row2['EFactor'],$row2['drawerCharge'],$row2['smallDrawerCharge'],$row2['largeDrawerCharge'], $mixDoorSpeciesFactor,$row2['IApplies'],$row2['FApplies'],$row2['GApplies'],$row2['SApplies'],$row2['drawers'],$row2['smallDrawerFronts'],$row2['largeDrawerFronts'],$row2['finishLeft']+$row2['finishRight'], $row2['H'],$row2['W'],$row2['D'],$row2['minSize'],$row2['methodID'],$row2['FUpcharge'],$CLfactor,1) . "\">".$b. number_format($aPrice,2,'.','').$be."</span></td-->
					</tr>";
			}
			$msg .="<tr class=\"border border-dark\"><td class=\"font-weight-bold\" colspan=\"8\">Touch Up</td><td class=\"font-weight-bold\">".$row['touchUp']."</td></tr>";
			//hidden until more information
			$msg .="<tr class=\"border border-dark d-none\"><td class=\"font-weight-bold\" colspan=\"8\">Hardware</td><<td class=\"font-weight-bold\">".$row['hardware']."</td></tr>";
			$msg .="</tbody></table>";
			if(!isset($roomFinishUpcharge))
				$roomFinishUpcharge =0;
			$totalOrder += $roomTotal+$roomFinishUpcharge;
		}
	//$msg .= "<div><h4 class=\"text-center\">Total Order: $$totalOrder pre HST & pre delivery</h4></div>";
	$msg .="</div>
  	<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js\" integrity=\"sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW\" crossorigin=\"anonymous\"></script>
</body>
</html>";
echo $msg;

include 'includes/foot.php';?>
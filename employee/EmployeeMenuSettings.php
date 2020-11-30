<?php include_once '../includes/db.php';?>
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
/* For local environment */
$local = "";
if(strcmp($_SERVER['SERVER_NAME'],"localhost")==0 || strcmp($_SERVER['SERVER_NAME'],"192.168.16.199")==0){
	$local = "/mobelOrderMgmt";
}
session_start();
if($_POST['mode']=="getOrders"){
	$arr = implode(', ', $_POST['value']);//getting all values from array
	$sql = "select m.oid,a.busName as 'company', m.tagName, m.po, concat(mu.firstName,' ',mu.lastName) as 'designer', email, s.name as 'status', DATE(m.dateSubmitted) dateSubmitted, m.state from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state in (".$arr.") order by m.state desc";
	$result = opendb($sql);
	$dbdata = array();
	while ( $row = $result->fetch_assoc())  {
		$dbdata[]=$row;
	}
	echo json_encode($dbdata);
	
	/*
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
			echo "<td  data-toggle=\"tooltip\" title=\"YYYY-MM-DD\"><b><a href=\"Order.php?OID=" . $row['oid'] . "\">".$row['dateSubmitted']."</b></td>";						
			echo "</tr>";
		}
	}else{
		echo "<tr>No data for that search criteria.</tr><tr>Please change your filter to see some records.</tr>";
	}*/
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
		echo "<a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=".$_POST['value']."\" id=\"searchOrderBtn\" class=\"btn btn-outline-primary btn-sm float-right\" type=\"button\" >Open Order</a>";
	}else{
		echo "<div class=\"alert alert-warning\" role=\"alert\">";
		echo "<p>Order <b>".$_POST['value']."</b> doesn't exist</p></div>";
	}
}

if($_POST['mode']=="updateHeader"){
	//update visibility
	$sql = "update ".$_POST['table']." set visible=".$_POST['checked']." where id=".$_POST['id'];
	opendb($sql);
	/*if($_POST['checked']=="false"){
		//Update Quotes when disable header value
		$sql = "update orderRoom orr set ".$_POST['table']."=null where orr.".$_POST['table']."=".$_POST['id']." and exists(select 1 from mosOrder mo where mo.oid = orr.oid and mo.state=1)";
		opendb($sql);
	}*/
}

if($_POST['mode']=="getDateStatus"){
	$sql = "select workDayInd from calendar where calendarDay = '".$_POST['date']."'";
	$result = opendb($sql);
	while ( $row = $result->fetch_assoc())  {
		echo $row["workDayInd"];
		
	}
}

if($_POST['mode']=="updateCalDay"){
	$sql = "update calendar set workDayInd = ".$_POST["newStatus"].", uid = ".$_SESSION["userid"].", updateDate = curdate() where calendarDay = '".$_POST['date']."'";
	opendb($sql);
}

if($_POST['mode']=="getOrderRooms"){ 
	$sql = "select orr.rid, orr.name, orr.cc, COALESCE(orr.fronts,0) fronts, DATE(COALESCE(deliveryDate,dateRequired)) dateRequired, (select count(1) from orderItem oi, item i where oi.iid = i.id and i.isCabinet = 0 and oi.rid = orr.rid)pieces from orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.oid = ".$_POST["oid"]." order by orr.name asc";
	$query = opendb($sql);
	$header = true;
	while($row = $query->fetch_assoc()){ 
		/*if($header){
			echo "<small class=\"form-text text-center alert-info mb-3\">This date is for the order and all it's rooms</small>
			<div class=\"row\">
				<div class=\"col-6 mx-auto\">
					<div class=\"form-group\">
						<div class=\"input-group mb-3\">					
							<div class=\"input-group-prepend\">
								<span class=\"input-group-text\">Order Delivery Date</span>
							</div>
							<input id=\"deliveryDate\" type=\"text\" maxlength=\"10\" data-provide=\"datepicker\" data-date-format=\"yyyy-mm-dd\" class=\"form-control datepicker text-center\" value=\"".$row['dateRequired']."\" onchange=\"updateDetail(".$row['rid'].",this.id, this.value)\">
						</div>		
					</div>								
				</div>
				<div class=\"col-6 mx-auto\">
					<select id=\"selDept\" multiple=\"multiple\">						
						<option selected value=\"1\" id=\"shipping\">SHIPPING</option>
						<option selected value=\"2\" id=\"wrapping\">WRAPPING</option>
						<option selected value=\"8\" id=\"sanding\">SANDING</option>
					</select>
				</div>
			</div>
			<div class=\"container\">";
			$header = false;
		}*/
		echo 	"<div class=\"dropdown-divider\"></div>
				<div id=\"".$row['rid']."\"class=\"row\">
					<div class=\"col\">
						<label>".$row['name']."</label>
					</div>
					<div class=\"col\">
						<div class=\"form-group\">
							<div class=\"input-group\">
							  <div class=\"input-group-prepend\">
								<span id=\"boxesPieces\" class=\"input-group-text\">Boxes</span>
							  </div>
							  <input required type=\"number\" class=\"form-control\" name=\"cc\" id=\"cc".$row['rid']."\"  placeholder=\"e.g. ".$row['cc']."\" aria-describedby=\"cc\" onchange=\"updateDetail(".$row['rid'].",'cc',this.value);\">
							</div>
						</div>
					</div>
					<div class=\"col\">
						<div class=\"form-group\">
							<div class=\"input-group\">
							  <div class=\"input-group-prepend\">
								<span class=\"input-group-text\">Fronts</span>
							  </div>
							  <input type=\"number\" class=\"form-control\" name=\"fronts\" id=\"fronts".$row['rid']."\" placeholder=\"e.g. ".$row['fronts']."\" aria-describedby=\"fronts\" onchange=\"updateDetail(".$row['rid'].",'fronts',this.value);\">
							</div>
						</div>
					</div>
					<div class=\"col\">
						<div class=\"form-group\">
							<div class=\"input-group\">
							  <div class=\"input-group-prepend\">
								<span class=\"input-group-text\">Pieces</span>
							  </div>
							  <input type=\"number\" class=\"form-control\" name=\"pieces\" id=\"pieces".$row['rid']."\" placeholder=\"e.g. ".$row['pieces']."\" aria-describedby=\"pieces\" onchange=\"updateDetail(".$row['rid'].",'pieces',this.value);\">
							</div>
						</div>
					</div>
				</div>"; 
	} 
	//echo "</div>";
}

if($_POST['mode']=="updateRoomDetails"){ 
	if(strcmp($_POST['col'],"deliveryDate")==0){
		//updating delivery date
		$sql = "update mosOrder set ".$_POST['col']." = '".$_POST['val']."' where oid = ".$_POST['oid'] ;
		opendb($sql);
		//Calculate and Insert or update wrapping and finishing dates into schedule
		//$sql2 = "select count(1) exist from schedule where oid = ".$_POST['oid'];
		//$sql2 = "select orr.rid,(select count(1) from schedule s where s.rid = orr.rid) exist, (select id from material m where m.id = (select mid from species sp where sp.id = orr.species) ) material, (select finishType from frontFinish ff where ff.id = orr.frontFinish ) finishType, glaze, sheen from orderRoom orr where orr.oid =".$_POST['oid']." order by material asc";
		$sql2 = "select (select count(1) from schedule ss where ss.rid = orr.rid) exist, (select id from material m where m.id = (select mid from species sp where sp.id = orr.species) ) material, (select finishType from frontFinish ff where ff.id = orr.frontFinish ) finishType, glaze, sheen, orr.rid, orr.name, orr.cc, orr.fronts, DATE(COALESCE(deliveryDate,dateRequired)) dateRequired, COALESCE(orr.pieces,(select count(1) from orderItem oi, item i where oi.iid = i.id and i.isCabinet = 0 and oi.rid = orr.rid))pieces from orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.oid = ".$_POST['oid']." order by orr.name asc";
		$result = opendb($sql2);
		$wrapping = scheduleFn($_POST['val'],5);
		$finishing = "null";
		$daysF = 0;
		$daysF2 = 3;
		while($row = $result->fetch_assoc()){ 
			$exists = $row['exist'];
			if(strcmp($row['material'],'1')!=0){//not Laminates
				if(strcmp($row['finishType'],'2')==0){
					$daysF = 3;//stain
				}else{
					$daysF = 4;//Paint or handwiped
				}
				if(strcmp($row['glaze'],'13')!=0){//if glaze
					$daysF += 1;
				}
				if(strcmp($row['sheen'],'3')==0){//if high gloss
					$daysF += 2;
				}
			}			
			if($daysF>$daysF2){
				$daysF2 = $daysF;
			}
			$finishing = scheduleFn($wrapping,$daysF2);
			if($exists==0){			
				$sql2 = "insert into schedule(rid,wrapping,finishing,updateDate) values(".$row['rid'].",'".$wrapping."','".$finishing."',curdate())";
			}else{
				$sql2 = "update schedule set wrapping = '".$wrapping."', finishing ='".$finishing."' where rid in(select rid from orderRoom where oid = ".$_POST['oid'].")" ;
			}
			opendb($sql2);
		}
	}else{
		//updating boxes and pieces
		$sql = "update orderRoom set ".$_POST['col']." = ".$_POST['val']." where rid = ".$_POST['rid'] ;
		//echo $sql;
		opendb($sql);
	}
}

if($_POST['mode']=="loadRoomDet"){ 
	$sql = "select * from orderRoom where rid = ".$_POST["rid"];
	$query = opendb($sql);
	$room = array();
	while($row = $query->fetch_assoc()){ 
		$data['cc'] = $row['cc'];
		$data['fronts'] = $row['fronts']; 
		array_push($room, $data); 
	} 
	echo json_encode($room);
}

if($_POST['mode']=="countBoxes"){ 
	$sql = "select distinct rid, (select count(1) from orderItem oi, item i where oi.iid = i.id and oi.rid = orr.rid and i.isCabinet=1) boxes,(select count(1) from orderItem oi, item i where oi.iid = i.id and oi.rid = orr.rid and i.isCabinet=0) pieces from orderRoom orr where orr.oid =".$_POST["oid"];
	$query = opendb($sql);
	while($row = $query->fetch_assoc()){ 
		$sql2 = "update orderRoom set cc = ".$row['boxes'].", pieces=".$row['pieces']." where rid = ".$row['rid'];
		opendb2($sql2);
	} 
}

if($_POST['mode']=="loadSchWeek"){ 
	/*-------------------------------------------------
	These variables are used for the table layout. Allow to compare every record with the previous to know if it's a new day or a new order.
	---------------------------------------------------*/
	$oid = 0;
	$day = date("Y/m/d");
	/*--------------------------------------------------
	Inside following switch, according to the dateType("$_POST['dateType']" which means what schedule is being used) the proper query is selected.
	-----------------------------------------------------------
	-----------------------------------------------------------
	Switch evaluates what schedule is going to be retrieved - $_POST['dateType']
	Case 3 - Shipping (deliveryDate column in mosOrder table is the base)
	Case 2 - Wrapping (wrapping column from schedule table is the base)
	Case 1 - Sanding(Finishing) (finishing column from schedule table is the base)
	Date = 0 means retrieve all the jobs
	$_POST['filter'] when True indicates that only completed jobs on previous departments are displayed
	---------------------------------------------------------*/
	if($_POST['date']==0)
		$dateFilter = "";
	switch ($_POST['dateType']) {
    case 3:
		if(strcmp($_POST['filter'],"true")==0){
			if($_POST['date']==0){
				$dateFilter = "and deliveryDate is not null";//this can be removed at some time
			}else{
				$dateFilter = "and deliveryDate between '".$_POST['date']."' and '".date('Y-m-d', strtotime($_POST['date']. ' + 6 days'))."'";
			}
			$sql = "select distinct mo.oid, orr.rid, (select sum(cc) from orderRoom orr2, mosOrder mo2 where orr2.oid = mo2.oid and mo2.oid = mo.oid and mo2.deliveryDate = mo2.deliveryDate and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) boxCurQty, (SELECT count(orr2.rid) from orderRoom orr2, mosOrder mo2 where orr2.oid = mo2.oid and mo2.deliveryDate = mo.deliveryDate and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and orr2.rid and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) as rooms,  (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc,fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc where dc.rid = orr.rid and did = ".$_POST['mydid'].") as completed, deliveryDate myDate FROM mosOrder mo, orderRoom orr,deptCompleted dc where mo.oid = orr.oid and orr.rid = dc.rid and state = 5 and deliveryDate is not null and dc.did = (select deptId from deptReqd dr where dr.myDeptId = ".$_POST['mydid'].") ".$dateFilter." order by myDate asc, mo.oid asc";
		}else{
			if($_POST['date']==0){
				$dateFilter = "and deliveryDate is not null";
			}else{
				$dateFilter = "and deliveryDate between '".$_POST['date']."' and '".date('Y-m-d', strtotime($_POST['date']. ' + 6 days'))."'";
			}
			$sql = "select mo.oid, orr.rid, (select sum(cc) from orderRoom orr2 where orr2.oid in (select mo3.oid from mosOrder mo3 where orr2.oid = mo3.oid and mo.deliveryDate = mo3.deliveryDate and mo3.state = 5)) boxCurQty, (SELECT count(1) from orderRoom orr3, mosOrder mo2 where orr3.oid = mo2.oid and mo2.deliveryDate = mo.deliveryDate and state = 5) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc where dc.rid = orr.rid and did = ".$_POST['mydid'].") completed, deliveryDate myDate FROM mosOrder mo, orderRoom orr where mo.oid = orr.oid and state = 5 ".$dateFilter." order by myDate asc, mo.oid asc";
		}
        break;
    case 2:
		if($_POST['date']!=0)
				$dateFilter = "and wrapping between '".$_POST['date']."' and '".date('Y-m-d', strtotime($_POST['date']. ' + 6 days'))."'";
		if(strcmp($_POST['filter'],"true")==0){
			$sql = "select mo.oid, orr.rid, (select sum(cc) from orderRoom orr2, schedule s2, mosOrder mo3 where mo3.oid = orr2.oid and mo3.state = 5 and orr2.rid = s2.rid and s2.wrapping = s.wrapping and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) boxCurQty, (select count(1) from schedule s2 where s2.wrapping = s.wrapping and s2.rid in (select dc2.rid from deptCompleted dc2, orderRoom orr2, mosOrder mo2 where dc2.rid = orr2.rid and orr2.oid = mo2.oid and mo2.state = 5 and dc2.did =(select dr2.deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid']."))) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and orr2.rid and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId =".$_POST['mydid'].") and orr2.rid = dc2.rid)) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc2 where dc2.rid = orr.rid and dc2.did = dr.myDeptId) as completed, wrapping myDate, s.updateDate FROM mosOrder mo, orderRoom orr,deptCompleted dc,schedule s,deptReqd dr where mo.oid = orr.oid and orr.rid = s.rid and orr.rid = dc.rid and state = 5 and dr.myDeptId = ".$_POST['mydid']." and dc.did = dr.deptId ".$dateFilter." order by myDate asc, mo.oid asc";
		}else{
			$sql = "select mo.oid, orr.rid, (select sum(cc) from orderRoom orr2 where orr2.rid in (select ss.rid from schedule ss where ss.wrapping = s.wrapping)) boxCurQty, (SELECT count(1) roomsDay FROM orderRoom orr, schedule SS where orr.rid = ss.rid and ss.wrapping = s.wrapping) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc where dc.rid = orr.rid and did = ".$_POST['mydid'].") completed, deliveryDate, wrapping myDate, s.updateDate FROM mosOrder mo, orderRoom orr, schedule s where mo.oid = orr.oid and orr.rid = s.rid ".$dateFilter." order by myDate asc, mo.oid asc";
		}
        break;
    case 1:
		if($_POST['date']!=0)
			$dateFilter = "and finishing between '".$_POST['date']."' and '".date('Y-m-d', strtotime($_POST['date']. ' + 6 days'))."'";
		if(strcmp($_POST['filter'],"true")==0){
			$sql = "select mo.oid, orr.rid, (select sum(cc) from orderRoom orr2, schedule s2, mosOrder mo3 where mo3.oid = orr2.oid and mo3.state = 5 and orr2.rid = s2.rid and s2.finishing = s.finishing and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) boxCurQty, (select count(1) from schedule s2 where s2.finishing = s.finishing and s2.rid in (select dc2.rid from deptCompleted dc2, orderRoom orr2, mosOrder mo2 where dc2.rid = orr2.rid and orr2.oid = mo2.oid and mo2.state = 5 and dc2.did =(select dr2.deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid']."))) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and orr2.rid and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId =".$_POST['mydid'].") and orr2.rid = dc2.rid)) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc2 where dc2.rid = orr.rid and dc2.did = dr.myDeptId) as completed, finishing myDate, s.updateDate FROM mosOrder mo, orderRoom orr, deptCompleted dc, schedule s, deptReqd dr where mo.oid = orr.oid and orr.rid = s.rid and orr.rid = dc.rid and state = 5 and dr.myDeptId = ".$_POST['mydid']." and dc.did = dr.deptId ".$dateFilter." order by myDate asc, mo.oid asc";
		}else{
			$sql = "select mo.oid, orr.rid, (select sum(cc) from orderRoom orr2 where orr2.rid in (select ss.rid from schedule ss where ss.finishing = s.finishing)) boxCurQty, (SELECT count(1) roomsDay FROM orderRoom orr, schedule SS where orr.rid = ss.rid and ss.finishing = s.finishing) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and orr2.rid in (select ss.rid from schedule ss where ss.finishing = s.finishing)) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc where dc.rid = orr.rid and did = ".$_POST['mydid'].") completed, deliveryDate, finishing myDate, s.updateDate FROM mosOrder mo, orderRoom orr, schedule s where mo.oid = orr.oid and orr.rid = s.rid ".$dateFilter." order by myDate asc, mo.oid asc";
		}
        break;
	default:
        break;
	}
	//echo $sql;
	//Once sql statement is selected, query is executed in this moment.
	opendb($sql);
	/*----------------------------------------------------------------
	Classes for every column are used for hide or show on the view. New columns will need new custom classes and options in the view.
	This layout displays jobs grouped by day and order. Every room is a row.
	-----------------------------------------------------------------*/
	if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
			/*if(strcmp($_POST['displayComp'],"true")!=0 && $row['completed']=='true')
				continue;*/
			$updated = "";
			$completed = "";
			if($row['completed']=='true')
				$completed = "checked";//mark as completed				
			/*if($row['updateDate']==date("Y-m-d",time() - 3600*24))
				$updated = "table-info";*/
			if(strcmp($oid, $row['oid'])==0 && strcmp($day,$row['myDate'])==0){//displaying new room same order
				echo "<tr><td class=\"rmnm align-middle $updated\" scope=\"row\">".$row['roomName']."</td>";//header row room 
				echo "<td class=\"box align-middle $updated\">".$row['cc']."</td>";
				echo "<td class=\"frt align-middle $updated\">".$row['fronts']."</td>";
				echo "<td class=\"itm align-middle $updated\">".$row['pieces']."</td>";					
				echo "<td class=\"mat align-middle $updated\">".$row['material']."</td>";					
				echo "<td class=\"drs align-middle $updated\">".$row['doorStyle']."</td>";					
				echo "<td class=\"fns align-middle $updated\">".$row['finish']."</td>";	
				echo "<td><div id=\"uptRoom".$row['rid']."\" class=\"custom-control custom-checkbox\"><input ".$completed." onchange=\"completeRoom(".$row['rid'].")\" type=\"checkbox\" class=\"custom-control-input\" id=\"chkDone".$row['rid']."\"><label class=\"custom-control-label\" for=\"chkDone".$row['rid']."\"></label></div></td></tr>";				
			}else if(strcmp($day,$row['myDate'])==0 && strcmp($oid, $row['oid'])!=0){
				$oid = $row['oid'];
				echo "<td id=\"".$row['oid']."\" class=\"align-middle $updated\" rowspan=\"".$row['rooms']."\" scope=\"rowgroup\"><b>".$row['oid']."</b></td>";//Order id header
				echo "<td class=\"rmnm $updated\" scope=\"row\">".$row['roomName']."</td>";//header row room
				echo "<td class=\"box $updated\">".$row['cc']."</td>";
				echo "<td class=\"frt $updated\">".$row['fronts']."</td>";
				echo "<td class=\"itm $updated\">".$row['pieces']."</td>";		
				echo "<td class=\"mat $updated\">".$row['material']."</td>";					
				echo "<td class=\"drs $updated\">".$row['doorStyle']."</td>";
				echo "<td class=\"fns $updated\">".$row['finish']."</td>";
				echo "<td><div id=\"uptRoom".$row['rid']."\" class=\"custom-control custom-checkbox\"><input ".$completed." onchange=\"completeRoom(".$row['rid'].")\" type=\"checkbox\" class=\"custom-control-input\" id=\"chkDone".$row['rid']."\"><label class=\"custom-control-label\" for=\"chkDone".$row['rid']."\"></label></div></td></tr>";
			}else{
				$day = $row['myDate'];//new day
				$oid = $row['oid'];//new order
				echo "<tr>";
				echo "<td class=\"align-middle\" rowspan=\"".$row['jobsDay']."\" scope=\"rowgroup\">".date("l M j",strtotime($day))."</br><small>Current total boxes: ".$row['boxCurQty']."</small></td>";
				echo "<td id=\"".$row['oid']."\" class=\"align-middle $updated\" rowspan=\"".$row['rooms']."\" scope=\"rowgroup\"><b>".$row['oid']."</b></td>";//Order id header
				echo "<td class=\"rmnm align-middle $updated\" scope=\"row\">".$row['roomName']."</td>";//header row room
				echo "<td class=\"box align-middle $updated\">".$row['cc']."</td>";
				echo "<td class=\"frt align-middle $updated\">".$row['fronts']."</td>";
				echo "<td class=\"itm align-middle $updated\">".$row['pieces']."</td>";		
				echo "<td class=\"mat align-middle $updated\">".$row['material']."</td>";					
				echo "<td class=\"drs align-middle $updated\">".$row['doorStyle']."</td>";
				echo "<td class=\"fns align-middle $updated\">".$row['finish']."</td>";
				echo "<td><div id=\"uptRoom".$row['rid']."\" class=\"custom-control custom-checkbox\"><input ".$completed." onchange=\"completeRoom(".$row['rid'].")\" type=\"checkbox\" class=\"custom-control-input\" id=\"chkDone".$row['rid']."\"><label class=\"custom-control-label\" for=\"chkDone".$row['rid']."\"></label></div></td></tr>";
			}	
		}
	}else{
		echo "<tr class=\"text-center\"><td colspan=\"10\"><h1 class=\"text-primary\">No data for this week</h1></td></tr>";
	}
}

if($_POST['mode']=="completeRoom"){
	if(strcmp($_POST['action'],'new')==0){
		$sql = "insert into deptCompleted values(".$_POST['mydid'].",".$_POST['rid'].",NOW(),".$_SESSION["userid"].")"; 
	}else{
		$sql = "delete from deptCompleted where did=".$_POST['mydid']." and rid=".$_POST['rid']; 
	}
	opendb($sql);
}

if($_POST['mode']=="completeJobsAuto"){
	$sql2 = "select rid from orderRoom where oid=".$_POST['oid'];
	$result = opendb2($sql2);
	while($row = $result->fetch_assoc()){ 
		if($_POST['action']=="complete"){
			$sql = "INSERT INTO deptCompleted (did, rid, completeDate, uid) VALUES (".$_POST['dept'].", ".$row['rid'].", now(), 0)";
		}else{
			$sql = "delete from deptCompleted where rid=".$row['rid']." and did = ".$_POST['dept'];
		}
		opendb($sql);
	}
}

if($_POST['mode']=="updateOrderStatus"){
	if($_POST['dept']==1){//Shipping - we can add more conditions(switch) to update states once a department has finialized
		$sql = "select oid from mosOrder mo where mo.oid in (select orr.oid from orderRoom orr where orr.rid = ".$_POST['rid'].")";
		$query = opendb($sql);
		$row = $query->fetch_assoc();
		$state = 7;
		if($_POST['action']=='old')
			$state = 5;
		$sql2 = "update mosOrder mo set state = $state where oid=".$row['oid'];
		opendb($sql2);
	}
	echo $sql;
	echo $sql2;
}
?>
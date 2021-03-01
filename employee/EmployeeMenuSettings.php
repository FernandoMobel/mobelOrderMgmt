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
	$result = opendb("select mainMenuDefaultStateFilter as state, clFilter, servFilter from employeeSettings where mosUser = " .$_SESSION["userid"]);
	$row = $result -> fetch_assoc();
	if($GLOBALS['$result']-> num_rows >0){	
		foreach ($GLOBALS['$result'] as $row) {
			$state = $row['state'];
			$state_ar = explode(', ', $state);//convert string to array to create control dinamically
			if($row['clFilter']==""){
				$clfilter = '1,2,3';
			}else{
				$clfilter = $row['clFilter'];			
			}
			if($row['servFilter']==""){
				$servfilter = "0";
			}else{
				$servfilter = $row['servFilter'];			
			}
			$cl_ar = explode(', ', $clfilter);//convert string to array to create control dinamically
			$srv_ar = explode(',', $servfilter);//convert string to array to create control dinamically
			$sqlS ="";
			$sqlW = "";
			foreach ($srv_ar as &$value2) {	
				switch($value2){
					case 4:
						$sqlS = " and isPriority = 1 ";
					break;
					case 5:
						$sqlW = " and isWarranty = 1 ";
					break;
				}
			}
			$sql ="select m.oid,a.busName as 'company', m.tagName, m.po, concat(mu.firstName,' ',mu.lastName) as 'designer', email, s.name as 'status', DATE(m.dateSubmitted) dateSubmitted, DATE(m.dateShipped) dateShipped,m.state, isPriority, isWarranty, CLid, m.deliveryDate from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state in (".$state.") and CLid in(".$clfilter.")".$sqlS.$sqlW." order by m.dateSubmitted asc";
			//echo $sql;
			opendb($sql);
		}
	}else{
		opendb("INSERT INTO employeeSettings (mosUser) VALUES ( ".$_SESSION["userid"] .")");//new user
		opendb("select m.oid,a.busName as 'company', m.tagName, m.po, concat(mu.firstName,' ',mu.lastName) as 'designer', email, s.name as 'status', DATE(m.dateSubmitted) dateSubmitted, m.state, isPriority, isWarranty, CLid, m.deliveryDate from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state > 1 and m.state <> 10 order by m.dateSubmitted asc");
	}


	$result = opendb($sql);
	$dbdata = array();
	while ( $row = $result->fetch_assoc())  {
		$data['oid'] = $row['oid'];
		$data['company'] = $row['company'];
		$data['tagName'] = $row['tagName'];
		$data['po'] = $row['po'];
		$data['designer'] = $row['designer'];
		$data['email'] = $row['email'];
		$data['status'] = array($row['status'],$row['state']);
		$data['dateSubmitted'] = $row['dateSubmitted'];
		$data['isPriority'] = $row['isPriority'];
		$data['isWarranty'] = $row['isWarranty'];
		$data['CLid'] = $row['CLid'];
		$data['deliveryDate'] = $row['deliveryDate'];
		$data['dateShipped'] = $row['dateShipped'];
		//$dbdata[]=$row;
		array_push($dbdata, $data);
	}
	//this returns a json with the data
	echo json_encode($dbdata);
}

if($_POST['mode']=="setFilter"){
	$arr = implode(', ', $_POST['value']);//getting all values from array
	//Checking which filter is going to be updated
	switch($_POST['id']){
		case "stateFilter":
		$sql = "update employeeSettings set mainMenuDefaultStateFilter = \"".$arr."\" where mosUser = " . $_SESSION["userid"];
		break;
		case "clFilter":
		$sql = "update employeeSettings set clFilter = \"".$arr."\" where mosUser = " . $_SESSION["userid"];
		break;
		case "servFilter":
		$sql = "update employeeSettings set servFilter = \"".$arr."\" where mosUser = " . $_SESSION["userid"];
		break;
		default:
		break;
	}
	echo $sql;
	opendb2($sql);
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
	$sql = "select orr.rid, orr.name, orr.cc, COALESCE(orr.fronts,0) fronts, DATE(COALESCE(deliveryDate,dateRequired)) dateRequired, pieces from orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.oid = ".$_POST["oid"]." order by orr.name asc";
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
		//Update delivery date (this is updated only when order status is sucessfully changed to "Detailed and Production Ready")
		$sql = "update mosOrder set ".$_POST['col']." = '".$_POST['val']."',detailedBy = ".$_SESSION["userid"] .", dateDetailed = CURRENT_TIMESTAMP() where oid = ".$_POST['oid'] ;
		opendb($sql);
		//Calculate and Insert or update wrapping and finishing dates into schedule
		$sql2 = "select (select count(1) from schedule ss where ss.rid = orr.rid) exist, (select id from material m where m.id = (select mid from species sp where sp.id = orr.species) ) material, (select finishType from frontFinish ff where ff.id = orr.frontFinish ) finishType, glaze, sheen, orr.rid, orr.name, orr.cc, orr.fronts, DATE(COALESCE(deliveryDate,dateRequired)) dateRequired, COALESCE(orr.pieces,(select count(1) from orderItem oi, item i where oi.iid = i.id and i.isCabinet = 0 and oi.rid = orr.rid))pieces from orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.oid = ".$_POST['oid']." order by orr.name asc";
		//echo $sql2;
		//echo "oid: ".$_POST['oid'].", col: ".$_POST['col'].", val: ".$_POST['val'];
		$result = opendb($sql2);
		$wrapping = scheduleFn($_POST['val'],5);
		//echo $wrapping;
		$finishing = "null";
		$cutting = "null";
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
			$cutting = scheduleFn($finishing,5);
			if($exists==0){			
				$sql2 = "insert into schedule(rid,wrapping,finishing,cutting,updateDate) values(".$row['rid'].",'".$wrapping."','".$finishing."','".$cutting."',curdate())";
			}else{
				$sql2 = "update schedule set wrapping = '".$wrapping."', finishing ='".$finishing."', cutting='".$cutting."' where rid in(select rid from orderRoom where oid = ".$_POST['oid'].")" ;
			}
			echo $sql2;
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
	$sql = "select distinct rid, (select count(1) from orderItem oi, item i where oi.iid = i.id and oi.rid = orr.rid and i.isCabinet=1) boxes,coalesce((select round(sum(qty)) from orderItem oi, item i where oi.iid = i.id and oi.rid = orr.rid and i.isCabinet=0),0) pieces from orderRoom orr where orr.oid =".$_POST["oid"];
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
	Case 1 - Sanding(finishing) (finishing column from schedule table is the base)
	Case 0 - Cutting(cutting column from schedule table is the base)
	Date = 0 means retrieve all the jobs
	$_POST['filter'] when True indicates that only completed jobs on previous departments are displayed
	$_POST['dateType'] indicates what schedule is assigned
	$_POST['mydid'] indicates the department(CNC, Assembly, Sanding, Wrapping, etc.)
	$_POST['displayComp'] indicate if completed jobs will be displayed or not
	$_POST['hideSpan'] when True indicates that only kitchens (Cabinet line 1) will be displayed
	$_POST['onlySpan'] when True indicates that only Span orders(Cabinet Line 3 will be displayed)
	---------------------------------------------------------*/
	//Date functionality
	if($_POST['date']==0)
		$dateFilter = "";
	//Display complete funtionality
	$compF = "";
	$compF2 = "";
	if(strcmp($_POST['displayComp'],"true")==0){
		$compF = " and not exists(select 1 from deptCompleted depc where depc.rid = orr.rid and depc.did=".$_POST['mydid'].") ";
		$compF2 = "and orr2.rid not in(select depc.rid from deptCompleted depc where depc.did=".$_POST['mydid'].")";
	}
	//Cabinet Line Functionality (Hide span or show only span)
	$clFilter = " is not null";
	if(strcmp($_POST['hideSpan'],"true")==0){
		$clFilter = " not in(3)";
	}
	if(strcmp($_POST['onlySpan'],"true")==0){
		$clFilter = " = 3";
	}

	switch ($_POST['dateType']) {
	/******************************************************************************************************************************************************************************
	*	Completition date (Given when order status is changed to "Detailed and production ready")
	*******************************************************************************************************************************************************************************/
    case 3:
    	//Show all jobs when date = 0, or only one week.
		if($_POST['date']==0){
			$dateFilter = "and deliveryDate is not null ";//this can be removed at some time
		}else{
			$dateFilter = "and deliveryDate between '".$_POST['date']."' and '".date('Y-m-d', strtotime($_POST['date']. ' + 6 days'))."' ";
		}
    	//Filter means: Hide jobs not ready on previous station
		if(strcmp($_POST['filter'],"true")==0){
			$sql = "select tagName,po,if(mo.shipAddress<2,'Pick up at Mobel', (select concat(coalesce(unit,' '),' ',street,', ',city,', ',province,' ',country,', ',postalCode) from accountAddress aA where aA.id =mo.shipAddress)) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2, mosOrder mo2 where orr2.oid = mo2.oid and mo2.oid = mo.oid and mo2.deliveryDate = mo2.deliveryDate and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) boxCurQty, (SELECT count(orr2.rid) from orderRoom orr2, mosOrder mo2 where orr2.oid = mo2.oid and mo2.CLid".$clFilter." and mo2.deliveryDate = mo.deliveryDate and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) as rooms,  (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc,fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc where dc.rid = orr.rid and did = ".$_POST['mydid'].") as completed, deliveryDate myDate FROM mosOrder mo, orderRoom orr,deptCompleted dc where mo.oid = orr.oid and orr.rid = dc.rid and state in(5,6) and deliveryDate is not null and dc.did = (select deptId from deptReqd dr where dr.myDeptId = ".$_POST['mydid'].") ".$dateFilter.$compF.$clFilter." order by myDate asc, mo.oid asc";
		}else{
			$sql = "select tagName,po,if(mo.shipAddress<2,'Pick up at Mobel', (select concat(coalesce(unit,' '),' ',street,', ',city,', ',province,' ',country,', ',postalCode) from accountAddress aA where aA.id =mo.shipAddress)) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2 where orr2.oid in (select mo3.oid from mosOrder mo3 where orr2.oid = mo3.oid and mo.deliveryDate = mo3.deliveryDate and mo3.state in(5,6))) boxCurQty, (SELECT count(1) from orderRoom orr2, mosOrder mo2 where orr2.oid = mo2.oid and mo2.CLid".$clFilter." and mo2.deliveryDate = mo.deliveryDate and state in(5,6) ".$compF2.") jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid ".$compF2.") as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc where dc.rid = orr.rid and did = ".$_POST['mydid'].") completed, deliveryDate myDate FROM mosOrder mo, orderRoom orr where mo.oid = orr.oid and state in(5,6) ".$dateFilter.$compF." and mo.CLid".$clFilter." order by myDate asc, mo.oid asc";
		}
        break;
    /******************************************************************************************************************************************************************************
	*	Completition date minus 1 week (Wrapping schedule = Completition date - 5 business days)
	*******************************************************************************************************************************************************************************/
    case 2:
    	//Show all jobs when date = 0, or only one week.
		if($_POST['date']!=0)
				$dateFilter = "and wrapping between '".$_POST['date']."' and '".date('Y-m-d', strtotime($_POST['date']. ' + 6 days'))."' ";
		//Filter means: Hide jobs not ready on previous station
		if(strcmp($_POST['filter'],"true")==0){
			$sql = "select tagName,po,if(mo.shipAddress<2,'Pick up at Mobel', (select concat(coalesce(unit,' '),' ',street,', ',city,', ',province,' ',country,', ',postalCode) from accountAddress aA where aA.id =mo.shipAddress)) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2, schedule s2, mosOrder mo3 where mo3.oid = orr2.oid and mo3.state = 5 and orr2.rid = s2.rid and s2.wrapping = s.wrapping and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) boxCurQty, (select count(1) from schedule s2 where s2.wrapping = s.wrapping and s2.rid in (select dc2.rid from deptCompleted dc2, orderRoom orr2, mosOrder mo2 where dc2.rid = orr2.rid and orr2.oid = mo2.oid and mo2.CLid".$clFilter." and mo2.state = 5 and dc2.did =(select dr2.deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid']."))) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and orr2.rid and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId =".$_POST['mydid'].") and orr2.rid = dc2.rid)) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc2 where dc2.rid = orr.rid and dc2.did = dr.myDeptId) as completed, wrapping myDate, s.updateDate FROM mosOrder mo, orderRoom orr,deptCompleted dc,schedule s,deptReqd dr where mo.oid = orr.oid and orr.rid = s.rid and orr.rid = dc.rid and state = 5 and dr.myDeptId = ".$_POST['mydid']." and dc.did = dr.deptId ".$dateFilter.$compF." and mo.CLid".$clFilter." order by myDate asc, mo.oid asc";
		}else{
			$sql = "select tagName,po,if(mo.shipAddress<2,'Pick up at Mobel', (select concat(coalesce(unit,' '),' ',street,', ',city,', ',province,' ',country,', ',postalCode) from accountAddress aA where aA.id =mo.shipAddress)) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2, mosOrder mo3 where mo3.oid = orr2.oid and mo3.state = 5 and orr2.rid in (select ss.rid from schedule ss where ss.wrapping = s.wrapping)) boxCurQty, (SELECT count(1) roomsDay FROM mosOrder mo3, orderRoom orr2, schedule ss where mo3.oid = orr2.oid and mo3.state = mo.state and mo3.CLid".$clFilter." and orr2.rid = ss.rid and ss.wrapping = s.wrapping ".$compF2.") jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid ".$compF2.") as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc where dc.rid = orr.rid and did = ".$_POST['mydid'].") completed, deliveryDate, wrapping myDate, s.updateDate FROM mosOrder mo, orderRoom orr, schedule s where mo.oid = orr.oid and state = 5 and orr.rid = s.rid ".$dateFilter.$compF." and mo.CLid".$clFilter." order by myDate asc, mo.oid asc";
		}
        break;
    /******************************************************************************************************************************************************************************
	*	Completition date minus 3-5 business days(Finishing schedule = Wrapping date - 3 to 5 business days)
	*******************************************************************************************************************************************************************************/
    case 1:
    	//Show all jobs when date = 0, or only one week.
		if($_POST['date']!=0)
			$dateFilter = "and finishing between '".$_POST['date']."' and '".date('Y-m-d', strtotime($_POST['date']. ' + 6 days'))."' ";
		//Filter means: Hide jobs not ready on previous station
		if(strcmp($_POST['filter'],"true")==0){
			$sql = "select tagName,po,if(mo.shipAddress<2,'Pick up at Mobel', (select concat(coalesce(unit,' '),' ',street,', ',city,', ',province,' ',country,', ',postalCode) from accountAddress aA where aA.id =mo.shipAddress)) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2, schedule s2, mosOrder mo3 where mo3.oid = orr2.oid and mo3.state = 5 and orr2.rid = s2.rid and s2.finishing = s.finishing and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) boxCurQty, (select count(1) from schedule s2 where s2.finishing = s.finishing and s2.rid in (select dc2.rid from deptCompleted dc2, orderRoom orr2, mosOrder mo2 where dc2.rid = orr2.rid and orr2.oid = mo2.oid and mo2.CLid".$clFilter." and mo2.state = 5 and dc2.did =(select dr2.deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid']."))) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and orr2.rid and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId =".$_POST['mydid'].") and orr2.rid = dc2.rid)) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc2 where dc2.rid = orr.rid and dc2.did = dr.myDeptId) as completed, finishing myDate, s.updateDate FROM mosOrder mo, orderRoom orr, deptCompleted dc, schedule s, deptReqd dr where mo.oid = orr.oid and orr.rid = s.rid and orr.rid = dc.rid and state = 5 and dr.myDeptId = ".$_POST['mydid']." and dc.did = dr.deptId ".$dateFilter.$compF." and mo.CLid".$clFilter." order by myDate asc, mo.oid asc";
		}else{
			$sql = "select tagName,po,if(mo.shipAddress<2,'Pick up at Mobel', (select concat(coalesce(unit,' '),' ',street,', ',city,', ',province,' ',country,', ',postalCode) from accountAddress aA where aA.id =mo.shipAddress)) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2 where orr2.rid in (select ss.rid from schedule ss where ss.finishing = s.finishing)) boxCurQty, (SELECT count(1) roomsDay FROM mosOrder mo3, orderRoom orr2, schedule ss where mo3.oid = orr2.oid and mo3.CLid".$clFilter." and mo3.state = mo.state and orr2.rid = ss.rid and ss.finishing = s.finishing ".$compF2.") jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and orr2.rid in (select ss.rid from schedule ss where ss.finishing = s.finishing) ".$compF2.") as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc where dc.rid = orr.rid and did = ".$_POST['mydid'].") completed, deliveryDate, finishing myDate, s.updateDate FROM mosOrder mo, orderRoom orr, schedule s where mo.oid = orr.oid and orr.rid = s.rid and state = 5 ".$dateFilter.$compF." and mo.CLid".$clFilter." order by myDate asc, mo.oid asc";
		}
        break;
    /******************************************************************************************************************************************************************************
	*	Completition date minus 3 weeks(Cutting schedule = Finishing date - 5 business days)
	*******************************************************************************************************************************************************************************/
    case 0:
    	//Show all jobs when date = 0, or only one week.
		if($_POST['date']!=0)
			$dateFilter = "and cutting between '".$_POST['date']."' and '".date('Y-m-d', strtotime($_POST['date']. ' + 6 days'))."' ";
		//Filter means: Hide jobs not ready on previous station
		if(strcmp($_POST['filter'],"true")==0){
			$sql = "select tagName,po,if(mo.shipAddress<2,'Pick up at Mobel', (select concat(coalesce(unit,' '),' ',street,', ',city,', ',province,' ',country,', ',postalCode) from accountAddress aA where aA.id =mo.shipAddress)) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2, schedule s2, mosOrder mo3 where mo3.oid = orr2.oid and mo3.state = 5 and orr2.rid = s2.rid and s2.cutting = s.cutting and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) boxCurQty, (select count(1) from schedule s2 where s2.cutting = s.cutting and s2.rid in (select dc2.rid from deptCompleted dc2, orderRoom orr2, mosOrder mo2 where dc2.rid = orr2.rid and orr2.oid = mo2.oid and mo2.CLid".$clFilter." and mo2.state = 5 and dc2.did =(select dr2.deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid']."))) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and orr2.rid and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId =".$_POST['mydid'].") and orr2.rid = dc2.rid)) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc2 where dc2.rid = orr.rid and dc2.did = dr.myDeptId) as completed, cutting myDate, s.updateDate FROM mosOrder mo, orderRoom orr, deptCompleted dc, schedule s, deptReqd dr where mo.oid = orr.oid and orr.rid = s.rid and orr.rid = dc.rid and state = 5 and dr.myDeptId = ".$_POST['mydid']." and dc.did = dr.deptId ".$dateFilter.$compF." and mo.CLid".$clFilter." order by myDate asc, mo.oid asc";
		}else{
			$sql = "select tagName,po,if(mo.shipAddress<2,'Pick up at Mobel', (select concat(coalesce(unit,' '),' ',street,', ',city,', ',province,' ',country,', ',postalCode) from accountAddress aA where aA.id =mo.shipAddress)) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2 where orr2.rid in (select ss.rid from schedule ss where ss.cutting = s.cutting)) boxCurQty, (SELECT count(1) roomsDay FROM mosOrder mo3, orderRoom orr3, schedule ss where mo3.oid = orr3.oid and mo2.CLid".$clFilter." and mo3.state = mo.state and orr3.rid = ss.rid and ss.cutting = s.cutting) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and orr2.rid in (select ss.rid from schedule ss where ss.cutting = s.cutting)) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc where dc.rid = orr.rid and did = ".$_POST['mydid'].") completed, deliveryDate, cutting myDate, s.updateDate FROM mosOrder mo, orderRoom orr, schedule s where mo.oid = orr.oid and orr.rid = s.rid and state = 5 ".$dateFilter.$compF." and mo.CLid".$clFilter." order by myDate asc, mo.oid asc";
		}
        break;
	default:
        break;
	}
	echo $sql;
	//Once sql statement is selected, query is executed in this moment.
	opendb($sql);
	/*----------------------------------------------------------------
	Classes for every column are used to hide or display on the view. New columns will need new custom classes and options in the view.
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
				echo "<td class=\"align-middle\"><div id=\"uptRoom".$row['rid']."\" class=\"custom-control custom-checkbox\"><input ".$completed." onchange=\"completeRoom(".$row['rid'].")\" type=\"checkbox\" class=\"custom-control-input\" id=\"chkDone".$row['rid']."\"><label class=\"custom-control-label\" for=\"chkDone".$row['rid']."\"></label></div></td></tr>";				
			}else if(strcmp($day,$row['myDate'])==0 && strcmp($oid, $row['oid'])!=0){
				$oid = $row['oid'];
				echo "<th id=\"".$row['oid']."\" class=\"align-middle $updated\" rowspan=\"".$row['rooms']."\" scope=\"rowgroup\"><p>".$row['oid']."</p></br><small><b>".$row['account']."</b></small></th>";//Order id header
				echo "<th class=\"tag align-middle $updated\" rowspan=\"".$row['rooms']."\">".$row['tagName']." - ". $row['po'] ."</th>";
				echo "<th class=\"sht align-middle $updated\" rowspan=\"".$row['rooms']."\">&nbsp".$row['shipTo']."</th>";
				echo "<td class=\"rmnm align-middle $updated\" scope=\"row\">".$row['roomName']."</td>";//header row room
				echo "<td class=\"box align-middle $updated\">".$row['cc']."</td>";
				echo "<td class=\"frt align-middle $updated\">".$row['fronts']."</td>";
				echo "<td class=\"itm align-middle $updated\">".$row['pieces']."</td>";		
				echo "<td class=\"mat align-middle $updated\">".$row['material']."</td>";					
				echo "<td class=\"drs align-middle $updated\">".$row['doorStyle']."</td>";
				echo "<td class=\"fns align-middle $updated\">".$row['finish']."</td>";	
				echo "<td class=\"align-middle\"><div id=\"uptRoom".$row['rid']."\" class=\"custom-control custom-checkbox\"><input ".$completed." onchange=\"completeRoom(".$row['rid'].")\" type=\"checkbox\" class=\"custom-control-input\" id=\"chkDone".$row['rid']."\"><label class=\"custom-control-label\" for=\"chkDone".$row['rid']."\"></label></div></td></tr>";
			}else{
				$day = $row['myDate'];//new day
				$oid = $row['oid'];//new order
				echo "<tr>";
				echo "<th class=\"align-middle\" rowspan=\"".$row['jobsDay']."\" scope=\"rowgroup\"><p>".date("l M j",strtotime($day))."</p><small class=\"d-print-none\">Total boxes: ".$row['boxCurQty']."</small></th>";
				echo "<th id=\"".$row['oid']."\" class=\"align-middle $updated\" rowspan=\"".$row['rooms']."\" scope=\"rowgroup\"><p>".$row['oid']."</p><small><b>".$row['account']."</b></small></th>";//Order id header
				echo "<th class=\"tag align-middle $updated\" rowspan=\"".$row['rooms']."\">".$row['tagName']." - ". $row['po'] ."</th>";
				echo "<th class=\"sht align-middle $updated\" rowspan=\"".$row['rooms']."\">".$row['shipTo']."</th>";
				echo "<td class=\"rmnm align-middle $updated\" scope=\"row\">".$row['roomName']."</td>";//header row room
				echo "<td class=\"box align-middle $updated\">".$row['cc']."</td>";
				echo "<td class=\"frt align-middle $updated\">".$row['fronts']."</td>";
				echo "<td class=\"itm align-middle $updated\">".$row['pieces']."</td>";		
				echo "<td class=\"mat align-middle $updated\">".$row['material']."</td>";					
				echo "<td class=\"drs align-middle $updated\">".$row['doorStyle']."</td>";
				echo "<td class=\"fns align-middle $updated\">".$row['finish']."</td>";					
				echo "<td class=\"align-middle\"><div id=\"uptRoom".$row['rid']."\" class=\"custom-control custom-checkbox\"><input ".$completed." onchange=\"completeRoom(".$row['rid'].")\" type=\"checkbox\" class=\"custom-control-input\" id=\"chkDone".$row['rid']."\"><label class=\"custom-control-label\" for=\"chkDone".$row['rid']."\"></label></div></td></tr>";
			}	
		}
	}else{
		echo "<tr class=\"text-center\"><td colspan=\"12\"><h1 class=\"text-primary\">No data for this week</h1></td></tr>";
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
	switch ($_POST['dept']) {
		case 1: //Dept 1 = Shipping department
			$state = 7;
			if($_POST['action']=='old')
				$state = 5;
			$sql2 = "update mosOrder mo set state = $state where oid = (select distinct orr.oid from orderRoom orr where orr.rid = ".$_POST['rid'].")";
			opendb($sql2);

			$sql3 = "update mosOrder mo set dateShipped = NOW() where oid= (select distinct orr.oid from orderRoom orr where orr.rid = ".$_POST['rid'].")";
			opendb($sql3);
			break;
		
		case 2: //Dept 2 = Wrapping department
			$state = 6;
			if($_POST['action']=='old')
				$state = 5;
			$sql2 = "update mosOrder mo set state = $state where oid in (select distinct orr.oid from orderRoom orr where orr.rid = ".$_POST['rid'].")";
			opendb($sql2);

			$sql3 = "update mosOrder mo set dateCompleted = NOW(),completedBy=".$_SESSION["userid"]." where oid= (select distinct orr.oid from orderRoom orr where orr.rid = ".$_POST['rid'].")";
			opendb($sql3);
			break;
	}

	/*if($_POST['dept']==1){//Dept 1 = Shipping department. You can add more conditions(maybe a switch) to update states once a department has finialized
		$sql = "select oid from mosOrder mo where mo.oid in (select orr.oid from orderRoom orr where orr.rid = ".$_POST['rid'].")";
		$query = opendb($sql);
		$row = $query->fetch_assoc();
		$state = 7;
		if($_POST['action']=='old')
			$state = 5;
		$sql2 = "update mosOrder mo set state = $state where oid=".$row['oid'];
		opendb($sql2);
		if($state == 7){
			$sql3 = "update mosOrder mo set dateShipped = NOW() where oid=".$row['oid'];
			opendb($sql3);
		}
	}*/
	//echo $sql;
	//echo $sql2;
	//echo $sql3;
}

if($_POST['mode']=="countBoxesxDay"){
	$sql = "select coalesce(sum(cc),0) total from orderRoom orr where orr.oid in (select mo.oid from mosOrder mo where mo.oid = orr.oid and mo.deliveryDate = '".$_POST['date']."' and mo.state = 5)";
	//echo $sql;
	$query = opendb($sql);
	$row = $query->fetch_assoc();
	echo $row['total'];
}

if($_POST['mode']=="getRequiredDate"){
	$sql = "select DATE_FORMAT(COALESCE(dateRequired,deliveryDate,curdate()), \"%Y-%m-%d\") mydate from mosOrder where oid =".$_POST['oid'];
	//echo $sql;
	$query = opendb($sql);
	$row = $query->fetch_assoc();
	echo $row['mydate'];
}

if($_POST['mode']=="getOrdFiles"){
    $result =opendb("select mo.account,orf.oid, orf.id, orf.name, coalesce((select orr.name from orderRoom orr where orr.rid=orf.rid),'N/A')roomname from orderFiles orf, mosOrder mo where mo.oid = orf.oid and orf.oid = ".$_POST['oid']);
    if($GLOBALS['$result']->num_rows > 0){
		while($row = $result->fetch_assoc()){
	    	echo "<tr>";
	    	echo "<td>";
	    	echo "<a href=\"#\" onclick=\"window.open('../uploads/DealerFiles/".$row['account']."/".$row['oid']."/" .$row['id'].".".strtolower(pathinfo($row['name'],PATHINFO_EXTENSION))."', '_blank', 'fullscreen=yes'); return false;\">
	    		<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-eye text-primary\" viewBox=\"0 0 16 16\">
				  <path d=\"M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z\"/>
				  <path d=\"M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z\"/>
				</svg>
				</a>
				</td>";
	        echo "<td><b><form action=\"../download.php\" method=\"post\"><input name=\"OGName\" type=\"hidden\" value=\"". $row['name'] . "\"></input><input name=\"DealerFile\" type=\"hidden\" value=\"". $row["account"]."/".$row["oid"]."/" . $row['id'] . "." . strtolower(pathinfo($row['name'],PATHINFO_EXTENSION)). "\" ></input><input type=\"submit\" value=\"" . $row['name'] . "\"/></form></b></td>";
	        echo "<td>" . $row['roomname'] . "</td>";
	        echo "<td>" . "N/A" . "</td>";
	        echo "<td>" . "N/A" . "</td>";
	    	echo "</tr>";
	    }
	}else{
		echo "<tr><td colspan=\"5\"><h3 class=\"text-info\">No files for this order</h3></td></tr>";
	}
}

if($_POST['mode']=="getItemsCat"){
	$sql = "select id, TRIM(name) name from item where description = '".$_POST['cat']."'";
	$result = opendb($sql);
	$items = array();
	while ($row = $result->fetch_assoc()) {
		$items[] = $row;
	}
	echo json_encode($items);
}

if($_POST['mode']=="getCategories"){
	$sql = "select distinct(description) cat from item where CLGroup = 4 order by cat";
	$result = opendb($sql);
	$cat = array();
	while ($row = $result->fetch_assoc()) {
		//$cat[] = htmlspecialchars($row['cat']);
		$cat[] = $row['cat'];
	}
	echo json_encode($cat);
}

if($_POST['mode']=="getItemRow"){
	$sql = "select i.id, i.name, i.description, ROUND(i.W, 2) W, ROUND(i.H, 2) H, ROUND(i.D, 2) D, coalesce(il.cvName,'No Item Code') cvCode, coalesce(il.cvLName,'No Item Code') cvLCode, coalesce(il.cvRName,'No Item Code') cvRCode FROM item i left join itemsLink il on i.id = il.itemId where id=".$_POST['item'];
	$result = opendb($sql);
	$item = array();
	while ($row = $result->fetch_assoc()) {
		$item[] = $row;
	}
	echo json_encode($item);
}

if($_POST['mode']=="getMultipleItemsRows"){
	$sql = "select i.id, i.name, i.description, ROUND(i.W, 2) W, ROUND(i.H, 2) H, ROUND(i.D, 2) D, coalesce(il.cvName,'No Item Code') cvCode, coalesce(il.cvLName,'No Item Code') cvLCode, coalesce(il.cvRName,'No Item Code') cvRCode FROM item i left join itemsLink il on i.id = il.itemId where id in(".implode(',',$_POST['items']).")";
	$result = opendb($sql);
	$item = array();
	while ($row = $result->fetch_assoc()) {
		$item[] = $row;
	}
	echo json_encode($item);
}

if($_POST['mode']=="linkItems"){

	switch ($_POST['door']) {
		case 'B': //No Door or double door
			$column = "cvName";
			break;
		
		case 'L': //Left Door
			$column = "cvLName";
			break;

		case 'R': //Right Door
			$column = "cvRName";
			break;
	}
	//First update items
	$update = "update itemsLink set ".$column."='".strtoupper($_POST['cv'])."' where itemId in(".implode(',',$_POST['items']).")";
	$resultU = opendb($update);
	//Insert items not present on the list
	$sql = "select id from (select id from item ii where ii.id in(".implode(',',$_POST['items']).")) i where not exists(select 1 from itemsLink il where i.id = il.itemId)";
	$insert = "";
	$result = opendb($sql);
	while ($row = $result->fetch_assoc()) {
		$insert .= "insert into itemsLink(itemId,".$column.") values(".$row['id'].",'".strtoupper($_POST['cv'])."'); ";
	}	
	opendbmulti($insert);
}
?>
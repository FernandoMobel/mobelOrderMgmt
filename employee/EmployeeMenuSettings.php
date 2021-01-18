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
	/*$arr = implode(', ', $_POST['value']);//getting all values from array
	$sql = "select m.oid,a.busName as 'company', m.tagName, m.po, concat(mu.firstName,' ',mu.lastName) as 'designer', email, s.name as 'status', DATE(m.dateSubmitted) dateSubmitted, m.state from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state in (".$arr.") order by m.state desc";
	$result = opendb($sql);
	$dbdata = array();
	while ( $row = $result->fetch_assoc())  {
		$dbdata[]=$row;
	}*/
	//this returns a json with the data
	//echo json_encode($dbdata);



	opendb2("select mainMenuDefaultStateFilter as state, clFilter, servFilter from employeeSettings where mosUser = " .$_SESSION["userid"]);
	if($GLOBALS['$result2']-> num_rows >0){	
		foreach ($GLOBALS['$result2'] as $row2) {
			$state = $row2['state'];
			$state_ar = explode(', ', $state);//convert string to array to create control dinamically
			if($row2['clFilter']==""){
				$clfilter = '1,2,3';
			}else{
				$clfilter = $row2['clFilter'];			
			}
			if($row2['servFilter']==""){
				$servfilter = "0";
			}else{
				$servfilter = $row2['servFilter'];			
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
			$sql ="select m.oid,a.busName as 'company', m.tagName, m.po, concat(mu.firstName,' ',mu.lastName) as 'designer', email, s.name as 'status', DATE(m.dateSubmitted) dateSubmitted, m.state, isPriority, isWarranty, CLid, m.deliveryDate from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state in (".$state.") and CLid in(".$clfilter.")".$sqlS.$sqlW." order by m.dateSubmitted asc";
			//echo $sql;
			opendb($sql);
		}
	}else{
		opendb("INSERT INTO employeeSettings (mosUser) VALUES ( ".$_SESSION["userid"] .")");//new user
		opendb("select m.*,DATE(m.dateSubmitted) dateSubmitted,s.name as 'status', a.busName as 'company', concat(mu.firstName,' ',mu.lastName) as 'designer', email, m.state, isPriority, isWarranty, CLid, m.deliveryDate from mosOrder m, state s, account a, mosUser mu where s.id = m.state and m.account = a.id and m.mosUser = mu.id and m.state > 1 and m.state <> 10 order by m.dateSubmitted asc");
	}	
	
	if($GLOBALS['$result']-> num_rows >0){	
		foreach ($GLOBALS['$result'] as $row) {
			$orderType="";
			if($row['isPriority']==1)
				$orderType="table-warning";
			if($row['isWarranty']==1)
				$orderType="table-danger";
			if($row['CLid']==3)
				$orderType="table-primary";
			if($row['CLid']==2)
				$orderType="table-info";
			echo "<tr class=\"$orderType\">";
			echo "<td>
					<a class=\"onlyhover\" href=\"#\" onclick=\"viewOrder(".$row['oid'].")\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-eye text-primary\" viewBox=\"0 0 16 16\">
							<path d=\"M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z\"/>
							<path d=\"M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z\"/>
					</svg></a>
					<a class=\"onlyhover\" onclick=\"getOrdFiles(".$row['oid'].");\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-folder2-open text-primary\" viewBox=\"0 0 16 16\">
					  <path d=\"M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v.64c.57.265.94.876.856 1.546l-.64 5.124A2.5 2.5 0 0 1 12.733 15H3.266a2.5 2.5 0 0 1-2.481-2.19l-.64-5.124A1.5 1.5 0 0 1 1 6.14V3.5zM2 6h12v-.5a.5.5 0 0 0-.5-.5H9c-.964 0-1.71-.629-2.174-1.154C6.374 3.334 5.82 3 5.264 3H2.5a.5.5 0 0 0-.5.5V6zm-.367 1a.5.5 0 0 0-.496.562l.64 5.124A1.5 1.5 0 0 0 3.266 14h9.468a1.5 1.5 0 0 0 1.489-1.314l.64-5.124A.5.5 0 0 0 14.367 7H1.633z\"/>
					</svg></a>
					<b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['oid']."</b></td>";
			echo "<td><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['company']."</b></td>";
			echo "<td><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">" . $row['tagName'] . "</td>";
			echo "<td><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">" . $row['po'] . "</td>";	
			echo "<td><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['designer']."</b></td>";
			echo "<td><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['email']."</b></td>";
			echo "<td>";
			echo "<select disabled onchange=\"saveOrder('state','" . $row['oid'] . "');\" id=\"state".$row['oid']."\" onfocus=\"setPrevious(this.value)\">";
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
			echo "<td  data-toggle=\"tooltip\" title=\"Submitted date\"><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['dateSubmitted']."</b></td>";	
			echo "<td  data-toggle=\"tooltip\" title=\"Completition date\"><b><a href=\"http://".$_SERVER['SERVER_NAME'].$local."/Order.php?OID=" . $row['oid'] . "\">".$row['deliveryDate']."</b></td>";		
			echo "</tr>";
		}
	}else{
		//echo "<tr>No data for that search criteria.</tr><tr>Please change your filter to see some records.</tr>";
	}
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
		$sql = "update mosOrder set ".$_POST['col']." = '".$_POST['val']."',detailedBy = ".$_SESSION["userid"] ." where oid = ".$_POST['oid'] ;
		opendb($sql);
		//Calculate and Insert or update wrapping and finishing dates into schedule
		$sql2 = "select (select count(1) from schedule ss where ss.rid = orr.rid) exist, (select id from material m where m.id = (select mid from species sp where sp.id = orr.species) ) material, (select finishType from frontFinish ff where ff.id = orr.frontFinish ) finishType, glaze, sheen, orr.rid, orr.name, orr.cc, orr.fronts, DATE(COALESCE(deliveryDate,dateRequired)) dateRequired, COALESCE(orr.pieces,(select count(1) from orderItem oi, item i where oi.iid = i.id and i.isCabinet = 0 and oi.rid = orr.rid))pieces from orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.oid = ".$_POST['oid']." order by orr.name asc";
		//echo $sql2;
		//echo "oid: ".$_POST['oid'].", col: ".$_POST['col'].", val: ".$_POST['val'];
		$result = opendb($sql2);
		$wrapping = scheduleFn($_POST['val'],5);
		//echo $wrapping;
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
			//echo $finishing . "/n";
			if($exists==0){			
				$sql2 = "insert into schedule(rid,wrapping,finishing,updateDate) values(".$row['rid'].",'".$wrapping."','".$finishing."',curdate())";
			}else{
				$sql2 = "update schedule set wrapping = '".$wrapping."', finishing ='".$finishing."' where rid in(select rid from orderRoom where oid = ".$_POST['oid'].")" ;
			}
			echo $sql2;
			opendb($sql2);
		}
		//Update control date when status Detailed and Production ready
		$sql2 = "update mosOrder set dateDetailed = CURRENT_TIMESTAMP() where oid = ".$_POST['oid'];
		opendb($sql2);
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
	$sql = "select distinct rid, (select count(1) from orderItem oi, item i where oi.iid = i.id and oi.rid = orr.rid and i.isCabinet=1) boxes,(select round(sum(qty)) from orderItem oi, item i where oi.iid = i.id and oi.rid = orr.rid and i.isCabinet=0) pieces from orderRoom orr where orr.oid =".$_POST["oid"];
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
			$sql = "(select concat(coalesce(unit,' '),' ',street,', ',city,', ',province,' ',country,', ',postalCode)  from accountAddress aA where aA.id =mo.shipAddress) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2, mosOrder mo2 where orr2.oid = mo2.oid and mo2.oid = mo.oid and mo2.deliveryDate = mo2.deliveryDate and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) boxCurQty, (SELECT count(orr2.rid) from orderRoom orr2, mosOrder mo2 where orr2.oid = mo2.oid and mo2.deliveryDate = mo.deliveryDate and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and orr2.rid and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) as rooms,  (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc,fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc where dc.rid = orr.rid and did = ".$_POST['mydid'].") as completed, deliveryDate myDate FROM mosOrder mo, orderRoom orr,deptCompleted dc where mo.oid = orr.oid and orr.rid = dc.rid and state = 5 and deliveryDate is not null and dc.did = (select deptId from deptReqd dr where dr.myDeptId = ".$_POST['mydid'].") ".$dateFilter." order by myDate asc, mo.oid asc";
		}else{
			if($_POST['date']==0){
				$dateFilter = "and deliveryDate is not null";
			}else{
				$dateFilter = "and deliveryDate between '".$_POST['date']."' and '".date('Y-m-d', strtotime($_POST['date']. ' + 6 days'))."'";
			}
			$sql = "select (select concat(coalesce(unit,' '),' ',street,', ',city,', ',province,' ',country,', ',postalCode)  from accountAddress aA where aA.id =mo.shipAddress) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2 where orr2.oid in (select mo3.oid from mosOrder mo3 where orr2.oid = mo3.oid and mo.deliveryDate = mo3.deliveryDate and mo3.state = 5)) boxCurQty, (SELECT count(1) from orderRoom orr3, mosOrder mo2 where orr3.oid = mo2.oid and mo2.deliveryDate = mo.deliveryDate and state = 5) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc where dc.rid = orr.rid and did = ".$_POST['mydid'].") completed, deliveryDate myDate FROM mosOrder mo, orderRoom orr where mo.oid = orr.oid and state = 5 ".$dateFilter." order by myDate asc, mo.oid asc";
		}
        break;
    case 2:
		if($_POST['date']!=0)
				$dateFilter = "and wrapping between '".$_POST['date']."' and '".date('Y-m-d', strtotime($_POST['date']. ' + 6 days'))."'";
		if(strcmp($_POST['filter'],"true")==0){
			$sql = "select (select concat(coalesce(unit,' '),' ',street,', ',city,', ',province,' ',country,', ',postalCode)  from accountAddress aA where aA.id =mo.shipAddress) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2, schedule s2, mosOrder mo3 where mo3.oid = orr2.oid and mo3.state = 5 and orr2.rid = s2.rid and s2.wrapping = s.wrapping and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) boxCurQty, (select count(1) from schedule s2 where s2.wrapping = s.wrapping and s2.rid in (select dc2.rid from deptCompleted dc2, orderRoom orr2, mosOrder mo2 where dc2.rid = orr2.rid and orr2.oid = mo2.oid and mo2.state = 5 and dc2.did =(select dr2.deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid']."))) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and orr2.rid and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId =".$_POST['mydid'].") and orr2.rid = dc2.rid)) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc2 where dc2.rid = orr.rid and dc2.did = dr.myDeptId) as completed, wrapping myDate, s.updateDate FROM mosOrder mo, orderRoom orr,deptCompleted dc,schedule s,deptReqd dr where mo.oid = orr.oid and orr.rid = s.rid and orr.rid = dc.rid and state = 5 and dr.myDeptId = ".$_POST['mydid']." and dc.did = dr.deptId ".$dateFilter." order by myDate asc, mo.oid asc";
		}else{
			$sql = "select (select concat(coalesce(unit,' '),' ',street,', ',city,', ',province,' ',country,', ',postalCode)  from accountAddress aA where aA.id =mo.shipAddress) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2 where orr2.rid in (select ss.rid from schedule ss where ss.wrapping = s.wrapping)) boxCurQty, (SELECT count(1) roomsDay FROM orderRoom orr, schedule ss where orr.rid = ss.rid and ss.wrapping = s.wrapping) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc where dc.rid = orr.rid and did = ".$_POST['mydid'].") completed, deliveryDate, wrapping myDate, s.updateDate FROM mosOrder mo, orderRoom orr, schedule s where mo.oid = orr.oid and orr.rid = s.rid ".$dateFilter." order by myDate asc, mo.oid asc";
		}
        break;
    case 1:
		if($_POST['date']!=0)
			$dateFilter = "and finishing between '".$_POST['date']."' and '".date('Y-m-d', strtotime($_POST['date']. ' + 6 days'))."'";
		if(strcmp($_POST['filter'],"true")==0){
			$sql = "select (select concat(coalesce(unit,' '),' ',street,' ',city,' ',province,' ',country,' ',postalCode)  from accountAddress aA where aA.id =mo.shipAddress) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2, schedule s2, mosOrder mo3 where mo3.oid = orr2.oid and mo3.state = 5 and orr2.rid = s2.rid and s2.finishing = s.finishing and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid'].") and orr2.rid = dc2.rid)) boxCurQty, (select count(1) from schedule s2 where s2.finishing = s.finishing and s2.rid in (select dc2.rid from deptCompleted dc2, orderRoom orr2, mosOrder mo2 where dc2.rid = orr2.rid and orr2.oid = mo2.oid and mo2.state = 5 and dc2.did =(select dr2.deptId from deptReqd dr2 where dr2.myDeptId = ".$_POST['mydid']."))) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and orr2.rid and exists (select 1 from deptCompleted dc2 where dc2.did =(select deptId from deptReqd dr2 where dr2.myDeptId =".$_POST['mydid'].") and orr2.rid = dc2.rid)) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc2 where dc2.rid = orr.rid and dc2.did = dr.myDeptId) as completed, finishing myDate, s.updateDate FROM mosOrder mo, orderRoom orr, deptCompleted dc, schedule s, deptReqd dr where mo.oid = orr.oid and orr.rid = s.rid and orr.rid = dc.rid and state = 5 and dr.myDeptId = ".$_POST['mydid']." and dc.did = dr.deptId ".$dateFilter." order by myDate asc, mo.oid asc";
		}else{
			$sql = "select (select concat(coalesce(unit,' '),' ',street,' ',city,' ',province,' ',country,' ',postalCode)  from accountAddress aA where aA.id =mo.shipAddress) shipTo,(select busDBA from account aa where aa.id = mo.account) account,mo.oid, orr.rid, (select sum(cc) from orderRoom orr2 where orr2.rid in (select ss.rid from schedule ss where ss.finishing = s.finishing)) boxCurQty, (SELECT count(1) roomsDay FROM orderRoom orr, schedule ss where orr.rid = ss.rid and ss.finishing = s.finishing) jobsDay, (select count(1) from orderRoom orr2 where orr2.oid = orr.oid and orr2.rid in (select ss.rid from schedule ss where ss.finishing = s.finishing)) as rooms, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, pieces, (SELECT if(count(1)>0,'true','false') exist FROM deptCompleted dc where dc.rid = orr.rid and did = ".$_POST['mydid'].") completed, deliveryDate, finishing myDate, s.updateDate FROM mosOrder mo, orderRoom orr, schedule s where mo.oid = orr.oid and orr.rid = s.rid ".$dateFilter." order by myDate asc, mo.oid asc";
		}
        break;
	default:
        break;
	}
	//echo $sql;
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
				echo "<td class=\"sht align-middle $updated\">".$row['shipTo']."</td>";	
				echo "<td><div id=\"uptRoom".$row['rid']."\" class=\"custom-control custom-checkbox\"><input ".$completed." onchange=\"completeRoom(".$row['rid'].")\" type=\"checkbox\" class=\"custom-control-input\" id=\"chkDone".$row['rid']."\"><label class=\"custom-control-label\" for=\"chkDone".$row['rid']."\"></label></div></td></tr>";				
			}else if(strcmp($day,$row['myDate'])==0 && strcmp($oid, $row['oid'])!=0){
				$oid = $row['oid'];
				echo "<td id=\"".$row['oid']."\" class=\"align-middle $updated\" rowspan=\"".$row['rooms']."\" scope=\"rowgroup\"><b>".$row['oid']."</b></br><small>".$row['account']."</small></td>";//Order id header
				echo "<td class=\"rmnm $updated\" scope=\"row\">".$row['roomName']."</td>";//header row room
				echo "<td class=\"box $updated\">".$row['cc']."</td>";
				echo "<td class=\"frt $updated\">".$row['fronts']."</td>";
				echo "<td class=\"itm $updated\">".$row['pieces']."</td>";		
				echo "<td class=\"mat $updated\">".$row['material']."</td>";					
				echo "<td class=\"drs $updated\">".$row['doorStyle']."</td>";
				echo "<td class=\"fns $updated\">".$row['finish']."</td>";	
				echo "<td class=\"sht align-middle $updated\">".$row['shipTo']."</td>";
				echo "<td><div id=\"uptRoom".$row['rid']."\" class=\"custom-control custom-checkbox\"><input ".$completed." onchange=\"completeRoom(".$row['rid'].")\" type=\"checkbox\" class=\"custom-control-input\" id=\"chkDone".$row['rid']."\"><label class=\"custom-control-label\" for=\"chkDone".$row['rid']."\"></label></div></td></tr>";
			}else{
				$day = $row['myDate'];//new day
				$oid = $row['oid'];//new order
				echo "<tr>";
				echo "<td class=\"align-middle\" rowspan=\"".$row['jobsDay']."\" scope=\"rowgroup\">".date("l M j",strtotime($day))."</br><small class=\"d-print-none\">Total boxes: ".$row['boxCurQty']."</small></td>";
				echo "<td id=\"".$row['oid']."\" class=\"align-middle $updated\" rowspan=\"".$row['rooms']."\" scope=\"rowgroup\"><b>".$row['oid']."</b></br><small>".$row['account']."</small></td>";//Order id header
				echo "<td class=\"rmnm align-middle $updated\" scope=\"row\">".$row['roomName']."</td>";//header row room
				echo "<td class=\"box align-middle $updated\">".$row['cc']."</td>";
				echo "<td class=\"frt align-middle $updated\">".$row['fronts']."</td>";
				echo "<td class=\"itm align-middle $updated\">".$row['pieces']."</td>";		
				echo "<td class=\"mat align-middle $updated\">".$row['material']."</td>";					
				echo "<td class=\"drs align-middle $updated\">".$row['doorStyle']."</td>";
				echo "<td class=\"fns align-middle $updated\">".$row['finish']."</td>";	
				echo "<td class=\"sht align-middle $updated\">".$row['shipTo']."</td>";
				echo "<td><div id=\"uptRoom".$row['rid']."\" class=\"custom-control custom-checkbox\"><input ".$completed." onchange=\"completeRoom(".$row['rid'].")\" type=\"checkbox\" class=\"custom-control-input\" id=\"chkDone".$row['rid']."\"><label class=\"custom-control-label\" for=\"chkDone".$row['rid']."\"></label></div></td></tr>";
			}	
		}
	}else{
		echo "<tr class=\"text-center\"><td colspan=\"11\"><h1 class=\"text-primary\">No data for this week</h1></td></tr>";
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
?>
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

include 'includes/db.php';?>
<?php
if($_POST['mode']=="getAllJobs"){
 $sql = "SELECT orderCode as title, schedComplDt as start, CASE WHEN orderCode like 'SKB%' THEN \"red\" WHEN orderCode like 'DIY%' THEN \"green\" ELSE \"blue\" END as color FROM mobelSch2020 where completedDt is null and schedComplDt is not null";
 $result = opendb($sql);
 $dbdata = array();
 while ( $row = $result->fetch_assoc())  {
	$dbdata[]=$row;
  }
  echo json_encode($dbdata);
  //echo $sql;
}

if($_POST['mode']=="getSchedule"){
	switch($_POST['schID']){
		case '4':
			$sql = "select calendarDay start, 'background' display, 'pink' color from calendar where workDayInd = 0";
			$result = opendb($sql);
			while ( $row = $result->fetch_assoc())  {
				$dbdata[]=$row;
			}
			echo json_encode($dbdata);
		break;
		case '0':
			$sql ="select distinct orr.oid id, concat('OID: ',orr.oid,' - ',mo.tagName) title, cutting start, 'true' allDay, CASE WHEN updateDate=curdate() THEN '#f5bf42' WHEN CLid=1 THEN '#dee2e6' WHEN CLid=2 THEN '#86cfda' WHEN CLid=3 THEN '#7abaff' ELSE '' END as color, 'black' textColor from schedule s, orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.rid = s.rid and state=5";
			$result = opendb($sql);
			$dbdata = array();
			while ( $row = $result->fetch_assoc())  {
				$dbdata[]=$row;
			}
			//---getting hollidays
			$sql = "select calendarDay start, 'background' display, 'pink' color from calendar where workDayInd = 0";
			$result = opendb($sql);
			while ( $row = $result->fetch_assoc())  {
				$dbdata[]=$row;
			}
			echo json_encode($dbdata);
		break;
		case '1':
			$sql ="select distinct orr.oid id, concat('OID: ',orr.oid,' - ',mo.tagName) title, finishing start, 'true' allDay, CASE WHEN updateDate=curdate() THEN '#f5bf42' WHEN CLid=1 THEN '#dee2e6' WHEN CLid=2 THEN '#86cfda' WHEN CLid=3 THEN '#7abaff' ELSE '' END as color, 'black' textColor from schedule s, orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.rid = s.rid and state=5";
			$result = opendb($sql);
			$dbdata = array();
			while ( $row = $result->fetch_assoc())  {
				$dbdata[]=$row;
			}
			//---getting hollidays
			$sql = "select calendarDay start, 'background' display, 'pink' color from calendar where workDayInd = 0";
			$result = opendb($sql);
			while ( $row = $result->fetch_assoc())  {
				$dbdata[]=$row;
			}
			echo json_encode($dbdata);
		break;
		case '2':
			$sql ="select distinct orr.oid id, concat('OID: ',orr.oid,' - ',mo.tagName) title, wrapping start, 'true' allDay, CASE WHEN updateDate=curdate() THEN '#f5bf42' WHEN CLid=1 THEN '#dee2e6' WHEN CLid=2 THEN '#86cfda' WHEN CLid=3 THEN '#7abaff' ELSE '' END as color, 'black' textColor from schedule s, orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.rid = s.rid and state=5";
			$result = opendb($sql);
			$dbdata = array();
			while ( $row = $result->fetch_assoc())  {
				$dbdata[]=$row;
			}
			//---getting hollidays
			$sql = "select calendarDay start, 'background' display, 'pink' color from calendar where workDayInd = 0";
			$result = opendb($sql);
			while ( $row = $result->fetch_assoc())  {
				$dbdata[]=$row;
			}
			echo json_encode($dbdata);
		break;
		case '3':
			$sql ="select distinct orr.oid id, concat('OID: ',orr.oid,' - ',mo.tagName) title, deliveryDate start, 'true' allDay, CASE WHEN updateDate=curdate() THEN '#f5bf42' WHEN CLid=1 THEN '#dee2e6' WHEN CLid=2 THEN '#86cfda' WHEN CLid=3 THEN '#7abaff' ELSE '' END as color, 'black' textColor from schedule s, orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.rid = s.rid and state=5";
			$result = opendb($sql);
			$dbdata = array();
			while ( $row = $result->fetch_assoc())  {
				$dbdata[]=$row;
			}
			//---getting hollidays
			$sql = "select calendarDay start, 'background' display, 'pink' color from calendar where workDayInd = 0";
			$result = opendb($sql);
			while ( $row = $result->fetch_assoc())  {
				$dbdata[]=$row;
			}
			echo json_encode($dbdata);
		break;
	}
}



if($_POST['mode']=="getLimitDate"){
	switch($_POST['schID']){
		case '2': //Getting completion date as limit
			$sql = "select deliveryDate as limitD from mosOrder where oid =".$_POST['oid'];
		break;
		case '1': //Getting wrapping date as limit
			$sql = "select max(wrapping) as limitD from schedule s, orderRoom orr where orr.rid = s.rid and orr.oid =".$_POST['oid'];
		break;
		case '0': //Getting finishing date as limit
			$sql = "select max(finishing) as limitD from schedule s, orderRoom orr where orr.rid = s.rid and orr.oid =".$_POST['oid'];
		break;
	}
	$result = opendb($sql);
	$row = $result->fetch_assoc();
	echo $row['limitD'];
}

if($_POST['mode']=="updateDate"){
	switch($_POST['schID']){
		case '0': //Cutting
			$sql = "update schedule set cutting ='".$_POST['date']."', updateDate=curdate() where rid in(select orr.rid from orderRoom orr where orr.oid =".$_POST['oid'].")";
			opendb($sql);
		break;
		case '1': //Finishing
			$cutting = scheduleFn($_POST['date'],5);
			$sql = "update schedule set finishing ='".$_POST['date']."', cutting='".$cutting."', updateDate=curdate() where rid in(select orr.rid from orderRoom orr where orr.oid =".$_POST['oid'].")";
			opendb($sql);
		break;
		case '2': //Wrapping
			$sql = "update schedule set wrapping ='".$_POST['date']."', updateDate=curdate() where rid in(select orr.rid from orderRoom orr where orr.oid =".$_POST['oid'].")";
			opendb($sql);
			
			//Calculate Finishing date
			$sql2 = "select rid,(select id from material m where m.id = (select mid from species sp where sp.id = orr.species)) material, (select finishType from frontFinish ff where ff.id = orr.frontFinish ) finishType, glaze, sheen from orderRoom orr where orr.oid =".$_POST['oid']." order by material asc";
			$result = opendb($sql2);
			$finishing = "null";
			$cutting = "null";
			$daysF = 0;
			$daysF2 = 3;
			while($row = $result->fetch_assoc()){ 		
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
				$finishing = scheduleFn($_POST['date'],$daysF2);
				$cutting = scheduleFn($finishing,5);
				$sql2 = "update schedule set finishing ='".$finishing."', cutting='".$cutting."' where rid =".$row['rid'];
				opendb2($sql2);
			}
		break;
		default:
		break;
	}
	//echo $sql;
}

if($_POST['mode']=="getTotalDay"){
	switch($_POST['schID']){
		case '0':
			$sql = "select coalesce(sum(cc),0) cc,coalesce(sum(fronts),0) fronts, coalesce(sum(pieces),0) pieces from orderRoom orr, schedule s, mosOrder mo where s.rid = orr.rid and mo.oid = orr.oid and state = 5 and s.cutting='".$_POST['date']."'";
		break;
		case '1':
			$sql = "select coalesce(sum(cc),0) cc,coalesce(sum(fronts),0) fronts, coalesce(sum(pieces),0) pieces from orderRoom orr, schedule s, mosOrder mo where s.rid = orr.rid and mo.oid = orr.oid and state = 5 and s.finishing='".$_POST['date']."'";
		break;
		case '2':
			$sql = "select coalesce(sum(cc),0) cc,coalesce(sum(fronts),0) fronts, coalesce(sum(pieces),0) pieces from orderRoom orr, schedule s, mosOrder mo where s.rid = orr.rid and mo.oid = orr.oid and state = 5 and s.wrapping='".$_POST['date']."'";
		break;
		case '3':
			$sql = "select coalesce(sum(cc),0) cc,coalesce(sum(fronts),0) fronts, coalesce(sum(pieces),0) pieces from orderRoom orr, mosOrder mo where mo.oid = orr.oid and state = 5 and mo.deliveryDate='".$_POST['date']."'";
		break;
	}
	
	$result = opendb($sql);
	$row = $result->fetch_assoc();
	$totals = array();
	$totals = $row;
	echo json_encode($totals);
}

if ($_POST['mode']=="getScheduleMain"){
	//$boxesLimit = 45; //capacity per day
	//$dailySum = 0; 
	//$i=1; //count
	//$open = false;//Flag to indicate when row day is open
	//$pivotRow = "";
	$oid = 0;//order id
	//--------------------------------------------------
	$sql = "select mo.oid, (select wrapping from schedule s where s.oid = orr.oid) wrapping, (select sum(cc) from orderRoom orr2 where orr2.oid = orr.oid) as totalorder, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontFinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, (select count(1) from orderItem oi where oi.rid = orr.rid) as items, deliveryDate FROM mosOrder mo, orderRoom orr where mo.oid = orr.oid and deliveryDate is not null order by deliveryDate desc, oid";
	$query = opendb($sql);
	$order = array();
	$extendedProps = array();
	while($row = $query->fetch_assoc()){
		//$available = checkAva($row['wrapping']);
		//$wrapping = scheduleFn($row['deliveryDate'],5);
		if(strcmp($oid, $row['oid'])==0){//displaying new room same order			
			$data['extendedProps']['room'] = $row['roomName'];
			$data['extendedProps']['material'] = $row['material'];
			$data['extendedProps']['doorStyle'] = $row['doorStyle'];
			$data['extendedProps']['finish'] = $row['finish'];
			$data['extendedProps']['boxes'] = $row['cc'];
			$data['extendedProps']['fronts'] = $row['fronts'];					
			array_push($order, $data);
		}else{
			$oid = $row['oid'];//Remember the current order
			$data['id'] = $row['oid'];
			$data['title'] = $row['oid'];
			$data['start'] = $row['wrapping'];
			$data['extendedProps']['room'] = $row['roomName'];
			$data['extendedProps']['material'] = $row['material'];
			$data['extendedProps']['doorStyle'] = $row['doorStyle'];
			$data['extendedProps']['finish'] = $row['finish'];
			$data['extendedProps']['boxes'] = $row['cc'];
			$data['extendedProps']['fronts'] = $row['fronts'];						
		}
	}
	echo json_encode($order);
}

if($_POST['mode']=="getOrderRooms"){ 
	if($_POST["oid"]){
		$sql = "select orr.rid, orr.name, orr.cc, COALESCE(orr.fronts,0) fronts, DATE(deliveryDate) dateRequired, pieces from orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.oid = ".$_POST["oid"]." order by orr.name asc";
		$query = opendb($sql);
		$order = array();
		while($row = $query->fetch_assoc()){ 
			$order[] = $row;
		}
		echo json_encode($order);	
	}	
}

if($_POST['mode']=="getJobFullSch"){ 
	if($_POST["oid"]){
		$orderFullSch = array();
		//Stage1
		$sql = "select orr.rid as id, orr.name title, DATE(DATE_ADD(dateDetailed, INTERVAL 1 DAY)) start, DATE(cutting) end, '#00c434' color  from orderRoom orr, mosOrder mo, schedule s where mo.oid = orr.oid and orr.rid = s.rid and orr.rid = s.rid and orr.oid = ".$_POST["oid"];
		$query = opendb($sql);
		while($row = $query->fetch_assoc()){ 
			$orderFullSch[] = $row;
		}
		//Stage2
		$sql = "select orr.rid as id, orr.name title, DATE(cutting) start, DATE(finishing) end, '#e6ed18' color  from orderRoom orr, mosOrder mo, schedule s where mo.oid = orr.oid and orr.rid = s.rid and orr.rid = s.rid and orr.oid = ".$_POST["oid"];
		$query = opendb($sql);
		while($row = $query->fetch_assoc()){ 
			$orderFullSch[] = $row;
		}
		//Stage3
		$sql = "select orr.rid as id, orr.name title, DATE(finishing) start, DATE(wrapping) end, '#edb90c' color  from orderRoom orr, mosOrder mo, schedule s where mo.oid = orr.oid and orr.rid = s.rid and orr.rid = s.rid and orr.oid = ".$_POST["oid"];
		$query = opendb($sql);
		while($row = $query->fetch_assoc()){ 
			$orderFullSch[] = $row;
		}
		//FinalStage
		$sql = "select orr.rid as id, orr.name title, DATE(wrapping) start, DATE(deliveryDate) end, '#ed260c' color  from orderRoom orr, mosOrder mo, schedule s where mo.oid = orr.oid and orr.rid = s.rid and orr.rid = s.rid and orr.oid = ".$_POST["oid"];
		$query = opendb($sql);
		while($row = $query->fetch_assoc()){ 
			$orderFullSch[] = $row;
		}
		echo json_encode($orderFullSch);	
	}	
}

if($_POST['mode']=="getDateOrdDetails"){ 
	switch($_POST['schID']){
		case '0':
			$sql = "select orr.oid id, concat('OID: ',orr.oid,' - ',mo.tagName) title, finishing start, 'true' allDay, CLid, sum(cc) cc, coalesce(sum(fronts),0) fronts, sum(pieces) pieces from schedule s, orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.rid = s.rid and state=5 and cutting = '".$_POST['date']."' group by orr.oid, title, start, allDay, CLid";
		break;
		case '1':
			$sql = "select orr.oid id, concat('OID: ',orr.oid,' - ',mo.tagName) title, finishing start, 'true' allDay, CLid, sum(cc) cc, coalesce(sum(fronts),0) fronts, sum(pieces) pieces from schedule s, orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.rid = s.rid and state=5 and finishing = '".$_POST['date']."' group by orr.oid, title, start, allDay, CLid";
		break;
		case '2':
			$sql = "select orr.oid id, concat('OID: ',orr.oid,' - ',mo.tagName) title, wrapping start, 'true' allDay, CLid, sum(cc) cc, coalesce(sum(fronts),0) fronts, sum(pieces) pieces from schedule s, orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.rid = s.rid and state=5 and wrapping = '".$_POST['date']."' group by orr.oid, title, start, allDay, CLid";
		break;
		case '3':
			$sql = "select orr.oid id, concat('OID: ',orr.oid,' - ',mo.tagName) title, deliveryDate start, 'true' allDay, CLid, sum(cc) cc, coalesce(sum(fronts),0) fronts, sum(pieces) pieces from schedule s, orderRoom orr, mosOrder mo where mo.oid = orr.oid and orr.rid = s.rid and state=5 and deliveryDate = '".$_POST['date']."' group by orr.oid, title, start, allDay, CLid";
		break;
	}

	$query = opendb($sql);
	$orders = array();
	while($row = $query->fetch_assoc()){ 
		$orders[] = $row;
	}
	echo json_encode($orders);
}
?>
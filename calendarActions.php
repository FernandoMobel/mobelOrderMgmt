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
}

if($_POST['mode']=="wrappingSch"){
	$sql = "select distinct orr.oid id, concat('Order:',orr.oid,' - Boxes: ',(select sum(cc) from orderRoom orr2 where orr2.oid = orr.oid)) title, wrapping start, 'true' allDay, if(updateDate=curdate(),'red', 'blue') color from schedule s, orderRoom orr where orr.rid = s.rid";
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
}

if($_POST['mode']=="updateDate"){
	//update schedule
	$sql = "update schedule set wrapping ='".$_POST['date']."', updateDate=curdate() where rid in(select orr.rid from orderRoom orr where orr.oid =".$_POST['oid'].")";
	opendb($sql);
	
	//Calculate Finishing date
	$sql2 = "select rid,(select id from material m where m.id = (select mid from species sp where sp.id = orr.species)) material, (select finishType from frontFinish ff where ff.id = orr.frontFinish ) finishType, glaze, sheen from orderRoom orr where orr.oid =".$_POST['oid']." order by material asc";
	$result = opendb($sql2);
	$finishing = "null";
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
		$sql2 = "update schedule set finishing ='".$finishing."' where rid =".$row['rid'];
		opendb2($sql2);
	}
}

if($_POST['mode']=="getTotalDay"){
	$sql = "select coalesce(sum(cc),0) boxCurQty from orderRoom orr, schedule s where s.rid = orr.rid and s.wrapping='".$_POST['date']."'";
	$result = opendb($sql);
	$row = $result->fetch_assoc();
	echo $row['boxCurQty'];
}

if ($_POST['mode']=="getScheduleMain"){
	//$boxesLimit = 45; //capacity per day
	//$dailySum = 0; 
	//$i=1; //count
	//$open = false;//Flag to indicate when row day is open
	//$pivotRow = "";
	$oid = 0;//order id
	//--------------------------------------------------
	$sql = "SELECT mo.oid, (select wrapping from schedule s where s.oid = orr.oid) wrapping, (select sum(cc) from orderroom orr2 where orr2.oid = orr.oid) as totalorder, (select name from species s where s.id = orr.species) material, (select name from door d where d.id = orr.door) doorStyle, (select name from frontfinish f where f.id = orr.frontFinish) finish, orr.name roomName, cc, fronts, (select count(1) from orderItem oi where oi.rid = orr.rid) as items, deliveryDate FROM mosOrder mo, orderRoom orr where mo.oid = orr.oid and deliveryDate is not null order by deliveryDate desc, oid";
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
?>
<?php
include_once 'includes/db.php';

function createORDX($oid){	
	date_default_timezone_set('America/Toronto');
	$fileName = $oid.".ordx";

	//Main node
	$Job = new SimpleXMLElement('<Job/>');
	$Job->addAttribute('Created', date("Y-m-d H:i:s"));

	setMainJobProperties($oid, $Job);

	//Getting all rooms
	$sql = "select orr.rid,orr.name rname,orr.note,(SELECT sum(W) FROM orderItem where rid = orr.rid)length,sp.name spname,irf.name irfname,dd.name ddname,ff.name ffname,db.name dbname,gl.name glname,sdf.name sdfname,sh.name shname,ldf.name ldfname,h.name hname,dg.name dgname, fe.name fename
            from orderRoom orr,species sp,interiorFinish irf,door dd,frontFinish ff,drawerBox db,glaze gl,smallDrawerFront sdf,sheen sh,largeDrawerFront ldf,hinge h,drawerGlides dg,finishedEnd fe where orr.oid=".$oid." and orr.species=sp.id and orr.door=dd.id and orr.frontFinish=ff.id and orr.glaze=gl.id and orr.glaze=gl.id and orr.sheen=sh.id and orr.hinge=h.id and orr.smallDrawerFront=sdf.id and orr.largeDrawerFront=ldf.id and orr.drawerGlides=dg.id and orr.drawerBox=db.id and orr.interiorFinish=irf.id and orr.finishedEnd=fe.id order by orr.name";
    $result = opendb($sql);
    //Loop over all the rooms
    while($row = $result->fetch_assoc()){
    	echo setRoom($row, $Job->Rooms[0]);
    }

    $myfile = fopen($fileName, "w") or die("Unable to open file!");

	Header('Content-type: text/xml');
	print($Job->asXML());
	fwrite($myfile, $Job->asXML());
	fclose($myfile);
}

function setMainJobProperties($oid, $Job){
	//Getting order details
	$sql = "select * ,(select concat(unit,' ',street,' ',city,' ',province,' ',country,' ',postalCode)  from accountAddress aA where aA.id =mo.shipAddress) shipTo from mosOrder mo, mosUser mu, account a, cabinetLine cl where mo.mosUser = mu.id and mo.account = a.id and mo.CLid = cl.id and mo.oid =".$oid;
    $result = opendb($sql);
    $row = $result->fetch_assoc();

    /***************************************************************************************************************
	*	Ordx file creation - Fernando Guazo 2021/Feb/01
    ***************************************************************************************************************/
		//First levels
		//These two are static
		$ProductVersion = $Job->addChild('ProductVersion',"12");
		$Unit = $Job->addChild('Unit',"inches");
		//These two are dynamic
		$Properties = $Job->addChild('Properties');
		$Rooms = $Job->addChild('Rooms');

			//Properties node
			$jobProperties = $Properties->addChild('Job');
				$Information = $jobProperties->addChild('Information');
					$jobInformation = $Information->addChild('Job');
						$jobInformation->addChild('Name', "Order ID: ".$row['oid']);
						$jobInformation->addChild('Description', $row['tagName']);
						$jobInformation->addChild('PurchaseOrder', $row['po']);
				$Attributes = $jobProperties->addChild('Attributes');
					$Parameter = $Attributes->addChild('Parameter');
						$Parameter->addChild('Name', "COMMENT");//Job Comments
						$Parameter->addChild('Type', "T");//<!--Measurement|Meas|M|Degrees|Deg|D|Integer|Int|I|Boolean|Bool|B|Decimal|Dec|D|Text|T|Currency|Cur|C-->
						$Parameter->addChild('Value', $row['note']);

			$Room = $Properties->addChild('Room');
				$Finishes = $Room->addChild('Finishes');
					$Finish = $Finishes->addChild('Finish');
						$Finish->addChild('Interior', "White");
						$Finish->addChild('Exterior', "AF5 Frostine");
				$Attributes = $Room->addChild('Attributes');
					$Parameter = $Attributes->addChild('Parameter');
						$Parameter->addChild('Name', "ROOM");
						$Parameter->addChild('Type', "I");//<!--Measurement|Meas|M|Degrees|Deg|D|Integer|Int|I|Boolean|Bool|B|Decimal|Dec|D|Text|T|Currency|Cur|C-->
						$Parameter->addChild('Value', "1");

			$Cabinet = $Properties->addChild('Cabinet');
				$Construction = $Cabinet->addChild('Construction');
					$Construction->addChild('Cabinet', "Builder Construction IMP");
					$Construction->addChild('DrawerBox', "Innotech");
					$Construction->addChild('RollOut', "Innotech RO");

				$Materials = $Cabinet->addChild('Materials');
					$BaseMaterialSchedules = $Materials->addChild('BaseMaterialSchedules');
						$BaseMaterialSchedules->addChild('Standard', "Paint White/White");
						$BaseMaterialSchedules->addChild('ExposedInterior', "Paint White/Paint White");

					$UpperMaterialSchedules = $Materials->addChild('UpperMaterialSchedules');
						$UpperMaterialSchedules->addChild('Standard', "Paint White/White");
						$UpperMaterialSchedules->addChild('ExposedInterior', "Paint White/Paint White");

					$DrawerBox = $Materials->addChild('DrawerBox',"White Mel");
					$RollOut = $Materials->addChild('RollOut',"White Mel RO");

				$Hardware = $Cabinet->addChild('Hardware');
					$Hardware->addChild('PullSchedule', "No Pulls/Knobs");
					$Hardware->addChild('HingeSchedule', "_Blum Soft Close");
					$Hardware->addChild('GuideSchedule', "Innotech");
					$Hardware->addChild('SlidingDoorRailSchedule', "Sliding Door Rail");
				
				$Doors = $Cabinet->addChild('Doors');
					$All = $Doors->addChild('All');
						$All->addChild('Style', "Tarsia");
						$All->addChild('Material', "3/4\" MDF");
						$All->addChild('RoutePath', "POCKET(SHAKER) TOOL SET");
						$All->addChild('Catalog', "door.ddb");

					$Base = $Doors->addChild('Base');
						$Base->addChild('Style', "Tarsia");
						$Base->addChild('Material', "3/4\" MDF");
						$Base->addChild('RoutePath', "POCKET(SHAKER) TOOL SET");
						$Base->addChild('Catalog', "door.ddb");

					$Drawer = $Doors->addChild('Drawer');
						$Drawer->addChild('Style', "Tarsia");
						$Drawer->addChild('Material', "3/4\" MDF");
						$Drawer->addChild('RoutePath', "POCKET(SHAKER) TOOL SET");
						$Drawer->addChild('Catalog', "door.ddb");

					$Upper = $Doors->addChild('Upper');
						$Upper->addChild('Style', "Tarsia");
						$Upper->addChild('Material', "3/4\" MDF");
						$Upper->addChild('RoutePath', "POCKET(SHAKER) TOOL SET");
						$Upper->addChild('Catalog', "door.ddb");

					$BaseEP = $Doors->addChild('BaseEP');
						$BaseEP->addChild('Style', "Tarsia");
						$BaseEP->addChild('Material', "3/4\" MDF");
						$BaseEP->addChild('RoutePath', "POCKET(SHAKER) TOOL SET");
						$BaseEP->addChild('Catalog', "door.ddb");

					$UpperEP = $Doors->addChild('UpperEP');
						$UpperEP->addChild('Style', "Tarsia");
						$UpperEP->addChild('Material', "3/4\" MDF");
						$UpperEP->addChild('RoutePath', "POCKET(SHAKER) TOOL SET");
						$UpperEP->addChild('Catalog', "door.ddb");
					$TallEP = $Doors->addChild('TallEP');
						$TallEP->addChild('Style', "Tarsia");
						$TallEP->addChild('Material', "3/4\" MDF");
						$TallEP->addChild('RoutePath', "POCKET(SHAKER) TOOL SET");
						$TallEP->addChild('Catalog', "door.ddb");

			$Closets = $Properties->addChild('Closets');

			$Molding = $Properties->addChild('Molding');
				$Molding->addChild('Material', "Oak");
				$Molding->addChild('Crown', "Crown 400 1/2 x 1 5/8");
				$Molding->addChild('LightRail', "Beaded Light Rail");
				$Molding->addChild('Scribe', "Scribe 500 3/16 x 3/4");
				$Molding->addChild('BaseBoard', "Base 300 1/2 x 1 5/8");
				$Molding->addChild('ChairRail', "CR 200 5/8 x 2 1/2");
				$Molding->addChild('Casing', "Batten 502 1/4 x 3/4");
				$Molding->addChild('Ceiling', "Ceiling");
}

function setRoom($row, $Rooms){
		//Rooms node
			//every room should be created here
			$Room = $Rooms->addChild('Room');
				$Perspective = $Room->addChild('Perspective');

				$RoomProperties = $Room->addChild('RoomProperties');
					$RoomChild = $RoomProperties->addChild('Room');
						$General = $RoomChild->addChild('General');
							$General->addChild('Name',$row['rname']);
							$General->addChild('Description',$row['note']);
							$General->addChild('Type',"Room");//<!--Room|Project|Plan|FloorLevel|Phase-->
							$General->addChild('Material',"Default");

						$Parameters = $RoomChild->addChild('Parameters');
							$Parameter = $Parameters->addChild('Parameter');
								$Parameter->addChild('Name',"ROOM");
								$Parameter->addChild('Type',"I");//<!--Measurement|Meas|M|Degrees|Deg|D|Integer|Int|I|Boolean|Bool|B|Decimal|Dec|D|Text|T|Currency|Cur|C-->
								$Parameter->addChild('Value',"1");


					$Cabinet = $RoomProperties->addChild('Cabinet'); //Doors and Species are specified here
						$DoorsCab = $Cabinet->addChild('Doors');
							$All = $DoorsCab->addChild('Upper');
							$All->addChild('Style', "Bayview");
							$All->addChild('Material', "Solid Cherry");
							//$All->addChild('Material', "3/4\" MDF");
							//$All->addChild('RoutePath', "POCKET(SHAKER) TOOL SET");
							$All->addChild('Catalog', "door.ddb");

					
					$Closets = $RoomProperties->addChild('Closets');
				//Walls configuration
				$Walls = $Room->addChild('Walls');
					$Wall = $Walls->addChild('Wall');
						$Wall->addChild('Number',"1");

						$Wall->addChild('Description',"Wall 1");

						//Wall length calculation
						$length = floatval($row['length']);
						$width = -150+$length;

						$Position = $Wall->addChild('Position');
							$Position->addChild('StartX',"-150.000000");
							$Position->addChild('StartY',"-60.000000");
							//$Position->addChild('EndX',"150.000000");//This is the only one which should change for this file
							$Position->addChild('EndX',$width);//This is the only one which should change for this file
							$Position->addChild('EndY',"-60.000000");

						$Type = $Wall->addChild('Type');
							$Type->addChild('Style',"Standard");//<!--Standard|Peninsula|Cathedral|VaultLeft|VaultRight-->

						$Dimensions = $Wall->addChild('Dimensions');
							$Position->addChild('Length',$length);//This is the total length of X vector (StartX - EndX)
							$Position->addChild('Height',"96.000000");
							$Position->addChild('Soffit',"12.000000");
							$Position->addChild('Thick',"4.500000");
							$Position->addChild('VaultHeight',"48.000000");

						//Here is where cabinets are defined for current wall
						$Assemblies = $Wall->addChild('Assemblies');

							$sql2 = "SELECT * FROM orderItem where rid = ".$row['rid']." order by position asc";
							$result2 = opendb($sql2);

							$x = 0.00000;//X axis position
						    //Loop over all the rooms
						    while($item = $result2->fetch_assoc()){
						    	//every node is a cabinet
								$Assembly = $Assemblies->addChild('Assembly');

									$Assembly->addChild('Catalog',"2020 Catalog");

									$PropertiesAssembly = $Assembly->addChild('Properties');
										$GeneralProp = $PropertiesAssembly->addChild('General');
											
											$GeneralProp->addChild('Name',"FDB48-"); //Name is fixed for now
											$GeneralProp->addChild('Description',$item['description']);
											//$GeneralProp->addChild('Class',"Upper");
											//$GeneralProp->addChild('Type',"Standard");
											$GeneralProp->addChild('Price',$item['price']);

											$Size = $GeneralProp->addChild('Size');
												$Size->addChild('Width',$item['W']);
												$Size->addChild('Height',$item['H']);
												$Size->addChild('Depth',$item['D']);

										$AttrPropAssem = $PropertiesAssembly->addChild('Attributes');
											$ParamPropAssem = $AttrPropAssem->addChild('Parameter');
												$ParamPropAssem->addChild('Name', "COMMENT");//Item notes
												$ParamPropAssem->addChild('Type', "T");//<!--Measurement|Meas|M|Degrees|Deg|D|Integer|Int|I|Boolean|Bool|B|Decimal|Dec|D|Text|T|Currency|Cur|C-->
												$ParamPropAssem->addChild('Value', $item['note']);

									$PositionAssembly = $Assembly->addChild('Position');
										$PositionAssembly->addChild('X',$x);//X for base and Y for upper	
										//Set the starting position for next item. This is done after set element since first element is placed at 0.0
										$x += floatval($item['W']);					    	
						    }
							

}
<?php 
/***********************************************************************************************
*	Date:			2021/Feb/09
*	Author: 		Fernando Guazo 
*	Description: 	This file creates all the ord files (Cabinet Vision) for the rooms inside an order on MOS. every file will contain the items inside the order.
*					Some items would not be on the system(MOS) but a webpage will be added to add those items.
*-----------------------------------------------------------------------------------------------
*	On this link you can find more description about what every section is for.
*	http://content.planit.com/cv/Help/CV_Help/Room_Level/Ribbonbar/Utilities_Tab/Import_Order/ORD_File_Format.htm#:~:text=The%20Order%20Entry%20(ORD)%20file,multiple%20door%20styles%20and%20options.
***********************************************************************************************/

function createORDX($oid,$account){
	$i = 1;//Folder version
	//Create path
	$path = 'CabinetVision/'.$account.'/'.$oid.'('.$i.')/';
	//validate folder existance
	if (!file_exists($path)) {
	    mkdir($path, 0777, true);
	}else{//If folder already exists a new folder(version) will be created
		while(file_exists($path)){
			$path = 'CabinetVision/'.$account.'/'.$oid.'('.$i.')/';
			$i++;
		}
		mkdir($path, 0777, true);
	}
	
	//Getting all rooms
	$sql = "select (select a.busDBA from account a where a.id = mo.account)busName, mo.po, mo.tagName, orr.rid,orr.name rname,orr.note,(SELECT sum(W) FROM orderItem where rid = orr.rid)length,sp.name spname,irf.name irfname,dd.name ddname,ff.name ffname,db.name dbname,gl.name glname,sdf.name sdfname,sh.name shname,ldf.name ldfname,h.name hname,dg.name dgname, fe.name fename
            from mosOrder mo, orderRoom orr,species sp,interiorFinish irf,door dd,frontFinish ff,drawerBox db,glaze gl,smallDrawerFront sdf,sheen sh,largeDrawerFront ldf,hinge h,drawerGlides dg,finishedEnd fe where mo.oid = orr.oid and orr.oid=".$oid." and orr.species=sp.id and orr.door=dd.id and orr.frontFinish=ff.id and orr.glaze=gl.id and orr.glaze=gl.id and orr.sheen=sh.id and orr.hinge=h.id and orr.smallDrawerFront=sdf.id and orr.largeDrawerFront=ldf.id and orr.drawerGlides=dg.id and orr.drawerBox=db.id and orr.interiorFinish=irf.id and orr.finishedEnd=fe.id order by orr.name";
    $result = opendb($sql);
    //Loop over all the rooms
    while($row = $result->fetch_assoc()){
    	$fileName = $oid." - ".$row['rname'].".ord";
		setRoom($row, $fileName, $path);
    }
    createZipFile($oid,$path);
}

function setRoom($row, $fileName,$path){
	//open file
	$myfile = fopen($path.$fileName, "w") or die("Unable to open file!");

	//Link section
	$txt = "[Link]
    PCDate=\"".date("Y-m-d")."\"
	CreateBy=\"MOS System\"";

	//Header section
	fwrite($myfile, $txt);

	$txt ="\n
	[Header]
	Version=1
	Unit=0
	Name=\"".$row['rname']."\"
	Description=\"".$row['tagName']."\"
	PurchaseOrder=\"".$row['po']."\"
	Comment=\"\"
	Customer=\"".$row['busName']."\"
	Contact=\"\"
	Address1=\"\"
	City=\"\"
	State=\"\"
	Zip=\"\"
	Phone=\"\"
	EMail=\"\"
	BaseDoors=\"Modest\",\"3/4\" MDF\",\"\",\"\",\"\",\"\",\"Door.ddb\"
	WallDoors=\"Modest\",\"3/4\" MDF\",\"\",\"\",\"\",\"\",\"Door.ddb\"
	DrawerFront=\"Modest\",\"3/4\" MDF\",\"\",\"\",\"\",\"\",\"Door.ddb\"
	BaseEndPanels=\"Modest\",\"3/4\" MDF\",\"\",\"\",\"\",\"\",\"Door.ddb\"
	WallEndPanels=\"Modest\",\"3/4\" MDF\",\"\",\"\",\"\",\"\",\"Door.ddb\"
	TallEndPanels=\"Modest\",\"3/4\" MDF\",\"\",\"\",\"\",\"\",\"Door.ddb\"
	CabinetConstruction=\"Dealers Construction IMP\"
	DrawerBoxConstruction=\"Innotech\"
	RollOutConstruction=\"Innotech RO\"
	BaseCabinetMaterials=\"3/4 White/3/4 White\"
	WallCabinetMaterials=\"3/4 White/3/4 White\"
	BaseExposedCabinetMaterials=\"3/4 Paint White/3/4 Paint White\"
	WallExposedCabinetMaterials=\"3/4 Paint White/3/4 Paint White\"
	DrawerBoxMaterials=\"White Mel\"
	RollOutMaterials=\" White Metabox RO\"
	PullMaterials=\"No Pulls/Knobs\"
	HingeMaterials=\"_Blum Soft Close\"
	GuideMaterials=\"Innotech\"
	InteriorFinish=\"White.\"
	ExteriorFinish=\"SEE SAMPLE-PAINT\"";
	fwrite($myfile, $txt);

	/***********************************************************************************************
	*	Walls section
	************************************************************************************************
	*	Position 	Type 		Value
	*	1			Float 		Wall X Position
	*	2 			Float 		Wall Z Position
	*	3			Float 		Wall Direction
	*	4 			Float 		Wall Length
	*	5 			Float 		Wall Height
	*	6 			Float 		Wall Thickness
	*	7			Integer 	Wall Number (1 based) Empty = Line Count
	*	8 			Integer 	Wall Type
	*	9 			String 		Left Wall Number (0 or Empty => No Wall Attached)
	*	10 			Float 		Wall Radius
	*	11			Integer 	Rotation of the Radius Wall (0 = CW, 1 = CCW)
	*	12			Float 		Arc Angle for the Radius Wall
	*	13			Integer 	Wall ID
	*	14 			String 		Modify Code
	***********************************************************************************************/
	//Wall length calculation
	$length = floatval($row['length']);
	$width = -150+$length;

	$txt ="\n
	[Walls]
	-150.0000,-70.0000,0.0625,".$length.",96.0000,4.5000,1,1,\"0\",,,,\"1\",\"S\"";
	fwrite($myfile, $txt);

	/***********************************************************************************************
	*	Catalog section
	***********************************************************************************************/
	$txt ="\n
	[Catalog]
	Name=\"2020 Catalog.cvc\"";
	fwrite($myfile, $txt);

	/***********************************************************************************************
	*	Items section start
	***********************************************************************************************
	*	Position 	Type 		Value
	*	1			Float 		Wall X Position
	*	2 			Float 		Wall Z Position
	*	3			Float 		Wall Direction
	*	4 			Float 		Wall Length
	*	5 			Float 		Wall Height
	*	6 			Float 		Wall Thickness
	*	7			Integer 	Wall Number (1 based) Empty = Line Count
	*	8 			Integer 	Wall Type
	*	9 			String 		Left Wall Number (0 or Empty => No Wall Attached)
	*	10 			Float 		Wall Radius
	*	11			Integer 	Rotation of the Radius Wall (0 = CW, 1 = CCW)
	*	12			Float 		Arc Angle for the Radius Wall
	*	13			Integer 	Wall ID
	*	14 			String 		Modify Code
	***********************************************************************************************/
	//***************************Parameters*********************************************************
	//$sql2 = "select * FROM orderItem oi left join itemsLink il on oi.iid = il.itemId where rid = ".$row['rid']." order by position asc";
	$sql2 = "select * FROM orderItem oi left join (select il.itemId, (select cvi.category from cvitem cvi where cvi.id = COALESCE(il.cvId,il.cvRId,il.cvLId)) category, (select cvi.suffix from cvitem cvi where cvi.id = COALESCE(il.cvId,il.cvRId,il.cvLId)) suffix, (select cvi.name from cvitem cvi where cvi.id = il.cvId) cvItemD, (select cvi.name from cvitem cvi where cvi.id = il.cvRId) cvItemR,(select cvi.name from cvitem cvi where cvi.id = il.cvLId) cvItemL from itemsLink il ) cvt on oi.iid = cvt.itemId where rid = ".$row['rid']." order by position asc";
	$result2 = opendb($sql2);

	$x = 0.00000;//X axis position
	$i = 1;//item count
    //Loop over all the rooms
    while($item = $result2->fetch_assoc()){
		$category = "DEFAULT";
		if($item['category'])
			$category = $item['category'];
		$suffix = "";
		if($item['suffix'])
			$suffix = $item['suffix'];
		$txt ="\n
		[Parameters]
		Note=\"NOTE1\",\"NOTE\",\"text\",\"".$category."\" 
		Note=\"NOTE2\",\"NOTE2\",\"text\",\"".$suffix."\"
		Note=\"NOTE3\",\"NOTE3\",\"text\",\"SEE SAMPLE-PAINT\"
		Note=\"NOTE4\",\"NOTE4\",\"text\",\"SEE SAMPLE-PAINT\"
		Note=\"NOTE5\",\"NOTE5\",\"text\",\"Standard\"
		Note=\"NOTE6\",\"NOTE6\",\"text\",\"\"";
		fwrite($myfile, $txt);
		
		/************************************************************************************************
		*	Position 	Type 		Value
		*	1			Float 		Order Entry Number (1 based)
		*	2 			String 		Cabinet's Catalog Nomenclature
		*	3 			Float 		Cabinet Width
		*	4 			Float 		Cabinet Height
		*	5 			Float 		Cabinet Depth
		*	6 			String 		Cabinet Hinging 
		*	7 			String 		Cabinet End Types
		*	8			Integer 	Cabinet Quantity
		*	9 			String 		Cabinet Comment
		*	10 			String 		Wall Number
		*	11 			Float 		Offset from Wall start
		*	12 			Float 		Distance from Floor 
		*	13 			Float 		Outset from Wall
		*	14 			Integer 	Cabinet Type 
		*	15 			Integer 	Fill Mode
		*	16 			String 		Section Code (e.g., "3V-D=L-O-D=R")
		*	17 			String 		Cabinet ID - ID for CW or CV to identify
		*	18 			String 		Modify Code
		************************************************************************************************/
		/*CV Item code Selection*/
		$cvItem = "";
		$hinge = "*";//Undefined
		//Select left or right hinge
		if($item['hingeLeft']==1 && $item['hingeRight']==0){//Cabinet is left hinged
			$cvItem = $item['cvItemL'];//Cabinet is left hinged
			$hinge = "L";
		}elseif($item['hingeLeft']==0 && $item['hingeRight']==1){//Cabinet is right hinged
			$cvItem = $item['cvItemR'];
			$hinge = "R";
		}else{//2 Doors or no doors
			$cvItem = $item['cvItemD'];
			if($item['hingeLeft']==1 && $item['hingeRight']==1)//Cabinet has a pair of doors
				$hinge = "P";
		}
		//When no item linked we use a default item
		if(empty($cvItem)){
			$cvItem = "DEFAULT";
		}

		$txt ="\n
		[Cabinets]
		".$i.",\"".$cvItem."\",".floatval($item['W']).",".floatval($item['H']).",".floatval($item['D']).",\"".$hinge."\",\"N\",1,\"".$item['note']."\",\"1-F\",".$x.",0.0000,0.0000,2,0,\"1V|T\",\"1\",\"S\"";
		fwrite($myfile, $txt);
		$x += floatval($item['W']);	
		$i++;
	}
	fclose($myfile);
}

function createZipFile($oid,$path){
	$zip = new ZipArchive();
	$filename = $path.$oid.".zip";

	if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
		exit("cannot open <$filename>\n");
	}

	// Create zip
	createZip($zip,$path);
	
	$zip->close();
}

function createZip($zip,$dir){
  	if (is_dir($dir)){
    	if ($dh = opendir($dir)){
       		while (($file = readdir($dh)) !== false){
	         	// If file
	         	if (is_file($dir.$file)) {
	            	if($file != '' && $file != '.' && $file != '..'){
	            		$zip->addFile($dir.$file,$file);
	            	}
	         	}else{
	         			// If directory
	            		if(is_dir($dir.$file) ){

	              			if($file != '' && $file != '.' && $file != '..'){

	                			// Add empty directory
	                			$zip->addEmptyDir($dir.$file);

	                			$folder = $dir.$file.'/';
	 
	                			// Read data of the folder
	                			createZip($zip,$folder);
	              			}
	            		}
		        }
    		}
    		closedir($dh);
    	}
  	}
}
?>
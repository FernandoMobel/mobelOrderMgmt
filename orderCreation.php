<?php
$myfile = fopen("testOrd.ord", "w") or die("Unable to open file!");
/***********************************************************************************************
*	On this link you can find more description about what every section is for.
*	http://content.planit.com/cv/Help/CV_Help/Room_Level/Ribbonbar/Utilities_Tab/Import_Order/ORD_File_Format.htm#:~:text=The%20Order%20Entry%20(ORD)%20file,multiple%20door%20styles%20and%20options.
***********************************************************************************************/
//Link section
$txt = "[Link]
PCDate=\"2717896816-30827754\"
CreateBy=\"MOS\"";

//Header section
fwrite($myfile, $txt);
$txt ="\n
[Header]
Version=1
Unit=0
Name=\"3116\"
Description=\"Tag name or description\"
PurchaseOrder=\"\"
Comment=\"\"
Customer=\"Customer Name\"
Contact=\"\"
Address1=\"Full address\"
City=\"City\"
State=\"State\"
Zip=\"\"
Phone=\"\"
EMail=\"\"";
fwrite($myfile, $txt);

//Walls section
$txt ="\n
[Walls]
-100.0000,-70.0000,0.0625,418.0625,96.0000,4.5000,1,2,\"0\",,,,\"1\",\"S\"";
fwrite($myfile, $txt);

//Catalog section
$txt ="\n
[Catalog]
Name=\"2020 Catalog.cvc\"";
fwrite($myfile, $txt);

/***********************************************************************************************
*	Items section start
*	First parameters are defined(notes for this case) followed by the cabinet
***********************************************************************************************/
$txt ="\n
[Parameters]
Note=\"NOTE1\",\"NOTE\",\"text\",\"FDB\"
Note=\"NOTE2\",\"NOTE2\",\"text\",\"\"
Note=\"NOTE3\",\"NOTE3\",\"text\",\"SEE SAMPLE-PAINT\"
Note=\"NOTE4\",\"NOTE4\",\"text\",\"SEE SAMPLE-PAINT\"
Note=\"NOTE5\",\"NOTE5\",\"text\",\"Standard\"
Note=\"NOTE6\",\"NOTE6\",\"text\",\"\"";
fwrite($myfile, $txt);

$txt ="\n
[Cabinets]
1,\"FDB24-R\",24.0000,34.5000,23.7500,\"*\",\"N\",1,\"\",\"1-F\",0.0000,0.0000,0.0000,2,0,\"1V|T\",\"1\",\"S\"";
fwrite($myfile, $txt);

fclose($myfile);
?>
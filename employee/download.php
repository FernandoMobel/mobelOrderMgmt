<?php
include_once '../includes/db.php';
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

$result = opendb("select account from mosOrder where oid=".$_POST['oid']);
$row = $result->fetch_assoc();
$i = 1;
$path = '../CabinetVision/'.$row['account'].'/'.$_POST['oid'].'('.$i.')/';
while(file_exists($path)){
	$i++;
	$path = '../CabinetVision/'.$row['account'].'/'.$_POST['oid'].'('.$i.')/';		
}
$i--;
$path = '../CabinetVision/'.$row['account'].'/'.$_POST['oid'].'('.$i.')/';		
if($i==0){
	$path = false;
}

if($path){
	$filename = $_POST['oid'].".zip";	
}else{
	$filename = "File_not_found.zip";	
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($filename));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($path.$filename));
ob_clean();
flush();
readfile($path.$filename);
exit;
?>
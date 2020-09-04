<?php
//standard header for authentication check and using database
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
?>
<?php include_once 'includes/db.php';?>


<?php
$result = opendb("select account from mosOrder where oid =".$_POST["oid"]);
$row = mysqli_fetch_assoc($result);
//$target_dir = "uploads/DealerFiles/".$_SESSION["account"]."/".$_POST["oid"]."/";
$target_dir = "uploads/DealerFiles/".$row['account']."/".$_POST["oid"]."/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        //echo "File is an image - " . $check["mime"] . ".";
        //$uploadOk = 1;
    } else {
        //echo "File is not an image.";
        //$uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 5200000) {
    echo "Sorry, your file is too large, there is a 50MB limit";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" && $imageFileType != "pdf" ) {
    echo "Sorry, only JPG, JPEG, PNG, PDF, & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    //get oid, rid, iid, mid and new file id
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    opendb("insert into orderFiles (name, oid, rid, iid, mid) values ('" . basename($target_file) . "'," . $_POST['oid'] . "," . $_POST['rid'] . "," . $_POST['iid'] . "," . $_POST['mid'] . ")");
    $last_id=$GLOBALS['$conn']->insert_id;
    echo $last_id;
   
    $target_file = $target_dir . $last_id . "." . $imageFileType;
    
    echo $target_file;
    //echo $_FILES["fileToUpload"]["tmp_name"];
    
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>
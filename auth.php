<?php session_start();?>

<?php include_once 'includes/db.php';?>
<?php 
echo $_POST["email"];
echo $_POST["password"];
opendb( "select firstName,lastName,email,id,userType,account from mosUser where email = '". $_POST['email'] ."' and pw = '". $_POST['password'] ."'");
if($GLOBALS['$result']->num_rows > 0){
    foreach ($GLOBALS['$result'] as $row) {
        $_SESSION['firstName'] = $row['firstName'];
        $_SESSION['lastName'] = $row['lastName'];
        $_SESSION['email'] = $row['email'];
        $_SESSION["username"] = $row['email'];
        $_SESSION["userid"] = $row['id'];
        $_SESSION["userType"] = $row['userType'];
        $_SESSION["account"] = $row['account'];
    }
    opendb("select leadTime, siteMode from webNotes");
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
            //$_SESSION['leadTime'] = $row['leadTime'];
            //$_SESSION['siteMode'] = $row['siteMode'];
        }
    }
    header("Location: viewOrder.php");
}else{
    header("Location: index.php?pw=wrong");
}
closedb();
exit();
  ?>
<?php session_start();?>

<?php include_once 'includes/db.php';
include 'includes/nav.php';
?>
<?php 
$_SESSION["auth"] = false;
$captcha;
$ip = $_SERVER['REMOTE_ADDR'];
opendb("SELECT count(ipAddress) AS failedLoginAttempt FROM failedLogin WHERE ipAddress = '".$ip."'  AND date BETWEEN DATE_SUB( NOW() , INTERVAL 1 DAY ) AND NOW()");
if($GLOBALS['$result']->num_rows > 0){
	foreach ($GLOBALS['$result'] as $row) {
		$_SESSION["attp"] = $row['failedLoginAttempt'];
	}
}
	
if($_SESSION["attp"]>2){
		if(isset($_POST['g-recaptcha-response'])){
          $captcha=$_POST['g-recaptcha-response'];
        }else{
			header("Location: index.php");
		}
		if(!$captcha){
			header("Location: index.php");
		}else{
			//change secret key for prod
			//$secretKey = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";//Dev
			$secretKey = "6LfxicQZAAAAAEVWFYE_emDg3SvdyoUu5Zw-0AKr"; //PRD
			// post request to server
			$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
			$response = file_get_contents($url);
			$responseKeys = json_decode($response,true);
			// should return JSON with success as true
			if($responseKeys["success"]) {
				echo $_POST["email"];
				echo $_POST["password"];
				opendb( "select firstName,lastName,email,id,userType,account,CLGroup,defaultCLid from mosUser where email = '". $_POST['email'] ."' and pw = '". $_POST['password'] ."'");
				if($GLOBALS['$result']->num_rows > 0){
					foreach ($GLOBALS['$result'] as $row) {
						$_SESSION['firstName'] = $row['firstName'];
						$_SESSION['lastName'] = $row['lastName'];
						$_SESSION['email'] = $row['email'];
						$_SESSION["username"] = $row['email'];
						$_SESSION["userid"] = $row['id'];
						$_SESSION["userType"] = $row['userType'];
						$_SESSION["account"] = $row['account'];
						$_SESSION["CLGroup"] = $row['CLGroup'];
						$_SESSION["defaultCLid"] = $row['defaultCLid'];
						$_SESSION["auth"] = true;
					}
					opendb("select leadTime, siteMode from webNotes");
					if($GLOBALS['$result']->num_rows > 0){
						foreach ($GLOBALS['$result'] as $row) {
							//$_SESSION['leadTime'] = $row['leadTime'];
							//$_SESSION['siteMode'] = $row['siteMode'];
						}
					}
					if(strlen($_SESSION["firstName"])==1 && $_SESSION["account"]==2){
						header("Location: ".$local."/employee/EmployeeMenu.php");
					}else{
						header("Location: viewOrder.php");
					}
				}else{	
					opendb("INSERT INTO failedLogin(ipAddress, date) VALUES ('".$ip."',NOW())");
					header("Location: index.php");
				}
			} else {					
					header("Location: index.php");
			}	
		}
	}else{
		//login($count);
		echo $_POST["email"];
		echo $_POST["password"];
		opendb( "select firstName,lastName,email,id,userType,account,CLGroup,defaultCLid from mosUser where email = '". $_POST['email'] ."' and pw = '". $_POST['password'] ."'");
		if($GLOBALS['$result']->num_rows > 0){
			foreach ($GLOBALS['$result'] as $row) {
				$_SESSION['firstName'] = $row['firstName'];
				$_SESSION['lastName'] = $row['lastName'];
				$_SESSION['email'] = $row['email'];
				$_SESSION["username"] = $row['email'];
				$_SESSION["userid"] = $row['id'];
				$_SESSION["userType"] = $row['userType'];
				$_SESSION["account"] = $row['account'];
				$_SESSION["CLGroup"] = $row['CLGroup'];
				$_SESSION["defaultCLid"] = $row['defaultCLid'];
				$_SESSION["auth"]=true;
			}
			opendb("select leadTime, siteMode from webNotes");
			if($GLOBALS['$result']->num_rows > 0){
				foreach ($GLOBALS['$result'] as $row) {
					//$_SESSION['leadTime'] = $row['leadTime'];
					//$_SESSION['siteMode'] = $row['siteMode'];
				}
			}
			if(strlen($_SESSION["firstName"])==1 && $_SESSION["account"]==2){
				header("Location: ".$local."/employee/EmployeeMenu.php");
			}else{
				header("Location: viewOrder.php");
			}
		}else{	
			opendb("INSERT INTO failedLogin(ipAddress, date) VALUES ('".$ip."',NOW())");
			header("Location: index.php");
		}
	}
closedb();
exit();
?>
<?php
session_start(); 
/* For local environment */
$local = "";
if(strcmp($_SERVER['SERVER_NAME'],"localhost")==0 || strcmp($_SERVER['SERVER_NAME'],"192.168.16.199")==0){
	$local = "/mobelOrderMgmt";
}
/*Is authorized?*/
if(isset($_SESSION["auth"])){
	if(!$_SESSION["auth"] && $_SERVER['REQUEST_URI']!= $local."/index.php"){
		header("Location: http://".$_SERVER['SERVER_NAME'].$local."/index.php");
    //header("Location: index2.php");
	}
}else{
	$_SESSION["auth"]=false;
	//header("Location: index2.php");
  header("Location: http://".$_SERVER['SERVER_NAME'].$local."/index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Mobel Ordering System (MOS)</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
<?php 
/* Scripts static address*/
echo "<link rel=\"icon\" type=\"image/png\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/header/favicon1.png\">" ;
echo "<link rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/js/bootstrap431/css/bootstrap.min.css\">" ;
echo "<link rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/js/bootstrapselect1139/dist/css/bootstrap-select.css\">" ;
echo "<link rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/js/bootstrap-select/dist/css/bootstrap-multiselect.css\">" ;
echo "<link rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/js/MDB/css/mdb.min.css\">";
echo "<link rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/js/MDB/css/addons/datatables.min.css\">";
echo "<link rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/js/jqueryui112/jquery-ui.min.css\">";
echo "<link rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/js/Calendar/main.css\">";

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.standalone.min.css" />
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<style>
:root {
  --input-padding-x: 1.5rem;
  --input-padding-y: .75rem;
}

textarea {
}

.noresize {
  resize: none; 
}

body {
  background: #007bff;
  background: linear-gradient(to right, #0062E6, #33AEFF);
}

.card-signin {
  border: 0;
  border-radius: 1rem;
  box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
}

.card-signin .card-title {
  margin-bottom: 2rem;
  font-weight: 300;
  font-size: 1.5rem;
}

.card-signin .card-body {
  padding: 2rem;
}

.form-signin {
  width: 100%;
}

.form-signin .btn {
  font-size: 80%;
  border-radius: 5rem;
  letter-spacing: .1rem;
  font-weight: bold;
  padding: 1rem;
  transition: all 0.2s;
}

.form-label-group {
  position: relative;
  margin-bottom: 1rem;
}

.form-label-group input {
  height: auto;
  border-radius: 2rem;
}

.form-label-group>input,
.form-label-group>label {
  padding: var(--input-padding-y) var(--input-padding-x);
}

.form-label-group>label {
  position: absolute;
  top: 0;
  left: 0;
  display: block;
  width: 100%;
  margin-bottom: 0;
  /* Override default `<label>` margin */
  line-height: 1.5;
  color: #495057;
  border: 1px solid transparent;
  border-radius: .25rem;
  transition: all .1s ease-in-out;
}

.form-label-group input::-webkit-input-placeholder {
  color: transparent;
}

.form-label-group input:-ms-input-placeholder {
  color: transparent;
}

.form-label-group input::-ms-input-placeholder {
  color: transparent;
}

.form-label-group input::-moz-placeholder {
  color: transparent;
}

.form-label-group input::placeholder {
  color: transparent;
}

.form-label-group input:not(:placeholder-shown) {
  padding-top: calc(var(--input-padding-y) + var(--input-padding-y) * (2 / 3));
  padding-bottom: calc(var(--input-padding-y) / 3);
}

.form-label-group input:not(:placeholder-shown)~label {
  padding-top: calc(var(--input-padding-y) / 3);
  padding-bottom: calc(var(--input-padding-y) / 3);
  font-size: 12px;
  color: #777;
}

.btn-google {
  color: white;
  background-color: #ea4335;
}

.btn-facebook {
  color: white;
  background-color: #3b5998;
}

</style>



</head>
<body>
<div class="container-fluid px-0">
  <div class="row">
     <div class="col-11 pr-0">
      <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
      
       <a class="navbar-brand" href="https://mobel.ca"><img id="logo" alt="logo" src="https://mobel.ca/wp-content/uploads/2019/01/Logo.png"/></a>
        <?php
        if ($_SESSION["auth"]){
        ?>
      	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
      		<span class="navbar-toggler-icon"></span>
      	  </button>
      	  <div class="collapse navbar-collapse" id="collapsibleNavbar">
        		<ul class="navbar-nav">
        		<?php
        			if(strlen($_SESSION["firstName"])==1 && $_SESSION["account"]==2){
        				echo "<li class=\"nav-item\">";
        				echo "<a class=\"nav-link\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/employee/EmployeeMenu.php\">Mobel Only</a>";
        				echo "</li>";
        			}else{
        				echo "<li class=\"nav-item\">";
        				echo "<a class=\"nav-link\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/newOrder.php\">New</a>";
        				echo "</li>";
        				echo "<li class=\"nav-item\">";
        				echo "<a class=\"nav-link\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/viewOrder.php\">Orders</a>";
        				echo "</li>"; 
        				echo "<li class=\"nav-item\">";
        				//echo "<a class=\"nav-link\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/myAccount.php\">Account</a>";
                echo "<a class=\"nav-link\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/userProfiles.php\">Account</a>";
        				echo "</li>";
        				if(array_key_exists("userType",$_SESSION)){
        					if($_SESSION["userType"]==3){				  
        							echo "<li class=\"nav-item\">";
        							echo "<a class=\"nav-link\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/employee/EmployeeMenu.php\">Mobel Only</a>";
        							echo "</li>";
        					}
        				}
        			}
        			echo "<li class=\"nav-item\">";
        			echo "<a class=\"nav-link\" href=\"http://".$_SERVER['SERVER_NAME'].$local."/logOut.php\">Log Out</a>";
        			echo "</li>";			
        		echo "</ul>";
      	 echo "</div>";
      	 }
      	?>
        </nav>
      </div>
      <div class="col-1 bg-dark d-flex justify-content-end d-print-none">
        <a href="mailto:mos.support@mobel.ca" data-toggle="tooltip" data-placement="left" title="Need some help? please send an email to mos.support@mobel.ca">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bug text-info" viewBox="0 0 16 16">
            <path d="M4.355.522a.5.5 0 0 1 .623.333l.291.956A4.979 4.979 0 0 1 8 1c1.007 0 1.946.298 2.731.811l.29-.956a.5.5 0 1 1 .957.29l-.41 1.352A4.985 4.985 0 0 1 13 6h.5a.5.5 0 0 0 .5-.5V5a.5.5 0 0 1 1 0v.5A1.5 1.5 0 0 1 13.5 7H13v1h1.5a.5.5 0 0 1 0 1H13v1h.5a1.5 1.5 0 0 1 1.5 1.5v.5a.5.5 0 1 1-1 0v-.5a.5.5 0 0 0-.5-.5H13a5 5 0 0 1-10 0h-.5a.5.5 0 0 0-.5.5v.5a.5.5 0 1 1-1 0v-.5A1.5 1.5 0 0 1 2.5 10H3V9H1.5a.5.5 0 0 1 0-1H3V7h-.5A1.5 1.5 0 0 1 1 5.5V5a.5.5 0 0 1 1 0v.5a.5.5 0 0 0 .5.5H3c0-1.364.547-2.601 1.432-3.503l-.41-1.352a.5.5 0 0 1 .333-.623zM4 7v4a4 4 0 0 0 3.5 3.97V7H4zm4.5 0v7.97A4 4 0 0 0 12 11V7H8.5zM12 6a3.989 3.989 0 0 0-1.334-2.982A3.983 3.983 0 0 0 8 2a3.983 3.983 0 0 0-2.667 1.018A3.989 3.989 0 0 0 4 6h8z"/>
          </svg>
        </a>
      </div>
  </div>
</div>
<?php
session_start();
if(isset($_SESSION["username"])){
    if(($_SESSION["username"]=="" || $_SESSION["username"]=="invalid") && $_SERVER['REQUEST_URI']!="/index.php"){
        header("Location: index.php");
        //exit();
    //echo $_SERVER['REQUEST_URI'];
    }
}else{
    $_SESSION["username"]="invalid";
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Mobel Ordering System (MOS)</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="js/bootstrap431/css/bootstrap.min.css">
<link rel="stylesheet" href="js/bootstrapselect1139/dist/css/bootstrap-select.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
<!-- Material Design Bootstrap -->
<link rel="stylesheet" href="js/MDB/css/mdb.min.css">
<link rel="stylesheet" href="js/MDB/css/addons/datatables.min.css">
<link rel="stylesheet" href="js/jqueryui112/jquery-ui.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.standalone.min.css" />


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

<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <a class="navbar-brand" href="https://mobel.ca"><img id="logo" alt="logo" src="https://mobel.ca/wp-content/uploads/2019/01/Logo.png"/></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">

      <li class="nav-item">
        <a class="nav-link" href="newOrder.php">New</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="viewOrder.php">Orders</a>
      </li> 
      <li class="nav-item">
        <a class="nav-link" href="myAccount.php">Account</a>
      </li>
      <?php
      if(array_key_exists("userType",$_SESSION)){
          if($_SESSION["userType"]==3){
              ?>
              <li class="nav-item">
            <a class="nav-link" href="submittedOrders.php">Mobel Only</a>
          </li>
              <?php 
          }
      }
      ?>
      <li class="nav-item">
        <a class="nav-link" href="logOut.php">Log Out</a>
      </li>
      
    </ul>
  </div>  
</nav>
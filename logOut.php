<?php
session_start();
$_SESSION["username"]="invalid";
session_destroy();
header("Location: index.php");
?>
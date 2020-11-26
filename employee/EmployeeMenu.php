<?php include '../includes/nav.php';?>
<?php include '../includes/db.php';?>
<script>

/*Navigation tabs and views functionality*/
function navtab(object){
	/*Active tabs functionality*/
	$('.nav-tabs').on('click', 'a', function() {
		$('.nav-tabs a.active').removeClass('active');
		$(this).addClass('active');
	});	
	
	/*Display view*/	
	switch(object) {
	  case "orderView":
		document.getElementById("orderTab").style.display = "block";
		document.getElementById("itemTab").style.display = "none";
		document.getElementById("headersTab").style.display = "none";
		document.getElementById("calendarTab").style.display = "none";
		document.getElementById("scheduleTab").style.display = "none";
		break;
	  case "itemView":
		document.getElementById("orderTab").style.display = "none";
		document.getElementById("itemTab").style.display = "block";
		document.getElementById("headersTab").style.display = "none";
		document.getElementById("calendarTab").style.display = "none";
		document.getElementById("scheduleTab").style.display = "none";
		break;
	  case "headersView":
		document.getElementById("orderTab").style.display = "none";
		document.getElementById("itemTab").style.display = "none";
		document.getElementById("headersTab").style.display = "block";
		document.getElementById("calendarTab").style.display = "none";
		document.getElementById("scheduleTab").style.display = "none";
		break;
	  case "calendarView":
		document.getElementById("orderTab").style.display = "none";
		document.getElementById("itemTab").style.display = "none";
		document.getElementById("headersTab").style.display = "none";
		document.getElementById("calendarTab").style.display = "block";
		document.getElementById("scheduleTab").style.display = "none";
		break;
	  case "scheduleView":
		document.getElementById("orderTab").style.display = "none";
		document.getElementById("itemTab").style.display = "none";
		document.getElementById("headersTab").style.display = "none";
		document.getElementById("calendarTab").style.display = "none";
		document.getElementById("scheduleTab").style.display = "block";
		break;
	  default:
		// code block
	}	
}
</script>
<style>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
<?php
	if(strlen($_SESSION["firstName"])==1 && $_SESSION["account"]==2){}else{
?>
		<div class="container-fluid">
			<ul id="empTabs" class="nav nav-tabs">
				  <li class="nav-item">
					<a class="nav-link active" id="orderView" onclick="navtab(this.id)"><b>Orders</b></a>
				  </li>
				  <!--li class="nav-item">
					<a class="nav-link" id="itemView" onclick="navtab(this.id)"><b>Items</b></a>
				  </li-->
				  <li class="nav-item">
					<a class="nav-link" id="headersView" onclick="navtab(this.id)"><b>Headers</b></a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link" id="calendarView" onclick="navtab(this.id)"><b>Calendar</b></a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link" id="scheduleView" onclick="navtab(this.id)"><b>Schedules</b></a>
				  </li>
			</ul>
		</div>
<?php 
	}
if(strlen($_SESSION["firstName"])==1 && $_SESSION["account"]==2){
	echo "<div id=\"scheduleTab\" style=\"display:block\">";
	include 'schedule.php';
	echo "</div>";
}else{
	echo "<div id=\"orderTab\" style=\"display:block\">";
	include 'mobelEmpOrders.php';
	echo "</div>";

	echo "<div id=\"itemTab\" style=\"display:none\">";
	include '../item/itemList.php';
	echo "</div>";

	echo "<div id=\"headersTab\" style=\"display:none\">";
	include 'orderHeaders.php';
	echo "</div>";

	echo "<div id=\"calendarTab\" style=\"display:none\">";
	include 'calendar.php';
	echo "</div>";

	echo "<div id=\"scheduleTab\" style=\"display:none\">";
	include 'schedule.php';
	echo "</div>";
}
?>
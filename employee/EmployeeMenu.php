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

	//console.log($('.nav-tabs'));
	
	/*Display view*/	
	switch(object) {
	  case "orderView":
		document.getElementById("orderTab").style.display = "block";
		document.getElementById("itemTab").style.display = "none";
		document.getElementById("headersTab").style.display = "none";
		document.getElementById("calendarTab").style.display = "none";
		document.getElementById("scheduleTab").style.display = "none";
		document.getElementById("linkItemsTab").style.display = "none";
		break;
	  case "itemView":
		document.getElementById("orderTab").style.display = "none";
		document.getElementById("itemTab").style.display = "block";
		document.getElementById("headersTab").style.display = "none";
		document.getElementById("calendarTab").style.display = "none";
		document.getElementById("scheduleTab").style.display = "none";
		document.getElementById("linkItemsTab").style.display = "none";
		break;
	  case "headersView":
		document.getElementById("orderTab").style.display = "none";
		document.getElementById("itemTab").style.display = "none";
		document.getElementById("headersTab").style.display = "block";
		document.getElementById("calendarTab").style.display = "none";
		document.getElementById("scheduleTab").style.display = "none";
		document.getElementById("linkItemsTab").style.display = "none";
		break;
	  case "calendarView":
		document.getElementById("orderTab").style.display = "none";
		document.getElementById("itemTab").style.display = "none";
		document.getElementById("headersTab").style.display = "none";
		document.getElementById("calendarTab").style.display = "block";
		document.getElementById("scheduleTab").style.display = "none";
		document.getElementById("linkItemsTab").style.display = "none";
		break;
	  case "scheduleView":
		document.getElementById("orderTab").style.display = "none";
		document.getElementById("itemTab").style.display = "none";
		document.getElementById("headersTab").style.display = "none";
		document.getElementById("calendarTab").style.display = "none";
		document.getElementById("scheduleTab").style.display = "block";
		document.getElementById("linkItemsTab").style.display = "none";
		break;
	  case "adjustScheduleView":
		location.href = "../adjustSchedule.php";
		break;
	  case "linkItemView":
		document.getElementById("orderTab").style.display = "none";
		document.getElementById("itemTab").style.display = "none";
		document.getElementById("headersTab").style.display = "none";
		document.getElementById("calendarTab").style.display = "none";
		document.getElementById("scheduleTab").style.display = "none";
		document.getElementById("linkItemsTab").style.display = "block";
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
		<div class="container-fluid  d-print-none">
			<?php
			if(!in_array($_SESSION["userid"],[31,34,35])){
			?>
			<ul id="empTabs" class="nav nav-tabs">
				  <li class="nav-item border rounded-top">
					<a class="nav-link active" id="orderView" onclick="navtab(this.id)">
						<b>Orders</b>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-ruled" viewBox="0 0 16 16">
						  <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm2-1a1 1 0 0 0-1 1v4h10V2a1 1 0 0 0-1-1H4zm9 6H6v2h7V7zm0 3H6v2h7v-2zm0 3H6v2h6a1 1 0 0 0 1-1v-1zm-8 2v-2H3v1a1 1 0 0 0 1 1h1zm-2-3h2v-2H3v2zm0-3h2V7H3v2z"></path>
						</svg>
					</a>
				  </li>
				  <li class="nav-item border rounded-top">
					<a class="nav-link" id="itemView" onclick="navtab(this.id)">
						<b>Items</b>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-list-stars" viewBox="0 0 16 16">
						  <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"></path>
						  <path d="M2.242 2.194a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.277.277 0 0 0-.094.3l.173.569c.078.256-.213.462-.423.3l-.417-.324a.267.267 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.277.277 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.271.271 0 0 0 .259-.194l.162-.53zm0 4a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.277.277 0 0 0-.094.3l.173.569c.078.255-.213.462-.423.3l-.417-.324a.267.267 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.277.277 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.271.271 0 0 0 .259-.194l.162-.53zm0 4a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.277.277 0 0 0-.094.3l.173.569c.078.255-.213.462-.423.3l-.417-.324a.267.267 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.277.277 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.271.271 0 0 0 .259-.194l.162-.53z"></path>
						</svg>
					</a>
				  </li>
				  <li class="nav-item border rounded-top">
					<a class="nav-link" id="headersView" onclick="navtab(this.id)">
						<b>Headers</b>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-card-heading" viewBox="0 0 16 16">
						  <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"></path>
						  <path d="M3 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0-5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-1z"></path>
						</svg>
					</a>
				  </li>
				  <li class="nav-item border rounded-top">
					<a class="nav-link" id="calendarView" onclick="navtab(this.id)">
						<b>Calendar</b>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calendar3" viewBox="0 0 16 16">
						  <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857V3.857z"></path>
						  <path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"></path>
						</svg>
					</a>
				  </li>
				  <li class="nav-item border rounded-top">
					<a class="nav-link" id="scheduleView" onclick="navtab(this.id)">
						<b>Schedules</b>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calendar-month" viewBox="0 0 16 16">
						  <path d="M2.56 11.332L3.1 9.73h1.984l.54 1.602h.718L4.444 6h-.696L1.85 11.332h.71zm1.544-4.527L4.9 9.18H3.284l.8-2.375h.02zm5.746.422h-.676V9.77c0 .652-.414 1.023-1.004 1.023-.539 0-.98-.246-.98-1.012V7.227h-.676v2.746c0 .941.606 1.425 1.453 1.425.656 0 1.043-.28 1.188-.605h.027v.539h.668V7.227zm2.258 5.046c-.563 0-.91-.304-.985-.636h-.687c.094.683.625 1.199 1.668 1.199.93 0 1.746-.527 1.746-1.578V7.227h-.649v.578h-.019c-.191-.348-.637-.64-1.195-.64-.965 0-1.64.679-1.64 1.886v.34c0 1.23.683 1.902 1.64 1.902.558 0 1.008-.293 1.172-.648h.02v.605c0 .645-.423 1.023-1.071 1.023zm.008-4.53c.648 0 1.062.527 1.062 1.359v.253c0 .848-.39 1.364-1.062 1.364-.692 0-1.098-.512-1.098-1.364v-.253c0-.868.406-1.36 1.098-1.36z"></path>
						  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"></path>
						</svg>
					</a>
				  </li>
				  <?php 
				  if(in_array($_SESSION["userid"],[1,2,11,30,32])){
				  ?>
				  <li class="nav-item border rounded-top">
					<a class="nav-link" id="adjustScheduleView" onclick="navtab(this.id)">
						<b>Edit Schedule</b>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calendar2-check" viewBox="0 0 16 16">
						  <path d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0z"></path>
						  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z"></path>
						  <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4z"></path>
						</svg>
					</a>
				  </li>
				  <?php 
				  }
				  if(in_array($_SESSION["userid"],[1,2,11,30,32])){
				  ?>
				   <!--li class="nav-item border rounded-top">
					<a class="nav-link" id="reportView" onclick="navtab(this.id)">
						<b>Reports</b>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-kanban" viewBox="0 0 16 16">
						  <path d="M13.5 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-11a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h11zm-11-1a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2h-11z"/>
						  <path d="M6.5 3a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3zm-4 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3zm8 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3z"/>
						</svg>
					</a>
				  </li-->
				  <li class="nav-item border rounded-top">
					<a class="nav-link" id="linkItemView" onclick="navtab(this.id)">
						<b>Link MOS - CV Items</b>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
						  <path d="M4.715 6.542L3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.001 1.001 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z"></path>
						  <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 0 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 0 0-4.243-4.243L6.586 4.672z"></path>
						</svg>
					</a>
				  </li>
				   <?php 
				  }				  
				  ?>
			</ul>
			<?php
			}
			?>			
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

	echo "<div id=\"linkItemsTab\" style=\"display:none\">";
	include 'linkCVitems.php';
	echo "</div>";
}
?>
<?php include '../includes/nav.php';?>
<?php include '../includes/db.php';?>
<script>
function init() {
    var tf = setFilterGrid("table1");
  }
  
function saveUser(objectID){
	$("#"+objectID).css("border-color", "#ba0000");
	myData = { mode: "updateUser", id: objectID, value: $("#"+objectID).val()};
	$.post("./OrderItem.php",
			myData, 
		       function(data, status, jqXHR) {
            		if(status == "success"){
            	    	$("#"+objectID).css("border-color", "#00b828");
            	    }
		        });
}

function saveSettings(){
	myData = { mode: "setCurrentLeadtime", automaticPeriod: $("#automaticPeriod").val() };
	$.post("../save.php",
		myData, 
	       function(data, status, jqXHR) {
        		if(status == "success"){
        			$("#automaticPeriod").css("border-color", "#00b828");            		
					$("#currentLeadtime").val(jqXHR['responseText']);
					alert('Auto lead time is now set to: ' + jqXHR['responseText']);
        	    }else{
            	    alert('Sorry, something went wrong. Did you get the old password right? Please reload the page and try again.');
        	    }
	        });
}

function saveOrder(objectID,OID){
	$("#"+objectID+OID).css("border-color", "#ba0000");
	myData = { mode: "updateOrder", id: objectID, value: $("#"+objectID+OID).val(), oid: OID};
	$.post("./OrderItem.php",
			myData, 
		       function(data, status, jqXHR) {
            		if(data == "success"){
            	    	$("#"+objectID+OID).css("border-color", "#00b828");
            	    }
		        });
}

function getOrderID(OID){
	$('#searchOrderBtn').empty();
	OID = OID.replace(/\s+/g, '');
	if (OID.trim()){			
		myData = { mode: "getOrderID", id: OID.trim(), value: OID.trim()};
		$.post("EmployeeMenuSettings.php",
			myData, 
		       function(data, status, jqXHR) {
						$('#searchOrderBtn').append(data);           	    
		        });
		
	}else{
		$('#searchOrderBtn').empty();
	}
	
}

function saveEmployeeSettings(objectID){
	var arr = $("#"+objectID).val();
	if(arr.length==0){
		 arr = ["1","2","3","4","5","6","7","8","9","10"]; 
	}
	myData = { mode: "setFilter", id: objectID, value: arr}; //$("#"+objectID).val()};
	$.post("EmployeeMenuSettings.php",
			myData, 
			   function(data, status, jqXHR) {
					//$('#orders').empty();
					window.location.reload();
				});
	loadOrders(arr);					
}

function loadOrders(objectID){
	myData = { mode: "getOrders", id: objectID, value: objectID }; //$("#"+objectID).val()};
	$.ajax({
	    url: 'EmployeeMenuSettings.php',
	    type: 'POST',
	    data: myData,
	    success: function(data, status, jqXHR) {						
    		        
    		    }
	});	
}
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
		break;
	  case "itemView":
		document.getElementById("orderTab").style.display = "none";
		document.getElementById("itemTab").style.display = "block";
		document.getElementById("headersTab").style.display = "none";
		document.getElementById("calendarTab").style.display = "none";
		break;
	  case "headersView":
		document.getElementById("orderTab").style.display = "none";
		document.getElementById("itemTab").style.display = "none";
		document.getElementById("headersTab").style.display = "block";
		document.getElementById("calendarTab").style.display = "none";
		break;
	  case "calendarView":
		document.getElementById("orderTab").style.display = "none";
		document.getElementById("itemTab").style.display = "none";
		document.getElementById("headersTab").style.display = "none";
		document.getElementById("calendarTab").style.display = "block";
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
<div class="container-fluid">
	<ul id="empTabs" class="nav nav-tabs">
	  <li class="nav-item">
		<a class="nav-link active" id="orderView" onclick="navtab(this.id)"><b>Orders</b></a>
	  </li>
	  <!--li class="nav-item">
		<a class="nav-link" id="itemView" onclick="navtab(this.id)"><b>Items</b></a>
	  </li>
	  <li class="nav-item">
		<a class="nav-link" id="headersView" onclick="navtab(this.id)"><b>Headers</b></a>
	  </li-->
	  <li class="nav-item">
		<a class="nav-link" id="calendarView" onclick="navtab(this.id)"><b>Calendar</b></a>
	  </li>
	</ul>
</div>

<?php 
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
?>
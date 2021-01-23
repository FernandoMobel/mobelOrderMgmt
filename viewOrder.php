<?php include 'includes/nav.php';?>
<?php include 'includes/db.php';?>
<script>
function switchQuote(oid) {
	$("#assignedU"+oid).css("border-color", "#ba0000");
	myData = { mode: "switchUser", oid: oid, newUser: $("#assignedU"+oid).val()};
	console.log(myData);
	$.post("OrderItem.php",
			myData, 
		       function(data, status, jqXHR) {
            		if(status == "success"){
            	    	$("#assignedU"+oid).css("border-color", "#00b828");
						console.log(jqXHR);
            	    }
		        });
}

/*Navigation tabs and views functionality*/
function navtab(object){
	//console.log(object);
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
		break;
	  case "itemView":
		document.getElementById("itemTab").style.display = "block";
		document.getElementById("orderTab").style.display = "none";
		break;
	  default:
		// code block
	}		
}
</script>
<!-- Navigation tabs-->
<div class="container-fluid" hidden>
	<ul id="empTabs" class="nav nav-tabs">
	  <li class="nav-item">
		<a class="nav-link active" id="orderView" onclick="navtab(this.id)"><b>Orders</b></a>
	  </li>
	  <li class="nav-item">
		<a class="nav-link" id="itemView" onclick="navtab(this.id)"><b>Item Request</b></a>
	  </li>
	  <!--li class="nav-item">
		<a class="nav-link" href="#">Requests</a>
	  </li-->
	</ul>
</div>

<?php 
echo "<div id=\"orderTab\">";
include 'accountOrders.php';
echo "</div>";

echo "<div id=\"itemTab\" style = \"display:none\">";
include 'item/itemRequest.php';
echo "</div>";

?>
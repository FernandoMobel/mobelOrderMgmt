<?php include 'includes/nav.php';?>
<?php include_once 'includes/db.php';?>
<?php 
/*Declare Variables */
$roomCount = 1; $dateRequired = ""; 
$invalidHeaderMessage = "";

/*Getting Order main specs*/
$userFilter = " and mosUser = ".$_SESSION["userid"];
if($_SESSION["userType"] == 3){
	$userFilter = "";
}
if($_SESSION["userType"] == 2){
	$userFilter = " and account = ".$_SESSION["account"];
}
$sql = "select m.*,s.name as 'status' from mosOrder m, state s  where m.state = s.id and m.oid = ".$_GET["OID"] . $userFilter;
$result = opendb($sql);
if($GLOBALS['$result']->num_rows > 0){
    $mosOrderTB = $result->fetch_assoc();
}else{
    include 'includes/foot.php';
    include '403.php';
    exit();
}
/*Getting general settings*/
$sql ="select * from settings";
$result = opendb($sql);
$settings = $result->fetch_assoc();
?>
<style>
table.table-sm td{
padding-top:.2rem;
padding-bottom:.2rem;
height: 1px !important;
}

table{
	text-align: center;
}

.zoom:hover {
  -ms-transform: scale(1.3); /* IE 9 */
  -webkit-transform: scale(1.3); /* Safari 3-8 */
  transform: scale(1.3); 
}

option{ white-space: normal; }

.bootstrap-select .filter-option { white-space: normal; }

table p{
	text-align: center;
    line-height: 1.5em;
    height: 1.5em;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
	width: 100%;
}

.print {display:none;}

@media print {
  .d-print-none {display:none;}
  .print {display:block!important;}
  body {font-size: 1.3em !important;}
  table td {overflow:hidden !important;font-size: .8em !important;overflow: visible !important;}
  table th {font-size: .8em !important;overflow: visible !important;}  
}

</style>
<!--script src="js/MDB/js/popper.min.js"></script-->

<script>
<?php
/*Read only order according to the state */
if($mosOrderTB['state']==1){
    echo "var viewOnly = 0;";
}else{
    echo "var viewOnly = 1;";
}
?>
var noChangeMsg = "To make changes to a submitted order, please contact Mobel.";
var refresh = 1;
function saveOrder(objectID){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
	if($("#"+objectID.replace(".","\\.")).attr('type')=="checkbox"){
		if($("#"+objectID.replace(".","\\.")).is(":checked")){
			$("#"+objectID.replace(".","\\.")).val(1);
		}else{
			$("#"+objectID.replace(".","\\.")).val(0);
		}
	}
	myData = { mode: "updateOrder", id: objectID, value: $("#"+objectID.replace(".","\\.")).val().replace("'","''"), oid: "<?php echo $_GET["OID"] ?>", isPriority: $("#isPriority").val()};
	$.post("OrderItem.php",
		myData,
	       function(data, status, jqXHR){	       	
	       		if(data == "success"){
        	    	$("#"+objectID).css("border-color", "#00b828");
					if(objectID=="CLid"){//reload page when updating Cabinet Line
						resetOrderDefault("<?php echo $_GET["OID"] ?>",$("#"+objectID).data('val'),$("#"+objectID).val());
						window.location.reload();
        	    	//$("#"+objectID).attr('title',data);
					}
        	    }else{
        	    	$("#"+objectID).css("border-color", "#ff0000");
        	    	//$("#"+objectID).attr('title',data);
        	    	alert(data);
        	    }
	        });

	if($("#isPriority").val()==0)
		$('#invoiceTo').val("");
}

/*This function reset options where cabinet lines changes between Span and Kitchen (reset headers)*/
function resetOrderDefault(orderId, ocl, ncl){
	var kitchen = ['1','2'];//Cabinet lines for kitchens
	var nokitchen = ['3'];//Cabinet lines not for kitchens
	$('#CLid').data('val', ncl);
	if((kitchen.includes(ocl) && nokitchen.includes(ncl)) || (kitchen.includes(ncl) && nokitchen.includes(ocl))){		
		myData = { mode: "resetOrder", oid: orderId};
		$.post("OrderItem.php",
			myData,
				function(data, status, jqXHR){	
		});
	}
}


function saveStyle(col,objectID){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
	$("#"+objectID).css("border-color", "#ba0000");
	var data = {mode:"setStyle",oid:<?php echo $_GET["OID"]?>, rid:$("a.nav-link.roomtab.active").attr("value"), column:col, id: $("#"+objectID).val()};
	$.post("save.php",data,function(data, status){
	    if(status == "success"){
	    	$("#"+objectID).css("border-color", "#00b828");
	    	loadItems($("a.nav-link.roomtab.active").attr("value"));
			if(col=="species" || col=="frontFinish"){
				location.reload();
			}
	    }
	});
	if( (col=='drawerBox' && $("#"+objectID).val()=='3')||($('#drawerBox'+$("a.nav-link.roomtab.active").attr("value")).val()==3))
		alert('Notice: Dovetail drawers available only for cabinets 10”wide or greater');
}

function setMinDate(){
	$('.datepicker').on('click', function(e) {
		   e.preventDefault();
		   $(this).attr("autocomplete", "off");
		});
}

function submitToMobel(){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
	if(!$('#shipAddress').val()){
		alert('Please select a delivery option');
		return;
	}
	if($('#isPriority').val()==1 && $('#OrderNote').val().length==0){
		alert('For service orders a reason is needed, please add your comments');
		return;
	}

	if($('#isPriority').val()==1 && !$('#invoiceTo').val()){
		alert('For service orders, you need to select who is going to be invoiced');
		//alert($('#invoiceTo').val());
		return;
	}
	myData = { mode: "submitToMobel", oid: "<?php echo $_GET["OID"] ?>"};
	$.post("save.php",
		myData, 
	       function(data, status, jqXHR) {
	    	   //console.log(jqXHR['responseText']);
			   window.location.reload();
	        });
}





function addRoom(roomQty){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
	var data = {mode:"addRoom",oid:<?php echo $_GET["OID"]?>};
	$.post("save.php",data,function(data, status,jqXHR){
	    if(status == "success"){
	    	window.open(window.location.pathname+"?OID="+<?php echo $_GET["OID"]?>+"#rnewroom"+roomQty,"_self")
			window.location.reload();
	    }else{
		    alert('Sorry, room could not be added.');
		    window.location.reload();
	    }
	});
}

function loadItems(rid){
	//empty item list
	$("#items").empty();
	if(typeof rid !== 'undefined'){
		myData = { mode: "getItems", oid: "<?php echo $_GET["OID"] ?>", rid: rid };
				
		$.ajax({
			url: 'OrderItem.php',
			type: 'POST',
			data: myData,
			success: function(data, status, jqXHR) {
				//getting all the current header options
				var arr = $('.container.tab-pane.float-left.col-12.header.active select').map(function(){
					  return this.value
				}).get();
				var incomplete = false;
				for (i = 0; i < arr.length; i++) {//looping headers array to confirm all the options are selected (only 12 headers)
					if(arr[i]=="0"){
						//This means there is one option in header not selected, price will not be displayed
						incomplete = true;
						break;
					}
				}
				if(!incomplete){//Header options are selected
					$("#Position").empty();
					$('#items').append(data);
					for(i=1; i<=$('#items tr.font-weight-bold').length; i++){
						$("#Position").append(new Option(i+".0", i));
					}
					$(".borderless").css('border-top','0px');
					if($('#itemListingTable tbody tr').hasClass('table-danger')){
						$('#beforeSbm').prop('disabled', true);
						$('#afterSbm').prop('disabled', true);
						$("#roomTotal").html("<b>Room Total: Please solve item incompatibilities</b>");
						alert('One or more items are not compatible, please remove them');
					}else{
						$("#roomTotal").html("<b>Room Total: $" + $('#TotalPrice').val() + "<br>pre HST & pre delivery ");
					}	
					//set printing properties
					printPrice();				
				}else{//One or more headers aren't selected, prices and item list will not be displayed
					$('#items').append("<h5 class=\"mx-auto\">Please ensure all the above options (Species, Finish, etc) are selected</h5>");
					$(".borderless").css('border-top','0px');
					$("#roomTotal").html("<b>Room Total: undefined </b>");
				}
			}
		});
	}else{
        $("#items").html('<h1>Error getting Items</h1>');
    }
}

function showResult(str) {
    if (str.length==0) {
        document.getElementById("livesearch").innerHTML="";
        document.getElementById("livesearch").style.border="0px";
        return;
    }
    if (window.XMLHttpRequest) {
      // code for IE7+, Firefox, Chrome, Opera, Safari
    	xmlhttp=new XMLHttpRequest();
    } else {  // code for IE6, IE5
    	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            document.getElementById("livesearch").innerHTML=this.responseText;
                  //document.getElementById("allItemsEdit").innerHTML=this.responseText;
                  //$('.selectpicker').selectpicker('refresh');
            document.getElementById("livesearch").style.border="1px solid #A5ACB2";
        }
    }
    xmlhttp.open("POST","OrderItem.php",true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
	if($("#startsWith").is(":checked")){
		$("#startsWith").val(1);
	}else{
		$("#startsWith").val(0);
	}
	if($('#editItemTitle').text() == "Edit/Delete Item"){//$('#editOrderItemPID').val()=="0" || 
    	xmlhttp.send("filter="+str+"&mode=getNewItem&com=and&type=item&startsWith="+$("#startsWith").val()+"&cabinetLine="+$("#CLid").val());
    }else{
    	xmlhttp.send("filter="+str+"&mode=getNewItem&com=and&type=mod&startsWith="+$("#startsWith").val()+"&cabinetLine="+$("#CLid").val());
    }
}

//mod is the ItemID of the mod of the main item. It is 0 if this is already a parent item.
//itemID is the parent item's id (or it's own ID) in the order item table
function editItems(itemID, mod){
	if(mod>0){
		$("#editOrderItemPID").val(itemID);
		$('#editItemTitle').text("Edit/Delete Mod");
		$('#Position').hide();
		$('#lblPosition').hide();
	}
	//document.getElementById("#editItemSearch").innerHTML="";
	//allItems('allItemsEdit','editItems',itemID, mod);
    cleanEdit();
	//$("#editItemModal").empty();
	//$('.bs-searchbox').attr("onkeyup=\"showResult(this.value)\"");
	
	myData = { mode: "editItemGetDetails", mod: mod, oid: "<?php echo $_GET["OID"] ?>", itemID: itemID};
	$.post("OrderItem.php",
			myData, 
		       function(data, status, jqXHR) {
					refresh = 0;
					data = data.replace(/[\u0000-\u0019]+/g,"\\n"); 					
					myObj= JSON.parse(data);
					document.getElementById("livesearch").innerHTML=myObj.name;
					$('#note').val("");
					$('#note').val(myObj.note);
	       			$('#W').val(parseFloat(myObj.w));
					$('#W2').val(parseFloat(myObj.w2));
	       			$('#H').val(parseFloat(myObj.h));
	       			$('#D').val(parseFloat(myObj.d));
	       			$('#D2').val(parseFloat(myObj.d2));
	       			$('#Qty').val(parseFloat(myObj.qty));
	       			//Disable option for mods or enable for items
	       			if($('#editItemID').val()!==0){//items
	       				$('#Position').val(myObj.position);
	       				$('#Position').prop('disabled',false);
	       			}else{//mods
	       				$('#Position').prop('disabled',true);
	       			}
	       			$('#HL').prop('checked',myObj.hingeLeft==1);
	       			$('#HR').prop('checked',myObj.hingeRight==1);
	       			$('#FL').prop('checked',myObj.finishLeft==1);
	       			$('#FR').prop('checked',myObj.finishRight==1);
		           //$('#allItemsEdit').val(myObj.iid);  //selects current item. Not consistent.
		           //$('#allItemsEdit').selectpicker('val',[myObj.iid]);
		           //$('#allItemsEdit').selectpicker('refresh');
		           $('#editItemID').val(myObj.iid);
		           $('#editOrderItemID').val(myObj.id);
		           $('#editItemTitle').text("Edit/Delete Item");
		           
		           if(mod>0){
		        	   $('#editItemTitle').text("Edit/Delete Mod");
		        	   $('#editItemID').val(mod);
		        	   $('#Position').hide();
						$('#lblPosition').hide();
		           }else{
			           $('#Position').show();
			           $('#lblPosition').show();
	       				getImage(itemID,true);
		           }
				   
				   if(parseFloat(myObj.w2)>0){
					   $('#W2lbl').show(); 
					   $('#W2').show(); 
				   }else{
					   $('#W2lbl').hide(); 
					   $('#W2').hide();
				   }
				   if(parseFloat(myObj.d2)>0){
					   $('#D2lbl').show();
					   $('#D2').show();
				   }else{
					   $('#D2lbl').hide();
					   $('#D2').hide();
				   }
    		         
					
		        });
	if(mod>0){
		$('#deleteItemButton').val(mod);
	}else{
		$('#deleteItemButton').val(itemID);
	} 
}

function cleanEdit(rqst){
	$('#itemImg').removeAttr('src');
	$('#editItemID').val(0);
	$('#editOrderItemID').val(0);
	$('#editOrderItemPID').val(0);
	$('#livesearch').empty();
	$('#note').val("");
	$('#note').css("border-color", "#ced4da");
	$('#W').val("");
	$('#W').css("border-color", "#ced4da");
	$('#H').val("");
	$('#H').css("border-color", "#ced4da");
	$('#D').val("");
	$('#D').css("border-color", "#ced4da");
	$('#W2').val("");
	$('#WW').css("border-color", "#ced4da");
	$('#D2').val("");
	$('#D2').css("border-color", "#ced4da");
	$('#Qty').val(1);
	$('#HL').prop('checked',false);
	$('#HR').prop('checked',false);
	$('#FL').prop('checked',false);
	$('#FR').prop('checked',false);
	$('#deleteItemButton').show();
	$('#deleteItemButton').val(0);
	$('#W2lbl').hide();
	$('#W2').hide();
	$('#D2lbl').hide();
	$('#D2').hide();
	//Hide delete button when new item
	if(rqst == "add"){
		$('#editItemTitle').text('Edit/Delete Item')
		$('#deleteItemButton').hide();
		$("#Position").empty();
		for(i=1; i<=$('#items tr.font-weight-bold').length+1; i++){
			$("#Position").append(new Option(i+".0", i));
		}
		$("#Position").val($('#items tr.font-weight-bold').length+1);
	}		
	if($('#editItemID').prop('value')==0){
		$("#Position").prop('disabled',true);	
	}else{
		$("#Position").prop('disabled',false);
	}
	if($('#editItemTitle').text() == "Edit/Delete Mod"){
		$("#Position").hide();
		$("#lblPosition").hide();
	}else{
		$("#Position").show();
		$("#lblPosition").show();

	}
}

function solvefirst(W,H,D,W2,H2,D2,name,catid) {
  return new Promise(resolve => {
  	//setTimeout(() => {
	    refresh = 0;
		$('#W').val(W);
		$('#H').val(H);
		$('#D').val(D);
		$('#W2').val(W2);
		//$('#H2').val(H2);
		$('#D2').val(D2);
		addItemID = catid;
		$('#editItemID').val(catid);
		$('#livesearch').val(name);
		saveItem();
		saveEditedItem('HL','hingeLeft');
		saveEditedItem('HR','hingeRight');
		saveEditedItem('FL','finishLeft');
		saveEditedItem('FR','finishRight');
    	saveEditedItem('note','note');
		if(W2>0){
			$('#W2lbl').show();		
			$('#W2').show();		
		}else{
			$('#W2lbl').hide();
			$('#W2').hide();
		}
		if(D2>0){
			$('#D2lbl').show();
			$('#D2').show();
		}else{
			$('#D2lbl').hide();
			$('#D2').hide();
		}
		$("#Position").prop('disabled', false);
    	resolve('');
    	//}, 5000); set time out
		}
  );
}

async function setSizes(W,H,D,W2,H2,D2,name,desc,catid) {
  const result = await solvefirst(W,H,D,W2,H2,D2,name,catid);
  $('#editItemSearch').val(result);
	document.getElementById("livesearch").innerHTML=name;
	loadItems($("a.nav-link.roomtab.active").attr("value"));
	if($('#editItemTitle').text() != "Edit/Delete Mod")
		getImage(catid,false);
}

function saveEditedItem(objectID,col){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
	$("#"+objectID).css("border-color", "#ba0000");
	if(objectID=="Qty"){
		if(isNaN($('#Qty').val())){
			$('#Qty').val(1);
		}
		if($('#Qty').val()<1){
			$('#Qty').val(1);
		}
		$('#Qty').val(Math.round($('#Qty').val()));
	}
	if(objectID=="Position"){
		refresh = 1;
	}
	var myMode = "";
	if($('#editItemTitle').text() == "Edit/Delete Mod"){
		myMode = "saveEditedMod";
	}else{
		myMode = "saveEditedItem";
	}
	if($("#"+objectID).is(":checkbox")){
		checkvalue = 0;
		if($("#"+objectID).prop("checked")){
			checkvalue = 1;
		}else{
			checkvalue = 0;
		}
		myData = { column:col, id: checkvalue, itemID: $('#editOrderItemID').val(), mode: myMode, rid: $("a.nav-link.roomtab.active").attr("value"), value: $("#"+objectID).val(), oid: "<?php echo $_GET["OID"] ?>"};
	}else{
		myData = { column:col, id: $("#"+objectID).val(), itemID: $('#editOrderItemID').val(), mode: myMode, rid: $("a.nav-link.roomtab.active").attr("value"), value: $("#"+objectID).val(), oid: "<?php echo $_GET["OID"] ?>"};
	}
	$.post("save.php",myData,function(data, status, jqXHR) {
		if(status == "success"){
	    	$("#"+objectID).css("border-color", "#00b828");
	    	if(refresh>0){
		    	//console.log(jqXHR['responseText']);
		    	loadItems($("a.nav-link.roomtab.active").attr("value"));
		    	refresh = 0;
	    	}
	    	if(data.length>1){
	    		$("#"+objectID).css("border-color", "#ba0000");
				alert(data);
	    	}
	    	//return 1;
	    }
	});
}
var addItemID = -1;
//myid is the id of this item/mod in the order table
//pid is 0  myid or the id of the parent
//addItemID is the id of the item - iid or mid (mid is the iid of a mod)
function saveItem(){
	var myid = $("#editOrderItemID").val();
	var pid = $("#editOrderItemPID").val();
	addItemID = $("#editItemID").val();


	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
	var mode = "addItem";
	if(pid>0){
		mode = "addMod";
	}
	
	if(myid >0){
		mode = "editItem";
		if(pid>0){
			mode = "editMod";
		}
		addItemID = myid;
	}
	myData = { mode: mode, pid: pid, id: $("#editItemID").val(), myid: myid, oid: "<?php echo $_GET["OID"] ?>", rid: $("a.nav-link.roomtab.active").attr("value")};
	$.post("OrderItem.php",
			myData, 
		       function(data, status, jqXHR) {
		       		//console.log(jqXHR['responseText']);
	       			if($("#editOrderItemID").val() == 0){
		       			//alert(data);
	       				$("#editOrderItemID").val(data);
						//Delete button can delete item recently added
						$('#deleteItemButton').val(data);
	       			}
	       			if(refresh>0){
			    	   loadItems($("a.nav-link.roomtab.active").attr("value"));
	       			}
	       			//Delete button is shown for new item added 	       			
	       			$('#deleteItemButton').show();
		        });
}

// mode is "allItems" or "editItems" to allow for autoselecting the item we are editing and allow it to be changed.
//no edit Items needed - autoselection handled in calling function.
function allItemsOld(objectID, mode, pid = 0, mid = 0){
	myObjectName = '#' + objectID;
	if(pid==0){
		$("#saveAddedItem").attr("onclick","saveItem()");
	}else{
		$("#saveAddedItem").attr("onclick","saveItem("+ pid +")");		
	}
	$(myObjectName).empty();
	if(mode=='allItems'){
 	   //$('#addItemModalTitle').text('adding item');
    }
    if(mode=='modItems'){
        mid = 1;
 	   //$('#addItemModalTitle').text('adding a modification or accessory');
    }
    
    if(mode=='editItems'){
     }
    if(mode=='editItems' && mid > 0){
  	   //$('#editItemTitle').text('editing a modification or accessory');
     }
    mode='allItems';
    myData = { mode: mode, mid: mid, oid: "<?php echo $_GET["OID"] ?>", rid: "+rid+"};
	$.post("OrderItem.php",
			myData, 
		       function(data, status, jqXHR) {
		           $(myObjectName).append(data);
		           $('.selectpicker').selectpicker('refresh');
		           
		        });
}





function editRoom(rid,rname){
	$('#RoomName').val(rname);
	$('#RoomNote').val($('#RoomNote'.concat(rid)).val());
	$('#RoomName').prop('title',rname);
}

function saveRoom(objectID){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
	$("#"+objectID).css("border-color", "#ba0000");
	
	if(objectID=="RoomName"){
		myData = { mode: "updateRoom", rid: $("a.nav-link.roomtab.active").attr("value"), value: $("#"+objectID).val().replace(/[^a-zA-Z0-9 ]/g, ""), oid: "<?php echo $_GET["OID"] ?>"};
		
	}
	if(objectID=="RoomNote"){
		myData = { mode: "updateRoomNote", rid: $("a.nav-link.roomtab.active").attr("value"), value: $("#"+objectID).val().replace("'","''"), oid: "<?php echo $_GET["OID"] ?>"};
	}
	
	$.post("save.php",
			myData, 
		       function(data, status, jqXHR) {
            		if(status == "success"){
            	    	$("#"+objectID).css("border-color", "#00b828");
            	    	//Change room name here!
            	    	if(objectID=="RoomName"){
            	    		$("#"+$('#RoomName').prop('title')).html($("#"+objectID).val());
            	    	}
            	    	if(objectID=="RoomNote"){
            	    		$('#RoomNote'.concat($("a.nav-link.roomtab.active").attr("value"))).val($('#RoomNote').val());
							if($('#RoomNote').val()==""){
								$('#RoomNotePreview'.concat($("a.nav-link.roomtab.active").attr("value"))).text('');
								$('#RoomNotePrint'.concat($("a.nav-link.roomtab.active").attr("value"))).text('');
							}else{
								$('#RoomNotePreview'.concat($("a.nav-link.roomtab.active").attr("value"))).text('Room note: '.concat($('#RoomNote').val()));
								$('#RoomNotePrint'.concat($("a.nav-link.roomtab.active").attr("value"))).text('Room note: '.concat($('#RoomNote').val()));
							}
            	    	}
            	    	
            	    }
		        });
}


function deleteRoom(){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
	DeleteRoomDialog('Are you sure you want to delete this room and all of its items');
}

function DeleteRoomDialog(message){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
  $('<div></div>').appendTo('body')
    .html('<div><h6>' + message + '?</h6></div>')
    .dialog({
      modal: true,
      title: 'Delete message',
      zIndex: 10000,
      autoOpen: true,
      width: 'auto',
      resizable: false,
      buttons: {
        Yes: function() {
        	myData = { mode: "deleteRoom", rid: $("a.nav-link.roomtab.active").attr("value"), oid: "<?php echo $_GET["OID"] ?>"};
        	$.post("save.php",
        			myData,
        		       function(data, status, jqXHR) {
        		       		//console.log(jqXHR['responseText']);
                    		if(status == "success"){
                    	    	location.reload();
                    	    }
        		        });
          // $(obj).removeAttr('onclick');                                
          // $(obj).parents('.Parent').remove();

          //$('body').append('<h1>Confirm Dialog Result: <i>Yes</i></h1>');

          $(this).dialog("close");
        },
        No: function() {
          //$('body').append('<h1>Confirm Dialog Result: <i>No</i></h1>');

          $(this).dialog("close");
        }
      },
      close: function(event, ui) {
        $(this).remove();
      }
    });
};


function deleteItem(){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
	var mod;
	mod = 0;
	if($('#editItemTitle').text()=="Edit/Delete Mod"){
		mod = $('#deleteItemButton').val();
	}
	DeleteItemDialog('Are you sure this item should be deleted',$('#deleteItemButton').val(),mod);
}

function DeleteItemDialog(message, itemID, mod = 0){
	
  $('<div></div>').appendTo('body')
    .html('<div><h6>' + message + '?</h6></div>')
    .dialog({
      modal: true,
      title: 'Delete message',
      zIndex: 10000,
      autoOpen: true,
      width: 'auto',
      resizable: false,
      buttons: {
        Yes: function() {
        	myData = { mode: "deleteItem", mod: mod, itemID: itemID, rid: $("a.nav-link.roomtab.active").attr("value"), oid: "<?php echo $_GET["OID"] ?>"};

        	$.post("save.php",
        			myData,
        		       function(data, status, jqXHR) {
                    		if(status == "success"){
                    	    	location.reload();
                    	    }
        		        });
          // $(obj).removeAttr('onclick');                                
          // $(obj).parents('.Parent').remove();

          //$('body').append('<h1>Confirm Dialog Result: <i>Yes</i></h1>');

          $(this).dialog("close");
        },
        No: function() {
          //$('body').append('<h1>Confirm Dialog Result: <i>No</i></h1>');

          $(this).dialog("close");
        }
      },
      close: function(event, ui) {
        $(this).remove();
      }
    });
};
goid = "NA";
grid = "NA";
giid = "NA";
gmid = "NA";

function deleteFile(id){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
	DeleteFileDialog('Are you sure you want to remove this file?',id);
}

function DeleteFileDialog(message,id){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
  $('#fileModal').modal('hide');
  $('<div></div>').appendTo('body')
    .html('<div><h6>' + message + '?</h6></div>')
    .dialog({
      modal: true,
      title: 'Delete message',
      zIndex: 10000,
      autoOpen: true,
      width: 'auto',
      resizable: false,
      buttons: {
        Yes: function() {
        	myData = { mode: "deleteFile", id: id};
        	$.post("OrderItem.php",
        			myData,
        		       function(data, status, jqXHR) {
                    		if(status == "success"){
                        		refreshFiles();
                    	    	//location.reload();
                    	    }
        		        });
          // $(obj).removeAttr('onclick');                                
          // $(obj).parents('.Parent').remove();

          //$('body').append('<h1>Confirm Dialog Result: <i>Yes</i></h1>');

          $(this).dialog("close");
          $('#fileModal').modal('show');
        },
        No: function() {
          //$('body').append('<h1>Confirm Dialog Result: <i>No</i></h1>');

          $(this).dialog("close");
          $('#fileModal').modal('show');
        }
      },
      close: function(event, ui) {
        $(this).remove();
      }
    });
};

function refreshFiles(){
	
	if(goid=="NA"){
		alert("Please open the file listing first before refreshing.");
	}else{
        myData = { mode: "getFiles", mid: gmid, rid: grid ,oid: goid, iid: giid};
    	$.post("OrderItem.php",
    			myData, 
    		       function(data, status, jqXHR) {
    					$('#FileList tbody').html(data);
    					//alert("still testing");
    	       			//document.getElementById("fileListing").innerHTML=data;
    	});
	}
}

function loadFiles(oid, rid = 0, iid = 0, mid = 0){
	//alert(oid + " " + rid);
	//modal fileModal
	//id fileListing
	goid = oid;
	grid = rid;
	giid = iid;
	gmid = mid;
	document.getElementById("fileListing").innerHTML="";


	
    	myData = { mode: "getFileModal", mid: mid, rid: rid ,oid: oid, iid: iid};
    	$.post("OrderItem.php",
    			myData, 
    		       function(data, status, jqXHR) {
    		(async()=>{
    	       			document.getElementById("fileListing").innerHTML=data;
    	       			$( document ).ready(function() {
    	       				refreshFiles();
    	       			});
    		})();
    					
    	});
}

function showSubmit(){
	$("#orderOptionsTitle").text("Submit to Mobel");
    $("#submitText").show();
    //$("#submitButton").show();
    $("#submitButton").css("visibility","visible");
    showOrderOptions();
}
function hideSubmit(){
	$("#orderOptionsTitle").text("Order Options");
    $("#submitText").hide();
    $("#submitButton").css("visibility","hidden");
    showOrderOptions();
}
function showOrderOptions(){
	if($("#isPriority").val()==0){
		$(".service").css("visibility","hidden");
	}else{
		$(".service").css("visibility","visible");
	}
}

function fixDate(){
	if($("#isPriority").val()==0){
		if($("#dateRequired").val() < $("#currentLeadtime").val()){
			$("#dateRequired").val($("#currentLeadtime").val());
			alert("Your leadtime has been adjusted to match the current leadtime.");
			saveOrder("dateRequired");
		}
		$("#isWarranty").val(0);
		$("#isWarranty").prop("checked",false);
		saveOrder('isWarranty');
	}
}

function printPrice(){
	if(printChk.checked == true){
		$('#roomTotal').removeClass('d-print-none');
		$('#roomTotal').addClass('d-print-block');
		$('#itemListingTable .priceCol').removeClass('d-print-none');
		$('#itemListingTable .priceCol').addClass('d-print-block');
		$('#printing .priceCol').removeClass('d-print-none');
		//$('#printing .priceCol').addClass('d-print-block');
	}else {
		$('#roomTotal').removeClass('d-print-block');
		$('#roomTotal').addClass('d-print-none');
		$('#itemListingTable .priceCol').removeClass('d-print-block');
		$('#itemListingTable .priceCol').addClass('d-print-none');
		//$('#printing .priceCol').removeClass('d-print-block');
		$('#printing .priceCol').addClass('d-print-none');
	}
}

/***********************************************************************************************************************************************
*	Get items from the order selected to make a selection and create them into the order.
***********************************************************************************************************************************************/
function getYourOrderItems(oid){
	//If order options modal is displayed, this should be closed and the copy items modal is displayed instead
	//var modal1 = "";
	if(($("#orderOptions").data('bs.modal') || {})._isShown){
		//Update "order from", on mosOrder
		myData = { mode: "updateFromOrder", foid:oid, curoid:<?php echo $_GET["OID"]?> };
		$.post("OrderItem.php",
				myData, 
				function(data, status, jqXHR) {
					//console.log(jqXHR['responseText']);
				});
		
		$('#selCopyOrd').val(oid);
		$('#orderOptions').modal('hide');
		$('#copyItemsModal').modal('show');
		$('#btnGetItems').prop('disabled',false);	
		$('#btnGetItems').addClass("btn-primary");
	}
	clearModal();
	myData = { mode: "getOrderItemsforCopy", oid:oid, CLid:$('#CLid').val() };
	var r = 0;
	$.post("OrderItem.php",
		myData, 
		function(data, status, jqXHR) {
			$('#itemTable').show();
			var table = "";
			var item = JSON.parse(jqXHR["responseText"]);
			item.forEach(function(obj) {
				if(r!==obj.rid){
					r=obj.rid;
					table = "<tr class=\"table-primary\"><td><input data-toggle=\"tooltip\" data-placement=\"top\" title=\"Select room\" onchange=\"checkRoom("+obj.rid+"),displayCopyBtn(this);\" type='checkbox' id=\"chkR"+obj.rid+"\"></td><td colspan='7'>Room: <b>"+obj.orName+"</b></td></tr>";
					$('#copyItemList').append(table);
				}
				table = "<tr>";
				table += "<td>";
				if(obj.sid=='0'){
					table += "<input class=\"item "+obj.rid+" "+obj.orderItemID+"\" onchange='displayCopyBtn(this),checkChild("+obj.orderItemID+");' type='checkbox' id='"+obj.orderItemID+"'></td>";
				}else{
					table += "<input class=\""+obj.rid+" "+obj.orderItemID+"\" type='checkbox' disabled></td>";
				}
				table += "<td>";
				if(obj.sid=='0'){
					table += "<b>"+obj.name+"</b>";
				}else{
					table += obj.name+"</td>";
				}
				table += "<td>"+ parseFloat(obj.W).toFixed(2)+"</td>";
				table += "<td>"+ parseFloat(obj.H).toFixed(2)+"</td>";
				table += "<td>"+ parseFloat(obj.D).toFixed(2)+"</td>";
				if(obj.HL.length>0 && obj.HR.length>0){
					table += "<td>B</td>";
				}else{
					table += "<td>"+obj.HL+obj.HR+"</td>";
				}
				if(obj.FL.length>0 && obj.FR.length>0){
					table += "<td>B</td>";
				}else{
					table += "<td>"+obj.FL+obj.FR+"</td>";
				}
				if(obj.note!=null){
					table += "<td>"+obj.note+"</td>";
				}else{
					table += "<td></td>";
				}
				table += "</tr>";	
				$('#copyItemList').append(table);
			});
			
		}
	);
}

function displayCopyBtn(obj){
	if($('#copyItemList input:checkbox:checked').length>0){
		$('#btnCopyItems').show();
	}else{
		$('#btnCopyItems').hide();
	}
	if($('#copyItemList input:checkbox:checked').length===$('#copyItemList input:checkbox').length){
		$('#selAllChk').prop('checked',true);
	}else{
		$('#selAllChk').prop('checked',false);
	}
	if($('#copyItemList input:checkbox:checked').length>0){
		$('#btnCopyItems').show();
	}else{
		$('#btnCopyItems').hide();
	}
	//console.log($(obj).prop('class'));
}

function clearModal(){
	//$('#copyItemList').empty();
	$('#copyItemList input:checkbox').prop('checked',false);
	$('#selAllChk').prop('checked',false);
	$('#btnCopyItems').hide();
}

function checkChild(parent){
	if($('#'+parent).prop('checked')){
		$('.'+parent+':checkbox').prop('checked',true);
	}
	else{
		$('.'+parent+':checkbox:checked').prop('checked',false);
	}
}

function selAllItems(){
	if($('#selAllChk').prop('checked')){
		$('#copyItemList input:checkbox').prop('checked',true);
		$('#btnCopyItems').show();
	}else{
		$('#copyItemList input:checkbox').prop('checked',false);
		$('#btnCopyItems').hide();
	}
}

function checkRoom(rid){
	if($('#chkR'+rid).prop('checked')){
		$('.'+rid+':checkbox').prop('checked',true);
	}else{
		$('.'+rid+':checkbox:checked').prop('checked',false);
	}
}

function copyItems(){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
	/*get a list for all the items checked*/
	let items = [];
	$('#copyItemList input.item:checked').each(function(){ 
		items.push(this.id);
	});
	myData = { mode: "copySomeItems", items:items, rid:$("a.nav-link.roomtab.active").attr("value")};
	$.post("OrderItem.php",
		myData, 
		function(data, status, jqXHR) {
			loadItems($("a.nav-link.roomtab.active").attr("value"));
		});
}

function copyItemRow(itemOrig){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
	myData = { mode: "copyRowItem", item:itemOrig, rid:$("a.nav-link.roomtab.active").attr("value")};
	$.post("OrderItem.php",
		myData, 
		function(data, status, jqXHR) {
			//console.log(jqXHR['responseText']);
			loadItems($("a.nav-link.roomtab.active").attr("value"));
		});
	
}

function copyRoom(rid){
	//console.log(rid);
	myData = { mode: "copyRoom", rid:rid };
	$.post("OrderItem.php",
		myData, 
		function(data, status, jqXHR) {
			//console.log(jqXHR['responseText']);
			window.location.reload();
		});
}

function itemFilter(desc){
	//console.log(desc);
	myData = { mode: "itemFilter", filter:desc};
	$.ajax({
	    url: 'OrderItem.php',
	    type: 'POST',
	    data: myData,
	    success: function(data, status, jqXHR) {
	    				var options = "";
						var item = JSON.parse(jqXHR["responseText"]);
						item.forEach(function(obj) {
							//console.log('id:'+obj.id+' name:'+obj.name+" description: "+obj.description);	
							//console.log(obj.name.split('-'));
						});					    		        
    	}
	});	
}

function getImage(item,orderItem){
	$('#itemImg').removeAttr('src');
	myData = { mode: "getImage", item: item, orderItem:orderItem};
	$.ajax({
	url: 'OrderItem.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {
				$image=jqXHR["responseText"];
				if($image!="false"){
					$('#itemImg').attr('src', $image+'#'+ new Date().getTime());
				}else{
					$('#itemImg').attr('src', 'header/unnamed2.png#'+ new Date().getTime());
				}
			}
	});
}

/**********************************************************************************
*	Validation before display modal for submission
***********************************************************************************/
function orderValidation(){
	if($('#tagName').val()=="Tag name not set" || $('#tagName').val()==""){
		alert("Please set your tag name before submit your quote.");
		$('#tagName').css('border-color','red');
		return;//exit if is empty
	}
	/*PO field validation*/
	if(!$('#PO').val()){
		alert("P.O is a mandatory field, please add some relevant information.");
		$('#PO').css('border-color','red');
		return;//exit if is empty
	}
	$('#PO').css('border-color','#ced4da');//set PO input color to default
	/*getting all headers for all the order (header class was added to identify the headers and their select objects)*/
	var arr = $('.container.tab-pane.float-left.col-12.header select').map(function(){
		  return this.value
	}).get();
	var incomplete = false;//initialize variable for incomplete headers
	for (i = 0; i < arr.length; i++) {//looping headers array to confirm all the options are selected
		if(arr[i]=="0"){
			//This means there is one option in header not selected, price will not be displayed
			incomplete = true;
			break;//exit the loop, there is no need to resume the iteration
		}		
	}
	//if some room is incomplete alert is displayed and function is stopped
	if(incomplete){
		alert("Warning!\nOne or more rooms options are not selected.\nPlease complete the options or delete the incomplete room to proceed.");
		return;
	}
	//verify there are some items in the order
	if($('#itemListingTable').length==0){
		alert('Please add some items or remove the room.');
		return;
	}
	/*Validation to prevent send a room without items*/
	myData = { mode: "isSomeRoomEmpty", OID:<?php echo $_GET["OID"]?>, CLid:$('#CLid').val() };
	//console.log(myData);
	$.ajax({
	url: 'OrderItem.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {
				//console.log(jqXHR["responseText"]);
				if(jqXHR["responseText"]==1){
					alert("Warning!\nOne or more rooms are empty.\nPlease add some items or delete the room.");
					return;
				}else{
					//if everything is ok validation was completed successfully then resume process
					setMinDate();
					showSubmit();
					$('#orderOptions').modal('toggle');	
				}
			}
	});	
}
</script>

<div class="navbar navbar-expand-sm bg-light navbar-light d-print-none">
	<div class="col-sm-12 col-md-12 col-lg-12 mx-auto pl-1 pr-1 ml-1 mr-1">
		<div class="row">
            <div class="col-sm-6 col-md-4 col-lg-3 align-self-center mb-0 pb-0">
                <label class="print" for="state">Order ID:</label>
				<textarea readonly class="form-control noresize print" rows="1" id="state">
				<?php echo $mosOrderTB['oid'];?>
				</textarea>
				<label class="d-print-none" for="OID">For Order Number <?php echo $mosOrderTB['oid'];?></label><br/>
				<input class="d-print-none" type="hidden" value="<?php echo $mosOrderTB['oid'];?>" id="OID">
				<button data-toggle="modal" onClick="setMinDate();hideSubmit();" data-target="#orderOptions" class="btn btn-primary text-nowrap px-2 py-2 mx-0  mt-0 d-print-none" data-toggle="modal" data-target="#fileModal" type="button" onClick="loadFiles(<?php echo $mosOrderTB['oid'];?>);">Options<span class="ui-icon ui-icon-gear"></span></button>&nbsp;
				<button class="btn btn-primary text-nowrap px-2 py-2 mx-0 mt-0 d-print-none\" data-toggle="modal" data-target="#fileModal" type="button" onClick="loadFiles( <?php echo $mosOrderTB['oid'];?>);">Files<span class="ui-icon ui-icon-disk"></span></button>&nbsp;
				<?php
                /*Only for Quoting, Submit button should be enabled*/
                if($mosOrderTB['state']==1){
					echo "<button id=\"afterSbm\" type=\"button\" data-toggle=\"modal\" onClick=\"orderValidation();\" class=\"btn btn-primary text-nowrap px-2 py-2  mt-0 mx-0 d-print-none\">Submit<span class=\"ui-icon ui-icon-circle-triangle-e\"></span></button>";
				}/*else{
                    echo "<button type=\"button\" data-toggle=\"modal\" data-target=\"#orderOptions\" class=\"btn btn-primary text-nowrap d-print-none px-2 py-2 mx-0  mt-0\">Order Details</button>";
				}*/
                ?>
            </div>
			<div class="col-sm-6 col-md-4 col-lg-2">
				<label for="state">Order Status:</label>
				<textarea readonly class="form-control noresize" rows="1" id="state"><?php echo $mosOrderTB['status']; ?></textarea>
			</div>
            <div class="col-sm-12 col-md-4 col-lg-3">
			    <label for="tagName">Tag Name:</label>
				<textarea onchange="saveOrder('tagName');" rows="1" class="form-control noresize"  id="tagName"><?php echo $mosOrderTB['tagName'];?></textarea>
			</div>
            <div class="col-sm-6 col-md-4 col-lg-2">
                <label for="PO">P.O:</label>
                <textarea onchange="saveOrder('PO');" rows="1" class="form-control rounded-0 noresize" id="PO"><?php echo $mosOrderTB['po'];?></textarea>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-2">
				<label for="state">Lead Time:</label>
				<textarea title="Some factors may increase your lead time. We will inform you as soon as possible once your quote is submitted." rows="1" readonly class="form-control noresize" id="currentLeadtime"><?php echo substr($settings['currentLeadtime'],0,10);?></textarea>
			</div>
            <div class="col-sm-6 col-md-4 col-lg-2 print">
			    <label for="state">Required date:</label>
				<textarea readonly class="form-control noresize" rows="1"><?php echo substr($dateRequired,0,10); ?></textarea>
			</div>
		</div>
        <?php
        if(in_array($_SESSION["userid"],[1,2,11,30,32]) && $mosOrderTB['state'] <> "1"){			
			echo "<div class=\"row\">";
                echo "<div class=\"col-12\">";
                    echo "Order Locked: <input type=\"checkbox\" ";
                    echo "onchange=\"if($('#isLocked').is(':checked')){viewOnly=1;}else{viewOnly=0;};\" checked id=\"isLocked\">";
			    echo "</div>";
            echo "</div>";
		}
        ?>
	</div>
</div>

<ul class="nav nav-tabs bg-dark d-print-none">
    <?php 
    $sql = "select * from orderRoom where oid = ".$mosOrderTB['oid']." order by name asc";
    $orderRoomList = opendb($sql);
    $s = " active";
    $i = 0;
    if($GLOBALS['$result']->num_rows > 0){
        while($row = $orderRoomList->fetch_assoc()){
            echo "<li value=\"".$row['rid']."\" class=\"btn-group nav-item" . $s . "\">
					<a value=\"".$row['rid']."\" class=\"nav-link roomtab" . $s . "\" onclick=\"loadItems(" .$row['rid']. ");\" href=\"#r". str_replace(" ","",$row['name']) . $i . "\">
						<span class=\"nav-link-active text-muted\">
							<b id=\"" . $row['name'] ."\">" . $row['name'] ."</b>
						</span>
					</a>
                  </li>";
            $s = "";
            $i++;
        }
        $roomCount = $i;
    }else{
        echo "<li class=\"nav-item" . $s . "\"><a class=\"nav-link active\"href=\"#NoRooms\">No Rooms</a></li>";
        $roomCount = 0;
    }
    echo "<li class=\"nav-item d-print-none\"><a onclick=\"addRoom(".$roomCount.")\" id=\"addRoom\" class=\"nav-link text-muted\"  >Add</a></li>";
    ?>
</ul>

<!-- Tab panes -->
<div id="tabs" class="tab-content mb-3 d-print-none">
    <?php 
    if($roomCount==0){
        ?>
        <div id="NoRooms" class="container tab-pane float-left col-12 active"><br>
            <h3>No Rooms</h3>
            <p>No rooms were found. Please click the "Add" tab to create a new room.</p>
        </div>
        <?php 
    }else{
        ?>
        <div id="Add" class="container tab-pane float-left col-12 fade"><br>
            <h3>Add Room</h3>
            <p>This creates a new room</p>
        </div>
        <?php
        $i=0;
        foreach($orderRoomList as $row){
            echo "<div id=\"r" . str_replace(" ","",$row['name']) . $i ."\" class=\"container tab-pane float-left col-12 header";
            if($i==0){
                echo " active";
            }
            echo "\"><br>";
            ?>
            <div class="row">
            	<div class="col-2">
					<a class="btn btn-primary px-2 py-1 text-nowrap ml-0 editbutton d-print-none" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<svg width=".8em" height=".8em" viewBox="0 0 16 16" class="bi bi-pencil-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"></path>
						</svg>
					</a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
						<a class="dropdown-item" onClick="editRoom(<?php echo $row['rid']."," .$row['name'];?>);"  data-toggle="modal" title="Edit room" data-target="#editRoomModal">Edit Room Name/Notes</a>
						<a class="dropdown-item" onclick="copyRoom(<?php echo $row['rid'];?>);">Copy Room '<?php echo ucfirst($row['name']); ?>'</a>
						<a class="dropdown-item" data-toggle="modal" data-target="#copyItemsModal" onclick="clearModal();">Copy Items From Order</a>
					</div>                							
                    <?php
					echo "<b><a  class=\"btn btn-primary px-3 py-1 mr-0 float-right d-print-none\" target=\"_blank\" ";
					if($mosOrderTB['CLid']==3){
					    echo "href=\"header/SPANSTYLES.pdf\">Span Catalogue</a></b>
				</div>"; 
					}else{
						echo "href=\"uploads/MobelCatalogue.pdf\">Catalogue</a></b>
				</div>"; 
					}
					?>		
				<div class="col-2 text-left">
					<button class="btn btn-primary px-3 py-1 ml-0 editbutton d-print-none" data-toggle="modal" data-target="#fileModal" type="button" onClick="loadFiles(<?php echo $mosOrderTB['oid'];?>,$('a.nav-link.roomtab.active').attr('value'));">Room Files 
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-folder" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.828 4a3 3 0 0 1-2.12-.879l-.83-.828A1 1 0 0 0 6.173 2H2.5a1 1 0 0 0-1 .981L1.546 4h-1L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3v1z"></path>
                            <path fill-rule="evenodd" d="M13.81 4H2.19a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4zM2.19 3A2 2 0 0 0 .198 5.181l.637 7A2 2 0 0 0 2.826 14h10.348a2 2 0 0 0 1.991-1.819l.637-7A2 2 0 0 0 13.81 3H2.19z"></path>
                        </svg>
                    </button>                                      
				</div>
				<div class="col-8">
					<?php
                    echo "<p id=\"RoomNotePreview".$row['rid']."\">";
                    if($row['note']) 
                        echo "Room note: <b>" . $row['note']."</b></p>";                    
                    ?>				    
				</div>
                    <?php
					/*if($row['note']){//note only for printing
						echo "<h5 class=\"print\" id=\"RoomNotePrint". $row['rid'] ."\"><b>Room note: </b>" . $row['note']."</h5>";
						echo "<div class=\"dropdown-divider mb-4\"></div>";
					}*/
				echo "</div>";
				
                echo "<input type=\"hidden\" value=\"" .  htmlspecialchars($row['note']) . "\" id=\"RoomNote". $row['rid'] ."\">";
                 
				?>
				<!--div id="cabLineOp"-->
				<?php
				if($mosOrderTB['CLid']==3)
					echo "<div hidden>";
				?> 
					<div class="row">
						<div class="col-2 text-right">
							<label for="species">Species</label>
						</div>
						<div class="col-4">
							<select onchange="saveStyle('species','<?php echo "species" . $row['rid'];?>');" id="<?php echo "species" . $row['rid'];?>" class="custom-select">
							<?php	
							$flag = false;					
							$sql = "select id, name,visible from species s where s.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$mosOrderTB['CLid'].") order by s.name";
							opendb2($sql);
							if($GLOBALS['$result2']->num_rows == 1){
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['visible']==0 && $state==1){
										echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
									}else{
										echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}	
									//If only one option then save Species
									$sql = "update orderRoom set species = ".$row2['id']." WHERE rid = ". $row['rid']; 
									opendb($sql);
								}
							}else if($GLOBALS['$result2']->num_rows > 1){						
								if(is_null($row['species'])||!$flag){
									echo "<option selected value=\"0\">" . "Choose a species" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['id']==$row['species']){
										$flag = true;
										if($row2['visible']==0 && $state==1){
											echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another one</option>";
										}else{
											echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										}											
									}else{
										echo "<option ".$disabled." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}else{
								$invalidHeaderMessage = $invalidHeaderMessage .  "<br>No species selected";
							}
							?>
							</select>
						</div>
						
						<div class="col-2 text-right">
							<?php
							if($mosOrderTB['CLid']==3){
								echo "<label for=\"interiorFinish\">Backing</label>";
							}else{
								echo "<label for=\"interiorFinish\">Interior Finish</label>";
							}
							?>							
						</div>
						<div class="col-4">
							<select onchange="saveStyle('interiorFinish','<?php echo "interiorFinish" . $row['rid'];?>');" id="<?php echo "interiorFinish" . $row['rid'];?>" class="custom-select">						
							<?php
							opendb2("select * from interiorFinish inf where inf.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$mosOrderTB['CLid'].") order by inf.name");
							if($GLOBALS['$result2']->num_rows == 1){
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['visible']==0 && $state==1){
										echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
									}else{
										echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}	
									//If only one option then save interiorFinish
									$sql = "update orderRoom set interiorFinish = ".$row2['id']." where rid = ". $row['rid']; 
									opendb($sql);
								}
							}else if($GLOBALS['$result2']->num_rows > 1){
								if(is_null($row['interiorFinish'])){
									echo "<option ". "selected" ." value=\"0\">" . "Choose an interior finish" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['id']==$row['interiorFinish']){
										if($row2['visible']==0 && $state==1){
											echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
										}else{
											echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										}	
									}else{
										echo "<option ".$disabled." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}else{
								$invalidHeaderMessage = $invalidHeaderMessage .  "<br>No interior finish selected";
							}
							?>
							</select>
						</div>
					</div>
				<?php
				if($mosOrderTB['CLid']==3)
					echo "</div>";
				?> 
					<div class="row">
						<div class="col-2 text-right">
							<?php
							if($mosOrderTB['CLid']==3){
								echo "<label  for=\"doorstyle\"><a id=\"doorPDF\" href=\"header/SPANSTYLES.pdf\" target=\"_blank\">Style</a></label>";
							}else{
								echo "<label  for=\"doorstyle\"><a id=\"doorPDF\" href=\"header/DOORSTYLES.pdf\" target=\"_blank\">Door Style</a></label>";
							}
							?>							
						</div>						
						<div class="col-4">
							<select onchange="$('#doorPDF').attr('href','header/'+$('option:selected', this).attr('doorPDFTag')); saveStyle('door','<?php echo "doorstyle" . $row['rid'];?>');" id="<?php echo "doorstyle" . $row['rid'];?>" class="custom-select">						
							<?php
							$sql = "select d.*,ds.visible from door d, doorSpecies ds where d.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$mosOrderTB['CLid'].") and d.id = ds.did and ds.sid = (select species from orderRoom where rid=".$row['rid'].") order by name";
							//echo $sql;
							opendb2($sql);
							if($GLOBALS['$result2']->num_rows == 1){
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['visible']==0 && $state==1){
										echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
									}else{
										echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}	
									//If only one option then save door
									$sql = "update orderRoom set door = ".$row2['id']." where rid = ". $row['rid']; 
									opendb($sql);
								}
							}else if($GLOBALS['$result2']->num_rows > 1){ 
								$match = 0;
								if(is_null($row['door'])){
									echo "<option doorPDFTag= \"DOORSTYLES.pdf\"". "selected" ." value=\"0\">" . "Choose a door" . "</option>";
								}                        
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['id']==$row['door']){
										if($row2['visible']==0 && $state==1){
											echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
										}else{
											echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										}	
										$match = 1;
									}else{
										echo "<option ".$disabled." doorPDFTag= \"" . $row2['PDF'] . "\"   value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
								if($match == 0 && !is_null($row['door'])){
									echo "<option selected value=\"0\">" . "Please choose a new door style" . "</option>";
									$invalidHeaderMessage = $invalidHeaderMessage .  "<br>No door selected";
								}
							}
							?>
							</select>
						</div>
						
						<div class="col-2 text-right">
							<?php
							if($mosOrderTB['CLid']==3){
								echo "<label  for=\"frontFinish\">Color</label>";
							}else{
								echo "<label for=\"frontFinish\">Finish</label>";
							}
							?>							
						</div>
						<div class="col-4">
							<select onchange="saveStyle('frontFinish','<?php echo "frontFinish" . $row['rid'];?>');" id="<?php echo "frontFinish" . $row['rid'];?>" class="custom-select">						
							<?php
							opendb2("select * from frontFinish where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$mosOrderTB['CLid'].") and finishType in (select ftid from finishTypeMaterial where mid in (select mid from species where id in (select species from orderRoom where rid = " . $row['rid'] . "))) order by name");
							if($GLOBALS['$result2']->num_rows == 1){
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['visible']==0 && $state==1){
										echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
									}else{
										echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}	
									//If only one option then save frontFinish
									$sql = "update orderRoom set frontFinish = ".$row2['id']." where rid = ". $row['rid']; 
									opendb($sql);
								}
							}else if($GLOBALS['$result2']->num_rows > 1){
								if(is_null($row['frontFinish'])){
									echo "<option ". "selected" ." value=\"0\">" . "Choose a finish" . "</option>";
								}
								$match = 0;
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['id']==$row['frontFinish']){										
										if($row2['visible']==0 && $state==1){
											echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
										}else{
											echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										}	
										$match = 1;
									}else{
										echo "<option ".$disabled." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
								if($match == 0 && !is_null($row['frontFinish'])){
									echo "<option ". "selected" ." value=\"0\">" . "Please choose a new finish" . "</option>";
									$invalidHeaderMessage = $invalidHeaderMessage . "<br>No finish selected";
								}
							}
							?>
							</select>
						</div>
					</div>
					<?php
					if($mosOrderTB['CLid']==3)
						echo "<div hidden>";
					?>            	
					<div class="row">
						<div class="col-2 text-right">
							<label for="drawerBox">Drawer Box</label>
						</div>						
						<div class="col-4">
							<select onchange="saveStyle('drawerBox','<?php echo "drawerBox" . $row['rid'];?>');" id="<?php echo "drawerBox" . $row['rid'];?>" class="custom-select">						
							<?php
							opendb2("select * from drawerBox where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$mosOrderTB['CLid'].") order by name");
							if($GLOBALS['$result2']->num_rows == 1){
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['visible']==0 && $state==1){
										echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
									}else{
										echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}	
									//If only one option then save drawerBox
									$sql = "update orderRoom set drawerBox = ".$row2['id']." where rid = ". $row['rid']; 
									opendb($sql);
								}
							}else if($GLOBALS['$result2']->num_rows > 1){
								if(is_null($row['drawerBox'])){
									echo "<option ". "selected" ." value=\"0\">" . "Choose a drawer box" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									//disable or enable option
									if($row2['visible']==0){
											$disabled = "disabled";											
									}else{
										$disabled = "";
									}
									if($row2['id']==$row['drawerBox']){
										if($row2['visible']==0 && $state==1){
											echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
										}else{
											echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										}
									}else{
										echo "<option ".$disabled." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}
							?>
							</select>
						</div>

						<div class="col-2 text-right">
							<label for="glaze">Glaze</label>
						</div>						
						<div class="col-4">
							<select onchange="saveStyle('glaze','<?php echo "glaze" . $row['rid'];?>');" id="<?php echo "glaze" . $row['rid'];?>" class="custom-select">
							
							<?php
							opendb2("select * from glaze where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$mosOrderTB['CLid'].") order by name");
							if($GLOBALS['$result2']->num_rows == 1){
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['visible']==0 && $state==1){
										echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
									}else{
										echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}	
									//If only one option then save glaze
									$sql = "update orderRoom set glaze = ".$row2['id']." where rid = ". $row['rid']; 
									opendb($sql);
								}
							}else if($GLOBALS['$result2']->num_rows > 1){
								if(is_null($row['glaze'])){
									echo "<option ". "selected" ." value=\"0\">" . "Choose a glaze" . "</option>";
									$invalidHeaderMessage = $invalidHeaderMessage . "<br>No glaze selected";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['id']==$row['glaze']){
										if($row2['visible']==0 && $state==1){
											echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
										}else{
											echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										}	
									}else{
										echo "<option ".$disabled." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}
								
							
							?>
							</select>
						</div>
					</div>

					<div class="row">						
						<div class="col-2 text-right">
							<label for="smallDrawerFront">Small Drawer Front</label>
						</div>
						<div class="col-4">
							<select onchange="saveStyle('smallDrawerFront','<?php echo "smallDrawerFront" . $row['rid']; ?>');" id="<?php echo "smallDrawerFront" . $row['rid'];?>" class="custom-select">
							
							<?php
							opendb2("select * from smallDrawerFront where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$mosOrderTB['CLid'].") order by name");
							if($GLOBALS['$result2']->num_rows == 1){
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['visible']==0 && $state==1){
										echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
									}else{
										echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}	
									//If only one option then save smallDrawerFront
									$sql = "update orderRoom set smallDrawerFront = ".$row2['id']." where rid = ". $row['rid']; 
									opendb($sql);
								}
							}else if($GLOBALS['$result2']->num_rows > 1){
								if(is_null($row['smallDrawerFront'])){
									echo "<option selected value=\"0\">" . "Choose a small drawer front" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['id']==$row['smallDrawerFront']){
										if($row2['visible']==0 && $state==1){
											echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
										}else{
											echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										}	
									}else{
										echo "<option ".$disabled." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}
							?>
							</select>
						</div>
						
						<div class="col-2 text-right">
							<label for="sheen">Sheen</label>
						</div>
						<div class="col-4">
							<select onchange="saveStyle('sheen','<?php echo "sheen" . $row['rid'];?>');" id="<?php echo "sheen" . $row['rid'];?>" class="custom-select">
							
							<?php
							$sql ="select * from sheen where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$mosOrderTB['CLid'].") and id in (
								select sid from finishTypeSheen where ftid in (
								select finishType from frontFinish where id in (
								select frontFinish from orderRoom where rid = " . $row['rid'] . "))) order by name";
							echo $sql;
							opendb2($sql);
							$match = 0; //if match is 0, no sheens work. If 1, a matching sheen was found. If 2, sheens were found, but not what was selected.
							if($GLOBALS['$result2']->num_rows == 1){	
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['visible']==0 && $state==1){
										echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
									}else{
										echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}	
									//If only one option then save Sheen
									$sql = "update orderRoom set sheen = ".$row2['id']." WHERE rid = ". $row['rid']; 
									opendb($sql);
								}
							}else if($GLOBALS['$result2']->num_rows > 1){
								echo "<option disabled=\"disabled\" ". "selected" ." value=\"0\">" . "Choose a sheen" . "</option>";
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['id']==$row['sheen']){
										if($row2['visible']==0 && $state==1){
											echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
										}else{
											echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										}	
										$match = 1;
									}else{
										echo "<option ".$disabled." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";								
									}							
								}
							}else{
								echo "<option disabled=\"disabled\" ". "selected" ." value=\"0\"> Please choose another finish</option>";
							}
							?>
							</select>
						</div>						
					</div>

					<div class="row">
						<div class="col-2 text-right">
							<label for="largeDrawerFront">Large Drawer Front</label>
						</div>						
						<div class="col-4">
							<select onchange="saveStyle('largeDrawerFront','<?php echo "LargeDrawerFront" . $row['rid'];?>');" id="<?php echo "LargeDrawerFront" . $row['rid'];?>" class="custom-select">
							
							<?php
							opendb2("select * from largeDrawerFront where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$mosOrderTB['CLid'].") order by name");
							if($GLOBALS['$result2']->num_rows == 1){
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['visible']==0 && $state==1){
										echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
									}else{
										echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}	
									//If only one option then save largeDrawerFront
									$sql = "update orderRoom set largeDrawerFront = ".$row2['id']." where rid = ". $row['rid']; 
									opendb($sql);
								}
							}else if($GLOBALS['$result2']->num_rows > 1){
								if(is_null($row['largeDrawerFront'])){
									echo "<option ". "selected" ." value=\"0\">" . "Choose a large drawer front" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['id']==$row['largeDrawerFront']){
										if($row2['visible']==0 && $state==1){
											echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
										}else{
											echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										}	
									}else{
										echo "<option ".$disabled." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}
							?>
							</select>
						</div>
					
						<div class="col-2 text-right">
							<label for="hinge">Hinge</label>
						</div>						
						<div class="col-4">
							<select onchange="saveStyle('hinge','<?php echo "hinge" . $row['rid'];?>');" id="<?php echo "hinge" . $row['rid'];?>" class="custom-select">
							
							<?php
							opendb2("select * from hinge where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$mosOrderTB['CLid'].") order by name");
							if($GLOBALS['$result2']->num_rows == 1){
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['visible']==0 && $state==1){
										echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
									}else{
										echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}	
									//If only one option then save hinge
									$sql = "update orderRoom set hinge = ".$row2['id']." where rid = ". $row['rid']; 
									opendb($sql);
								}
							}else if($GLOBALS['$result2']->num_rows > 1){
								if(is_null($row['hinge'])){
									echo "<option ". "selected" ." value=\"0\">" . "Choose a hinge" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['id']==$row['hinge']){
										if($row2['visible']==0 && $state==1){
											echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
										}else{
											echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										}	
									}else{
										echo "<option ".$disabled." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}
							?>
							</select>
						</div>
					</div>
					<div class="row">

						<div class="col-2 text-right">
							<label for="drawerGlides">Drawer Glides</label>
						</div>
						<div class="col-4">
							<select onchange="saveStyle('drawerGlides','<?php echo "drawerGlides" . $row['rid'];?>');" id="<?php echo "drawerGlides" . $row['rid'];?>" class="custom-select">
							
							<?php
							opendb2("select * from drawerGlides where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$mosOrderTB['CLid'].") order by name");
							if($GLOBALS['$result2']->num_rows == 1){
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['visible']==0 && $state==1){
										echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
									}else{
										echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}	
									//If only one option then save drawerGlides
									$sql = "update orderRoom set drawerGlides = ".$row2['id']." where rid = ". $row['rid']; 
									opendb($sql);
								}
							}else if($GLOBALS['$result2']->num_rows > 1){
								if(is_null($row['drawerGlides'])){
									echo "<option ". "selected" ." value=\"0\">" . "Choose a drawer glide" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['id']==$row['drawerGlides']){
										if($row2['visible']==0 && $state==1){
											echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
										}else{
											echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										}	
									}else{
										echo "<option ".$disabled." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}
							?>
							</select>
						</div>
						
						<div class="col-2 text-right">
							<label for="finishedEnd">Finished End</label>
						</div>
						<div class="col-4">
							<select onchange="saveStyle('finishedEnd','<?php echo "finishedEnd" . $row['rid'];?>');" id="<?php echo "finishedEnd" . $row['rid'];?>" class="custom-select">							
							<?php
							opendb2("select * from finishedEnd where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$mosOrderTB['CLid'].") order by name");
							if($GLOBALS['$result2']->num_rows == 1){
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['visible']==0 && $state==1){
										echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
									}else{
										echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}	
									//If only one option then save finishedEnd
									$sql = "update orderRoom set finishedEnd = ".$row2['id']." where rid = ". $row['rid']; 
									opendb($sql);
								}
							}else if($GLOBALS['$result2']->num_rows > 1){
								if(is_null($row['finishedEnd'])){
									echo "<option ". "selected" ." value=\"0\">" . "Choose a finished end" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['visible']==0){//not available
										$disabled = "disabled";
									}else{
										$disabled = "";
									}
									if($row2['id']==$row['finishedEnd']){
										if($row2['visible']==0 && $state==1){
											echo "<option ".$disabled." selected" ." value=\"0\">" . $row2['name'] . " is disabled, Please select another</option>";
										}else{
											echo "<option ".$disabled." selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										}	
									}else{
										echo "<option ".$disabled." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}
							?>
							</select>
						</div>
						<?php
						if($mosOrderTB['CLid']==3)
							echo "</div>";
						?> 

					</div>
            	           	
                <!--/div-->
				<?php
				
				echo "</div>";
				$i++;
            //}
        }
    }
    
    ?>
</div>


<div  class="container tab-pane float-left col-12 d-print-none">

    <hr/>
    
    <!-- Trigger the modal with a button -->
    <?php 
    if ($roomCount >0){
    ?>
    <div class="d-flex justify-content-between">
		<!--button type="button"  onClick=cleanEdit("add"); class="btn btn-primary pt-2 pb-2 d-print-none" data-toggle="modal" data-target="#editItemModal">Add Item<span class="ui-icon ui-icon-plus"></span></button-->
		<!--div class="input-group mb-3"-->
		  <div class="input-group-prepend">
			<button type="button" onClick="cleanEdit('add');" class="btn btn-primary py-2 d-print-none" data-toggle="modal" data-target="#editItemModal">Add Item<span class="ui-icon ui-icon-plus"></span></button>
			<!--button type="button" class="btn bt-sm btn-primary py-2 d-print-none dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			</button-->
			<div class="dropdown-menu">
			  <a class="dropdown-item" data-toggle="modal" data-target="#copyItemsModal">Copy Items from another order</a>
			</div>
		  </div>
		<!--/div-->
		<span class="ml-auto d-print-none" id="roomTotal"></span>
	</div>
	
	<?php
	//if($_SESSION["userType"]>1){
	?>
	<div id="divPrintPrice">
		<input class="d-flex float-right" type="checkbox" id="printChk" name="printChk" onclick="printPrice();">
		<small class="d-flex float-right" for="printChk">Print price &nbsp;</small><br>
	</div>
	<?php
	//}
	?>
	
	
    <?php 
    }
    ?>

    

<div id="confirmCopyRoom" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Copy room</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>this action will copy the entire room (including items and mods) as a new room.<br/>Do you want to continue?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="copyRoom()">Yes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Copy item-->
    <div id="copyItemsModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
        
        <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
					<div class="container-fluid">
						<div class="row">
							<div class="col">
								<h5 class="modal-title">Copy Items</h5>
							</div>
							<div class="col">
								<select id="selCopyOrd" onchange="getYourOrderItems(this.value)" class="custom-select">
									<option value="">Please select an order</option>
									<?php
									$admin = "";
									if($_SESSION["userType"]>1){
										$admin = "or m.account = " . $_SESSION["account"];
									}
									$sql = "select m.oid,m.tagName from mosOrder m, mosUser u where m.mosUser = u.id and (u.email = '" . $_SESSION["username"] . "' ". $admin ."  )";
									$result = opendb($sql);
									while($row=$result->fetch_assoc()){
										echo "<option value=\"".$row['oid']."\">".$row['oid']." - ".$row['tagName']."</option>";
									}
									?>
								</select>
							</div>
							<div class="col">
								<button onclick="clearModal();" type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
						</div>
					</div>
                </div>
                
                <div class="modal-body">
				<table id="itemTable" class="table table-striped">
					<thead>
						<tr>
							<th><input data-toggle="tooltip" data-placement="top" title="Select All" type="checkbox" onchange="selAllItems();" id="selAllChk"/></th>
							<th>Description</th>
							<th>W</th>
							<th>H</th>
							<th>D</th>
							<th>Hinged</th>
							<th>F.E.</th>
							<th>Note</th>
						</tr>
					</thead>
					<tbody id="copyItemList">
					</tbody>
				</table>
                </div>
                
                <div class="modal-footer">
                    <button id="btnCopyItems" onClick="copyItems();" type="button" class="btn btn-default" data-dismiss="modal">Copy Items</button>
                    <button onclick="clearModal();" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


<!-- Modal edit item-->
    <div id="editItemModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
        
        <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="editItemTitle">Edit/Delete Item</h4>
                </div>
                
                <div class="modal-body">
					<p>Changes save as you finish making them.</p>
					<div class="row">
						<div class="col-7">
							Starts with:<input type="checkbox" id="startsWith" checked>
							<div class="col-xs-2">
								Find Item:
								<input class="col-xs-2" autocomplete="off" type="text"  id="editItemSearch" onkeyup="showResult(this.value)">
							</div>
							<div class="col-xs-2" id="livesearch"></div>

							<input type="hidden" id="editOrderItemPID" name="editOrderItemPID" value="0" >
							<input type="hidden" id="editItemID" name="editItemID" value="0" >
							<input type="hidden" id="editOrderItemID" name="editOrderItemID" value="0" >
							<br/>
							<div class="row">
								<div class="col-auto text-left">
									<span class="form-inline">									
										<label for="Qty">Quantity:</label>
										<textarea onchange="saveEditedItem('Qty','qty');" rows="1" cols="8" class="form-control mx-1" id="Qty"></textarea>
										<label id="lblPosition" for="Position">Position:</label>
										<select disabled id="Position" class="form-control" onchange="saveEditedItem('Position','position');"></select>										
									</span>
									<br/>
								</div>
							</div>
							<div id="itemSizes"  class="col-auto text-left">
								<span class="form-inline d-flex justify-content-start">
									<label for="W" data-toggle="tooltip" data-placement="top" title="Width">Width</label>							
									<textarea data-toggle="tooltip" data-placement="top" title="Width" onchange="saveEditedItem('W','W');"  rows="1" cols="7" class="form-control" id="W"></textarea>&nbsp;
									<!--label id="W2lbl" for="W2" data-toggle="tooltip" data-placement="top" title="Width right">Width right:</label>
									<textarea data-toggle="tooltip" data-placement="top" title="Width right" onchange="saveEditedItem('W2','W2');"  rows="1" cols="3" class="form-control" id="W2"></textarea>&nbsp;-->
									<label data-toggle="tooltip" data-placement="top" title="Height" for="H">Height</label>
									<textarea data-toggle="tooltip" data-placement="top" title="Height" onchange="saveEditedItem('H','H');" rows="1" cols="7" class="form-control" id="H"></textarea>&nbsp;
									<label for="D" data-toggle="tooltip" data-placement="top" title="Depth">Depth</label>
									<textarea data-toggle="tooltip" data-placement="top" title="Depth" onchange="saveEditedItem('D','D');"  rows="1" cols="7" class="form-control" id="D"></textarea>
									<!--label id="D2lbl" for="D2" data-toggle="tooltip" data-placement="top" title="Depth right">Depth Right:</label>
									<textarea data-toggle="tooltip" data-placement="top" title="Depth right" onchange="saveEditedItem('D2','D2');"  rows="1" cols="3" class="form-control" id="D2"></textarea-->
								</span>
								<span class="form-inline d-flex justify-content-start pt-3">
									<label id="W2lbl" for="W2" data-toggle="tooltip" data-placement="top" title="Width right">Width right</label>
									<textarea data-toggle="tooltip" data-placement="top" title="Width right" onchange="saveEditedItem('W2','W2');"  rows="1" cols="7" class="form-control" id="W2"></textarea>&nbsp;
									<label id="D2lbl" for="D2" data-toggle="tooltip" data-placement="top" title="Depth right">Depth right</label>
									<textarea data-toggle="tooltip" data-placement="top" title="Depth right" onchange="saveEditedItem('D2','D2');"  rows="1" cols="7" class="form-control" id="D2"></textarea>
								</span>
							</div><br/>
							<div class="row">
								<div class="col-6 text-left">
									<label for="Hinged">Hinge:</label> Left <input onchange="saveEditedItem('HL','hingeLeft');" type="checkbox" id="HL" checked>
									<input onchange="saveEditedItem('HR','hingeRight');" type="checkbox" id="HR">&nbsp;Right
								</div>
								<div class="col-6 text-left">
									Finished: Left <input type="checkbox" value="" id="FL" onchange="saveEditedItem('FL','finishLeft');" ><input type="checkbox" id="FR" onchange="saveEditedItem('FR','finishRight');" >&nbsp;Right
								</div>
							</div>
						</div>
						<div class="col-5 zoom">
							<img id="itemImg" class="img-fluid">
						</div>
                    </div>
                    <br/>
                    <div class="row">Notes:<textarea maxlength="138" onchange="saveEditedItem('note','note');"  rows="4" cols="20" class="form-control" id="note"></textarea></div>
                </div>
                
                <div class="modal-footer">
                    <button id="deleteItemButton" onClick=deleteItem(); type="button" class="btn btn-default" data-dismiss="modal">Delete Item</button>
                    <!-- <button type="button" onClick=saveItem(); class="btn btn-default" data-dismiss="modal">Add Item</button>-->
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    
    
    
    
    <!-- Modal Room Editor-->
    <div id="editRoomModal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-xl">
    
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            
            <h4 class="modal-title">Room Tools</h4>
			<button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <p>Adjust the room name or add notes here. Click delete room to remove this room and all items and attached files.</p>
            <?php 
            echo "<div class=\"col-sm-6 col-md-6 col-lg-6\">";
            echo "<label for=\"Room Name\">Room Name:</label>";
            echo "<textarea onchange=\"saveRoom('RoomName');\" class=\"form-control\"  id=\"RoomName\">";
            echo "<span id=\"modalroomname\">error</span>";
            echo "</textarea>";
            echo "</div>";
            
            echo "<div class=\"col-sm-6 col-md-6 col-lg-6\">";
            echo "<label for=\"RoomNote\">Room Notes:</label>";
            echo "<textarea onchange=\"saveRoom('RoomNote');\" class=\"form-control\"  id=\"RoomNote\">";
            echo "<span id=\"modalroomnote\">error</span>";
            echo "</textarea>";
            echo "</div>";
            ?>
            
            
          
            
          </div>
          <div class="modal-footer">
          	<button type="button" onClick=deleteRoom(); class="btn btn-default" data-dismiss="modal">Delete Room</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>







    <!-- Modal File Editor-->
    <div id="fileModal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-xl">
    
        <!-- Modal content-->
        <div class="modal-content">
          <!-- <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">My Files</h4>
          </div> -->
          <div class="modal-body" id="fileListing">

          </div>
          <div class="modal-footer">
          	<!-- <button type="button" onClick=deleteRoom(); class="btn btn-default" data-dismiss="modal">Delete Room</button> -->
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>








    <!-- Submission Info-->
    <div id="orderOptions" class="modal fade" role="dialog">
      	<div class="modal-dialog modal-xl">
    
	        <!-- Modal content-->
	        <div class="modal-content">
	          <!-- <div class="modal-header">

	          </div>-->
	          	<div class="modal-body">
		            <h2  class="modal-title"><span id="orderOptionsTitle"></span>
		            <button type="button" class="close" data-dismiss="modal">&times;</button></h2>
		            <hr>
		          	
		            <!--div class="col-12"-->
						<div class="row">
							<div class="col-xs-3 col-lg-3">
								<b>This is a:</b>
								<?php 
								echo "<select onchange=\"saveOrder('isPriority');fixDate();showOrderOptions('isPriority');\" class=\"form-control \" id=\"isPriority\">";						
								if($mosOrderTB['isPriority']==0){
									echo "<option selected value=\"0\">Standard Order</option>";
									echo "<option value=\"1\">Service Order</option>";
								}else{
									echo "<option value=\"0\">Standard Order</option>";
									echo "<option selected value=\"1\">Service Order</option>";
								}
								echo "</select>";							
								?>					
							</div>
							<div class="col-xs-2 col-lg-2">
								Required Date: 
								<?php 
								echo "<input title=\"Some factors may increase your lead time. We will inform you as soon as possible once your quote is submitted.\" type=\"text\" maxlength=\"10\" data-provide=\"datepicker\" data-date-format=\"yyyy-mm-dd\" onchange=\"saveOrder('dateRequired');\" class=\"form-control datepicker\"  value=\"". substr($dateRequired,0,10) ."\" id=\"dateRequired\">";
								?>
							</div>
							<div class="col-xs-1 col-lg-1 text-center service">
								Warranty: 
								<?php 
								echo "<input type=\"checkbox\"";
								if($mosOrderTB['isWarranty']>0){
									echo " checked ";
								}
								echo " onchange=\"saveOrder('isWarranty');\" class=\"form-control  \"  id=\"isWarranty\">";
								?>
							</div>
							<div class="col-xs-3 col-lg-3 service">
								Original Order Number:
								<select onchange="getYourOrderItems(this.value)" class="custom-select">
									<option value="">Please select an order</option>
									<?php
									$admin = "";
									if($_SESSION["userType"]>1){
										$admin = "or m.account = " . $_SESSION["account"];
									}
									$sql = "select m.oid,m.tagName from mosOrder m, mosUser u where m.mosUser = u.id and (u.email = '" . $_SESSION["username"] . "' ". $admin ."  )";
									$result = opendb($sql);
									while($row=$result->fetch_assoc()){
										echo "<option ";
										if($row['oid']==$fromOrder)
											echo "selected ";
										echo "value=\"".$row['oid']."\">".$row['oid']." - ".$row['tagName']."</option>";
										//echo "<option value=\"".$row['oid']."\">".$row['oid']." - ".$row['tagName']."</option>";
									}
									?>
								</select>						
							</div>					
							<div class="col-xs-3 col-lg-3">          
								<?php 
								//Cabinet Lines functionality start
								if($_SESSION["CLGroup"]>3){
									echo "Line:<select onchange=\"saveOrder('CLid');\" class=\"form-control \" id=\"CLid\">";
									$sql = "select * from cabinetLine cl where cl.id in (select clg.CLid from cabinetLineGroups clg where clg.CLGid = ".$_SESSION["CLGroup"].")";
									//echo $sql;
									$result = opendb($sql);
									if($result->num_rows >0){
										while ( $row = $result->fetch_assoc())  {
											echo "<option ";
											if($mosOrderTB['CLid']==$row["id"]) echo "selected "; 
											echo "class=\"form-control \"  value=\"".$row["id"]."\">".$row["CabinetLine"]."</option>";
										}
									}else{
										echo "<option selected>No Cabinet Line was found for your profile, please check with the administrator</option>";
									}
									echo "</select>";
								}
								//Cabinet Lines functionality end
								?>					
							</div>
						</div>
					<!--/div-->
		            <br/>            
		            <!--div class="col-sm-12 col-md-12 col-lg-12"-->
		    	        <div class="row">
		    	        	<div class="col-sm-12 col-md-12 col-lg-12">
		          	          <label for="shipAddress">Ship To:</label>
		                    </div>
		                </div>
		                <div class="row">
		                	<div class="col-sm-12 col-md-12 col-lg-12">
			     		    	<select onchange="saveOrder('shipAddress','shipAddress');" id="shipAddress" class="custom-select">
				 		       	<?php 
				               	opendb("SELECT a.*,m.shipAddress,m.note FROM mosOrder m, accountAddress a, addressType t WHERE a.aType = t.id and t.name = 'Shipping' and m.account = a.aid and m.oid = ". $_GET["OID"] ." order by contactName asc");
				                if($GLOBALS['$result']->num_rows > 0){
				                    foreach ($GLOBALS['$result'] as $row) {
				                        $OrderNote = $row['note'];
				                        if(is_null($row['shipAddress'])||$row['shipAddress']==""){
				                            echo "<option value=\"\">" . "Please choose a ship location" . "</option>";
				                        }

				                		foreach ($GLOBALS['$result'] as $row) {
				                		    if($row['shipAddress']==$row['id']){
				                                echo "<option ". "selected" ." value=\"" . $row['id'] . "\">" . $row['contactName']. " " . $row['contactEmail']. " " . $row['contactPhone']. " " . $row['unit']. " " . $row['street'].  ", " .$row['city']. ", " . $row['province']. " " . $row['postalCode'].  "</option>";
				                		    }else{
				                		        echo "<option value=\"" . $row['id'] . "\">" . $row['contactName']. " " . $row['contactEmail']. " " . $row['contactPhone']. " " . $row['unit']. " " . $row['street'].  ", " .$row['city']. ", " . $row['province']. " " . $row['postalCode'].  "</option>";
				                		    }
				                		}
				                		if($row['shipAddress']=='0'){
				                		    echo "<option ". "selected" ." value=\"" . $row['id'] . "\">" . "Custom Site Delivery (additional charge may apply)" . "</option>";
				                		}else{
				                		    echo "<option value=\"" . $row['id'] . "\">" . "Custom Site Delivery (additional charge may apply)" . "</option>";
				                		}
				                		if($row['shipAddress']=='1'){
				                		    echo "<option ". "selected" ." value=\"1\">" . "Pick up at Mobel" . "</option>";
				                		}else{
				                		    echo "<option value=\"1\">" . "Pick up at Mobel" . "</option>";
				                		}
				                    }
				                }
			                    ?>
			                	</select>
		                	</div>
		            	</div>
		            <!--/div-->
		            <br/>
		            <div class="row mb-2">
				        <div class="col-sm-12 col-md-12 col-lg-8 date">
				            <label for="OrderNote">Order Notes:</label>
				            <?php 
				            echo "<textarea onchange=\"saveOrder('OrderNote');\" class=\"form-control\"  id=\"OrderNote\">".$OrderNote."</textarea>";
				            ?>
				        </div>
				        <?php 
				        $invoiceArr = ["Mobel","Dealer","Builder","Retailer","Designer","Installer"];
				        $result = opendb("select invoiceTo from mosOrder where oid=". $_GET["OID"]);
				        $row = $result->fetch_assoc();
				        ?>
				       	<div class="col-sm-12 col-md-12 col-lg-4 service">
				       		<label for="shipAddress">Invoice to:</label>
				    		<select onchange="saveOrder('invoiceTo');" id="invoiceTo" class="custom-select">
				    			<option value="">Please select an option</option>
				    			<?php				    			
				    			foreach($invoiceArr as $val){
				    				$selected = "";
				    				if($val==$row['invoiceTo']){
				    					$selected = "selected";
				    				}
				    				echo "<option value=\"".$val."\" $selected>".$val."</option>";
				    			}
				    			?>
				       		</select>
				    	</div>
				    </div>
			        <div class="modal-footer">
			          	<div class="container">
				          	<div class="row d-flex justify-content-end py-auto">
				          		<div id="submitText" class="col-sm-12 col-md-12 col-lg-8">
						           	<p>Please check your shipping method is correct and indicate the date your order is required.</p>
							        <p>Your order will be electronically submitted to orders@mobel.ca and will be processed by our staff.<br/> You will get a copy of the report and will hear from us soon.</p>
						        </div>
						        <div class="col-sm-12 col-md-12 col-lg-4">
						        	<div class="row">
					          			<button id="submitButton" type="button" onClick=submitToMobel(); class="btn btn-default mx-auto" data-dismiss="modal" data-toggle="tooltip" data-placement="top" title="Please first remember to select all the options">Submit to Mobel</button>
					            		<button type="button" class="btn btn-default mx-auto" data-dismiss="modal">Close</button>
						            </div>
						        </div>
				          	</div>
				        </div>
			        </div>
	        	</div>
      		</div>
   	 	</div>
   	</div>






    <div id="items">
    </div>
</div>




<input type="hidden" id="invalidHeaderMessage" value="<?php echo ""; //$invalidHeaderMessage;?>">


<!--This is made for printing only -->
<div id="printing" class="print">
<?php
$sql = "select *,coalesce(invoiceTo,'N/A')invoiceTo ,(select concat(coalesce(unit,' '),' ',street,' ',city,' ',province,' ',country,' ',postalCode)  from accountAddress aA where aA.id =mo.shipAddress) shipTo, coalesce((select concat(mu.firstName,' ',mu.lastName) from mosUser mu where mu.id = mo.submittedBy),'No name')whoSubmit, (select a.busDBA from account a where a.id = mo.account)busName,isPriority, isWarranty, CLid from mosOrder mo, mosUser mu, account a, cabinetLine cl where mo.mosUser = mu.id and mo.account = a.id and mo.CLid = cl.id and mo.oid = '" . $_GET["OID"] . "'";
$result = opendb($sql);
$row = $result->fetch_assoc();
$accountName = $row['busDBA'];
$mailOID = $row['oid'];
$CLfactor = $row['factor'];
$orderType="";
$discount = floatval($row['discount']);
$orderTypeDesc="Dealer";
if($row['CLid']==3){
	$orderType="table-primary";
	$orderTypeDesc = "Span Medical";
}
if($row['CLid']==2){
	$orderType="table-info";
	$orderTypeDesc = "Builder";
}
if($row['isPriority']==1){
	$orderType="table-warning";
	$orderTypeDesc = "Service";
}
if($row['isWarranty']==1){
	$orderType="table-danger";
	$orderTypeDesc = "Service w/warranty";
}
$msg = "
<body>
	<div class=\"bg-white container-fluid\">
		<div class=\"row d-flex justify-content-around align-items-center\">
			<img id=\"logo\" alt=\"logo\" src=\"https://mobel.ca/wp-content/uploads/2019/01/Logo.png\"/>
			<h1>MOS #&nbsp;<b>".$mailOID."</b></h1>
		</div>
		<div class=\"row\">			
			<div class=\"col-12\">
				<table class=\"table table-sm my-auto mx-5\">
					<tr>
						<td class=\"border-0 text-right\"><h5>Customer:</h5></td>
						<td class=\"border-0 text-left\"><h5 class=\"font-weight-bold\">". $row['busName']."</h5></td>
						<td class=\"border-0 text-right\"><h5>Submitted by:</h5></td>
						<td class=\"border-0 text-left\"><h5>". $row['whoSubmit'] ."</h5></td>						
					</tr>";
if($_SESSION["userType"]==3){
			$msg .= "<tr>
						<td class=\"border-0 text-right\"><h5>Order Type:</h5></td>
						<td class=\"border-0 text-left\"><h5 class=\"font-weight-bold\">". $orderTypeDesc ."</h5></td>
						<td class=\"border-0 text-right\"><h5>Invoice to:</h5></td>
						<td class=\"border-0 text-left\"><h5>". $row['invoiceTo'] ."</h5></td>
					</tr>";
}
$msg .= "			<tr>
						<td class=\"border-0 text-right\"><h5>Date Submitted:</h5></td>
						<td class=\"border-0 text-left\"><h5>". substr($row['dateSubmitted'],0,10) ."</h5></td>
						<td class=\"border-0 text-right\"><h5>Tag Name / PO:</h5></td>
						<td class=\"border-0 text-left\"><h5 class=\"font-weight-bold\">".$row['tagName']." - ". $row['po'] ."</h5></td>
					</tr>
					<tr>						
						<td class=\"border-0 text-right\"><h5>Ship to:</td>
						<td colspan=\"5\" class=\"border-0 text-left\"><h5>". $row['shipTo'] ."</h5></td>
					</tr>
				</table>
			</div>
		</div>
		<div style=\"height: 7px\" class=\"bg-dark\">&nbsp</div>";
		if(isset($row['note']))
			$msg .= "<h5 class=\"font-weight-bold\">Order notes: ".$row['note']."</h5>";
		//Rooms start here
		$sql = "select orr.rid,orr.note,orr.name rname,sp.name spname,irf.name irfname,dd.name ddname,ff.name ffname,db.name dbname,gl.name glname,sdf.name sdfname,sh.name shname,ldf.name ldfname,h.name hname,dg.name dgname, fe.name fename
		from orderRoom orr,species sp,interiorFinish irf,door dd,frontFinish ff,drawerBox db,glaze gl,smallDrawerFront sdf,sheen sh,largeDrawerFront ldf,hinge h,drawerGlides dg,finishedEnd fe where orr.oid=".$mailOID." and orr.species=sp.id and orr.door=dd.id and orr.frontFinish=ff.id and orr.glaze=gl.id and orr.glaze=gl.id and orr.sheen=sh.id and orr.hinge=h.id and orr.smallDrawerFront=sdf.id and orr.largeDrawerFront=ldf.id and orr.drawerGlides=dg.id and orr.drawerBox=db.id and orr.interiorFinish=irf.id and orr.finishedEnd=fe.id order by orr.name";
		$result = opendb($sql);
		$totalOrder = 0;
    	while($row = $result->fetch_assoc()){
    		$roomTotal = 0;
	    	$msg .="<table class=\"table table-sm mt-1 mb-0 border border-dark\">
				<tr class=\"table-secondary\">
					<td class=\"text-start py-1 my-auto\"><h5><b>Room: ".$row['rname']."</b></h5></td>
					<td class=\"text-start py-1 my-auto\" colspan=\"3\"><b>Room notes: ".$row['note']."</b></td>
				</tr>
				<tr>
					<td class=\"text-right py-0 font-weight-bold\">Species:</td>
					<td class=\"text-start py-0 font-weight-bold\">".$row['spname']."</td>
					<td class=\"text-right py-0 font-weight-bold\">Interior Finish:</td>
					<td class=\"text-start py-0 font-weight-bold\">".$row['irfname']."</td>
				</tr>
				<tr>
					<td class=\"text-right py-0 font-weight-bold\">Door:</td>
					<td class=\"text-start py-0 font-weight-bold\">".$row['ddname']."</td>
					<td class=\"text-right py-0 font-weight-bold\">Finish:</td>
					<td class=\"text-start py-0 font-weight-bold\">".$row['ffname']."</td>
				</tr>
				<tr>
					<td class=\"text-right py-0 font-weight-bold\">Drawer Box:</td>
					<td class=\"text-start py-0 font-weight-bold\">".$row['dbname']."</td>
					<td class=\"text-right py-0 font-weight-bold\">Glaze:</td>
					<td class=\"text-start py-0 font-weight-bold\">".$row['glname']."</td>
				</tr>
				<tr>
					<td class=\"text-right py-0 font-weight-bold\">Small Drawer Front:</td>
					<td class=\"text-start py-0 font-weight-bold\">".$row['sdfname']."</td>
					<td class=\"text-right py-0 font-weight-bold\">Sheen:</td>
					<td class=\"text-start py-0 font-weight-bold\">".$row['shname']."</td>
				</tr>
				<tr>
					<td class=\"text-right py-0 font-weight-bold\">Large Drawer Front:</td>
					<td class=\"text-start py-0 font-weight-bold\">".$row['ldfname']."</td>
					<td class=\"text-right py-0 font-weight-bold\">Hinge:</td>
					<td class=\"text-start py-0 font-weight-bold\">".$row['hname']."</td>
				</tr>
				<tr>
					<td class=\"text-right py-0 font-weight-bold\">Drawer Glides:</td>
					<td class=\"text-start py-0 font-weight-bold\">".$row['dgname']."</td>
					<td class=\"text-right py-0 font-weight-bold\">Finished End:</td>
					<td class=\"text-start py-0 font-weight-bold\">".$row['fename']."</td>
				</tr>
			</table>
			<table class=\"table table-sm border border-dark mb-3\">
				<thead>
					 <tr>
	                	<th class=\"font-weight-bold border text-center border-dark\">Item</th>
		                <th class=\"font-weight-bold border border-dark\">Description</th>
		                <th class=\"font-weight-bold border text-center border-dark\">W</th>
		                <th class=\"font-weight-bold border text-center border-dark\">H</th>
		                <th class=\"font-weight-bold border text-center border-dark\">D</th>
		                <th class=\"font-weight-bold border text-center border-dark\">Qty</th>
		                <th class=\"font-weight-bold border text-center border-dark\">Hinged</th>
		                <th class=\"font-weight-bold border text-center border-dark\">F.E.</th>
		                <th class=\"font-weight-bold border border-dark\">Note</th>";
		        if($_SESSION["userType"]>1){
                    $msg .="<th class=\"d-print-none font-weight-bold border border-dark priceCol\">Price</th>";
                }
	              	$msg .= "</tr>
				</thead>
				<tbody>";

			$sql2 = "select * from (SELECT orr.rid,it.CLGroup,oi.description, oi.note, oi.id as item, 0 as sid,oi.position, oi.qty, oi.name, oi.price, oi.sizePrice, 0 as 'parentPercent', ds.factor as 'DFactor', irf.factor as 'IFactor', ff.factor as 'FFactor', ff.upcharge as 'FUpcharge', sh.factor as 'SFactor', gl.factor as 'GFactor', sp.finishedEndSizePrice as 'EFactor', (db.upcharge + dg.upcharge) as 'drawerCharge', sdf.upcharge as 'smallDrawerCharge', ldf.upcharge as 'largeDrawerCharge', oi.doorFactor as 'DApplies', oi.speciesFactor as 'SpeciesApplies', oi.interiorFactor as 'IApplies', oi.finishFactor as 'FApplies', oi.sheenFactor as 'SApplies', oi.glazeFactor as 'GApplies',oi.drawers, oi.smallDrawerFronts, oi.largeDrawerFronts, oi.H, oi.W, oi.D, oi.W2, oi.D2, oi.minSize, it.pricingMethod as methodID, oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight
	    FROM  orderItem oi, orderRoom orr, doorSpecies ds, interiorFinish irf, item it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg, smallDrawerFront sdf, largeDrawerFront ldf, species sp
	    WHERE it.id = oi.iid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = " .$row['rid']." 
	    union all
	        SELECT orr.rid,it.CLGroup,oi.description, oi.note, oi.pid,oi.id as sid, oi.position, oi.qty, oi.name, oi.price, oi.sizePrice, parentPercent, ds.factor as 'DFactor', irf.factor as 'IFactor', ff.factor as 'FFactor', ff.upcharge as 'FUpcharge', sh.factor as 'SFactor', gl.factor as 'GFactor', sp.finishedEndSizePrice as 'EFactor', (db.upcharge + dg.upcharge) as 'drawerCharge', sdf.upcharge as 'smallDrawerCharge', ldf.upcharge as 'largeDrawerCharge', oi.doorFactor as 'DApplies', oi.speciesFactor as 'SpeciesApplies', oi.interiorFactor as 'IApplies', oi.finishFactor as 'FApplies', oi.sheenFactor as 'SApplies', oi.glazeFactor as 'GApplies',oi.drawers, oi.smallDrawerFronts, oi.largeDrawerFronts, oi.H, oi.W, oi.D, oi.W2, oi.D2, oi.minSize, it.pricingMethod as methodID, oi.hingeLeft,oi.hingeRight,oi.finishLeft,oi.finishRight
	    FROM  orderItemMods oi, orderRoom orr, doorSpecies ds, interiorFinish irf, itemMods it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg,  smallDrawerFront sdf, largeDrawerFront ldf, species sp
	    WHERE it.id = oi.mid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = ".$row['rid'].") as T1 order by rid,position,item,sid";

		    $result2 = opendb2($sql2);
		    $i = 0;
            $si= 0;
            $parentID = -1;
            $isParent = -1;
		    while($row2 = $result2->fetch_assoc()) {
		    	if($parentID !== $row2['item']){ //new parent item
                    $parentID = $row2['item'];
                    $isParent = 1;
                    $parentPrice = 0;
                    $si = 0;
                    $i++;
                }else{
                    opendb2("select price from item where id = (select iid from orderItem where id = " . $row2['item'] . ")");
                    foreach($GLOBALS['$result2'] as $row1){
                        $parentPrice = $row1['price'];
                    }
                    $isParent = 0;
                    $si = $si + 1;
                    //$i = $i - 1;
                }
	            $hinging = "";
	            if($row2['hingeLeft']==1){
	                $hinging = "L";
	            }
	            if($row2['hingeRight']=="1"){
	                $hinging = "R";
	            }
	            if($row2['hingeLeft']=="1" && $row2['hingeRight'] =="1"){
	                $hinging = "B";
	            }
	            $finishedEnds = "";
	            if($row2['finishLeft']=="1"){
	                $finishedEnds = "L";
	            }
	            if($row2['finishRight']=="1"){
	                $finishedEnds = "R";
	            }
	            if($row2['finishLeft']=="1" && $row2['finishRight']=="1"){
	                $finishedEnds = "B";
	            }
	            $mixDoorSpeciesFactor = 0;
                if($row2['DApplies'] == 1 || $row2['SpeciesApplies']==1){
                    $mixDoorSpeciesFactor = 1;
                }else{
                    $mixDoorSpeciesFactor = 0;
                }
                $b=""; 
                $be = "";
                if($isParent===1){
                	$b = "<b>";
                	$be = "</b>";
                }
                $aPrice =  getPrice($row2['qty'],$row2['price'],$row2['sizePrice'],$parentPrice,$row2['parentPercent'],$row2['DFactor'],$row2['IFactor'],$row2['FFactor'],$row2['GFactor'],$row2['SFactor'],$row2['EFactor'],$row2['drawerCharge'],$row2['smallDrawerCharge'],$row2['largeDrawerCharge'], $mixDoorSpeciesFactor,$row2['IApplies'],$row2['FApplies'],$row2['GApplies'],$row2['SApplies'],$row2['drawers'],$row2['smallDrawerFronts'],$row2['largeDrawerFronts'],$row2['finishLeft']+$row2['finishRight'], $row2['H'],$row2['W'],$row2['D'],$row2['minSize'],$row2['methodID'],$row2['FUpcharge'],$CLfactor);
                $roomFinishUpcharge=$row2['FUpcharge'];
                if($isParent === 1){
                    $parentPrice = $aPrice;
                }
                $roomTotal += $aPrice;
			    $msg .="<tr>
						<td class=\"border text-center border-dark\">".$b.$i.".".$si.$be."</td>
						<td class=\"border border-dark\">".$b.$row2['name']." - ".$row2['description'].$be."</td>
						<td class=\"border text-center border-dark\">".$b.(float)$row2['W'].$be."</td>
						<td class=\"border text-center border-dark\">".$b.(float)$row2['H'].$be."</td>
						<td class=\"border text-center border-dark\">".$b.(float)$row2['D'].$be."</td>
						<td class=\"border text-center border-dark\">".$b.(float)$row2['qty'].$be."</td>
						<td class=\"border text-center border-dark\">".$b.$hinging.$be."</td>
						<td class=\"border text-center border-dark\">".$b.$finishedEnds.$be."</td>
						<td class=\"border border-dark\" style=\"max-width: 450px;\">".$row2['note']."</td>
						<td class=\"d-print-none border text-center border-dark priceCol\"><span title = \"" . getPrice($row2['qty'],$row2['price'],$row2['sizePrice'],$parentPrice,$row2['parentPercent'],$row2['DFactor'],$row2['IFactor'],$row2['FFactor'],$row2['GFactor'],$row2['SFactor'],$row2['EFactor'],$row2['drawerCharge'],$row2['smallDrawerCharge'],$row2['largeDrawerCharge'], $mixDoorSpeciesFactor,$row2['IApplies'],$row2['FApplies'],$row2['GApplies'],$row2['SApplies'],$row2['drawers'],$row2['smallDrawerFronts'],$row2['largeDrawerFronts'],$row2['finishLeft']+$row2['finishRight'], $row2['H'],$row2['W'],$row2['D'],$row2['minSize'],$row2['methodID'],$row2['FUpcharge'],$CLfactor,1) . "\">".$b. number_format($aPrice,2,'.','').$be."</span></td>
					</tr>";
			}
			if($_SESSION["userType"]>1){
                $msg .= "<tr class=\"d-print-none border-top border-dark priceCol\">
                            <td class=\"text-end\" colspan=\"9\"><h5 class=\"font-weight-bold\">Room Total:</h5></td>
                            <td class=\"text-center\"><h5 class=\"font-weight-bold\">$".round($roomTotal,2)."</h5></td>
                        </tr>";
            }
			$msg .="</tbody></table>";
			$totalOrder += $roomTotal+$roomFinishUpcharge;
		}
	if($_SESSION["userType"]>1){
		$discountPer = $discount*100;
		$disAmnt = round(($totalOrder*$discount),2);
		$subTotal = round($totalOrder*(1-$discount),2);
		$msg .= "<div class=\"d-print-none row justify-content-end priceCol\">
					<div class=\"col-6 offset-6 align-self-end\">
						<table  class=\"table border-0\">
							<tr>
								<th><h4 class=\"font-weight-bold\">Total:</h4></th>
								<th><h4 class=\"font-weight-bold\">$$totalOrder</h4></th>
								<th>pre HST & pre delivery</th>
							</tr>
							<tr>
								<th><h4 class=\"font-weight-bold\">Discount(".$discountPer."%):</h4></th>
								<th><h4 class=\"font-weight-bold\">$".$disAmnt."</h4></th>
								<th></th>
							</tr>
							<tr>
								<th><h4 class=\"font-weight-bold\">Sub Total:</h4></th>
								<th><h4 class=\"font-weight-bold\">$".$subTotal."</h4></th>
								<th>pre HST & pre delivery</th>
							</tr>
						</table>
					</div>
				</div>";
	}
	$msg .="</div>
</body>
</html>";
echo $msg;


?>
</div>

<?php include 'includes/foot.php';?>
<style>
.tab-pane{ background-color: #fff;}
.tab-content{ background-color: #fff;}
</style>
<script>

$(document).ready(function(){
    
	$(".nav-tabs li a").click(function(){
	    $(this).tab('show');
	});
	$(".dropdown-toggle a").click(function(){
	});
	$('#btnGetItems').hide();
	$('#btnGetItems2').hide();
	$('#W2lbl').hide();
	$('#W2').hide();
	$('#D2lbl').hide();
	$('#D2').hide();
	$('#itemTable').hide();
	$('#btnCopyItems').hide();
	$('.datepicker').datepicker({ 
		startDate: new Date()
	});
	$('#CLid').on('focusin', function(){
		$(this).data('val', $(this).val());
	});

	$('[href="' + window.location.hash + '"]').tab('show');
    
	$(".modal").draggable({
		handle: ".modal-header"
    });
	$(".modal-content").resizable({
		minHeight: 630,
		minWidth: 500
    });	
	<?php
	if($_SESSION["userType"]==1){
		echo "$('#divPrintPrice').hide();";
		echo "$('#roomTotal').hide();";
	}
	?>
	loadItems($("a.nav-link.roomtab.active").attr("value"));	
});

var arr = new Array();

$('#allItems').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
	  addItemID = $('#allItems').val();
	});

$( "#itemSizes" ).click(function() {
	  refresh = 1;
	});
$( "#note" ).click(function() {
	  refresh = 1;
	});
$( "#HL" ).click(function() {
	  refresh = 1;
	});
$( "#HR" ).click(function() {
	  refresh = 1;
	});
$( "#FL" ).click(function() {
	  refresh = 1;
	});
$( "#FR" ).click(function() {
	  refresh = 1;
	});
$( "#Qty" ).click(function() {
	  refresh = 1;
	});

$('#fileListing').on('change','.custom-file-input',function(){
//Add the following code if you want the name of the file appear on select
//$(".custom-file-input").on("change", function() {
	//alert('loading filename');
	var fileName = $('#fileToUpload').val().split("\\").pop();
	$('#fileToUpload').siblings(".custom-file-label").addClass("selected").html(fileName);
});
//$('#sendFile').on('click',
$('#fileListing').on('click','#sendFile',
 function () {
	 //alert('working?');
	  $.ajax({
	    // Your server script to process the upload
	    url: 'upload.php',
	    type: 'POST',

	    // Form data
	    data: new FormData($('form')[0]),

	    // Tell jQuery not to process data or worry about content-type
	    // You *must* include these options!
	    cache: false,
	    contentType: false,
	    processData: false,

	    // Custom XMLHttpRequest
	    xhr: function () {
	      var myXhr = $.ajaxSettings.xhr();
	      if (myXhr.upload) {
	        // For handling the progress of the upload
	        myXhr.upload.addEventListener('progress', function (e) {
	          if (e.lengthComputable) {

	            $('progress').attr({
	              value: e.loaded,
	              max: e.total,
	            });
	            if(e.total==e.loaded){
	            	refreshFiles();
	            }
	          }
	        }, false);
	      }
	      refreshFiles(); //refresh listing upon completion.
		  return myXhr;
	      refreshFiles();
	    }
	  });
	});
</script>
<?php include 'includes/nav.php';?>
<?php include_once 'includes/db.php';?>
<?php $roomCount = 1; $dateRequired = ""; //$shipAddress = 0;?>
<style>
table.table-sm td{
padding-top:.3rem;
padding-bottom:.3rem;
height: 1px !important;
}

table{
	text-align: center;
}

option{ white-space: normal; }

.bootstrap-select .filter-option { white-space: normal; }

p{
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
  .print  {display:block!important;}
  body {font-size: 1.3em !important;}
  table td {font-size: .8em !important;overflow: visible !important;}
  table th {font-size: .8em !important;overflow: visible !important;}
}

</style>
<script src="js/MDB/js/popper.min.js"></script>

<script>
var viewOnly = 0;
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
						resetOrderDefault("<?php echo $_GET["OID"] ?>");
						window.location.reload();
        	    	//$("#"+objectID).attr('title',data);
					}
        	    }else{
        	    	$("#"+objectID).css("border-color", "#ff0000");
        	    	//$("#"+objectID).attr('title',data);
        	    	alert(data);
        	    }
	        });
}

function resetOrderDefault(orderId){
	myData = { mode: "resetOrder", oid: orderId};
	$.post("OrderItem.php",
		myData,
			function(data, status, jqXHR){				
			});
}


function saveStyle(col,objectID){
	if(viewOnly>0){
		alert(noChangeMsg);
		return;
	}
	$("#"+objectID).css("border-color", "#ba0000");
	var data = {mode:"setStyle",oid:<?php echo $_GET["OID"]?>, rid:$("a.nav-link.roomtab.active").attr("value"), column:col, id: $("#"+objectID).val()};
	$.post("save.php",data,function(data, status){
		//$(this). attr("href", newUrl);
		
	    if(status == "success"){
	    	$("#"+objectID).css("border-color", "#00b828");
	    	//if(col == "species" || col == "door" || col == "interiorFinish" || col == "frontFinish" || col == "interiorFinish" || col == "interiorFinish" || col == "interiorFinish" || col == "interiorFinish" || col == "interiorFinish" || col == "interiorFinish" || col == "interiorFinish"){
				
		    	loadItems($("a.nav-link.roomtab.active").attr("value"));
				
		    	if(col=="species" || col=="frontFinish"){
		    		location.reload();
		    	}
	    	//}
	    }
	});
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
	myData = { mode: "submitToMobel", oid: "<?php echo $_GET["OID"] ?>"};
	$.post("save.php",
			myData, 
		       function(data, status, jqXHR) {
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
	$("#items").empty();
	//if($("#invalidHeaderMessage").val() <> ""   ){
	//	$("#items").append(  $("#invalidHeaderMessage").val()  );
	//	$("#roomTotal").html("<b>Room Total: $" + "undefined" + "</b></br>pre HST & pre delivery");
	//}else{
	if(typeof rid !== 'undefined'){
    	myData = { mode: "getItems", oid: "<?php echo $_GET["OID"] ?>", rid: rid};
    	    	
    	$.ajax({
	    url: 'OrderItem.php',
	    type: 'POST',
	    data: myData,
	    success: function(data, status, jqXHR) {
    		           $('#items').append(data);
    		           $(".borderless").css('border-top','0px');
    		           $("#roomTotal").html("<b>Room Total: $" + $("#TotalPrice").val() + "</b></br>pre HST & pre delivery");
    		        }
	  	});
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
	
    if($('#editOrderItemPID').val()=="0"){
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
					// remove non-printable and other non-valid JSON chars
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
	$('#editItemID').val(0);
	$('#editOrderItemID').val(0);
	$('#editOrderItemPID').val(0);
	$('#livesearch').empty();
	$('#note').val("");
	$('#W').val("");
	$('#H').val("");
	$('#D').val("");
	$('#W2').val("");
	$('#D2').val("");
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
		$('#deleteItemButton').hide();
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
    	resolve('');
    	//}, 5000); set time out
  });
}

async function setSizes(W,H,D,W2,H2,D2,name,catid) {
  const result = await solvefirst(W,H,D,W2,H2,D2,name,catid);
  $('#editItemSearch').val(result);
	document.getElementById("livesearch").innerHTML=name;
	loadItems($("a.nav-link.roomtab.active").attr("value"));
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
    					/*
    					refresh = 0;
    					//$("allItemsEdit").append("myItem");
    					//$('.selectpicker').selectpicker('refresh');
    					myObj= JSON.parse(data);
    					document.getElementById("livesearch").innerHTML=myObj.name;
    					$('#note').val("");
    					$('#note').val(myObj.note);
    	       			$('#W').val(parseFloat(myObj.w));
    	       			$('#H').val(parseFloat(myObj.h));
    	       			$('#D').val(parseFloat(myObj.d));
    	       			$('#Qty').val(parseFloat(myObj.qty));
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
    		           }
        		        */
        		        
    
    	       			
    					
    	});
    	//alert("testing");
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
	//this function add and remove classes for printing
	var price = document.getElementById("roomTotal");
	var printChk = document.getElementById("printChk");
	var table = document.getElementById("itemListingTable");
	var tdPrice = null;
	if(printChk.checked == true){
		price.classList.remove('d-print-none');
		price.classList.add('d-print-block');
		for (i=0; i<table.rows.length; i++){
			tdPrice = document.getElementById("priceCol"+i);
			tdPrice.classList.remove('d-print-none');
			tdPrice.classList.add('d-print-block');			
		}
	}else {
		price.classList.remove('d-print-block');
		price.classList.add('d-print-none');
		for (i=0; i<table.rows.length; i++){
			tdPrice = document.getElementById("priceCol"+i);
			tdPrice.classList.remove('d-print-block');
			tdPrice.classList.add('d-print-none');
		}
	}
}

</script>






















<div class="navbar d-print-block navbar-expand-sm bg-light navbar-light">
<div class="col-sm-12 col-md-12 col-lg-12 mx-auto pl-1 pr-1 ml-1 mr-1">
<div class="row">
    <?php
    
    
    $userFilter = " and mosUser = ".$_SESSION["userid"];
    if($_SESSION["userType"] == 3){
        $userFilter = "";
    }
    if($_SESSION["userType"] == 2){
        $userFilter = " and account = ".$_SESSION["account"];
    }
    //echo "My user id is:" . $_SESSION["userid"];
    //echo "My account id is:" . $_SESSION["account"];
    //echo "My account type is:" . $_SESSION["userType"];
 
    opendb("select m.*,s.name as 'status' from mosOrder m, state s  where m.state = s.id and m.oid = ".$_GET["OID"] . $userFilter);
    
    if($GLOBALS['$result']->num_rows > 0){
        
        foreach ($GLOBALS['$result'] as $row) {
            $dateRequired = $row['dateRequired'];
            $isWarranty = $row['isWarranty'];
            $isPriority = $row['isPriority'];
            $CLid = $row['CLid'];
            $fromOrder = $row['fromOrder'];
            $state = $row['state'];
            
            echo "<div class=\"col-sm-3 col-md-3 col-lg-3  align-self-center\">";
            
            echo "<label for=\"OID\">For Order Number ".$row['oid'];
            
            
                
            echo "</label>";
            echo "<input type=\"hidden\" value=\"".$row['oid']."\" id=\"OID\"><br/>";
            
            //echo "<div class=\"btn-group \">";
            echo "<button data-toggle=\"modal\" onClick=\"setMinDate();hideSubmit();\" data-target=\"#orderOptions\" class=\"btn btn-primary text-nowrap px-2 py-2 mx-0  mt-0 d-print-none\" data-toggle=\"modal\" data-target=\"#fileModal\" type=\"button\" onClick=\"loadFiles( ".$_GET["OID"].");\">Options<span class=\"ui-icon ui-icon-gear\"></span></button>&nbsp;";
            echo "<button class=\"btn btn-primary text-nowrap px-2 py-2 mx-0 mt-0 d-print-none\" data-toggle=\"modal\" data-target=\"#fileModal\" type=\"button\" onClick=\"loadFiles( ".$_GET["OID"].");\">Files<span class=\"ui-icon ui-icon-disk\"></span></button>&nbsp;";
            
            if($row['status'] == "Quoting"){
                if($row['tagName'] == "Tag name not set"){
                    echo "<button class=\"d-print-none\" type=\"button\" onClick=\"alert('Please set your tag name and refresh to submit your quote.')\">Submit to Mobel</button>";
                    echo "<script>viewOnly = 0;</script>";
                }else{
                    echo "<button type=\"button\" data-toggle=\"modal\" onClick=\"setMinDate();showSubmit();\" data-target=\"#orderOptions\" class=\"btn btn-primary text-nowrap px-2 py-2  mt-0 mx-0 d-print-none\">Submit<span class=\"ui-icon ui-icon-circle-triangle-e\"></span></button>";
                    echo "<script>viewOnly = 0;</script>";
                }
            }else{
                if($_SESSION["userType"] == 3){
                    echo "<script>viewOnly = 1;</script>";
                }else{
                    echo "<script>viewOnly = 1;</script>";
                }
                echo "<button type=\"button\" data-toggle=\"modal\" data-target=\"#orderOptions\"class=\"btn btn-primary text-nowrap px-2 py-2 mx-0  mt-0 \">Order Details</button>";
                //echo $row['status'] . " " . substr($row['dateSubmitted'],0,10);
                
            }
            
            //echo "</div>";
            
            //echo "<div class=\"col-sm-2 col-md-2 col-lg-2\">";

            echo "</div>";
            
            //$shipAddress = $row['shipAddress'];
            echo "<div class=\"col-sm-2 col-md-2 col-lg-2\">";
            echo "<label for=\"state\">Order Status:</label>";
            echo "<textarea readonly class=\"form-control noresize\" rows=\"1\" id=\"state\">";
            echo $row['status'];
            echo "</textarea>";
            echo "</div>";
            
            
            echo "<div class=\"col-sm-3 col-md-3 col-lg-3\">";
            echo "<label for=\"tagName\">Tag Name:</label>";
            echo "<textarea onchange=\"saveOrder('tagName');\" rows=\"1\" class=\"form-control noresize\"  id=\"tagName\">";
            echo $row['tagName'];
            echo "</textarea>";
            echo "</div>";
            
            
            echo "<div class=\"col-sm-2 col-md-2 col-lg-2\">";
            echo "<label for=\"PO\">P.O:</label>";
            echo "<textarea onchange=\"saveOrder('PO');\" rows=\"1\" class=\"form-control rounded-0 noresize\" id=\"PO\">";
            echo $row['po'];
            echo "</textarea>";
            echo "</div>";
        }
    }else{
        //echo "Webpage forbidden";
        ob_start();
        header("Location: viewOrder.php");
        ob_end_flush();
        echo "<script> location.href='viewOrder.php'; </script>";
        exit("Sorry, this page is not available for you.");
    }
    
    opendb("select * from settings");
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row){
            echo "<div class=\"col-sm-1 col-md-2 col-lg-2\">";
            echo "<label for=\"state\">Lead Time:</label>";
            echo "<textarea title=\"Some factors may increase your lead time. We will inform you as soon as possible once your quote is submitted.\" rows=\"1\" readonly class=\"form-control noresize\" id=\"currentLeadtime\">";
            echo substr($row['currentLeadtime'],0,10);
            echo "</textarea>";
            echo "</div>";
        }
    }
    
    if($_SESSION["userType"] == 3 && $state <> "1"){
        echo "</div><div class=\"row\"><div class=\"col-12\">";
        echo "Order Locked: <input type=\"checkbox\"";
        echo "onchange=\"if($('#isLocked').is(':checked')){viewOnly=1;}else{viewOnly=0;};\"  \" checked id=\"isLocked\">";
        echo "</div>";
    }
    ?>
</div>
</div>
</div>


















<ul class="nav nav-tabs bg-dark">
    <?php 
    //$r =0;
	//echo window.location.href();
    opendb("select * from orderRoom where oid = ".$_GET["OID"]." order by name asc");
    $s = " active";
    $i = 0;
    //window.location.replace(window.location.href+'test');
    if($GLOBALS['$result']->num_rows > 0){
        foreach ($GLOBALS['$result'] as $row) {
//            echo "<li value=\"".$row['rid']."\" class=\"nav-item" . $s . "\"><a value=\"".$row['rid']."\" class=\"nav-link roomtab" . $s . "\" onclick=\"loadItems(" .$row['rid']. ");\" href=\"#r". $row['name'] . $i . "\">" . $row['name'] . "(" . $row['rid'] . ")</a></li>";
            echo "<li value=\"".$row['rid']."\" class=\"nav-item" . $s . "\"><a value=\"".$row['rid']."\" class=\"nav-link roomtab" . $s . "\" onclick=\"loadItems(" .$row['rid']. ");\" href=\"#r". str_replace(" ","",$row['name']) . $i . "\"><span class=\"nav-link-active text-muted\"><b id=\"" . $row['name'] ."\">" . $row['name'] ."</b></span></a></li>";
            if($s != ""){
                //$r = $i;
            }
            $s = "";
            $i = $i + 1;
        }
        $roomCount = $i;
    }else{
        echo "<li class=\"nav-item" . $s . "\"><a class=\"nav-link active\"href=\"#NoRooms\">No Rooms</a></li>";
        $roomCount = 0;
    }
    echo "<li class=\"nav-item d-print-none\"><a onclick=\"addRoom(".$i.")\" id=\"addRoom\" class=\"nav-link text-muted\"  >Add</a></li>";
    //href=\"#Add\"
    
    ?>
</ul>







<!-- Tab panes -->
<div id="tabs" class="tab-content mb-3">
    <?php 
    if($i==0){
        ?>
        <div id="NoRooms" class="container tab-pane float-left col-12 active"><br>
        <h3>No Rooms</h3>
        <p>No rooms were found. Please click the "Add" tab to create a new room.</p>
        </div>
        <?php 
    }
    ?>
    <div id="Add" class="container tab-pane float-left col-12 fade"><br>
        <h3>Add Room</h3>
        <p>This creates a new room</p>
    </div>
    <?php
    $invalidHeaderMessage = "";
    if($i!=0){
        //$RID = $r;
        $i=0;
		$sql = "select * from orderRoom where oid = ". $_GET["OID"] ." order by name asc";
		//echo $sql;
        opendb($sql);
        if($GLOBALS['$result']->num_rows > 0){
            foreach ($GLOBALS['$result'] as $row) {
                //$i=$i+1;
                echo "<div id=\"r" . str_replace(" ","",$row['name']) . $i ."\" class=\"container tab-pane float-left col-12 ";
                if($i==0){
                    echo " active";
                }
                echo "\"><br>";
                
                echo "<div class=\"row\">";
                
                echo "<div class=\"col-2\"><button  class=\"btn btn-primary px-2 py-1 text-nowrap ml-0 editbutton d-print-none\" type=\"button\" onClick=\"editRoom(".$row['rid']. ",'" . $row['name'] . "');\"  data-toggle=\"modal\" title=\"Edit room\" data-target=\"#editRoomModal\"><span class=\"ui-icon ui-icon-pencil\"></span></button>";
                
				echo "<b><a  class=\"btn btn-primary px-3 py-1 mr-0 float-right d-print-none\" target=\"_blank\" ";

				if($CLid==3){
					echo "href=\"header/SPANSTYLES.pdf\">Span Catalogue</a></b></div>"; //"<label  for=\"doorstyle\"><a id=\"doorPDF\" href=\"header/SPANSTYLES.pdf\" target=\"_blank\">Style</a></label>";
				}else{
					echo "href=\"uploads/MobelCatalogue.pdf\">Catalogue</a></b></div>"; 
				}				
				
                echo "<div class=\"col text-left\"><button class=\"btn btn-primary px-3 py-1 ml-0 editbutton d-print-none\" data-toggle=\"modal\" data-target=\"#fileModal\" type=\"button\" onClick=\"loadFiles(".$_GET["OID"] . ",$('a.nav-link.roomtab.active').attr('value'));\">Room Files<span class=\"ui-icon ui-icon-disk\"></span></button>";
                                
                if($_SESSION["userType"] == 3){
                    //echo "<button onClick=\"alert('test');\">Testing Only</button>";
                }
                
                echo "</div>";
				
				echo "<div class=\"col-9 text-left\">";
				echo "<p class=\"d-print-none\" id=\"RoomNotePreview". $row['rid'] ."\">";
				
				if($row['note'])
				echo "<b>Room note: </b>" . $row['note'] ;
				
				echo "</p>";
				
				echo "</div>";
				if($row['note']){
					echo "<h5 class=\"print\" id=\"RoomNotePrint". $row['rid'] ."\"><b>Room note: </b>" . $row['note']."</h5>";
					echo "<div class=\"dropdown-divider mb-4\"></div>";
				}
				
				echo "</div>";
				
                //echo "<button class=\"btn btn-primary ml-0 \" data-toggle=\"modal\" data-target=\"#fileModal\" type=\"button\" onClick=\"loadFiles( ".$_GET["OID"].");\">All Files<span class=\"ui-icon ui-icon-disk\"></span></button><br/>";
                echo "<input type=\"hidden\" value=\"" . $row['note'] . "\" id=\"RoomNote". $row['rid'] ."\">";
                 
				?>
				<!--div id="cabLineOp"-->
					<div class="row">
						<div class="col-2 text-right">
							<label for="species">Species</label>
						</div>
						<div class="col-4">
							<select onchange="saveStyle('species','<?php echo "species" . $row['rid'];?>');" id="<?php echo "species" . $row['rid'];?>" class="custom-select">
							<?php
							$sql = "select * from species s where s.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CLid.") order by s.name";
							echo $sql;
							opendb2($sql);
							if($GLOBALS['$result2']->num_rows > 0){
								if(is_null($row['species'])){
									echo "<option ". "selected" ." value=\"\">" . "Choose a Species" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['species']){
										echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}else{
										echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
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
							if($CLid==3){
								echo "<label for=\"interiorFinish\">Backing</label>";
							}else{
								echo "<label for=\"interiorFinish\">Interior Finish</label>";
							}
							?>							
						</div>
						<div class="col-4">
							<select onchange="saveStyle('interiorFinish','<?php echo "interiorFinish" . $row['rid'];?>');" id="<?php echo "interiorFinish" . $row['rid'];?>" class="custom-select">						
							<?php
							opendb2("select * from interiorFinish inf where inf.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CLid.") order by inf.name");
							if($GLOBALS['$result2']->num_rows > 0){
								if(is_null($row['interiorFinish'])){
									echo "<option ". "selected" ." value=\"\">" . "Choose an Interior Finish" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['interiorFinish']){
										echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}else{
										echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}else{
								$invalidHeaderMessage = $invalidHeaderMessage .  "<br>No interior finish selected";
							}
							?>
							</select>
						</div>
					</div>

					<div class="row">
					   <!-- 
						<div class="col-2 text-right">
						<label for="edge">Edge</label>
						</div>
						
						<div class="col-4">
						<select onchange="saveStyle('edge','<?php echo "edge" . $row['rid'];?>');" id="<?php echo "edge" . $row['rid'];?>" class="custom-select">
						
						<?php
						opendb2("select * from edge order by name");
						if($GLOBALS['$result2']->num_rows > 0){
							if(is_null($row['edge'])){
								echo "<option ". "selected" ." value=\"\">" . "Choose an Edge" . "</option>";
							}
							foreach ($GLOBALS['$result2'] as $row2) {
								if($row2['id']==$row['edge']){
									echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
								}else{
									echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
								}
							}
						}
						?>
						</select>
						</div>
						 -->

						<div class="col-2 text-right">
							<?php
							if($CLid==3){
								echo "<label  for=\"doorstyle\"><a id=\"doorPDF\" href=\"header/SPANSTYLES.pdf\" target=\"_blank\">Style</a></label>";
							}else{
								echo "<label  for=\"doorstyle\"><a id=\"doorPDF\" href=\"header/DOORSTYLES.pdf\" target=\"_blank\">Door Style</a></label>";
							}
							?>							
						</div>						
						<div class="col-4">
							<select onchange="$('#doorPDF').attr('href','header/'+$('option:selected', this).attr('doorPDFTag')); saveStyle('door','<?php echo "doorstyle" . $row['rid'];?>');" id="<?php echo "doorstyle" . $row['rid'];?>" class="custom-select">						
							<?php
							$sql = "select d.* from door d, doorSpecies ds where d.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CLid.") and d.id = ds.did and ds.sid = '" . $row['species'] . "'";
							echo $sql;
							opendb2($sql);
							if($GLOBALS['$result2']->num_rows > 0){ 
								$match = 0;
								if(is_null($row['door'])){
									echo "<option doorPDFTag= \"DOORSTYLES.pdf\"". "selected" ." value=\"\">" . "Choose a Door" . "</option>";
								}                        
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['door']){
										echo "<option doorPDFTag= \"" . $row2['PDF'] . "\"". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										$match = 1;
									}else{
										echo "<option doorPDFTag= \"" . $row2['PDF'] . "\"   value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
								if($match == 0 && !is_null($row['door'])){
									echo "<option value=\"" . "Please choose a new door style" . "\">" . "Please choose a new door style" . "</option>";
									$invalidHeaderMessage = $invalidHeaderMessage .  "<br>No door selected";
								}
							}
							?>
							</select>
						</div>
						
						<div class="col-2 text-right">
							<?php
							if($CLid==3){
								echo "<label  for=\"frontFinish\">Color</label>";
							}else{
								echo "<label for=\"frontFinish\">Finish</label>";
							}
							?>							
						</div>
						<div class="col-4">
							<select onchange="saveStyle('frontFinish','<?php echo "frontFinish" . $row['rid'];?>');" id="<?php echo "frontFinish" . $row['rid'];?>" class="custom-select">						
							<?php
							opendb2("select * from frontFinish where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") and finishType in (select ftid from finishTypeMaterial where mid in (select mid from species where id in (select species from orderRoom where rid = " . $row['rid'] . "))) order by name");
							if($GLOBALS['$result2']->num_rows > 0){
								if(is_null($row['frontFinish'])){
									echo "<option ". "selected" ." value=\"\">" . "Choose a Finish" . "</option>";
								}
								$match = 0;
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['frontFinish']){
										echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										$match = 1;
									}else{
										echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
								if($match == 0 && !is_null($row['frontFinish'])){
									echo "<option ". "selected" ." value=\"" . "Please choose a new finish" . "\">" . "" . "</option>";
									$invalidHeaderMessage = $invalidHeaderMessage . "<br>No finish selected";
								}
							}
							?>
							</select>
						</div>
					</div>
                	
					<div class="row">
						<div class="col-2 text-right">
							<label for="drawerBox">Drawer Box</label>
						</div>						
						<div class="col-4">
							<select onchange="saveStyle('drawerBox','<?php echo "drawerBox" . $row['rid'];?>');" id="<?php echo "drawerBox" . $row['rid'];?>" class="custom-select">						
							<?php
							opendb2("select * from drawerBox where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name");
							if($GLOBALS['$result2']->num_rows > 0){
								if(is_null($row['drawerBox'])){
									echo "<option ". "selected" ." value=\"\">" . "Choose a Drawer Box" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['drawerBox']){
										echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}else{
										echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
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
							opendb2("select * from glaze where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name");
							if($GLOBALS['$result2']->num_rows > 0){
								if(is_null($row['glaze'])){
									echo "<option ". "selected" ." value=\"\">" . "Choose a Glaze" . "</option>";
									$invalidHeaderMessage = $invalidHeaderMessage . "<br>No glaze selected";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['glaze']){
										echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}else{
										echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
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
							<select onchange="saveStyle('smallDrawerFront','<?php echo "smallDrawerFront" . $row['rid'];?>');" id="<?php echo "smallDrawerFront" . $row['rid'];?>" class="custom-select">
							
							<?php
							opendb2("select * from smallDrawerFront where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name");
							if($GLOBALS['$result2']->num_rows > 0){
								if(is_null($row['smallDrawerFront'])){
									echo "<option ". "selected" ." value=\"\">" . "Choose a Small Drawer Front" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['smallDrawerFront']){
										echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}else{
										echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
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
							$sql ="select * from sheen where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") and id in (
								select sid from finishTypeSheen where ftid in (
								select finishType from frontFinish where id in (
								select frontFinish from orderRoom where rid = " . $row['rid'] . "))) order by name";
							echo $sql;
							opendb2($sql);
							$match = 0; //if match is 0, no sheens work. If 1, a matching sheen was found. If 2, sheens were found, but not what was selected.
							if($GLOBALS['$result2']->num_rows == 1){						
								foreach ($GLOBALS['$result2'] as $row2) {
										echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										//If only one option then save Sheen
										$sql = "update orderRoom set sheen = ".$row2['id']." WHERE rid = ". $row['rid']; 
										opendb($sql);
								}
							}else if($GLOBALS['$result2']->num_rows > 1){
								echo "<option disabled=\"disabled\" ". "selected" ." value=\"\">" . "Choose a Sheen" . "</option>";
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['sheen']){
										echo "<option selected value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										$match = 1;
									}else{
										echo "<option value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";								
									}							
								}
							}else{
								echo "<option disabled=\"disabled\" ". "selected" ." value=\"\"> Please choose another Finish</option>";
							}
							/*if($GLOBALS['$result2']->num_rows > 0){
								$match = 2;
								if(is_null($row['sheen'])){
									echo "<option disabled=\"disabled\" ". "selected" ." value=\"\">" . "Choose a Sheen" . "</option>";
									$invalidHeaderMessage = $invalidHeaderMessage . "<br>No sheen selected";
									$match = 3;
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['sheen']){
										echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
										$match = 1;
									}else{
										echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}
							if($match==0){
								echo "<option ". "selected" ." value=\"\">" . "" . "</option>";
							}
							if($match==2){
								echo "<option disabled=\"disabled\"  ". "selected" ." value=\"null\">" . "Please choose your new sheen" . "</option>";
								$invalidHeaderMessage = $invalidHeaderMessage . "<br>No sheen selected";
							}*/
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
							opendb2("select * from largeDrawerFront where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name");
							if($GLOBALS['$result2']->num_rows > 0){
								if(is_null($row['largeDrawerFront'])){
									echo "<option ". "selected" ." value=\"\">" . "Choose a Large Drawer Front" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['largeDrawerFront']){
										echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}else{
										echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
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
							opendb2("select * from hinge order by name");
							if($GLOBALS['$result2']->num_rows > 0){
								if(is_null($row['hinge'])){
									echo "<option ". "selected" ." value=\"\">" . "Choose a Hinge" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['hinge']){
										echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}else{
										echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}
							?>
							</select>
						</div>

						<div class="col-2 text-right">
							<label for="drawerGlides">Drawer Glides</label>
						</div>
						<div class="col-4">
							<select onchange="saveStyle('drawerGlides','<?php echo "drawerGlides" . $row['rid'];?>');" id="<?php echo "drawerGlides" . $row['rid'];?>" class="custom-select">
							
							<?php
							opendb2("select * from drawerGlides where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name");
							if($GLOBALS['$result2']->num_rows > 0){
								if(is_null($row['drawerGlides'])){
									echo "<option ". "selected" ." value=\"\">" . "Choose a Drawer Glide" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['drawerGlides']){
										echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}else{
										echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
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
							opendb2("select * from finishedEnd where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name");
							if($GLOBALS['$result2']->num_rows > 0){
								if(is_null($row['finishedEnd'])){
									echo "<option ". "selected" ." value=\"\">" . "Choose a Finished End" . "</option>";
								}
								foreach ($GLOBALS['$result2'] as $row2) {
									if($row2['id']==$row['finishedEnd']){
										echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}else{
										echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
									}
								}
							}
							?>
							</select>
						</div>						
					</div>

					<div class="row">
					</div>
            	            	
					<div class="row">
						<!-- 
						<div class="col-2 text-right">
						<label for="exteriorFinish">Exterior Finish</label>
						</div>
						
						<div class="col-4">
						<select onchange="saveStyle('exteriorFinish','<?php echo "exteriorFinish" . $row['rid'];?>');" id="<?php echo "exteriorFinish" . $row['rid'];?>" class="custom-select">
						
						<?php
						opendb2("select * from exteriorFinish order by name");
						if($GLOBALS['$result2']->num_rows > 0){
							if(is_null($row['exteriorFinish'])){
								echo "<option ". "selected" ." value=\"\">" . "Choose an Exterior Finish" . "</option>";
							}
							foreach ($GLOBALS['$result2'] as $row2) {
								if($row2['id']==$row['exteriorFinish']){
									echo "<option ". "selected" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
								}else{
									echo "<option ". "" ." value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
								}
							}
						}
						?>
						</select>
						</div>
						 -->
					</div>             	
                <!--/div-->
				<?php
				
				echo "</div>";
				$i++;
            }
        }
    }
    
    ?>
</div>













<div  class="container tab-pane float-left col-12">

    <hr/>
    
    <!-- Trigger the modal with a button -->
    <?php 
    if ($roomCount >0){
    ?>
    <div class="d-flex justify-content-between"><!-- onClick=allItems('allItems','allItems');  --> <button type="button"  onClick=cleanEdit("add"); class="btn btn-primary pt-2 pb-2 d-print-none" data-toggle="modal" data-target="#editItemModal">Add Item<span class="ui-icon ui-icon-plus"></span></button>
	<span class="ml-auto d-print-none" id="roomTotal"></span>
	</div>
	
	<?php
	if($_SESSION["userType"]>1){
	?>
	<div class="d-print-none">
		<input class="d-flex float-right" type="checkbox" id="printChk" name="printChk" onclick="printPrice()"></input>
		<small class="d-flex float-right" for="printChk">Print price&nbsp</small><br>
	</div>
	<?php
	}
	?>
	
	
    <?php 
    }
    ?>
    <!-- Modal add item-->
    





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
                    <p>Changes save as you finish making them.</p>Starts with:<input type="checkbox" id="startsWith" checked></input>
                    <div class="col-xs-2">
                    Find Item:
                    <input class="col-xs-2" autocomplete="off" type="text"  id="editItemSearch" onkeyup="showResult(this.value)">
                    </div>
					<div class="col-xs-2" id="livesearch" ></div>

					<input type="hidden" id="editOrderItemPID" name="editOrderItemPID" value="0" >
                    <input type="hidden" id="editItemID" name="editItemID" value="0" >
                    <input type="hidden" id="editOrderItemID" name="editOrderItemID" value="0" >
                    <!-- 
                    
                    <select id="allItemsEdit" data-live-search="true" class="selectpicker col-12">
                    </select> -->
                    <br/><br/>
                    <div class="row">
                        <div class="col-auto text-left">
                            <span class="form-inline">
                            
                            <label for="Qty">Quantity:</label>
                            <textarea onchange="saveEditedItem('Qty','qty');" rows="1" cols="8" class="form-control" id="Qty"></textarea>
                            </span>
                            <br/>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div id="itemSizes"  class="col-auto text-left">
                            <span class="form-inline">
                            
                            <label for="W" data-toggle="tooltip" data-placement="top" title="Width">Width:</label>							
                            <textarea data-toggle="tooltip" data-placement="top" title="Width" onchange="saveEditedItem('W','W');"  rows="1" cols="7" class="form-control" id="W"></textarea>&nbsp;
                            <!--div id="W2div" class=""-->
							<label id="W2lbl" for="W2" data-toggle="tooltip" data-placement="top" title="Right Width">Width Right:</label>
                            <textarea data-toggle="tooltip" data-placement="top" title="Width" onchange="saveEditedItem('W2','W2');"  rows="1" cols="7" class="form-control" id="W2"></textarea>&nbsp;
                            <!--/div-->
                            <label for="H">Height:</label>
                            <textarea onchange="saveEditedItem('H','H');" rows="1" cols="7" class="form-control" id="H"></textarea>&nbsp;
                            
							<label for="D" data-toggle="tooltip" data-placement="top" title="Depth">Depth:</label>
                            <textarea onchange="saveEditedItem('D','D');"  rows="1" cols="7" class="form-control" id="D"></textarea>
							<!--div id="D2div" class="col-auto text-left"-->
							<label id="D2lbl" for="D2" data-toggle="tooltip" data-placement="top" title="Right Width">Depth Right:</label>
                            <textarea data-toggle="tooltip" data-placement="top" title="Width" onchange="saveEditedItem('D2','D2');"  rows="1" cols="7" class="form-control" id="D2"></textarea>
                            <!--/div-->
							</span>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-6 text-left">
                        	<label for="Hinged">Hinge:</label> Left <input onchange="saveEditedItem('HL','hingeLeft');" type="checkbox" id="HL" checked></input><input onchange="saveEditedItem('HR','hingeRight');" type="checkbox" id="HR"></input>&nbsp;Right
                        </div>
                        <div class="col-6 text-left">
                        	Finished: Left <input type="checkbox" value="" id="FL" onchange="saveEditedItem('FL','finishLeft');" ></input><input type="checkbox" id="FR" onchange="saveEditedItem('FR','finishRight');" ></input>&nbsp;Right
                        </div>
                    </div>
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
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Room Tools</h4>
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
          	
            <div class="col-12 " >
				<div class="row">
					<div class="col-3">
						This is a:
						<?php 
						
						echo "<select onchange=\"saveOrder('isPriority');fixDate();showOrderOptions('isPriority');\" class=\"form-control \"  id=\"isPriority\"><option ";
						if($isPriority==0){
							echo "selected";
						}
						echo " value=\"0\">standard order.</option><option ";
						
						if($isPriority>0){
							echo "selected";
						}
						echo " value=\"1\">service order.</option></select>";
						
						?>					
					</div>
					<div class="col-2">
						Required Date: 
						<?php 
						echo "<input title=\"Some factors may increase your lead time. We will inform you as soon as possible once your quote is submitted.\" type=\"text\" maxlength=\"10\" data-provide=\"datepicker\" data-date-format=\"yyyy-mm-dd\" onchange=\"saveOrder('dateRequired');\" class=\"form-control date\"  value=\"". substr($dateRequired,0,10) ."\" id=\"dateRequired\">";
						?>
					</div>
					<div class="col-1 text-center service">
						Warranty: 
						<?php 
						echo "<input type=\"checkbox\"";
						if($isWarranty>0){
							echo " checked ";
						}
						echo "onchange=\"saveOrder('isWarranty');\" class=\"form-control  \"  id=\"isWarranty\">";
						?>
					</div>
					<div class="col-3 service">
						Original Order Number: 
						<?php 
						echo "<input type=\"text\" maxlength=\"30\"";
						echo "value=\"".$fromOrder."\"";
						echo "onchange=\"saveOrder('fromOrder');\" class=\"form-control  \"  id=\"fromOrder\">";
						?>
					</div>					
					<div class="col-3">               
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
									if($CLid==$row["id"]) echo "selected "; 
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
			</div>
            <br/>
            
            
            <div class="col-sm-12 col-md-12 col-lg-12">
    	        <div class="row">
    	        	<div class="col-sm-12 col-md-12 col-lg-12">
          	          <label for="shipAddress">Ship To:</label>
                    </div>
                </div>
                <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
     		    <select onchange="saveOrder('shipAddress','shipAddress');" id="shipAddress" class="custom-select">
     		       
 		       <?php 
 		       
 		           
                //echo "<p>This is a test. Does this fit wellThis is a test. Does this fit wellThis is a test. Does this fit wellThis is a test. Does this fit wellThis is a test. Does this fit wellThis is a test. Does this fit wellThis is a test. Does this fit well</p>";
               opendb("SELECT a.*,m.shipAddress,m.note FROM mosOrder m, accountAddress a, addressType t WHERE a.aType = t.id and t.name = 'Shipping' and m.account = a.aid and m.oid = ". $_GET["OID"] ." order by contactName asc");
                if($GLOBALS['$result']->num_rows > 0){
                    foreach ($GLOBALS['$result'] as $row) {
                        $OrderNote = $row['note'];
                        if(is_null($row['shipAddress'])||$row['shipAddress']==""){
                            echo "<option ". "selected" ." value=\"" . $row['id'] . "\">" . "Please choose a ship location" . "</option>";
                        }

                		foreach ($GLOBALS['$result'] as $row) {
                		    if($row['shipAddress']==$row[id]){
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
                		    echo "<option ". "selected" ." value=\"" . $row['id'] . "\">" . "Pick up at Mobel" . "</option>";
                		}else{
                		    echo "<option value=\"" . $row['id'] . "\">" . "Pick up at Mobel" . "</option>";
                		}
                    }
                }
                    ?>
                </select>
                </div>
            </div>
            </div>
            <br/>
            <div class="col-sm-6 col-md-6 col-lg-6 date">
                <label for="OrderNote">Order Notes:</label>
                <?php 
                echo "<textarea onchange=\"saveOrder('OrderNote');\" class=\"form-control\"  id=\"OrderNote\">".$OrderNote."</textarea>";
                ?>
            </div>
            
          </div>
          <div class="modal-footer">
          <div id="submitText" class="col-sm-8 col-md-8 col-lg-8">
            	<p>Please check your shipping method is correct and indicate the date your order is required.</p>
	            <p>Your order will be electronically submitted to orders@mobel.ca and will be processed by our staff.<br/> You will get a copy of the report and will hear from us soon.</p>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-4">
          	<button id="submitButton" type="button" onClick=submitToMobel(); class="btn btn-default" data-dismiss="modal">Submit to Mobel</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    
      </div>
    </div>






    <div id="items">
    </div>
</div>




<input type="hidden" id="invalidHeaderMessage" value="<?php echo ""; //$invalidHeaderMessage;?>">






<?php include 'includes/foot.php';?>
<style>
.tab-pane{ background-color: #fff;}
.tab-content{ background-color: #fff;}
</style>
<script>

$(document).ready(function(){
	  $(".nav-tabs a").click(function(){
	    $(this).tab('show');
	  });
	  $('#W2lbl').hide();
	  $('#W2').hide();
	  $('#D2lbl').hide();
	  $('#D2').hide();
	});

$(document).ready(function(){
	$('[href="' + window.location.hash + '"]').tab('show');
	loadItems($("a.nav-link.roomtab.active").attr("value"));
	$(".modal").draggable({
		handle: ".modal-header"
    });
	$(".modal-content").resizable({
		minHeight: 630,
		minWidth: 500
    });
});





var arr = new Array();




$('#allItems').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
	  addItemID = $('#allItems').val();
	});

/*
 
 $( "#W" ).click(function() {
	  refresh = 1;
	});
$( "#H" ).click(function() {
	  refresh = 1;
	});
$( "#D" ).click(function() {
	  refresh = 1;
	});
*/	
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
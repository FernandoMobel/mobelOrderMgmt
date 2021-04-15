<?php 
include '../includes/nav.php';
include_once '../includes/db.php';

//getting status from DB
$result = opendb("select id,name from state where id > 1");
while($row = $result->fetch_assoc()) {
	$states[] = $row;
}
$states = json_encode($states);

//getting account types from DB
$result = opendb("select id,accountType from accountType");
while($row = $result->fetch_assoc()) {
	$accType[] = $row;
}
$accTypes = json_encode($accType);
?>
<style>
/* Hide arrows for number inputs - start */
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
/* Hide arrows for number inputs - end */
</style>
<script>
<?php 
//set status
echo "const states = ".$states.";";
//set account types
echo "const accTypes = ".$accTypes.";";?>

function updateYear(yr){
    table.destroy();
	loadOrders(yr);
}

/* Used to update state only using same functionality from mobl only screen */
function saveOrder(objectID,OID){
	$("#"+objectID+OID).css("border-color", "#fa0000");
	myData = { mode: "updateOrder", id: objectID, value: $("#"+objectID+OID).val(), oid: OID};
	$.post("../OrderItem.php",
			myData, 
				function(data, status, jqXHR) {
					//console.log($("#"+objectID+OID).val());
					if(data == "success"){
						if(objectID=='state'){
							if($("#"+objectID+OID).val()>7){//State updated Invoiced or beyond
								$('#dateInvoiced'+OID).prop('disabled',false);
								if($("#"+objectID+OID).val()==8){
									//today date
									var today = new Date();
									var dd = String(today.getDate()).padStart(2, '0');
									var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
									var yyyy = today.getFullYear();
									today = yyyy+'-'+mm+'-'+dd;
									$('#dateInvoiced'+OID).val(today);
								}
							}else{
								//State is not invoiced so date should be deleted
								updateInvoiceDate(objectID,0,OID);
								$('#dateInvoiced'+OID).prop('disabled',true);
								$('#dateInvoiced'+OID).val('');
							}
						}
						$("#"+objectID+OID).css("border-color", "#08fa00");							
					}
				});
	//}
}

function updateInvoiceDate(obj,date,oid){
	$("#"+obj).css("border-color", "#fa0000");		
	myData = { mode: "updateInvoicedDate", date: date, oid: oid};
	$.post("EmployeeMenuSettings.php",
		myData, 
		function(data, status, jqXHR) {
			$("#"+obj).css("border-color", "#08fa00");		
			//console.log(jqXHR['responseText']);
		});
}

function calculateInv(oid){
	let buildCabinet=Number($('#'+oid+"-buildCab").val());
	let buildCounter=Number($('#'+oid+"-buildCou").val());
	let buildInstall=Number($('#'+oid+"-buildIns").val());
	let buildDeliver=Number($('#'+oid+"-buildDel").val());
	let hst=0;
	let amt=0;
	hst = (buildCabinet+buildCounter+buildInstall+buildDeliver)*.13;
	amt = buildCabinet+buildCounter+buildInstall+buildDeliver+hst;
	$('#'+oid+"-hst").val(hst.toFixed(2));
	$('#'+oid+"-amt").val(amt.toFixed(2));
}

function updateAcc(obj,col,val,oid){
	$("#"+obj).css("border-color", "#fa0000");
	myData = { mode: "updateAccounting", col: col, value: val, oid: oid};
	$.post("EmployeeMenuSettings.php",
		myData, 
		function(data, status, jqXHR) {
			console.log(jqXHR['responseText']);
			$("#"+obj).css("border-color", "#08fa00");
		});
}

function updateAccAmt(obj,col,val,oid){
	$("#"+obj).css("border-color", "#fa0000");

	myData = { mode: "updateAccountingAmt", col: col, value: val, oid: oid, hst: $('#'+oid+"-hst").val(), total: $('#'+oid+"-amt").val()};
	console.log(myData);
	$.post("EmployeeMenuSettings.php",
		myData, 
		function(data, status, jqXHR) {
			console.log(jqXHR['responseText']);
			$("#"+obj).css("border-color", "#08fa00");
		});
}

function updateOrder(obj,col,val,oid){
	$("#"+obj).css("border-color", "#fa0000");
	myData = { mode: "updateOrder", col: col, value: val, oid: oid};
	$.post("EmployeeMenuSettings.php",
		myData, 
		function(data, status, jqXHR) {
			console.log(jqXHR['responseText']);
			if(col=="dateInvoiced" && val==8){
				$('#dateInvoiced').prop('disabled',false);				
			}else{
				$('#dateInvoiced').prop('disabled',true);
			}
			$("#"+obj).css("border-color", "#08fa00");
		});
}

function loadOrders(yr){
	var dataSet;
	var rowClass = "";
	var order;
	var table;
	//var state;
	var html;
	myData = { mode: "getOrdersAccounting",year:yr}; 
	$.ajax({
	    url: 'EmployeeMenuSettings.php',
	    type: 'POST',
	    data: myData,
	    success: function(data, status, jqXHR) {						
    		        dataSet =  JSON.parse(jqXHR['responseText']);
					//console.log(jqXHR['responseText']);
					table = $('#mainTable').DataTable({
						//"sDom": '<"top"i>rt<"bottom"flp><"clear">',
						order: [[ 0, 'asc' ], [ 1, 'asc' ]],
						ordering: false,
						//colReorder: false,
						//lengthMenu: [500,1000],
						paging: false,
						//stateSave: true,
						data: dataSet,
						columns : [
							{
								className: "font-weight-normal align-middle mth",
								data : "mth"
							},
							{
								className: "font-weight-normal align-middle day",
								data : "day"
							},
							{
								className: "font-weight-normal align-middle yr",
								data : "yr"
							},
							{
                                className: "font-weight-bold align-middle oid",
								data : "orderID",
								render: function(data, type) {
									order = data;
									return data;									
								}
							},
							{
								className: "font-weight-normal align-middle cst",
								data : "busName"
							},
							{
								className: "font-weight-normal align-middle cnt",
								data : "contract"
							},
							{	
                                className: "font-weight-normal align-middle sls",							
								data : "sales"
							},
							{								
                                className: "font-weight-normal align-middle dd",
								data : "dateShipped"
							},
							{	
								//Retail & Contract Amount						
                                className: "font-weight-normal align-middle rca",
								data : "retailContAmt",
								render: function(data, type) {
									if(data){
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input id=\"retailContAmt\" value=\""+data+"\" type=\"number\" class=\"form-control\" onchange=\"updateAcc(this.id,this.value,"+order+");\"></div>";									
									}else{
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input id=\"retailContAmt\" type=\"number\" class=\"form-control\" onchange=\"updateAcc(this.id,this.value,"+order+");\"></div>";									
									}
								}
							},
							{		
								//Builders and dealers					
                                className: "font-weight-normal align-middle bd",
								data : "accountType",
								render: function(data, type,row) {	
									var html = "<select id=\"accType"+order+"\" class=\"custom-select\" onchange=\"updateOrder(this.id,'accountType',this.value,"+order+");\">";
									if(data){
										accTypes.forEach(function(obj) {
											if(data == obj.id){
												html += "<option selected value=\""+obj.id+"\">"+obj.accountType+"</option>";
											}else{
												html += "<option value=\""+obj.id+"\">"+obj.accountType+"</option>";
											}
										});
									}else{
										html += "<option value=\"\">Select type</option>";
										accTypes.forEach(function(obj) {
											html += "<option value=\""+obj.id+"\">"+obj.accountType+"</option>";
										});
									}							
									html += "</select>";
									return html;
								}
							},
							{
								//Status
								className: "font-weight-normal align-middle",
								data : "state",
								render: function(data, type,row) {
									html='<select id=\"state'+order+'\" class="custom-select" onchange="saveOrder(\'state\','+order+');">';
									states.forEach(function(obj){	
										html += '<option ';
										if(obj.id == row['state'])
											html += 'selected ';
										html += 'value="'+obj.id+'">'+obj.name+'</option>';
									});
									html += '</select>';
									return html;										
								}			
							},
							{			
								//Invoice Date			                                
								className: "font-weight-normal align-middle invdt",
								data : "dateInvoiced",
								render: function(data, type,row) {
									if(row['state']>7){
										return "<input id=\"dateInvoiced"+order+"\" type=\"text\" maxlength=\"10\" data-provide=\"datepicker\" data-date-format=\"yyyy-mm-dd\" class=\"form-control datepicker\" value=\""+data+"\" onchange=\"updateInvoiceDate(this.id,this.value,"+order+");\">";
									}else{
										return "<input disabled id=\"dateInvoiced"+order+"\" type=\"text\" maxlength=\"10\" data-provide=\"datepicker\" data-date-format=\"yyyy-mm-dd\" class=\"form-control datepicker\"  value=\"\" onchange=\"updateInvoiceDate(this.id,this.value,"+order+");\">";
									}
								}
							},
							{		
								//Invoice Number						
                                className: "font-weight-normal align-middle invid",
								data : "invId",
								render: function(data, type) {
									if(data){
										return "<div class=\"input-group input-group-sm\"><input id=\"invId"+order+"\" value=\""+data+"\" type=\"text\" class=\"form-control\" onchange=\"updateAcc(this.id,'invId',this.value,"+order+");\"></div>";
									}else{
										return "<div class=\"input-group input-group-sm\"><input id=\"invId"+order+"\" type=\"text\" class=\"form-control\" placeholder=\"Inv ID\" onchange=\"updateAcc(this.id,'invId',this.value,"+order+");\"></div>";										
									}
								}
							},
							{		
								//$$ Build Cabinet						
                                className: "font-weight-normal align-middle bca",
								data : "cabinetAmt",
								render: function(data, type) {
									if(data){
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input id=\""+order+"-buildCab\" value=\""+data+"\" type=\"number\" class=\"form-control\" onkeyup=\"calculateInv("+order+");\" onchange=\"updateAccAmt(this.id,'cabinetAmt',this.value,"+order+");\"></div>";									
									}else{
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input id=\""+order+"-buildCab\" type=\"number\" class=\"form-control\" onkeyup=\"calculateInv("+order+");\" onchange=\"updateAccAmt(this.id,'cabinetAmt',this.value,"+order+");\"></div>";									
									}
								}
							},
							{	
								//$$Build Counter							
                                className: "font-weight-normal align-middle bcta",
								data : "counterAmt",
								render: function(data, type) {
									if(data){
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input id=\""+order+"-buildCou\" value=\""+data+"\" type=\"number\" class=\"form-control\" onkeyup=\"calculateInv("+order+");\" onchange=\"updateAccAmt(this.id,'counterAmt',this.value,"+order+");\"></div>";									
									}else{
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input id=\""+order+"-buildCou\" type=\"number\" class=\"form-control\" onkeyup=\"calculateInv("+order+");\" onchange=\"updateAccAmt(this.id,'counterAmt',this.value,"+order+");\"></div>";									
									}
								}
							},
							{			
								//$$ Install Amount					
                                className: "font-weight-normal align-middle bia",
								data : "installAmt",
								render: function(data, type) {
									if(data){
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input value=\""+data+"\" id=\""+order+"-buildIns\" type=\"number\" class=\"form-control\" onkeyup=\"calculateInv("+order+");\" onchange=\"updateAccAmt(this.id,'installAmt',this.value,"+order+");\"></div>";									
									}else{
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input id=\""+order+"-buildIns\" type=\"number\" class=\"form-control\" onkeyup=\"calculateInv("+order+");\" onchange=\"updateAccAmt(this.id,'installAmt',this.value,"+order+");\"></div>";									
									}
								}
							},
							{						
								//$$ Delivery Amount		
                                className: "font-weight-normal align-middle bda",
								data : "deliveryAmt",
								render: function(data, type) {
									if(data){
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input value=\""+data+"\" id=\""+order+"-buildDel\" type=\"number\" class=\"form-control\" onkeyup=\"calculateInv("+order+");\" onchange=\"updateAccAmt(this.id,'deliveryAmt',this.value,"+order+");\"></div>";									
									}else{
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input id=\""+order+"-buildDel\" type=\"number\" class=\"form-control\" onkeyup=\"calculateInv("+order+");\" onchange=\"updateAccAmt(this.id,'deliveryAmt',this.value,"+order+");\"></div>";									
									}
								}
							},
							{			
								//HST Amount					
                                className: "font-weight-normal align-middle hst",
								data : "hstAmt",
								render: function(data, type) {
									if(data){
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input value=\""+data+"\" id=\""+order+"-hst\" value=\""+data+"\" type=\"number\" class=\"form-control\" readonly></div>";									
									}else{
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input id=\""+order+"-hst\" type=\"number\" class=\"form-control\" readonly></div>";									
									}
								}
							},
							{		
								//Total Amount						
                                className: "font-weight-normal align-middle totamt",
								data : "totalAmt",
								render: function(data, type) {
									if(data){
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input value=\""+data+"\" id=\""+order+"-amt\" value=\""+data+"\" type=\"number\" class=\"form-control\" readonly></div>";									
									}else{
										return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input id=\""+order+"-amt\" type=\"number\" class=\"form-control\" readonly></div>";									
									}
								}
							}
						],
						rowCallback: function( row, data ) {						    
							//Set color code for every row
							if(data['CLid']==3)
								$(row).addClass('table-primary');
							if(data['CLid']==2)
								$(row).addClass('table-info');
							if(data['isPriority']==1)
								$(row).addClass('table-warning');
							if(data['isWarranty']==1)
								$(row).addClass('table-danger');
						}						
					});					
    	},
		complete: function () {
			loadFilters();
     	}
	});
}

function loadFilters(){
	cols = new Array();
	if(localStorage.getItem('cst')=='false'){
		$('.cst').hide();
		$( table.column( 2 ).header() ).addClass( 'never' );
	}else{
		cols.push('cst');
	}
	if(localStorage.getItem('cnt')=='false'){
		$('.cnt').hide();
	}else{
		cols.push('cnt');
	}
	if(localStorage.getItem('sls')=='false'){
		$('.sls').hide();
	}else{
		cols.push('sls');
	}
	if(localStorage.getItem('dd')=='false'){
		$('.dd').hide();
	}else{
		cols.push('dd');
	}
	if(localStorage.getItem('rca')=='false'){
		$('.rca').hide();
	}else{
		cols.push('rca');
	}
	if(localStorage.getItem('bd')=='false'){
		$('.bd').hide();
	}else{
		cols.push('bd');
	}
	if(localStorage.getItem('invdt')=='false'){
		$('.invdt').hide();
	}else{
		cols.push('invdt');
	}
	if(localStorage.getItem('invid')=='false'){
		$('.invid').hide();
	}else{
		cols.push('invid');
	}
	if(localStorage.getItem('bca')=='false'){
		$('.bca').hide();
	}else{
		cols.push('bca');
	}
	if(localStorage.getItem('bcta')=='false'){
		$('.bcta').hide();
	}else{
		cols.push('bcta');
	}
	if(localStorage.getItem('bia')=='false'){
		$('.bia').hide();
	}else{
		cols.push('bia');
	}
	if(localStorage.getItem('bda')=='false'){
		$('.bda').hide();
	}else{
		cols.push('bda');
	}
	//set visible cols 
	if(cols.length>0){
		$("#columns").val(cols);
		$('#columns').multiselect('refresh');
	}
}
</script>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="row">                
				<div class="col-md-2 col-sm-12">
					<select id="columns" multiple="multiple">
						<!--option selected value="mth" id="chkmth">MTH</option>
						<option selected value="day" id="chkday">DAY</option>
						<option selected value="yr" id="chkyr">YR</option-->
						<option selected value="cst" id="chkcst">CUSTOMER</option>
						<option selected value="cnt" id="chkcnt">CONTRACT</option>
						<option selected value="sls" id="chksls">SALES PERSON</option>
						<option selected value="dd" id="chkdd">DELIVERY DATE</option>
						<option selected value="rca" id="chkrca">RETAIL & CONTRACT AMOUNT</option>
						<option selected value="bd" id="chkbd">BUILDERS & DEALERS</option>
						<!-- Invoice -->
						<option selected value="invdt" id="chkinvdt">INVOICE DATE</option>
						<option selected value="invid" id="chkinvid">INVOICE</option>
						<option selected value="bca" id="chkbca">BUILD CABINET</option>
						<option selected value="bcta" id="chkbcta">BUILD COUNTER</option>
						<option selected value="bia" id="chkbia">BUILD INSTALL</option>
						<option selected value="bda" id="chkbda">BUILD DELIVER</option>
						<!--option selected value="hst" id="chkhst">HST</option>
						<option selected value="totamt" id="chktotamt">AMT</option-->					
					</select>
				</div>
				<div class="col-md-2 col-sm-8">
                <select id="selYear" class="custom-select" onchange="updateYear(this.value);">
                <?php
                $result = opendb("select distinct year(dateSubmitted) year from mosOrder where state > 1 and dateSubmitted is not null");
                while($row = $result->fetch_assoc()){
                    echo "<option value=\"".$row['year']."\" ";
                    if(date("Y")==$row['year'])
                        echo "selected";
                    echo ">".$row['year']."</option>";
                }            
                ?>
                </select>
                </div>
                <div class="col-md-1 col-sm-4">
                    <div class="input-group-prepend">
                        <a id="popOrderType" tabindex="0" role="button" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-container="body" data-html="true">
                            <label class="input-group-text input-group-sm bg-primary text-white" for="stateFilter">Order Types</label>
                        </a>
                    </div>
                </div>
				<!-- Button reset order-->
				<!--div class="col"> 
					<button id="reset" type="button" class="btn btn-primary">Reset</button>
				</div-->
                <div class="col-md-7 col-sm-12">
                    <h5 class="font-weight-normal text-center"> JOBS - BUILDERS / DEALERS / RETAIL</h5>  
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="mainTable" class="table table-sm table-bordered text-center">
                    <thead class="thead-light">
						<tr>
                            <th class="font-weight-bold mth">MTH</th>
                            <th class="font-weight-bold day">DAY</th>
                            <th class="font-weight-bold yr">YR</th>
                            <th class="font-weight-bold">OID</th>
                            <th class="font-weight-bold cst">CUSTOMER</th>
                            <th class="font-weight-bold cnt">CONTRACT</th>
                            <th class="font-weight-bold sls">SALES PERSON</th>
                            <th class="font-weight-bold dd">DELIVERY DATE</th>
                            <th class="font-weight-bold rca">RETAIL & CONTRACT AMOUNT</th>
                            <th class="font-weight-bold bd">BUILDERS & DEALERS</th>
							<th class="font-weight-bold">STATUS</th>
							<!-- Invoice -->
							<th class="font-weight-bold invdt">INVOICE DATE</th>
							<th class="font-weight-bold invid">INVOICE</th>
							<th class="font-weight-bold bca">BUILD CABINET</th>
							<th class="font-weight-bold bcta">BUILD COUNTER</th>
							<th class="font-weight-bold bia">BUILD INSTALL</th>
							<th class="font-weight-bold bda">BUILD DELIVER</th>
							<th class="font-weight-bold hst">HST</th>
							<th class="font-weight-bold totamt">AMT</th>
                        </tr>
                    </thead>
                    <tbody id="tbAccounting"></tbody>
                    <tfoot class="thead-light">
						<tr>
							<th class="font-weight-bold mth">MTH</th>
							<th class="font-weight-bold day">DAY</th>
							<th class="font-weight-bold yr">YR</th>
							<th class="font-weight-bold">OID</th>
							<th class="font-weight-bold cst">CUSTOMER</th>
							<th class="font-weight-bold cnt">CONTRACT</th>
							<th class="font-weight-bold sls">SALES PERSON</th>
							<th class="font-weight-bold dd">DELIVERY DATE</th>
							<th class="font-weight-bold rca">RETAIL & CONTRACT AMOUNT</th>
							<th class="font-weight-bold bd">BUILDERS & DEALERS</th>
							<th class="font-weight-bold">STATUS</th>
							<!-- Invoice -->
							<th class="font-weight-bold invdt">INVOICE DATE</th>
							<th class="font-weight-bold invid">INVOICE</th>
							<th class="font-weight-bold bca">BUILD CABINET</th>
							<th class="font-weight-bold bcta">BUILD COUNTER</th>
							<th class="font-weight-bold bia">BUILD INSTALL</th>
							<th class="font-weight-bold bda">BUILD DELIVER</th>
							<th class="font-weight-bold hst">HST</th>
							<th class="font-weight-bold totamt">AMT</th>
						</tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="popovercontent" hidden>
	<table class="table table-sm table-bordered text-center m-auto">
        <tr><td class="p-1"><small><b>Standard</b></small></td></tr>
		<tr><td class="table-warning p-1"><small><b>Service</b></small></td></tr>
		<tr><td class="table-danger p-1"><small><b>Service w/warranty</b></small></td></tr>		
		<tr><td class="table-primary p-1"><small><b>Span Medical</b></small></td></tr>
		<!--tr><td class="table-info p-1"><small><b>Builders</b></small></td></tr-->
	</table>
</div> 
<?php include '../includes/foot.php';?>
<script>
$(document).ready(function () {
    loadOrders($('#selYear').val());
	
	$('#columns').multiselect({
		allSelectedText: 'All columns are visible',
		buttonWidth: '100%',
		dropRight: true,
		onChange: function(option, checked) {
			$("."+$(option).val()).toggle('display');
			localStorage.setItem($(option).val(), checked);//store cookie for column filter
			//console.log(localStorage);			
		}
	});

	$('.datepicker').datepicker({
	});

    /*Tooltips and Popovers use a built-in sanitizer to sanitize options which accept HTML */
	$.fn.popover.Constructor.Default.whiteList.table = [];
    $.fn.popover.Constructor.Default.whiteList.tr = [];
    $.fn.popover.Constructor.Default.whiteList.td = [];
    $.fn.popover.Constructor.Default.whiteList.th = [];
    $.fn.popover.Constructor.Default.whiteList.div = [];
    $.fn.popover.Constructor.Default.whiteList.tbody = [];
    $.fn.popover.Constructor.Default.whiteList.thead = [];
	$("#popOrderType").popover({
    	html: true, 
		content: function() {
        	return $('#popovercontent').html();
        }
	});
});
</script>
<?php 
include '../includes/nav.php';
include_once '../includes/db.php';

//getting status from DB
$result = opendb("select id,name from state where id > 1");
while($row = $result->fetch_assoc()) {
	$states[] = $row;
}
$states = json_encode($states);
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
//set status to display
echo "const states = ".$states.";"?>

function updateYear(yr){
    table.destroy();
	loadOrders(yr);
}

function saveOrder(objectID,OID){
	$("#"+objectID+OID).css("border-color", "#ba0000");
	/*if($("#"+objectID+OID).val()==5){
		getOrderRooms(OID);
		getRequiredDate(OID);
		$('#detailsModal').modal('toggle');
	}else{*/
	myData = { mode: "updateOrder", id: objectID, value: $("#"+objectID+OID).val(), oid: OID};
	$.post("../OrderItem.php",
			myData, 
				function(data, status, jqXHR) {
					if(data == "success"){
						$("#"+objectID+OID).css("border-color", "#00b828");							
					}
				});
	//}
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
						order: [[1,2,3, "asc" ]],
						colReorder: true,
						lengthMenu: [50, 100, 250, 500],
						stateSave: true,
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
								data : "oid",
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
                                className: "font-weight-normal align-middle rca",
								data : "amount",
								render: function(data, type) {
									return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input type=\"number\" class=\"form-control\"></div>";									
								}
							},
							{								
                                className: "font-weight-normal align-middle bd",
								//data : "state"
								defaultContent: ""
							},
							{			
								//Invoice Date - Status (when is not invoiced show status otherwise display date)					
                                className: "font-weight-normal align-middle invdt",
								data : "state",
								//defaultContent: "<i>Not set</i>",
								render: function(data, type,row) {
									if(row['state']==8){
										html = row['dateInvoiced'];
											return html;																					
									}else{
										html='<select  class="custom-select" onchange="saveOrder(\'state\','+order+');">';
										states.forEach(function(obj){	
											html += '<option ';
											if(obj.id == row['state'])
												html += 'selected ';
											html += 'value="'+obj.id+'">'+obj.name+'</option>';
										});
										html += '</select>';
										return html;										
									}			
								}
							},
							{		
								//Invoice Number						
                                className: "font-weight-normal align-middle invid",
								data : "amount",
								render: function(data, type) {
									return "<div class=\"input-group input-group-sm\"><input type=\"text\" class=\"form-control\" placeholder=\"Inv ID\"></div>";									
								}
							},
							{								
                                className: "font-weight-normal align-middle bca",
								data : "amount",
								render: function(data, type) {
									return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input type=\"number\" class=\"form-control\"></div>";									
								}
							},
							{								
                                className: "font-weight-normal align-middle bcta",
								data : "amount",
								render: function(data, type) {
									return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input type=\"text\" class=\"form-control\"></div>";									
								}
							},
							{								
                                className: "font-weight-normal align-middle bia",
								data : "amount",
								render: function(data, type) {
									return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input type=\"text\" class=\"form-control\"></div>";									
								}
							},
							{								
                                className: "font-weight-normal align-middle bda",
								data : "amount",
								render: function(data, type) {
									return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input type=\"text\" class=\"form-control\"></div>";									
								}
							},
							{								
                                className: "font-weight-normal align-middle hst",
								data : "amount",
								render: function(data, type) {
									return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input type=\"text\" class=\"form-control\"></div>";									
								}
							},
							{								
                                className: "font-weight-normal align-middle totamt",
								data : "amount",
								render: function(data, type) {
									return "<div class=\"input-group input-group-sm\"><div class=\"input-group-prepend\"><span class=\"input-group-text\">$</span></div><input type=\"text\" class=\"form-control\"></div>";									
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
    		    }
	});
}
</script>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="row">                
				<div class="col-md-2 col-sm-12">
					<select id="columns" multiple="multiple">
						<option selected value="mth" id="chkmth">MTH</option>
						<option selected value="day" id="chkday">DAY</option>
						<option selected value="yr" id="chkyr">YR</option>
						<option selected value="oid" id="chkoid">OID</option>
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
						<option selected value="hst" id="chkhst">HST</option>
						<option selected value="totamt" id="chktotamt">AMT</option>					
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
                            <th class="font-weight-bold oid">OID</th>
                            <th class="font-weight-bold cst">CUSTOMER</th>
                            <th class="font-weight-bold cnt">CONTRACT</th>
                            <th class="font-weight-bold sls">SALES PERSON</th>
                            <th class="font-weight-bold dd">DELIVERY DATE</th>
                            <th class="font-weight-bold rca">RETAIL & CONTRACT AMOUNT</th>
                            <th class="font-weight-bold bd">BUILDERS & DEALERS</th>
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
							<th class="font-weight-bold oid">OID</th>
							<th class="font-weight-bold cst">CUSTOMER</th>
							<th class="font-weight-bold cnt">CONTRACT</th>
							<th class="font-weight-bold sls">SALES PERSON</th>
							<th class="font-weight-bold dd">DELIVERY DATE</th>
							<th class="font-weight-bold rca">RETAIL & CONTRACT AMOUNT</th>
							<th class="font-weight-bold bd">BUILDERS & DEALERS</th>
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
			//localStorage.setItem($(option).val(), checked);//store cookie for column filter
			//console.log(localStorage);
		}
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
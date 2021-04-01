<?php 
include '../includes/nav.php';
include_once '../includes/db.php';
?>
<script>
function updateYear(yr){
    table.destroy();
	loadOrders(yr);
}

function loadOrders(yr){
	var dataSet;
	var rowClass = "";
	var order;
	var state;
	myData = { mode: "getOrdersAccounting",year:yr}; 
	$.ajax({
	    url: 'EmployeeMenuSettings.php',
	    type: 'POST',
	    data: myData,
	    success: function(data, status, jqXHR) {						
    		        dataSet =  JSON.parse(jqXHR['responseText']);
                    console.log(jqXHR['responseText']);
					table = $('#mainTable').DataTable({
						//order: [[ 7, "asc" ]],
						lengthMenu: [50, 100, 250],
						//stateSave: true,
						//retrieve: true,
						data: dataSet,
						columns : [
							{
								className: "font-weight-normal",
								data : "mth"
							},
							{
								className: "font-weight-normal",
								data : "day"
							},
							{
								className: "font-weight-normal",
								data : "yr"
							},
							{
                                className: "font-weight-normal",
								data : "oid"
							},
							{
								className: "font-weight-normal",
								data : "busName"
							},
							{
								className: "font-weight-normal",
								data : "tagName"
							},
							{	
                                className: "font-weight-normal",							
								data : "sales"
							},
							{								
                                className: "font-weight-normal",
								data : "dateShipped"
							},
							{								
                                className: "font-weight-normal",
								data : "amount"
							},
							{		
                                className: "font-weight-normal",						
								data : "sales"
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
                            <label class="input-group-text bg-primary text-white" for="stateFilter">Order Types</label>
                        </a>
                    </div>
                </div>
                <div class="col-md-9 col-sm-12">
                    <h5 class="font-weight-normal text-center"> JOBS - BUILDERS / DEALERS / RETAIL</h5>  
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="mainTable" class="table table-sm table-bordered text-center">
                    <thead class="thead-light">
                        <tr>
                            <th class="font-weight-bold">MTH</th>
                            <th class="font-weight-bold">DAY</th>
                            <th class="font-weight-bold">YR</th>
                            <th class="font-weight-bold">OID</th>
                            <th class="font-weight-bold">CUSTOMER</th>
                            <th class="font-weight-bold">CONTRACT</th>
                            <th class="font-weight-bold">SALES PERSON</th>
                            <th class="font-weight-bold">DELIVERY DATE</th>
                            <th class="font-weight-bold">RETAIL & CONTRACT AMOUNT</th>
                            <th class="font-weight-bold">BUILDERS & DEALERS</th>
                        </tr>
                    </thead>
                    <tbody id="tbAccounting"></tbody>
                    <tfoot class="thead-light">
                        <th class="font-weight-bold">MTH</th>
                        <th class="font-weight-bold">DAY</th>
                        <th class="font-weight-bold">YR</th>
                        <th class="font-weight-bold">OID</th>
                        <th class="font-weight-bold">CUSTOMER</th>
                        <th class="font-weight-bold">CONTRACT</th>
                        <th class="font-weight-bold">SALES PERSON</th>
                        <th class="font-weight-bold">DELIVERY DATE</th>
                        <th class="font-weight-bold">RETAIL & CONTRACT AMOUNT</th>
                        <th class="font-weight-bold">BUILDERS & DEALERS</th>
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
<script>
var currentDate = getMondayCurWeek();
<?php
$sql = "select * from departments where id=".(int)$_SESSION['firstName'];
$result = opendb($sql);
$row = $result->fetch_assoc();
if(strlen($_SESSION["firstName"])==1 && $_SESSION["account"]==2){
	echo "const department =".(int)$_SESSION['firstName'].";";
	echo "const dateType =".$row['dateType'].";";
	echo "setTimeout(function(){
		   window.location.reload(1);
		}, 60000);";
}else{
	/*echo "const department =1;";
	echo "const dateType =3;";*/
	echo "var department =1;";
	echo "var dateType =3;";
}
?>

function loadSchWeek(date, dept){	
	/*if(date.length==1){
		date = 0;//localStorage.setItem('date',getMondayCurWeek());
	}*/
	//What schedule are using
	switch (dept){
		case '3'://Shipping
			department = 1;		
			dateType = 3;	
		break;
		case '2'://Wrapping
			department = 2;
			dateType = 2;
		break;
		case '1'://Sanding
			department = 8;
			dateType = 1;
		break;
	  default:
		// code block
	}

	console.log('Date:'+localStorage.getItem('date')+' dateType:'+dateType+' Department:'+department+' Filter:'+localStorage.getItem('onlyReady')+' see complete: '+localStorage.getItem('displayComp'));
	myData = { mode: "loadSchWeek", date: date/*localStorage.getItem('date')*/, dateType: dateType, mydid:department, filter:localStorage.getItem('onlyReady'), displayComp:localStorage.getItem('displayComp')};
	
	$.ajax({
			url: 'EmployeeMenuSettings.php',
			type: 'POST',
			data: myData})
	  .done(function(data, status, jqXHR) {
			//console.log(jqXHR['responseText']);
			$('#scheduleWeek').empty();
			$('#scheduleWeek').append(data);
			if(date==0){
				$('#fromDate').text('All Jobs');
				$('#fromDatePrint').text('All Jobs');
			}else{
				$('#fromDate').text('Week of '+localStorage.getItem('date'));
				$('#fromDatePrint').text('Week of '+localStorage.getItem('date'));
			}
			loadFilters();
	  })
	  .fail(function(xhr, status, error) {
		  //Ajax request failed.
		  var errorMessage = xhr.status + ': ' + xhr.statusText
		  console.log('Error - ' + errorMessage);
		  console.log(xhr);
		  console.log(error);
	})
}

function getNewWeek(nextWeek){
	var newDate2 = new Date(currentDate);
	if(nextWeek){
		newDate2.setDate(newDate2.getDate() + 8);
	}else{
		newDate2.setDate(newDate2.getDate() - 6);
	}
	currentDate = formatDate(newDate2);
	localStorage.setItem('date',currentDate);
	loadSchWeek(currentDate);
}

function formatDate(noformat){//output YYYY-MM-DD
	var d = new Date(noformat);
	var month = d.getMonth()+1;
	var day = d.getDate();

	var output = d.getFullYear() + '-' +
		(month<10 ? '0' : '') + month + '-' +
		(day<10 ? '0' : '') + day;
	return output;
}

function getMondayCurWeek(){
	d = new Date();
	var day = d.getDay(), diff = d.getDate() - day + (day == 0 ? -6:1); // adjust when day is sunday

	d = new Date(d.setDate(diff));
	month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;
	currentDate = [year, month, day].join('-');
    return currentDate;
}

function completeRoom(rid){
	$action = 'new';
	if(!$('#chkDone'+rid).prop('checked')){
		$action = 'old';
	}	
	if((!$('#chkDone'+rid).prop('checked') && getWithExpiry('completed'+rid)==true) || $('#chkDone'+rid).prop('checked')){
		myData = { mode: "completeRoom", rid: rid, mydid:department, action:$action};
		$.post("EmployeeMenuSettings.php",
			myData, 
			   function(data, status, jqXHR) {	
					setWithExpiry("completed"+rid, $('#chkDone'+rid).prop('checked'), 50000);//50 seconds to change your mind
					if(department==1)//this means Shipping department has finished therefore order status will be updated as Shipped
						updateOrderStatus(rid, $action);
				});	
	}else{
		$('#chkDone'+rid).prop('disabled',true);
		$('#chkDone'+rid).prop('checked',true);
	}
}

function updateOrderStatus(rid, action){
	console.log('state will be updated');
	myData = { mode:"updateOrderStatus", rid:rid, dept:department, action:action};
		$.post("EmployeeMenuSettings.php",
			myData, 
			   function(data, status, jqXHR) {	
					console.log(jqXHR['responseText']);
				});	
}

function loadFilters(){
	cols = new Array();
	if(localStorage.getItem('rmnm')=='false'){
		$('.rmnm').hide();
	}else{
		cols.push('rmnm');
	}
	if(localStorage.getItem('box')=='false'){
		$('.box').hide();
	}else{
		cols.push('box');
	}
	if(localStorage.getItem('frt')=='false'){
		$('.frt').hide();
	}else{
		cols.push('frt');
	}
	if(localStorage.getItem('itm')=='false'){
		$('.itm').hide();
	}else{
		cols.push('itm');
	}
	if(localStorage.getItem('mat')=='false'){
		$('.mat').hide();
	}else{
		cols.push('mat');
	}
	if(localStorage.getItem('drs')=='false'){
		$('.drs').hide();
	}else{
		cols.push('drs');
	}
	if(localStorage.getItem('fns')=='false'){
		$('.fns').hide();
	}else{
		cols.push('fns');
	}
	if(localStorage.getItem('sht')=='false'){
		$('.sht').hide();
	}else{
		cols.push('sht');
	}
	//set visible cols 
	if(cols.length>0){
		$("#columns").val(cols);
	}
	
	//this is for checkbox for only display completed jobs
	if(localStorage.getItem('onlyReady')=='true'){
		$('#displayCmpt').prop('checked',true);
	}else{
		$('#displayCmpt').prop('checked',false);
	}
}

function onlyCompleted(){
	if($(displayCmpt).prop('checked')){
		localStorage.setItem('onlyReady',true);		
	}else{
		localStorage.setItem('onlyReady',false);
	}
	loadSchWeek(localStorage.getItem('date'));
}

function hideMyCompleted(){
	if($(hideMyCmpt).prop('checked')){
		localStorage.setItem('displayComp',true);		
	}else{
		localStorage.setItem('displayComp',false);
	}
	loadSchWeek(localStorage.getItem('date'));
}

function setWithExpiry(key, value, ttl) {
	const now = new Date()

	// item is an object which contains the original value
	// as well as the time when it's supposed to expire
	const item = {
		value: value,
		expiry: now.getTime() + ttl,
	}
	localStorage.setItem(key, JSON.stringify(item))
}

function getWithExpiry(key) {
	const itemStr = localStorage.getItem(key)
	// if the item doesn't exist, return null
	if (!itemStr) {
		return null
	}
	const item = JSON.parse(itemStr)
	const now = new Date()
	// compare the expiry time of the item with the current time
	if (now.getTime() > item.expiry) {
		// If the item is expired, delete the item from storage
		// and return null
		localStorage.removeItem(key)
		return null
	}
	return item.value
}
</script> 
<style>
  @media (max-width: 1025px) {
	.hidden-mobile {
	  display: none;
	}
  }
  
  td{padding:5px !important}
</style>

<div class="card card-signin my-3 mx-0">
	<?php
	$superUser = array(11,30,32);
	if(in_array($_SESSION["userid"],$superUser)){
	?>
	<div class="card-header d-print-none">
		<div class="d-flex flex-row">
			<div class="p-2">
				<div class="custom-control custom-radio">
					<input onchange="loadSchWeek(0,this.value)" type="radio" class="custom-control-input" id="chk3" value="3" name="defaultExampleRadios" checked>
					<label class="custom-control-label" for="chk3">COMPLETITION</label>
				</div>
			</div>
			<div class="p-2">
				<div class="custom-control custom-radio">
					<input onchange="loadSchWeek(0,this.value)" type="radio" class="custom-control-input" id="chk2" value="2" name="defaultExampleRadios">
					<label class="custom-control-label" for="chk2">WRAPPING</label>
				</div>
			</div>
			<div class="p-2">
				<div class="custom-control custom-radio">
					<input onchange="loadSchWeek(0,this.value)" type="radio" class="custom-control-input" id="chk1" value="1" name="defaultExampleRadios">
					<label class="custom-control-label" for="chk1">SANDING</label>
				</div>
			</div>
			<!--div class="p-2">
				<div class="custom-control custom-radio">
					<input onchange="loadSchWeek(0,this.value)" type="radio" class="custom-control-input" id="chk0" value="1" name="defaultExampleRadios">
					<label class="custom-control-label" for="chk0">CNC</label>
				</div>
			</div-->
		</div>
	</div>
	<?php
	}
	?>	
	<div class="card-body px-3 pt-1">
		<div class="row text-center d-print-block py-3" hidden>
			<h5 id="fromDatePrint"></h5>
		</div>
		<div class="row d-print-none">
			<div class="d-flex justify-content-start col-sm-6 col-lg-4 mx-auto">
				<div class="p-2">
					<select id="columns" multiple="multiple">
						<option selected value="rmnm" id="chkRN">ROOM NAME</option>
						<option selected value="box" id="chkB">BOXES</option>
						<option selected value="frt" id="chkFT">FRONTS</option>
						<option selected value="itm" id="chkIT">ITEMS</option>
						<option selected value="mat" id="chkMT">MATERIAL</option>
						<option selected value="drs" id="chkDS">DOOR STYLE</option>
						<option selected value="fns" id="chkFS">FINISH</option>
						<option selected value="sht" id="chkST">SHIPPING TO</option>
					</select>
				</div>
			</div>
			<div class="d-flex justify-content-end col-sm-6 col-lg-2">
				<div class="col custom-control custom-checkbox p-2 mx-auto">
					<input onchange="onlyCompleted();" type="checkbox" class="custom-control-input" id="displayCmpt">
					<label class="custom-control-label mx-auto" for="displayCmpt">Hide jobs not ready on previous station</label>
				</div>
			</div>
				<!--div class="custom-control custom-checkbox p-2">
					<input onchange="hideMyCompleted();" type="checkbox" class="custom-control-input" id="hideMyCmpt">
					<label class="custom-control-label" for="hideMyCmpt">See completed</label>
				</div-->
			<!--/div>
			<div class="container-fluid">
				<div class="d-flex justify-content-between"-->
			<div class="d-flex col-sm-12 col-lg-6 mx-auto">
					<div class="d-flex justify-content-start align-middle col-sm-2 mr-auto p-2">
						<a class="btn-sm" onclick="getNewWeek(false);">
							<svg width="3em" height="3em" viewBox="0 0 16 16" class="bi bi-arrow-left-square-fill btn-primary" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							  <path fill-rule="evenodd" d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm9.5 8.5a.5.5 0 0 0 0-1H5.707l2.147-2.146a.5.5 0 1 0-.708-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 0 0 .708-.708L5.707 8.5H11.5z"/>
							</svg><small class="hidden-mobile">Previous week</small>
						</a>
					</div>
					<div class="d-flex justify-content-center align-middle col-sm-8 p-2">
						<h5 id="fromDate" onclick="loadSchWeek(0);" data-toggle="tooltip" data-placement="top" title="Click to see all jobs"></h5>
					</div>
					<div class="d-flex justify-content-end align-middle col-sm-2 p-2">
						<a class="btn-sm" onclick="getNewWeek(true);"><small class="hidden-mobile">Next week</small>
							<svg width="3em" height="3em" viewBox="0 0 16 16" class="bi bi-arrow-right-square-fill btn-primary" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							  <path fill-rule="evenodd" d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm2.5 8.5a.5.5 0 0 1 0-1h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5z"/>
							</svg>
						</a>
					</div>
			</div>
		</div>
		<div class="table-responsive">
			<table id="tbSchedule" class="table text-center align-middle table-bordered" style="width:100%" >
				<thead class="thead-light">
					<tr>
						<th>DUE DATE</th>
						<th>OID</th>
						<th class="rmnm">ROOM NAME</th>
						<th class="box">BOXES</th>			
						<th class="frt">FRONTS</th>
						<th class="itm">ITEMS</th>
						<th class="mat">MATERIAL</th>
						<th class="drs">DOOR STYLE</th>
						<th class="fns">FINISH</th>
						<th class="sht">SHIPPING TO</th>
						<th>DONE</th>
					</tr>
				</thead>
				<tbody id="scheduleWeek">
				</tbody>
			</table>
		</div>
	</div>
</div>


<?php if(strlen($_SESSION["firstName"])==1 && $_SESSION["account"]==2) include '../includes/foot.php';?>  
<script>
$(document).ready(function () {
	if(!localStorage.getItem('date')){
		localStorage.setItem('date',getMondayCurWeek());
	}	
	loadSchWeek(localStorage.getItem('date'));
	loadFilters();
	
	$('#columns').multiselect({
		allSelectedText: 'All columns are visible',
		buttonWidth: '100%',
		dropRight: true,
		onChange: function(option, checked) {
			$("."+$(option).val()).toggle('display');
			localStorage.setItem($(option).val(), checked);//store cookie for column filter
			console.log(localStorage);
		}
	});
});
</script>
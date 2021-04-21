<style type="text/css">
	#divAbsolute{
		position: absolute;
  		top: 50%;
  		left: 50%;
  		transform: translate(-50%, -50%);
  	}
</style>
<script type="text/javascript">
function btnReset(){
	$('#tbItems').empty();
	$('#divSelection').hide();
	$('#selItems option:selected').each(function() {
        $(this).prop('selected', false);
    })
	$('#selItems').multiselect('refresh');
}

function selectCat(app,val){
	var postMode;
	switch(app){
		case 'cv':
			postMode = 'getCVcodes';
			$('#cvCode').val('');
			if(val){
				$('#cvCode').prop('disabled',false);
				$.ajax({
			          	url: "EmployeeMenuSettings.php",
			          	dataType: "json",
			          	type: 'POST',
			          	data: {mode: postMode, cat:val},
			          	success: function( data ) {
			          		//console.log(data);
			          		$( "#cvCode" ).autocomplete({
						    	source: data
						    });
			          	}
		        	});
			}else{
				$('#cvCode').prop('disabled',true);
			}
		break;
		case 'mos':
			postMode = 'getItemsCat';
			$('#selItems').empty();
			$('#selItems').multiselect('destroy');

			if(val){
				myData = { mode: postMode, cat: val};
				$.post("EmployeeMenuSettings.php",
						myData, 
					       function(data, status, jqXHR) {
			            		var items = JSON.parse(jqXHR['responseText']);            		
			            		items.forEach(item => {
			            			$('#selItems').append(new Option(item["name"], item["id"]));            			
								});
								$('#selItems').multiselect({
									templates: {
										resetButton: '<div onClick="btnReset();" class="multiselect-reset text-center p-2"><a class="btn btn-sm btn-block btn-outline-primary">Reset Selection</a></div>'
									},
						        	allSelectedText: 'All items selected',
						        	includeSelectAllOption: true,
						            enableFiltering: true,
						            enableCaseInsensitiveFiltering: true,
						            includeResetOption: true,
						            includeResetDivider: true,
						            maxHeight: 600,
						            buttonWidth: '350px',
						            selectAllNumber: true,
						            onChange: function(options, checked) {
						            	if($('#selItems :selected').length>0){
						            		$('#divSelection').show();
						            		if(checked){
							            		myData = { mode: "getItemRow", item: $(options).attr('value')};
												$.post("EmployeeMenuSettings.php",
													myData, 
												       	function(data, status, jqXHR) {
													       	var item = JSON.parse(jqXHR['responseText']);
													       	var html = '<tr id="'+item[0].id+'"><td class="font-weight-bold">'+item[0].name+'</td><td>'+item[0].description+'</td><td>'+item[0].W+'</td><td>'+item[0].H+'</td><td>'+item[0].D+'</td><td>'+item[0].cvCode+'</td><td>'+item[0].cvLCode+'</td><td>'+item[0].cvRCode+'</td></tr>';
													       	$('#tbItems').append(html);
														});
											}else{
												$('#'+$(options).attr('value')).remove();
						            		}
						            	}else{
						            		$('#divSelection').hide();
						            		$('#tbItems').empty();
						            	}
						            },
						            onSelectAll: function() {
						            	var items = [];
						            	$('#selItems :selected').each(function(){
											//remove existing rows
											$('#'+$(this).val()).remove();
											//add item to array
											items.push($(this).val());
										});
										//myData = { mode: "getMultipleItemsRows", items: items};
										myData = { mode: "reloadTable", items: items};
										$.post("EmployeeMenuSettings.php",
											myData, 
										       	function(data, status, jqXHR) {
										       		var items = JSON.parse(jqXHR['responseText']);
											       	items.forEach(item=>{
											       		var html = '<tr id="'+item.id+'"><td class="font-weight-bold">'+item.name+'</td><td>'+item.description+'</td><td>'+item.W+'</td><td>'+item.H+'</td><td>'+item.D+'</td><td>'+item.cvCode+'</td><td>'+item.cvLCode+'</td><td>'+item.cvRCode+'</td></tr>';
											       		$('#tbItems').append(html);	
											       	})											       	
												});
										$('#divSelection').show();								
						            }
						        });
						        
						        $('#selItems').multiselect('enable');
							});
			}else{
		    	$('#selItems').multiselect('disable');
		    }
		break;
	}
	
	
}

function unselectAll(){
	if($('#selItems :selected').length==0){
		$('#tbItems').empty();
		$('#divSelection').hide();
	}
}

function linkCVtoMOS(){
	if($('#selCVcat :selected').length==0){
		alert('Please select a category in Cabinet Vision section');		
		return;
	}
	if(!$('#cvCode').val()){
		alert('Please add an Item Code in Cabinet Vision section');
		return;
	}
	if($('#tbItems tr').length==0){
		alert('Please select one or more cabinets in MOS section');
		return;
	}
	//After validation items will be linked
	var items =$('#tbItems tr').map(function(){
		return parseInt(this.id);
	}).get();
	myData = { mode: "linkItems", cv: $('#cvCode').val().trim(), items: items, door: $('#selDoor').val(), cvCat: $('#selCVcat').val()};
	$.post("EmployeeMenuSettings.php",
	myData, 
   	function(data, status, jqXHR) {
   		//Reload table
   		var arr = $('#selItems :selected').map(function(){
			  return this.value
		}).get();
   		$('#tbItems').empty();
   		myData = { mode: "reloadTable", items:arr};
		$.post("EmployeeMenuSettings.php",
			myData, 
		       	function(data, status, jqXHR) {
			       	var item = JSON.parse(jqXHR['responseText']);				       	
			       	item.forEach(it => {
			       		$('#tbItems').append('<tr id="'+it['id']+'"><td class="font-weight-bold">'+it['name']+'</td><td>'+it['description']+'</td><td>'+it['W']+'</td><td>'+it['H']+'</td><td>'+it['D']+'</td><td>'+it['cvCode']+'</td><td>'+it['cvLCode']+'</td><td>'+it['cvRCode']+'</td></tr>');
			       	});
				});
	});
}
</script>
<div class="container-fluid px-0">
	<div class="card card-signin py-1">
		<div class="card-body p-1">
			<ul class="list-group list-group-flush">
				<li class="list-group-item">
					<div class="row">
						<div class="col-md-2">
							<h5 class="card-title">Cabinet Vision</h5>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text">Category</span>
									</div>
									<select id="selCVcat" onChange="selectCat('cv',this.value);" class="custom-select">
									<?php 
									$flag = true;
									$sql = "select distinct category, catDescription from cvItem";
									$result = opendb($sql);							
									while ($row = $result->fetch_assoc()){
										if($flag){
											echo "<option value=\"\">Please choose an option</option>";
											echo "<option value=\"".htmlspecialchars($row['category'])."\">".htmlspecialchars($row['category'])." - ".htmlspecialchars($row['catDescription'])."</option>";
											$flag =false;
										}else{
											echo "<option value=\"".htmlspecialchars($row['category'])."\">".htmlspecialchars($row['category'])." - ".htmlspecialchars($row['catDescription'])."</option>";
										}
									}
									?>	
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text">Item</span>
									</div>
									<input type="text" id="cvCode" style="text-transform:uppercase">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text">Door</span>
									</div>
									<select id="selDoor" class="custom-select">
										<option value="B">Default (Double Door/No Door)</option>
										<option value="L">Left Door</option>
										<option value="R">Right Door</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</li>
				<li class="list-group-item">
					<div class="row">
						<div class="col-md-2">
							<h5 class="card-title">MOS</h5>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<div class="input-group mx-auto">
								  	<div class="input-group-prepend">
								    	<label class="input-group-text" for="selCate">Category</label>
								  	</div>
								  	<select id="selCate" onChange="selectCat('mos',this.value);" class="custom-select">
									<?php 
									$flag = true;
									$sql = "select distinct(description) cat from item where CLGroup = 4 order by cat";
									$result = opendb($sql);							
									while ($row = $result->fetch_assoc()){
										if($flag){
											echo "<option value=\"\">Please choose an option</option>";
											echo "<option value=\"".htmlspecialchars($row['cat'])."\">".htmlspecialchars($row['cat'])."</option>";
											$flag =false;
										}else{
											echo "<option value=\"".htmlspecialchars($row['cat'])."\">".htmlspecialchars($row['cat'])."</option>";
										}
									}
									?>
									</select>
		      					</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<div class="input-group mx-auto">
									<div class="input-group-prepend">
										<span class="input-group-text">Item</span>
									</div>
									<select id="selItems" class="custom-select" onchange="unselectAll();" multiple>									
								    </select>
								</div>
							</div>
						</div>
						<div class="col-md-2 mx-auto">
							<button class="btn btn-sm btn-secondary" onclick="btnReset();">Clear List</button>
							<button id="bnSubmit" class="btn btn-sm btn-success" onclick="linkCVtoMOS();">Submit</button>
						</div>	
					</div>
				</li>
			</ul>
		</div>
	</div>
	<div id="divSelection" class="card card-signin my-1">
		<div class="card-body">
			<table class="table table-sm text-center">
				<thead class="thead-light">
					<tr>
						<th class="font-weight-bold">Item</th>
						<th class="font-weight-bold">Description</th>
						<th class="font-weight-bold">Width</th>
						<th class="font-weight-bold">Height</th>
						<th class="font-weight-bold">Depth</th>
						<th class="font-weight-bold">CV - Default</th>
						<th class="font-weight-bold">CV - Left Door</th>
						<th class="font-weight-bold">CV - Right Door</th>
					</tr>
				</thead>
				<tbody id="tbItems">
					
				</tbody>
			</table>
		</div>
	</div>
</div>
<div id="divAbsolute">
	<div id="loadingDiv" class="spinner-border" style="width: 4rem; height: 4rem;" role="status">                
	    <span class="sr-only"></span>
	</div>            
</div>
<?php
//include '../includes/foot.php';
?>
<script>
	$(document)
	  .ajaxStart(function () {
	  	$('#loadingDiv').show();
	  })
	  .ajaxStop(function () {
	  	$('#loadingDiv').hide();
	  });

	$(document).ready(function() {
		
		$('#divSelection').hide();
		$('#selCate').multiselect({
			buttonWidth: '300px',
			enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 800,
		});

        $('#selItems').multiselect('disable');

        $('#selCVcat').multiselect({
			buttonWidth: '300px',
			enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 800,
		});

		$('#cvCode').prop('disabled',true);

		$('#loadingDiv').hide();
    });
</script>

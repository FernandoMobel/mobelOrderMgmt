<?php

?>
<script type="text/javascript">
function btnReset(){
	$('#tbItems').empty();
	$('#divSelection').hide();
	$('#selItems option:selected').each(function() {
        $(this).prop('selected', false);
    })
	$('#selItems').multiselect('refresh');
}

function selectCat(val){
	$('#selItems').empty();
	$('#selItems').multiselect('destroy');
	if(val){
		myData = { mode: "getItemsCat", cat: val};
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
								myData = { mode: "getMultipleItemsRows", items: items};
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
	myData = { mode: "linkItems", cv: $('#cvCode').val(), items: items, door: $('#selDoor').val()};
	$.post("EmployeeMenuSettings.php",
	myData, 
       	function(data, status, jqXHR) {
       		console.log(jqXHR['responseText']);								       	
		});
}
</script>
<div class="container-fluid px-0">
	<div class="card card-signin py-1">
		<div class="card-body p-1">
			<ul class="list-group list-group-flush">
				<li class="list-group-item">
					<div class="row">
						<div class="col-sm-2">
							<h5 class="card-title">Cabinet Vision</h5>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text">Category</span>
									</div>
									<select id="selCVcat" class="custom-select" multiple>										
										<optgroup label="Wall Cabinets">
											<option value="W">W - Wall Cabinets</option>
											<option value="WDR">WDR - Wall 6&#8220 High Drawer</option>
											<option value="WDR2">WDR2 - Wall Two 6&#8220 High Drawers</option>
											<option value="WD18">WD18 - Wall Door 18&#8220</option>
											<option value="WOS18">WOS18 - Wall 18&#8220 Open Shelf</option>
										</optgroup>
										<optgroup label="Corner Cabinets">
											<option value="90WC">90WC - 90 Deg wall Corner</option>
											<option value="WDC">WDC - Wall Diagonal Corner</option>
											<option value="WBC">WBC - Wall Blind Corner</option>
											<option value="WEAC">WEAC - Wall End Angled Corner</option>
											<option value="WEMC">WEMC - Wall End Mitred Corner</option>
										</optgroup>
										<optgroup label="Flip-Up Door Cabinets">
											<option value="WFUD">WFUD - Wall Flip Up Door</option>
											<option value="WDFUD">WDFUD - Wall Double Flip Up Door</option>
											<option value="WLUF">WLUF - Wall Lift Up</option>
										</optgroup>
										<optgroup label="Microwave Cabinets">
											<option value="WMW">WMW - Wall Microwave</option>
											<option value="WMWP">WMWP - Wall 18" Microwave Panel</option>
										</optgroup>
										<optgroup label="Wine Rack">
											<option value="WWR">WWR - Wall Wine Rack</option>
											<option value="WWRL">WWRL - Wall Wine Rack Lattice</option>
											<option value="WWRL18">WWRL18 - Wall 18" Wine Rack Lattice</option>
											<option value="WCRCR">WCRCR - Wall CrissCross Configuration</option>
										</optgroup>
										<optgroup label="Book Case">
											<option value="BKW">BKW - Bookcase Wall Unit</option>
											<option value="90BKW">90BKW - 90 Deg Bookcase Wall Unit</option>
											<option value="WBKDR1">WBKDR1 - Wall Bookcase 6" Drawer</option>
											<option value="WBKDR2">WBKDR2 - Wall Bookcase Two 6" Drawer</option>
											<option value="WBK-WRL18">WBK-WRL18 - Wall Bookcase & Wine Rack Lattice 18"</option>
											<option value="WBK-WR06">WBK-WR06 - Wall Bookcase & Wine Rack 6"</option>
											<option value="WBK-WR12">WBK-WR12 - Wall Bookcase & Wine Rack 12"</option>
											<option value="BKWOS18">BKWOS18 - Bookcase Wall Open Shelf 18"</option>
										</optgroup>
										<optgroup label="Wall Decorative Shelves">
											<option value="WDSE">WDSE -(E) Wall Decorative Shelf</option>
											<option value="WDSF">WDSF -(F) Wall Decorative Shelf</option>
										</optgroup>
										<!-- --------------------------------------------------------------------->
										<optgroup label="Base Cabinets">
											<option value="FDB">FDB - Full Height Door</option>
											<option value="B">B - Base w/1 Top Drawer</option>
											<option value="BS">BS - Base w/ Split Drawer</option>
										</optgroup>
										<optgroup label="Corner Cabinets">
											<option value="BC">BC - Base Blind Corner</option>
											<option value="90FDBC">90FDBC - 90 Deg Full Door Base Corner</option>
											<option value="FDBDC">FDBDC - Full Door Base Diagonal Corner</option>
										</optgroup>
										<optgroup label="Drawer Base">
											<option value="DB">DB - Drawer Base</option>
											<option value="DBS">DBS - Drawer Base Split on Top </option>
											<option value="FDBPP">FDBPP - Full Door Base Pots & Pans </option>
											<option value="LKD">LKD - Lap Knee Drawer</option>
											<option value="WSDB">WSDB - Window Seat Drawer Base</option>
											<option value="DWDB">DWDB - Dishwasher Drawer Base</option>
										</optgroup>
										<optgroup label="Sink Cabinets">
											<option value="FDSB">FDSB - Full Door Sink Base</option>
											<option value="SB">SB - Sink Base</option>
											<option value="SBFS">SBFS - Sink Base Farmer Sink </option>
											<option value="SBTO">SBTO - Sink Base W / Tip Out Tray</option>
										</optgroup>
										<optgroup label="Cook Top Cabinets">
											<option value="GCTFDB">GCTFDB - Gas Cook Top Full Door Base</option>
											<option value="CTR">CTR - Cook Top Range Drawer Base</option>
											<option value="ORB">ORB - Cook Top Oven Range Base</option>
											<option value="OB">OB - Oven Base</option>
										</optgroup>
										<optgroup label="Microwave Cabinets">
											<option value="FDBMWF">FDBMWF - Full Door Base 15" Microwave Front</option>
										</optgroup>
										<optgroup label="Open Shelf Cabinets">
											<option value="FDBOS">FDBOS - Full Door Base 12" Open Shelf</option>
											<option value="DBOS">DBOS - Drawer Base 18" Open Shelf</option>
											<option value="2DBOS">2DBOS - Two Drawer Base 18" Open Shelf</option>
											<option value="BPPOS">BPPOS - Base Pots & Pans Drawer & 18" Open Shelf</option>
											<option value="BOS/BBK">BOS/BBK - Base 24" Open Shelf/ Bookcase</option>
										</optgroup>
										<optgroup label="Wine Rack Cabinets">
											<option value="BWRL">BWRL - Base 24" Wine Rack Lattice</option>
											<option value="FDBWRL">FDBWRL - Full Door Base 15" Wine Rack Lattice</option>
											<option value="DBWRL">DBWRL- Drawer Base 18" Wine Rack Lattice</option>
										</optgroup>
										<!-- --------------------------------------------------------------------->										
										<optgroup label="Vanity Cabinets 31 1/2&#8220 Height">
											<option value="FDV">FDV - Full Door Vanity</option>
											<option value="FDSV">FDSV - Full Door Sink Vanity</option>
											<option value="VSB">VSB - Vanity Sink Base</option>
											<option value="VB">VB - Vanity Base</option>
											<option value="VDB">VDB - Vanity Drawer Base</option>
											<option value="FDVBDR">FDVBDR - Full Door Vanity w/Bottom Drawer</option>
											<option value="VTSP">VTSP - 46</option>
										</optgroup>
										<optgroup label="Vanity Cabinets 34 1/2&#8220 Height">
											<option value="FDV(34.5)">FDV(34.5) - Full Door Vanity</option>
											<option value="FDSV(34.5)">FDSV(34.5) - Full Door Sink Vanity</option>
											<option value="VSB(34.5)">VSB(34.5) - Vanity Sink Base</option>
											<option value="VB(34.5)">VB(34.5) - Vanity Base</option>
											<option value="VDB(34.5)">VDB(34.5) - Vanity Drawer Base</option>
											<option value="FDVBDR(34.5)">FDVBDR(34.5) - Full Door Vanity w/Bottom Drawer</option>
											<option value="VTSP(34.5)">VTSP(34.5) - 46</option>
										</optgroup>
										<!-- --------------------------------------------------------------------->										
										<optgroup label="Tall Cabinets">
											<option value="TP">TP - Tall Pantry</option>
											<option value="TPTDB">TPTDB - Tall Pantry W/ Tall Doors on Bottom</option>
											<option value="TPOSDR">TPOSDR - Tall Pantry 13 1/2" Open Shelf 6" Drawer</option>
											<option value="TPMWPDR">TPMWPDR - Tall Pantry 13 1/2" MW Panel 6" Drawer</option>
										</optgroup>
										<optgroup label="Broom Cabinets">
											<option value="TB">TB - Tall Broom</option>
											<option value="TPB">TPB - Tall Pantry & Broom</option>
											<option value="TBTDB">TBTDB - Tall Broom W/ Tall Doors on Bottom</option>
										</optgroup>
										<optgroup label="Oven Pantries">
											<option value="SO">SO - Single Oven</option>
											<option value="SODR1">SODR1 - Single Over One Drawer</option>
											<option value="SODR3">SODR3 - Single Oven Three Drawer</option>
											<option value="DODR12">DODR12 - Double Oven 12" Drawer</option>
											<option value="DOOSDR12">DOOSDR12 - Double Oven 12" Drawer & Open Shelf</option>
										</optgroup>
									</select>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text">Item Code</span>
									</div>
									<input type="text" id="cvCode" style="text-transform:uppercase">
								</div>
							</div>
						</div>
						<div class="col-sm-3">
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
						<div class="col-sm-2">
							<h5 class="card-title">MOS</h5>
						</div>
						<div class="col-sm-5">
							<div class="form-group">
								<div class="input-group mx-auto">
								  	<div class="input-group-prepend">
								    	<label class="input-group-text" for="selCate">Category</label>
								  	</div>
								  	<select id="selCate" onChange="selectCat(this.value);" class="custom-select">
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
						<div class="col-sm-4">
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
						<div class="col-sm-1">
							<button id="bnSubmit" class="btn btn-success" onclick="linkCVtoMOS();">Submit</button>
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
<?php
//include '../includes/foot.php';
?>
<script>
	$(document).ready(function() {
		$('#divSelection').hide();
		$('#selCate').multiselect({
			buttonWidth: '350px',
			enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 800,
		});

        $('#selItems').multiselect('disable');

        $('#selCVcat').multiselect({
        	buttonWidth: '350px',
        	enableCollapsibleOptGroups: true,
            collapseOptGroupsByDefault: true,
        	enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 800,
            onChange: function(option, checked) {
                var values = [];
                $('#selCVcat option').each(function() {
                    if ($(this).val() !== option.val()) {
                        values.push($(this).val());
                    }
                });
                $('#selCVcat').multiselect('deselect', values);
                $('#cvCode').val((option.val()));                
            }
        });
    });
</script>

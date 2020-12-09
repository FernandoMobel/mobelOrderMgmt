<style>
div.sticky {
  position: -webkit-sticky;
  position: sticky;
  top: 0;
}
card ,h1,h2,h3,h4 {
  color: white;
  //font-size: .95vw;
}

.catlabel {
  background: rgba(20,50,255,0.9)
}

.catalog {
  background: rgba(20,50,255,0.7)
}

.catalog:hover {
  opacity: 0.3;
}
toggleEl{
	display: none;
}

.find:hover {
        background-color: #ccc;
      }
</style>
<script>
function validateForm() {
	var name = document.forms["formItem"]["name"].value;
	var desc = document.forms["formItem"]["description"].value;
	if (name.trim() == "") {
		alert("Name must be filled out");
		return false;
	}else if(desc.trim() == ""){
		alert("Description must be filled out");
		return false;
	}else{
		return true;
	}
}

function createItem(){
var formData = $("#formItem").serialize();
	if(validateForm()){
		console.log(formData);
		myData = { mode: "insertNewItem",  data: formData};
			$.ajax({
			url: '../item/itemActions.php',
			type: 'POST',
			data: myData,
			success: function(data, status, jqXHR) {
							console.log(jqXHR);
							document.getElementById("dbMessage").innerText = "Your item has been created!";
							$('.toast').toast('show');
							$('#createBtn').hide();
						}
			});
	}
}

function enableUpdView(){
	clearInputs();
	document.getElementById("lbTitle").innerText = "Update Item";
	$('#enableNewBtn').show();	
	$('#divFindItem').show();
	$('#createBtn').hide();	
	$('#enableUpdBtn').hide();
	$('#updateBtn').hide();
	$('.toggleEl').show();
	$('#divFindItem').removeClass( "col-lg-12 find");	
	$('#divFindItem').addClass( "col-lg-8");	
	$('#itemsMainDiv').removeClass( "col-lg-12");	
	$('#itemsMainDiv').addClass( "col-lg-4" );
}
	
function addItem(){
	clearInputs();
	document.getElementById("lbTitle").innerText = "New Item";
	$('#enableNewBtn').hide();	
	$('#updateBtn').hide();
	$('#divFindItem').hide();
	$('#createBtn').show();	
	$('#divNewItems1').show();	
	$('#divNewItems2').show();
	$('#divForm').show();
	$('#enableUpdBtn').show();
	const mainDiv = document.getElementById("itemsMainDiv");
	mainDiv.className = 'col-11 mx-auto';
	/********************* Setting placeholders***********************/
	document.getElementById("name").placeholder = "LV-M4-MA";
	document.getElementById("description").placeholder = "1.5 LIGHT VALANCE (DOOR MATCH PROFILE)";
	document.getElementById("price").placeholder = "635.0000";
	document.getElementById("sizePrice").placeholder = "0.75000";
	document.getElementById("minSize").placeholder = "0.00000";
	document.getElementById("W").value = "0.00000";
	document.getElementById("H").value = "0.00000";
	document.getElementById("D").value = "0.00000";
	document.getElementById("W2").value = "0.00000";
	document.getElementById("H2").value = "0.00000";
	document.getElementById("D2").value = "0.00000";
	document.getElementById("minW").value = "0.00000";
	document.getElementById("minH").value = "0.00000";
	document.getElementById("minD").value = "0.00000";
	document.getElementById("maxW").value = "0.00000";
	document.getElementById("maxH").value = "0.00000";
	document.getElementById("maxD").value = "0.00000";
	$("#doorFactor").val(1);
	$("#finishFactor").val(1);
	$("#sheenFactor").val(1);
	$("#speciesFactor").val(1);
	$("#interiorFactor").val(1);
	$("#glazeFactor").val(1);
	document.getElementById("drawers").value = "0";
	document.getElementById("smallDrawerFronts").value = "0";
	document.getElementById("largeDrawerFronts").value = "0";
	document.getElementById("pricingMethod").value = "0";
	$("#isCabinet").val(0);
	$("#itemVisible").val(0);
}

function confirm(){
	$('#confirmUpdate').modal('toggle');
}

function updateItem(){
	var formData = $("#formItem :input").serialize();
	formData = formData.replace(/[^\x20-\x7E]+/g, "");
	var id = document.getElementById("itemID").innerText;
	myData = { mode: "updateItemById", id: id, data: formData};
	$.ajax({
	url: '../item/itemActions.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {
					$('#livesearch').empty();
					console.log(jqXHR);
					document.getElementById("dbMessage").innerText = "Your item has been updated!";
					$('.toast').toast('show');
					$('#updateBtn').hide();
					$('#confirmUpdate').modal('toggle');
				}
	});
}

function clearInputs(){
	$('#formItem').trigger("reset");
	document.getElementById("itemID").innerHTML ="";
	document.getElementById("editItemSearch").value ="";
	$('#clearBtn').hide();
	$('#updateBtn').hide();
	$('#divForm').hide();
}

function loadItemData(id) {
	$('.toggleEl').hide();
	$('#divFindItem').removeClass("col-lg-8");	
	$('#divFindItem').addClass("col-lg-12 find");	
	$('#itemsMainDiv').removeClass("col-lg-4");	
	$('#itemsMainDiv').addClass("col-lg-12");	
	document.getElementById("lbTitle").innerText = "Update Item";
	$('#livesearch').empty();
	$('#fileName').text('');
	$('#progressBar').css('width', '0%');
	
	myData = { mode: "getItemById", id: id };
	$.ajax({
	url: '../item/itemActions.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {	
					//console.log(jqXHR["responseText"]);
					var item = JSON.parse(jqXHR["responseText"]);
					$("#itemImg").removeAttr("src");
					imageExists(item[0].id);//Checking if image exists
					document.getElementById("editItemSearch").value ="";
					document.getElementById("itemID").innerHTML =item[0].id;
					document.getElementById("name").value =item[0].name;
					document.getElementById("itemName").innerHTML =item[0].name;
					document.getElementById("description").value =item[0].description;
					document.getElementById("itemDescription").innerHTML =item[0].description;
					document.getElementById("price").value =item[0].price;
					document.getElementById("sizePrice").value =item[0].sizePrice;
					$('#pricingMethod').val(item[0].pricingMethod);
					document.getElementById("minSize").value =item[0].minSize;
					document.getElementById("W").value =item[0].W;
					document.getElementById("W2").value =item[0].W2;
					document.getElementById("H").value =item[0].H;
					document.getElementById("H2").value =item[0].H2;
					document.getElementById("D").value =item[0].D;
					document.getElementById("D2").value =item[0].D2;
					document.getElementById("minW").value =item[0].minW;
					document.getElementById("minH").value =item[0].minH;
					document.getElementById("minD").value =item[0].minD;
					document.getElementById("maxW").value =item[0].maxW;
					document.getElementById("maxH").value =item[0].maxH;
					document.getElementById("maxD").value =item[0].maxD;
					$('#doorFactor').val(item[0].doorFactor);
					$('#speciesFactor').val(item[0].speciesFactor);
					$('#finishFactor').val(item[0].finishFactor);
					$('#interiorFactor').val(item[0].interiorFactor);
					$('#sheenFactor').val(item[0].sheenFactor);
					$('#glazeFactor').val(item[0].glazeFactor);
					document.getElementById("drawers").value =item[0].drawers;
					document.getElementById("smallDrawerFronts").value =item[0].smallDrawerFronts;
					document.getElementById("largeDrawerFronts").value =item[0].largeDrawerFronts;
					$('#CLGroup').val(item[0].CLGroup);
					$('#isCabinet').val(item[0].isCabinet);					
					$('#itemVisible').val(item[0].visible);
					if(!item[0].visible){//some items are null, this should be updated 
						$('#itemVisible').val(1);
					}else{
						$('#itemVisible').val(item[0].visible);
					}
					$('#clearBtn').show();
					$('#updateBtn').show();
					$('#divForm').show();
					$('#itemImage').show();
				}
	});
}

function imageExists(itemId){
	myData = { mode: "getImage", id: itemId};

	$.ajax({
	url: '../item/itemActions.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {
				$image=jqXHR["responseText"];
				if($image!="false"){
					$('#imgExist').attr('value', $image);
					$('#itemImg').attr('src', './'+$image+'#'+ new Date().getTime());
					$('#fileAlert').hide();
				}else{
					$('#imgExist').attr('value', '0');
					$('#fileAlert').show();
					$('#fileAlert').removeClass('alert-danger');
					$('#fileAlert').removeClass('alert-warning');
					$('#fileAlert').addClass('alert-info');
					$('#fileAlert').html('There is no image, please upload a nice one..');
				}
			}
	});
}

function showResult(str) {
	$('#livesearch').empty();
	var div = document.getElementById("livesearch");
    if (str.length==0) {
        div.innerHTML="";
        div.style.border="0px";
        return;
    }
	
	myData = { mode: "getItems", str: str };
		$.ajax({
	    url: '../item/itemActions.php',
	    type: 'POST',
	    data: myData,
	    success: function(data, status, jqXHR) {
						var items = JSON.parse(jqXHR["responseText"]);
						items.forEach(el => {
							var option = document.createElement("option");
							option.setAttribute('value',el["name"]+' - '+el["description"]);
							option.textContent= el["name"]+' - '+el["description"];
							option.setAttribute('onclick', 'loadItemData("'+el["id"]+'")');
							div.appendChild(option);							
					   });
    		        }
	  	});
}

function uploadImage(){
	var myForm, myFile, files, file;
	if(!$('#imgExist').val()=='0'){
		
	}
	myForm = document.getElementById('imageAjax');
	myFile = document.getElementById('fileToUpload');
	files = myFile.files;
	var formData = new FormData();
	file = files[0]; 
	console.log(files);
	// Check the file type
	if (!file.type.match('image.*')) {
		$('#fileAlert').show();
		$('#fileAlert').removeClass('alert-info');
		$('#fileAlert').addClass('alert-warning');
		$('#fileAlert').html('The file selected is not an image.');
		$('#fileName').text('Select an image please');
		return;
	}else if(file.size/1000000>5){ //Check file size. limit - 5mb
		$('#fileAlert').show();
		$('#fileAlert').removeClass('alert-info');
		$('#fileAlert').addClass('alert-warning');
		$('#fileAlert').html('Sorry, your file is too large, there is a 5MB limit');
		$('#fileName').text('Select an image please');
		return;
	}
	$('#fileName').text(file.name);
	
	// Add the file and others to the AJAX request
    formData.append('fileToUpload', file, file.name);
    formData.append('itemID', $('#itemID').html());
    formData.append('mode', 'uploadItemImg');
    formData.append('imgExist', $('#imgExist').val());

    // Set up the request
    var xhr = new XMLHttpRequest();

    // Open the connection
    xhr.open('POST', '../upload.php', true);
	//Progress bar
	xhr.upload.addEventListener("progress", function (event) {
        if (event.lengthComputable) {
            var complete = (event.loaded / event.total * 100 | 0);
            $('#progressBar').css('width', complete + '%');
        }
    });
    // Set up a handler for when the task for the request is complete 
    xhr.onload = function () {
      if (xhr.status == 200) {
        $('#fileAlert').html('Upload complete!');
		$('#fileAlert').removeClass('alert-info');
		$('#fileAlert').removeClass('alert-warning');
		$('#fileAlert').addClass('alert-success');
		$('#imgExist').val(xhr['responseText'].trim());
		$("#itemImg").attr("src", xhr['responseText'].trim()+'#'+ new Date().getTime());
		console.log('hecho...');
      } else {
        $('#fileAlert').html('Upload error. Try again.');
		$('#fileAlert').removeClass('alert-info');
		$('#fileAlert').removeClass('alert-warning');
		$('#fileAlert').addClass('alert-danger');
      }
    };

    // Send the data.
    xhr.send(formData);
}

function showFindItem(){
	clearInputs();
	$('.toggleEl').show();
	$('#divFindItem').removeClass( "col-lg-12 find");	
	$('#divFindItem').addClass( "col-lg-8");	
	$('#itemsMainDiv').removeClass( "col-lg-12");	
	$('#itemsMainDiv').addClass( "col-lg-4" );	
}

</script>
<div class="container-fluid">
	<div class="row">
		<div id="divFindItem" class="col-lg-8 col-sm-6 mx-auto">
			<!--------------************************************** Find Item Box Start **************************************-------------->
			<div class="card my-1">
				<div onclick="showFindItem()" class="d-flex flex-row card-header py-0">
					<div class="p-2">
						<svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-search text-primary" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/>
							<path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
						</svg>
					</div>
					<div class="p-2 toggleEl">
						<h5>Find Item</h5>
					</div>
				</div>
				<div class="card-body toggleEl">					
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text">Search:</span>
						</div>
						<input class="form-control" name="editItemSearch" autocomplete="off" type="text"  id="editItemSearch" onkeyup="showResult(this.value)" autofocus>
					</div>					
					<div id="livesearch">
					</div>
					<!--------------************************************** Catalogue Start **************************************-------------->
					<!--div class="container">
						<div class="row my-3">
							<div class="col">
								<div class="card h-100"  style="background:transparent url('./img/snk.jpg') no-repeat center center /cover; min-height: 150px">
									<div class="catalog h-100">
										<h1 class="mx-3 my-3">70</h1>
									</div>
									<h4 class="catlabel text-center m-0">CABINETS</h4>
								</div>
							</div>
							<div class="col">
								<div class="card h-100"  style="background:transparent url('./img/sq1.jpg') no-repeat center center /cover; min-height: 150px">
									<div class="catalog h-100">
										<h1 class="mx-3 my-3">6</h1>
									</div>
									<h4 class="catlabel text-center m-0">DECORATIVE HOODS</h4>
								</div>
							</div>
							<div class="col">
								<div class="card h-100"  style="background:transparent url('./img/tall.jpg') no-repeat center center /cover; min-height: 150px">
									<div class="catalog h-100">
										<h1 class="mx-3 my-3">59</h1>
									</div>
									<h4 class="catlabel text-center m-0">TALL CABINETS</h4>
								</div>
							</div>
							<div class="col">
								<div class="card h-100"  style="background:transparent url('./img/sq2.jpg') no-repeat center center /cover; min-height: 150px">
									<div class="catalog h-100">
										<h1 class="mx-3 my-3">33</h1>
									</div>
									<h4 class="catlabel text-center m-0">VANITY CABINETS</h4>
								</div>
							</div>
						</div>
						<div class="row my-3">
							<div class="col-8">
								<div class="card h-100"  style="background:transparent url('./img/w2.png') no-repeat center center /cover; min-height: 250px">
									<div class="catalog h-100">
										<h1 class="mx-3 my-3">81</h1>
									</div>
									<h4 class="catlabel text-center m-0">BASE CABINETS</h4>
								</div>
							</div>
							<div class="col-4">
								<div class="card h-100"  style="background:transparent url('./img/w1.jpg') no-repeat center center /cover; min-height: 250px">
									<div class="catalog h-100">
										<h1 class="mx-3 my-3">33</h1>
									</div>
									<h4 class="catlabel text-center m-0">FINISHED ENDS & PANELS</h4>
								</div>
							</div>
						</div>
						<div class="row my-3">
							<div class="col-4">
								<div class="card h-100"  style="background:transparent url('./img/w3.jpg') no-repeat center center /cover; min-height: 200px">
									<div class="catalog h-100">
										<h1 class="mx-3 my-3">7</h1>
									</div>
									<h4 class="catlabel text-center m-0">FILLERS</h4>
								</div>
							</div>
							<div class="col-3">
								<div class="card h-100"  style="background:transparent url('./img/w4.jpg') no-repeat center center /cover; min-height: 200px">
									<div class="catalog h-100">
										<h1 class="mx-3 my-3">6</h1>
									</div>
									<h4 class="catlabel text-center m-0">MOULDINGS</h4>
								</div>
							</div>
							<div class="col-5">
								<div class="card h-100"  style="background:transparent url('./img/sq1.png') no-repeat center center /cover; min-height: 200px">
									<div class="catalog h-100">
										<h1 class="mx-3 my-3">294</h1>
									</div>
									<h4 class="catlabel text-center m-0">ALL</h4>
								</div>
							</div>
						</div>
					</div>
					<!--------------************************************** Catalogue End **************************************-------------->
				</div>				
			</div>
			<!--------------************************************** Find Item Box End **************************************-------------->
			<!--------------************************************** Item Image Start **************************************-------------->
			<div hidden id="itemImage" class="card my-1">
				<div class="card-header">
					<h5 id="itemName"></h5>
					<small id="itemDescription"></small>
				</div>
				<div class="card-body">					
					<img id="itemImg" class="img-fluid">
					
					<div id="fileAlert" class="alert alert-info text-center" role="alert">
					  There is no image, please upload a nice one..
					</div>
					<div class="progress" style="height: 2px;">
					  <div id="progressBar" class="progress-bar  progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
					</div>
					<form id="imageAjax" action="upload.php" method="POST">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupFileAddon01">Select an Image file</span>
							</div>
							<div class="custom-file">
								<input onchange="uploadImage();" type="file" class="custom-file-input" name="fileToUpload" id="fileToUpload" aria-describedby="fileToUpload">
								<label id="fileName" class="custom-file-label" for="fileToUpload">Choose file</label>							
							</div>
						</div>
						<input type="hidden" id="imgExist" name="imgExist" value="0"/>
					</form>					
				</div>
			</div>
			<!--------------************************************** Item Image End **************************************-------------->
		</div>
		<!--------------************************************** Find Item End **************************************-------------->
		<div id="itemsMainDiv" class="col-lg-4 col-sm-6 mx-auto">
			<div class="card sticky my-3">
				<h5 class="card-header">
					<label id="lbTitle" >Item Actions</label>
					<div class="float-right">
						<button type="button" class="btn btn-outline-primary" onclick="enableUpdView()" id="enableUpdBtn">Return to update item</button>
						<button type="button" class="btn btn-outline-primary" onclick="addItem()" id="enableNewBtn">Add new item</button>
					</div>
				</h5>
				<label id="itemID" hidden></label>
				<div id="divForm" class="card-body">					
					<form id="formItem">	
						<div class="col-12">
							<div class="form-group">
								<div class="input-group mb-3">
								  <div class="input-group-prepend">
									<span class="input-group-text">Item Name</span>
								  </div>
								  <input type="text" class="form-control" name="name" id="name" maxlength="99" aria-describedby="name">
								</div>
								<small id="ordReq" class="form-text text-muted alert-danger" hidden>Order name is mandatory</small>
							</div>
						</div>
						<div class="col-12">
							<div class="form-group">
								<div class="input-group mb-3">
								  <div class="input-group-prepend">
									<span class="input-group-text">Description</span>
								  </div>
								  <input type="text" class="form-control" name="description" id="description" maxlength="249" aria-describedby="description">
								</div>
							</div>
						</div>
						<div class="container-fluid">
							<div class="row">
								<div class="col-lg-1 mb-0"><label class="text-muted">Pricing</label></div>
								<div class="col-lg-11 dropdown-divider mb-0"></div>
							</div>
							<div class="row d-flex justify-content-between">
								<div class="col-md-3">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Price</span>
											</div>
											<input type="number" step="0.0001" min="0.00000" class="form-control" name="price" id="price" value="0" aria-describedby="price">
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Size Price</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="sizePrice" id="sizePrice" value="0" aria-describedby="sizePrice">
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Min Size</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="minSize" id="minSize" value="0" aria-describedby="minSize">
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Pricing Method</span>
											</div>
											<select class="form-control" name="pricingMethod" id="pricingMethod">
												<option selected value="0">Method 1 - (Size = W*H*D)</option>
												<option value="1">Method 2 - (Size = W*D)</option>
												<option value="2">Method 3 - (Size = H*D)</option>
												<option value="3">Method 4 - (Size = W*H)</option>
											</select>
											<!--input type="number" class="form-control" name="pricingMethod" id="pricingMethod" min="0" value="0" aria-describedby="pricingMethod"-->
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-1 mb-0"><label class="text-muted">Sizes</label></div>
								<div class="col-lg-11 dropdown-divider mb-0"></div>
							</div>
							<div class="row d-flex justify-content-between">
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Width</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="W" id="W" value="0" aria-describedby="W">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Height</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="H" id="H" value="0" aria-describedby="H">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Depth</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="D" id="D" value="0" aria-describedby="D">
										</div>
									</div>
								</div>
							</div>
							<div class="row d-flex justify-content-between">
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Width 2</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="W2" id="W2" value="0" aria-describedby="W2">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Height 2</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="H2" id="H2" value="0" aria-describedby="H2">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Depth 2</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="D2" id="D2" value="0" aria-describedby="D2">
										</div>
									</div>
								</div>	
							</div>
					  
							<div class="row d-flex justify-content-between">
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Min Width</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="minW" id="minW" value="0" aria-describedby="minW">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Min Height</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="minH" id="minH" value="0" aria-describedby="minH">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Min Depth</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="minD" id="minD" value="0" aria-describedby="minD">
										</div>
									</div>
								</div>
							</div>
							<div class="row d-flex justify-content-between">
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Max Width</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="maxW" id="maxW" value="0" aria-describedby="maxW">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Max Height</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="maxH" id="maxH" value="0" aria-describedby="maxH">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Max Depth</span>
											</div>
											<input type="number" step="0.0001" min="0" class="form-control" name="maxD" id="maxD" value="0" aria-describedby="maxD">
										</div>
									</div>
								</div>
							</div>																		
							<div class="row d-flex justify-content-between">
								<div class="col-lg-1 mb-0"><label class="text-muted">Factors</labels></div>
								<div class="col-lg-11 dropdown-divider mb-0"></div>
							</div>
							<div class="row d-flex justify-content-between">
								<div class="col-md-2">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-append">
												<span class="input-group-text">Door Factor</span>
											</div>											
											<select id="doorFactor" class="form-control" name="doorFactor"><option value="0">No</option><option value="1">Yes</option></select>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-append">
												<span class="input-group-text">Species Factor</span>
											</div>
											<select id="speciesFactor" class="form-control" name="speciesFactor"><option value="0">No</option><option value="1">Yes</option></select>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-append">
												<span class="input-group-text">Finish Factor</span>
											</div>
											<select id="finishFactor" class="form-control" name="finishFactor"><option value="0">No</option><option value="1">Yes</option></select>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-append">
												<span class="input-group-text">Interior Factor</span>
											</div>
											<select id="interiorFactor" class="form-control" name="interiorFactor"><option value="0">No</option><option value="1">Yes</option></select>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-append">
												<span class="input-group-text">Sheen Factor</span>
											</div>
											<select id="sheenFactor" class="form-control" name="sheenFactor"><option value="0">No</option><option value="1">Yes</option></select>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-append">
												<span class="input-group-text">Glaze Factor</span>
											</div>
											<select id="glazeFactor" class="form-control" name="glazeFactor"><option value="0">No</option><option value="1">Yes</option></select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--------------*******************-------------- Only for new items End--------------*******************-------------->
						<div class="container-fluid">
							<div class="row">
								<div class="col-lg-1 mb-0"><label class="text-muted">Drawers</label></div>
								<div class="col-lg-11 dropdown-divider mb-0"></div>
							</div>
							<div class="row d-flex justify-content-between">
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Drawers</span>
											</div>
											<input type="number" class="form-control" name="drawers" id="drawers" min="0" step="1" aria-describedby="drawers">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Small Drawer Fronts</span>
											</div>
											<input type="number" class="form-control" name="smallDrawerFronts" id="smallDrawerFronts" min="0" step="1" aria-describedby="smallDrawerFronts">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text">Large Drawer Fronts</span>
											</div>
											<input type="number" class="form-control" name="largeDrawerFronts" id="largeDrawerFronts" min="0" step="1" aria-describedby="largeDrawerFronts">
										</div>
									</div>
								</div>
							</div>
							<div class="row d-flex justify-content-between">
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-append">
												<span class="input-group-text">Cabinet Line Group</span>
											</div>
											<?php
											opendb2("SELECT * FROM cabinetLineGroup");
											echo "<select id=\"CLGroup\" placeholder=\"Cabinet Line Group\" class=\"form-control\" name=\"CLGroup\">";
											if($GLOBALS['$result2']->num_rows > 0){			
												foreach ($GLOBALS['$result2'] as $row2) {
													echo "<option value=\"".$row2['id']."\" ";
													if($_SESSION["defaultCLid"]==$row2['id'])
														echo "selected";
													echo ">".$row2['Name']."</option>" ;													
												}
											}
											echo "</select>";
											?>
										</div>
									</div>
								</div>
								<!--------------************************************** Check boxes **************************************-------------->
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-append">
												<span class="input-group-text">Visible</span>
											</div>		
											<select id="itemVisible" class="form-control" name="visible"><option value="0">No</option><option value="1">Yes</option></select>
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-append">
												<span class="input-group-text" for="isCabinet">Is cabinet</span>
											</div>	
											<select id="isCabinet" class="form-control" name="isCabinet"><option value="0">No</option><option value="1">Yes</option></select>											
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--------------************************************** Main Buttons Start **************************************-------------->
						<div class="float-right">
							<button type="button" class="btn btn-success" id="createBtn" onclick="createItem()">Create New</button>
							<button type="button" class="btn btn-success" id="updateBtn" onclick="confirm()">Update</button>
							<button type="button" class="btn btn-outline-primary" onclick="clearInputs()" id="clearBtn">Clear</button>
						 </div>
						<!--------------************************************** Main Buttons End **************************************-------------->
					</form>
				</div>
			</div>
		</div>
	</div>
	<div style="position: absolute; top: 0; right: 0;">
		<div id="myToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
			<div class="toast-header">
				<!--img src="..." class="rounded mr-2" alt="..."-->
				<strong class="mr-auto">MOS - Mobel</strong>
				<small>Just now</small>
				<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div id="dbMessage" class="toast-body">
			</div>
		</div>
	</div>
</div>

<div id="confirmUpdate" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Please confirm.</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Some of these changes can affect current live quotes.<br/>Do you want to continue?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="updateItem()">Yes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>

<?php include '../includes/foot.php';?>
<script>
$(document).ready(function(){
	  $('#updateBtn').hide();
	  $('#itemImage').hide();
	  $('#clearBtn').hide();
	  $('#createBtn').hide();
	  $('#enableUpdBtn').hide();
	  $('#divForm').hide();
	    $("#myToast").toast({
            delay: 5000
        });
})
</script>
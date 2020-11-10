<?php
$sql = "select distinct description from item where CLGroup in(SELECT CLGid FROM cabinetLineGroups WHERE CLid = ".$_SESSION["defaultCLid"].")";
//Getting all categories
$query = opendb($sql);
$categories = array(); 
if($query->num_rows > 0){ 
	while($row = $query->fetch_assoc()){ 
		array_push($categories, $row["description"]); 
	} 
} 
//results as json encoded array 
//echo json_encode($categories);
?>
<style>
.autocomplete {
  /*the container must be positioned relative:*/
  position: relative;
  display: inline-block;
.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}
.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff;
  border-bottom: 1px solid #d4d4d4;
}
.autocomplete-items div:hover {
  /*when hovering an item:*/
  background-color: #e9e9e9;
}
.autocomplete-active {
  /*when navigating through the items using the arrow keys:*/
  background-color: DodgerBlue !important;
  color: #ffffff;
}
}
</style>
<script>
function showResult(str) {
	$('#livesearch').empty();
	var div = document.getElementById("livesearch");
    if (str.length==0) {
        div.innerHTML="";
        div.style.border="0px";
        return;
    }
	
	myData = { mode: "getItemsRestricted", str: str };
		$.ajax({
	    url: './item/itemActions.php',
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

function loadItemData(id) {
	$('#livesearch').empty();
	
	myData = { mode: "getItemById", id: id };
	$.ajax({
	url: './item/itemActions.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {					
					var item = JSON.parse(jqXHR["responseText"]);
					document.getElementById("editItemSearch").value ="";
					document.getElementById("itemID").innerHTML =item[0].id;
					document.getElementById("name").value =item[0].name;
					//document.getElementById("itemName").innerHTML =item[0].name;
					document.getElementById("description").value =item[0].description;
					//document.getElementById("itemDescription").innerHTML =item[0].description;
					document.getElementById("minW").value =item[0].minW;
					document.getElementById("minH").value =item[0].minH;
					document.getElementById("minD").value =item[0].minD;
					document.getElementById("maxW").value =item[0].maxW;
					document.getElementById("maxH").value =item[0].maxH;
					document.getElementById("maxD").value =item[0].maxD;
					$('#requestBtn').prop('disabled', false);
				}
	});
}

function sendRequest(){
	var formData = $("#formItem").serialize();
	formData = formData.replace(/[^\x20-\x7E]+/g, "");
	console.log(formData);
	var id = document.getElementById("itemID").innerText;
	myData = { mode: "reqItemUpdate", id: id, data: formData};

	$.ajax({
	url: './item/itemActions.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {
					$('#livesearch').empty();
					console.log(jqXHR);
					//document.getElementById("dbMessage").innerText = "Your item has been updated!";
					//$('.toast').toast('show');
					$('#requestBtn').hide();
				}
	});
}
</script>
<div class="container-fluid">
	<div class="row">
		<div id="divFindItem" class="col-lg-7 col-sm-7 mx-auto">
			<div class="card my-3">
				<h5 class="card-header"><strong>Find Item</strong></h5>
				<div class="card-body">	
					<!--input class="form-control" name="categorySearch" type="text" id="categorySearch" autofocus-->
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text">Search:</span>
						</div>
						<input class="form-control" name="editItemSearch" autocomplete="off" type="text"  id="editItemSearch" onkeyup="showResult(this.value)" autofocus>
					</div>					
					<div id="livesearch">
					</div>
				</div>
			</div>
		</div>
		<div id="divFindItem" class="col-lg-5 col-sm-5 mx-auto">
			<label id="itemID" hidden></label>
			<div class="card my-3">
				<h5 class="card-header"><strong>Request</strong></h5>				
				<div class="card-body">	
					<form id="formItem">					
					  <div class="form-group">
						<div class="input-group mb-3">
						  <div class="input-group-prepend">
							<span class="input-group-text">Item Name</span>
						  </div>
						  <input type="text" class="form-control" name="name" id="name" maxlength="99" aria-describedby="name" style="text-transform:uppercase">
						</div>
					  </div>
					  <div class="form-group">
						<div class="input-group mb-3">
						  <div class="input-group-prepend">
							<span class="input-group-text">Category</span>
						  </div>
						  <input disabled type="text" class="form-control" name="description" id="description" maxlength="249" aria-describedby="description" style="text-transform:uppercase">
						</div>
					  </div>
					  <div class="row">
						<div class="col">
							<div class="form-group">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text">Min Width</span>
									</div>
									<input type="number" step="0.1" min="0" class="form-control" name="minW" id="minW" value="0" aria-describedby="minW">
								</div>
							</div>
						</div>
						<div class="col">
							<div class="form-group">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text">Min Height</span>
									</div>
									<input type="number" step="0.1" min="0" class="form-control" name="minH" id="minH" value="0" aria-describedby="minH">
								</div>
							</div>
						</div>
						<div class="col">
							<div class="form-group">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text">Min Depth</span>
									</div>
									<input type="number" step="0.1" min="0" class="form-control" name="minD" id="minD" value="0" aria-describedby="minD">
								</div>
							</div>
						</div>
					  </div>
					  <div class="row">
						<div class="col">
							<div class="form-group">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text">Max Width</span>
									</div>
									<input type="number" step="0.1" min="0" class="form-control" name="maxW" id="maxW" value="0" aria-describedby="maxW">
								</div>
							</div>
						</div>
						<div class="col">
							<div class="form-group">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text">Max Height</span>
									</div>
									<input type="number" step="0.1" min="0" class="form-control" name="maxH" id="maxH" value="0" aria-describedby="maxH">
								</div>
							</div>
						</div>
						<div class="col">
							<div class="form-group">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text">Max Depth</span>
									</div>
									<input type="number" step="0.1" min="0" class="form-control" name="maxD" id="maxD" value="0" aria-describedby="maxD">
								</div>
							</div>
						</div>
					  </div>
					  <!--------------************************************** Main Buttons Start **************************************-------------->
					  <div><small><strong> * This request requires approval from MOBEL.</strong></small></div>
					  <div class="float-right">
						<button disabled type="button" class="btn btn-success" id="requestBtn" onclick="sendRequest()">Send Request</button>
						<!--button type="button" class="btn btn-outline-primary" onclick="clearInputs()" id="clearBtn">Clear</button-->
					  </div>
					  <!--------------************************************** Main Buttons End **************************************-------------->
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include 'includes/foot.php';?>
<!--script>
function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
              b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
      x[i].parentNode.removeChild(x[i]);
    }
  }
}

/*execute a function when someone clicks in the document:*/
document.addEventListener("click", function (e) {
    closeAllLists(e.target);
});
}
var categories = <?php //echo json_encode($categories);?>;

autocomplete(document.getElementById("categorySearch"),categories);
</script-->
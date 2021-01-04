<?php include 'includes/nav.php';?>
<?php include 'includes/db.php';?>
<script>
function profileView(user){
	$('#usrDetails').empty();
	myData = { mode:"getUsrDet", uid:user[0]};
	$.ajax({
	url: 'userActions.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {
					//console.log(jqXHR['responseText']);					
					$('#usrDetails').append(jqXHR['responseText']);
				}
	});
	$('#usrEmail').val(user[7]);
	$('#firstName').val(user[5]);
	$('#lastName').val(user[6]);
	$('#phone').val(user[8]);
	$('#account').val(user[11]);
	$('#userType').val(user[3]);
	$('#CLGroup').val(user[9]);
	$('#defaultCLid').val(user[12]);
}

function updateDetail(obj){	
	myData = { mode:"updateDetail", email:$('#usrEmail').val(), col:obj, value:$('#'+obj).val()};
	$.ajax({
	url: 'userActions.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {
					//console.log(jqXHR['responseText']);					
					$('#'+obj).css("border-color", "#00b828");
				}
	});
}

function savePw(){
	myData = { mode: "updatePassword", email:$('#usrEmail').val(), pw: $("#pw").val(), pw2: $("#pw2").val(), pw3:$("#pw3").val()};
	if($("#pw2").val()!=$("#pw3").val()){
		alert("Sorry, your new passwords don't match. Please try again");
	}else{
    	$.post("userActions.php",
    			myData, 
    		       function(data, status, jqXHR) {
                		if(status == "success"){
                			$('#confirmPassword').modal('toggle');
                			$("#pw").val("");
                			$("#pw2").val("");
                			$("#pw3").val("");
                    		alert('Password updated successfully.');
                	    }else{
                    	    alert('Sorry, something went wrong. Did you get the old password right? Please try again.');
                	    }
    		        });
	}
}

function chkPassword(){
	if($('#usrEmail').val()!= <?php echo "'".$_SESSION['email']."'"?> ){
		$('#oldPsw').hide();
	}else{
		$('#oldPsw').show();
	}
}

function uploadImage(){
	var myForm, myFile, files, file;
	myFile = document.getElementById('profileFile');
	files = myFile.files;
	file = files[0]; 
	var formData = new FormData();
	if (!file.type.match('image.*')) {
		alert('The file selected is not an image.');
		//$('#fileName').text('Select an image please');
		return;
	}else if(file.size/1000000>5){ //Check file size. limit - 5mb
		alert('Sorry, your file is too large, there is a 5MB limit');
		//$('#fileName').text('Select an image please');
		return;
	}

	// Add the file and others to the AJAX request
    formData.append('fileToUpload', file, file.name);
    formData.append('userId', $('#itemID').html());
    formData.append('mode', 'uploadUserImg');
    //formData.append('imgExist', $('#imgExist').val());

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
        /*$('#fileAlert').html('Upload complete!');
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
		$('#fileAlert').addClass('alert-danger');*/
      }
    };

    // Send the data.
    xhr.send(formData);
}

function createUser(){
	var formData = $("#newUser").serialize();
	console.log(formData);
	myData = { mode: "addNewUser",  data: formData};
	$.ajax({
	url: 'userActions.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {
					alert("The new user has been created!");
					window.location.reload();
		},
	error: function(data, status, errorThrown) {
			alert(errorThrown);
		}
	});
	return false;
}

function createAccount(){
	var formData = $("#newAccount").serialize();
	myData = { mode: "addNewAccount",  data: formData};
	$.ajax({
	url: 'userActions.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {
					alert("The new account has been created!");
					window.location.reload();
				}
	});
	return false;
}

function createAccountAddress(){
	var formData = $("#newAccountAddress").serialize();
	myData = { mode: "addNewAccountAddress",  data: formData};
	$.ajax({
	url: 'userActions.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {
					alert("The new address has been created!");
					$('#addAccountAddress').modal('toggle');
				}
	});
	return false;
}

function checkUser(user){
	myData = { mode: "userExist",  email: user};
	$.ajax({
	url: 'userActions.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {
					if(jqXHR['responseText']>0){
						$('#btnNewUsr').prop('disabled',true);
						$('#nusrEmail').css("border-color", "#dc3545");
						$('#alertEmail').fadeIn();
					}else{
						$('#btnNewUsr').prop('disabled',false);
						$('#nusrEmail').css("border-color", "#28a745");
						$('#alertEmail').fadeOut();
					}
				}
	});
}

function confPass(){
	if($('#nusrPassword').val()==$('#nusrConfPassword').val()){
		$('#btnNewUsr').prop('disabled',false);
		$('#alertPass').fadeOut();
	}else{
		$('#btnNewUsr').prop('disabled',true);
		$('#alertPass').fadeIn();
	}
}
</script>
<style>
div.sticky {
  position: -webkit-sticky;
  position: sticky;
  top: 0;
}
</style>
<div class="container-fluid">
	<div class="row">
		<div id="profileCard" class="col-4 mx-auto">
			<div class="card sticky">
				<div class="card profile-card-2">
                    <div class="card-body pt-5">
						<div class="row">
							<div class="col-3">
								<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" fill="currentColor" class="bi bi-person-square text-primary" for="profileFile" viewBox="0 0 16 16">
	  								<path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
	  								<path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1v-1c0-1-1-4-6-4s-6 3-6 4v1a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12z"/>
								</svg>
	    						<div id="usrDetails">
								<?php 								
								$sql2 = "SELECT s.id, s.name as state, count(*)as qty FROM mosOrder mo, state s where mo.state = s.id and mosUser = ".$_SESSION["userid"]." group by s.name order by s.id"; 
								$result2 = opendb($sql2);
								$total = 0;
								while ( $row2 = $result2->fetch_assoc())  {	
									$total += $row2["qty"];
									if($row2["id"]<7){
										echo "<div class=\"text-center my-3 p-0\"><h5 class=\"my-0\"><b>".$row2["qty"];
										echo "</b></h5><small class=\"mx-auto\">".$row2["state"]."</small></div>";
									}
								}
								echo "<div class=\"text-center my-auto\"><h4><b>".$total;
								echo "</b></h4><small class=\"mx-auto\">Total Orders</small></div>";
								?>
								</div>
							</div>
							<div class="col-9">
								<?php
								$sql = "select mu.id, a.busName as account, a.description, mus.id as userTypeId, mus.name as userType, mu.firstName, mu.lastName, mu.email, mu.phone, clg.id clid, clg.name as cabinetLine,a.id aid, mu.defaultCLid defaultCLid ";
								$sql .= "from mosUser mu, account a, mosUserTypes mus, cabinetLineGroup clg "; 
								$sql .= "where mu.account = a.id and mu.userType = mus.id and mu.CLGroup = clg.id and mu.id=".$_SESSION["userid"];
								$result = opendb2($sql);
								$row2 = $result->fetch_assoc();
								?>
								<div class="input-group mb-4">
									<div class="input-group-prepend">
										<span class="input-group-text">Email</span>
									</div>
									<input value="<?php echo $row2['email']?>" type="email" class="form-control" name="usrEmail" id="usrEmail" maxlength="30" aria-describedby="usrEmail" readonly>									
								</div>
								<div class="input-group mb-4">
									<!--div class="input-group-prepend">
										<span class="input-group-text">New Password</span>
									</div>
									<input type="password" class="form-control" name="usrPassword" id="usrPassword" maxlength="30" aria-describedby="usrPassword"-->		
									<div class="input-group-append">
										<button onclick="chkPassword();" class="btn-primary form-control" type="button" data-toggle="modal" data-target="#confirmPassword">Update Password</button>
									</div>
								</div>
								<div class="input-group mb-4">
									<div class="input-group-prepend">
										<span class="input-group-text">Name</span>
									</div>
									<input onchange="updateDetail(this.id);" value="<?php echo $row2['firstName']?>" type="text" class="form-control" name="firstName" id="firstName" maxlength="30" aria-describedby="firstName">									
								</div>
								<div class="input-group mb-4">
									<div class="input-group-prepend">
										<span class="input-group-text">Last Name</span>
									</div>
									<input onchange="updateDetail(this.id);" value="<?php echo $row2['lastName']?>" type="text" class="form-control" name="lastName" id="lastName" maxlength="30" aria-describedby="lastName">									
								</div>								
								<div class="input-group mb-4">
									<div class="input-group-prepend">
										<span class="input-group-text">Phone</span>
									</div>
									<input onchange="updateDetail(this.id);" value="<?php echo $row2['phone']?>" type="phone" class="form-control" name="phone" id="phone" maxlength="30" aria-describedby="phone">									
								</div>								
								<div class="input-group mb-4">
									<div class="input-group-append">
										<span class="input-group-text">Account</span>
									</div>
									<?php
									opendb2("SELECT * FROM account");
									echo "<select onchange=\"updateDetail(this.id);\" ";
									if($_SESSION["userType"] != '3')
										echo "disabled ";
									echo "id=\"account\" placeholder=\"Account\" class=\"form-control\" name=\"account\">";
									if($GLOBALS['$result2']->num_rows > 0){			
										foreach ($GLOBALS['$result2'] as $row2) {
											if($row2['id']==$_SESSION["account"]){
												echo "<option value=\"".$row2['id']."\" selected>".$row2['busName']."</option>" ;
											}else{
												echo "<option value=\"".$row2['id']."\">".$row2['busName']."</option>" ;
											}
										}
									}
									echo "</select>";
									?>
								</div>
								<div class="input-group mb-4">
									<div class="input-group-append">
										<span class="input-group-text">User Type</span>
									</div>
									<?php
									if($_SESSION["userType"]=='3')
										opendb2("SELECT * FROM mosUserTypes");
									if($_SESSION["userType"]=='2')
										opendb2("SELECT * FROM mosUserTypes where id <=".$_SESSION["userType"]);
									if($_SESSION["userType"]=='1')
										opendb2("SELECT * FROM mosUserTypes where id =".$_SESSION["userType"]);
									echo "<select ";
									if($_SESSION["userType"]=='1') echo "disabled "; 
									echo "onchange=\"updateDetail(this.id);\" id=\"userType\" placeholder=\"User types\" class=\"form-control\" name=\"userType\" >";
									if($GLOBALS['$result2']->num_rows > 0){			
										foreach ($GLOBALS['$result2'] as $row2) {
											if($row2['id']==$_SESSION["userType"]){
												echo "<option value=\"".$row2['id']."\" selected>".$row2['name']."</option>" ;
											}else{
												echo "<option value=\"".$row2['id']."\">".$row2['name']."</option>" ;
											}
										}
									}
									echo "</select>";
									?>
								</div>
								<div class="input-group mb-4" <?php if($_SESSION["userType"]!='3') echo "hidden" ?>>
									<div class="input-group-append">
										<span class="input-group-text">Cabinet Line Group</span>
									</div>
									<?php
									if($_SESSION["userType"]=='3')
										opendb2("SELECT * FROM cabinetLineGroup");
									if($_SESSION["userType"]=='2')
										opendb2("SELECT * FROM cabinetLineGroup where id=".$_SESSION["CLGroup"]);
									if($_SESSION["userType"]=='1')
										opendb2("SELECT * FROM cabinetLineGroup where id=".$_SESSION["CLGroup"]);
									echo "<select onchange=\"updateDetail(this.id);\" id=\"CLGroup\" placeholder=\"Cabinet Line Group\" class=\"form-control\" name=\"CLGroup\">";
									if($GLOBALS['$result2']->num_rows > 0){			
										foreach ($GLOBALS['$result2'] as $row2) {
											if($row2['id']==$_SESSION["CLGroup"]){
												echo "<option value=\"".$row2['id']."\" selected>".$row2['Name']."</option>" ;
											}else{
												echo "<option value=\"".$row2['id']."\">".$row2['Name']."</option>" ;
											}
										}
									}
									echo "</select>";
									?>
								</div>
								<div class="input-group mb-4" <?php if($_SESSION["userType"]!='3') echo "hidden" ?>>
									<div class="input-group-append">
										<span class="input-group-text">Default Cabinet Line</span>
									</div>
									<?php
									$flag = true;
									opendb2("SELECT * FROM cabinetLine");
									echo "<select onchange=\"updateDetail(this.id);\" id=\"defaultCLid\" placeholder=\"Default Cabinet Line\" class=\"form-control\" name=\"defaultCLid\">";
									if($GLOBALS['$result2']->num_rows > 0){			
										foreach ($GLOBALS['$result2'] as $row2) {
											if($row2['id']==$_SESSION["defaultCLid"]){
												echo "<option selected value=\"".$row2['id']."\">".$row2['CabinetLine']."</option>" ;
											}else{
												echo "<option value=\"".$row2['id']."\">".$row2['CabinetLine']."</option>" ;
											}
										}
									}
									echo "</select>";
									?>
							    </div>
							</div>
						</div>
                    </div>
                </div>
    		</div>
		</div>
		<div id="usersTb" class="col-8" <?php if($_SESSION["userType"]==1) echo "hidden";?>>
			<div class="card">
				<?php
				if($_SESSION["userType"]=='3'){
				?>
				<div class="card-header">
					<button class="btn btn-sm btn-primary" type="button" data-toggle="modal" data-target="#addUser" data-toggle="tooltip" data-placement="bottom" title="New User">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-plus-fill" viewBox="0 0 16 16">
						  <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
						  <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"/>
						</svg>
					</button>
					<button class="btn btn-sm btn-primary" type="button" data-toggle="modal" data-target="#addAccount" data-toggle="tooltip" data-placement="bottom" title="New Account">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-node-plus-fill" viewBox="0 0 16 16">
						  <path d="M11 13a5 5 0 1 0-4.975-5.5H4A1.5 1.5 0 0 0 2.5 6h-1A1.5 1.5 0 0 0 0 7.5v1A1.5 1.5 0 0 0 1.5 10h1A1.5 1.5 0 0 0 4 8.5h2.025A5 5 0 0 0 11 13zm.5-7.5v2h2a.5.5 0 0 1 0 1h-2v2a.5.5 0 0 1-1 0v-2h-2a.5.5 0 0 1 0-1h2v-2a.5.5 0 0 1 1 0z"/>
						</svg>
					</button>
					<button class="btn btn-sm btn-primary" type="button" data-toggle="modal" data-target="#addAccountAddress" data-toggle="tooltip" data-placement="bottom" title="New Account Address">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-shop-window" viewBox="0 0 16 16">
						  <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.371 2.371 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976l2.61-3.045zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0zM1.5 8.5A.5.5 0 0 1 2 9v6h12V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5zm2 .5a.5.5 0 0 1 .5.5V13h8V9.5a.5.5 0 0 1 1 0V13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5a.5.5 0 0 1 .5-.5z"/>
						</svg>
					</button>
				</div>
				<?php
				}
				?>
				<div class="card-body">
					<table id="tbEmp" class="table table-sm text-center table-hover" style="width:100%">
						<thead class="thead-light">
							<tr>
								<th>NAME</th>
								<th>EMAIL</th>
								<th>PHONE</th>
								<th>ACCOUNT</th>
								<th>USER-TYPE</th>
								<th>CABINET LINE GROUP</th>
							</tr>
						</thead>
						<tfoot class="thead-light">
							<tr>
								<th>NAME</th>
								<th>EMAIL</th>
								<th>PHONE</th>
								<th>ACCOUNT</th>
								<th>USER-TYPE</th>
								<th>CABINET LINE GROUP</th>
							</tr>
						</tfoot>
						<tbody id="empBody">
						<?php 
							$sql = "select mu.id, a.busName as account, a.description, mus.id as userTypeId, mus.name as userType, mu.firstName, mu.lastName, mu.email, mu.phone, clg.id clid, clg.name as cabinetLine,a.id aid,mu.defaultCLid ";
							$sql .= "from mosUser mu, account a, mosUserTypes mus, cabinetLineGroup clg "; 
							switch($_SESSION["userType"]){
								case '3':
									$sql .= "where mu.account = a.id and mu.userType = mus.id and mu.CLGroup = clg.id";
									break;
								case '2':
									$sql .= "where mu.account = a.id and mu.userType = mus.id and mu.CLGroup = clg.id and mu.account=".$_SESSION["account"];
									break;
							}														
							$result = opendb($sql);
							while ( $row = $result->fetch_assoc())  {
								echo "<tr id=\"".$row["id"]."\" onclick=\"profileView(['".implode('\', \'', $row)."'])\">";
								echo "<td><b>".$row["firstName"]." ".$row["lastName"]."</b></td>";
								echo "<td>".$row["email"]."</td>";
								echo "<td>".$row["phone"]."</td>";
								echo "<td>".$row["account"]."</td>";
								echo "<td>".$row["userType"]."</td>";
								echo "<td>".$row["cabinetLine"]."</td>";
								echo "</tr>";								
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="confirmPassword" tabindex="-1" role="dialog" aria-labelledby="confirmPassword" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="was-validated">
      <div class="modal-body">
      	<div id="oldPsw" class="form-label-group">
            <input name="Previous Password" type="password" id="pw" class="form-control" placeholder="Password" required autofocus>
            <label for="pw">Old Password</label>
        </div>
        <div class="form-label-group">
            <input name="New Password" type="password" id="pw2" class="form-control" placeholder="Password" required autofocus>
            <label for="pw2">New Password</label>
        </div>
        <div class="form-label-group">
            <input name="New Password (again)" type="password" id="pw3" class="form-control" placeholder="Password" required>
            <label for="pw3">Confirm New Password</label>
        </div>
      </div>
  	  </form>
      <div class="modal-footer">
      	<button type="button" class="btn btn-primary mx-auto" onclick="savePw();">Update Password</button>
      </div>
    </div>
  </div>
</div>
<div id="addUser" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addUser" aria-hidden="true">
	<div class="modal-dialog modal-lg">
	    <div class="modal-content p-1">
	    	<div class="modal-header">
		        <h5 class="modal-title">Add New User</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		    </div>
		    <form class="was-validated" id="newUser" onsubmit="return createUser();">
      			<div class="modal-body">
      				<div class="container-fluid">
      					<div class="row">
      						<div class="col-12">
	      						<div class="form-group">
							    	<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text">Email</span>
										</div>
										<input onchange="checkUser(this.value);" type="email" class="form-control" placeholder="example@mobel.ca" name="nusrEmail" id="nusrEmail" maxlength="30" aria-describedby="nusrEmail" required>
									</div>
									<div id="alertEmail" class="alert alert-danger fade show" role="alert">
									  <strong>Warning.</strong> This email is already in use, please use another.
									  <!--button type="button" class="close" data-dismiss="alert" aria-label="Close">
									    <span aria-hidden="true">&times;</span>
									  </button-->
									</div>
								</div>
							</div>
						</div>
						<div class="row d-flex justify-content-between">
							<div class="col-6">
								<div class="form-group">
									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text">Password</span>
										</div>
										<input onChange="confPass();" type="password" class="form-control" name="nusrPassword" id="nusrPassword" maxlength="30" aria-describedby="nusrPassword" required>	
									</div>
								</div>
							</div>	
							<div class="col-6">
								<div class="form-group">
									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text">Confirm Password</span>
										</div>
										<input onChange="confPass();" type="password" class="form-control" id="nusrConfPassword" maxlength="30" aria-describedby="nusrConfPassword" required>
									</div>
								</div>
							</div>
							<div class="col-12">	
								<div id="alertPass" class="alert alert-danger fade show" role="alert">
									<strong>Warning.</strong> Passwords doesn't match, please try again.
								</div>
							</div>
						</div>
					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text">Name</span>
						</div>
						<input type="text" class="form-control" name="nfirstName" id="nfirstName" maxlength="30" aria-describedby="nfirstName" required>									
					</div>
					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text">Last Name</span>
						</div>
						<input type="text" class="form-control" name="nlastName" id="nlastName" maxlength="30" aria-describedby="nlastName" required>									
					</div>								
					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text">Phone</span>
						</div>
						<input type="number" class="form-control" name="nphone" id="nphone" min="0" maxlength="20" aria-describedby="nphone">									
					</div>	
					<div class="row d-flex justify-content-between">
							<div class="col-6">
								<div class="form-group">							
									<div class="input-group mb-4">
										<div class="input-group-append">
											<span class="input-group-text">Account</span>
										</div>
										<?php
										$flag = true;
										opendb2("SELECT * FROM account");
										echo "<select ";
										if($_SESSION["userType"] != '3')
											echo "disabled ";
										echo "id=\"naccount\" placeholder=\"Account\" class=\"form-control\" name=\"naccount\" required>";
										if($GLOBALS['$result2']->num_rows > 0){			
											foreach ($GLOBALS['$result2'] as $row2) {
												if($flag){
													echo "<option value=\"\">Please choose an account</option>" ;
													$flag = false;
													echo "<option value=\"".$row2['id']."\">".$row2['busName']."</option>" ;
												}else{
													echo "<option value=\"".$row2['id']."\">".$row2['busName']."</option>" ;
												}
											}
										}
										echo "</select>";
										?>
									</div>
								</div>
							</div>
							<div class="col-6">
								<div class="form-group">
									<div class="input-group mb-4">
										<div class="input-group-append">
											<span class="input-group-text">User Type</span>
										</div>
										<?php
										$flag = true;
										if($_SESSION["userType"]=='3')
											opendb2("SELECT * FROM mosUserTypes");
										if($_SESSION["userType"]=='2')
											opendb2("SELECT * FROM mosUserTypes where id <=".$_SESSION["userType"]);
										if($_SESSION["userType"]=='1')
											opendb2("SELECT * FROM mosUserTypes where id =".$_SESSION["userType"]);
										echo "<select id=\"nuserType\" placeholder=\"User types\" class=\"form-control\" name=\"nuserType\" required>";
										if($GLOBALS['$result2']->num_rows > 0){			
											foreach ($GLOBALS['$result2'] as $row2) {
												if($flag){
													echo "<option value=\"\">Please choose a user type</option>" ;
													$flag = false;
													echo "<option value=\"".$row2['id']."\">".$row2['name']."</option>" ;
												}else{
													echo "<option value=\"".$row2['id']."\">".$row2['name']."</option>" ;
												}
											}
										}
										echo "</select>";
										?>
									</div>
								</div>
							</div>
						</div>
						<div class="input-group mb-4" <?php if($_SESSION["userType"]!='3') echo "hidden" ?>>
							<div class="input-group-append">
								<span class="input-group-text">Cabinet Line Group</span>
							</div>
							<?php
							$flag = true;
							if($_SESSION["userType"]=='3')
								opendb2("SELECT * FROM cabinetLineGroup");
							if($_SESSION["userType"]=='2')
								opendb2("SELECT * FROM cabinetLineGroup where id=".$_SESSION["CLGroup"]);
							if($_SESSION["userType"]=='1')
								opendb2("SELECT * FROM cabinetLineGroup where id=".$_SESSION["CLGroup"]);
							echo "<select id=\"nCLGroup\" placeholder=\"Cabinet Line Group\" class=\"form-control\" name=\"nCLGroup\" required>";
							if($GLOBALS['$result2']->num_rows > 0){			
								foreach ($GLOBALS['$result2'] as $row2) {
									if($flag){
										echo "<option value=\"\">Please choose a cabinet line group</option>" ;
										$flag = false;
										echo "<option value=\"".$row2['id']."\">".$row2['Name']."</option>" ;
									}else{
										echo "<option value=\"".$row2['id']."\">".$row2['Name']."</option>" ;
									}
								}
							}
							echo "</select>";
							?>
					    </div>
					    <div class="input-group mb-4">
							<div class="input-group-append">
								<span class="input-group-text">Default Cabinet Line</span>
							</div>
							<?php
							$flag = true;
							opendb2("SELECT * FROM cabinetLine");
							echo "<select id=\"ndefaultCLid\" placeholder=\"Default Cabinet Line\" class=\"form-control\" name=\"ndefaultCLid\" required>";
							if($GLOBALS['$result2']->num_rows > 0){			
								foreach ($GLOBALS['$result2'] as $row2) {
									if($flag){
										echo "<option value=\"\">Please choose a cabinet line group</option>" ;
										$flag = false;
										echo "<option value=\"".$row2['id']."\">".$row2['CabinetLine']."</option>" ;
									}else{
										echo "<option value=\"".$row2['id']."\">".$row2['CabinetLine']."</option>" ;
									}
								}
							}
							echo "</select>";
							?>
					    </div>
					</div>
					<div class="modal-footer">
		      			<button id="btnNewUsr" type="submit" class="btn btn-primary mx-auto" >Submit</button>
					</div>
				</div>
			</form>
	  	</div>
	</div>
</div>
<div id="addAccount" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addAccount" aria-hidden="true">
	<div class="modal-dialog modal-lg">
	    <div class="modal-content p-1">
	    	<div class="modal-header">
		        <h5 class="modal-title">Add New Account</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		    </div>
		    <form class="was-validated" id="newAccount" onsubmit="return createAccount();">
      			<div class="modal-body pb-1">
			    	<!--div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text">Bussines Number</span>
						</div>
						<input type="number" class="form-control" name="busNumber" id="busNumber" min="0" aria-describedby="busNumber">				
					</div-->
					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text">Bussines Name</span>
						</div>
						<input type="text" class="form-control" placeholder="Business Name" name="busName" id="busName" maxlength="50" aria-describedby="busName" required>					
					</div>
					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text">Business DBA</span>
						</div>
						<input type="text" class="form-control" placeholder="Business Name Corp" name="busDBA" id="busDBA" maxlength="50" aria-describedby="busDBA" required>		
					</div>
					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text">Phone</span>
						</div>
						<input type="number" class="form-control" name="phone" id="phone" min="0" placeholder="19895557777" maxlength="20" aria-describedby="phone">
					</div>								
					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text">Discount</span>
						</div>
						<input type="number" class="form-control" placeholder="Value between 0 and 1 (e.g. .65)" name="discount" id="discount" min="0" max="1" step=".01" aria-describedby="discount" required>									
					</div>
					<!--div class="input-group mb-4" hidden>
						<div class="input-group-prepend">
							<span class="input-group-text">Description</span>
						</div>
						<input type="text" class="form-control" name="description" id="description" maxlength="100" aria-describedby="description">	
					</div-->							
					<div class="input-group mb-4">
						<div class="input-group-append">
							<span class="input-group-text">Cabinet Line Group</span>
						</div>
						<?php
						opendb2("SELECT * FROM cabinetLineGroup");
						echo "<select id=\"CLGroup\" placeholder=\"Cabinet Line Group\" class=\"form-control\" name=\"CLGroup\" required>";
						if($GLOBALS['$result2']->num_rows > 0){			
							foreach ($GLOBALS['$result2'] as $row2) {
								if($flag){
									echo "<option value=\"\">Please choose a cabinet line group</option>" ;
									$flag = false;
									echo "<option value=\"".$row2['id']."\">".$row2['Name']."</option>" ;
								}else{
									echo "<option value=\"".$row2['id']."\">".$row2['Name']."</option>" ;
								}
							}
						}
						echo "</select>";
						?>
				    </div>
				</div>
				<div class="modal-footer py-1">
	      			<button type="submit" class="btn btn-primary mx-auto" >Submit</button>
				</div>
			</form>
	  	</div>
	</div>
</div>
<div id="addAccountAddress" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addAccountAddress" aria-hidden="true">
	<div class="modal-dialog modal-lg">
	    <div class="modal-content">
	    	<div class="modal-header">
		        <h5 class="modal-title">Add New Account Address</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		    </div>
		    <form class="was-validated" id="newAccountAddress" onsubmit="return createAccountAddress();">
      			<div class="modal-body pb-1">
      				<div class="container-fluid">
      					<div class="row">
      						<div class="col-6">
	      						<div class="form-group">
				      				<div class="input-group mb-4">
										<div class="input-group-append">
											<span class="input-group-text">Account</span>
										</div>
										<?php
										$flag = true;
										opendb2("SELECT * FROM account");
										echo "<select id=\"aid\" class=\"form-control\" name=\"aid\" required>";
										if($GLOBALS['$result2']->num_rows > 0){			
											foreach ($GLOBALS['$result2'] as $row2) {
												if($flag){
													echo "<option value=\"\">Please choose an account</option>" ;
													$flag = false;
													echo "<option value=\"".$row2['id']."\">".$row2['busName']."</option>" ;
												}else{
													echo "<option value=\"".$row2['id']."\">".$row2['busName']."</option>" ;
												}
											}
										}
										echo "</select>";
										?>
								    </div>
								</div>
							</div>
							<div class="col-6">
	      						<div class="form-group">
								    <div class="input-group mb-4">
										<div class="input-group-append">
											<span class="input-group-text">Address Type</span>
										</div>
										<?php
										$flag = true;
										opendb2("SELECT * FROM addressType");
										echo "<select id=\"aType\" class=\"form-control\" name=\"aType\" required>";
										if($GLOBALS['$result2']->num_rows > 0){			
											foreach ($GLOBALS['$result2'] as $row2) {
												if($flag){
													echo "<option value=\"\">Please choose an address type</option>" ;
													$flag = false;
													echo "<option value=\"".$row2['id']."\">".$row2['name']."</option>" ;
												}else{
													echo "<option value=\"".$row2['id']."\">".$row2['name']."</option>" ;
												}
											}
										}
										echo "</select>";
										?>
								    </div>
								</div>
							</div>
						</div>
						<div class="row d-flex justify-content-between">
							<div class="col-12">
								<div class="form-group">
									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text">Contact Name</span>
										</div>
										<input type="text" class="form-control" placeholder="Name and last name" name="contactName" id="contactName" maxlength="50" aria-describedby="contactName" required>					
									</div>
								</div>
							</div>
						</div>
						<div class="row d-flex justify-content-between">
							<div class="col-6">
								<div class="form-group">
									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text">Contact Email</span>
										</div>
										<input type="email" class="form-control" placeholder="example@mobel.ca" name="contactEmail" id="contactEmail" maxlength="50" aria-describedby="contactEmail" required>
									</div>
								</div>
							</div>
							<div class="col-6">
								<div class="form-group">
									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text">Contact Phone</span>
										</div>
										<input type="number" class="form-control" name="contactPhone" id="contactPhone" min="0" placeholder="19895557777" maxlength="20" aria-describedby="contactPhone" required>
									</div>								
								</div>								
							</div>
						</div>
						<div class="row d-flex justify-content-between">
							<div class="col-4 pr-1">
								<div class="form-group">
									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text">Country</span>
										</div>
										<select name="country" id="country" class="form-control" required>	
											<option value="Canada">Canada</option>
										</select>								
									</div>
								</div>
							</div>
							<div class="col-4 px-1">
								<div class="form-group">
									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text">Province</span>
										</div>
										<select name="province" id="province" class="form-control" required>	
											<option value="Alberta">Alberta - AB</option>
											<option value="British Columbia">British Columbia - BC</option>
											<option value="Manitoba">Manitoba - MB</option>
											<option value="New Brunswick">New Brunswick - NB</option>
											<option value="Newfoundland and Labrador">Newfoundland and Labrador - NL</option>
											<option value="Nova Scotia">Nova Scotia - NS</option>
											<option selected value="Ontario">Ontario - ON</option>
											<option value="Prince Edward Island">Prince Edward Island - PE</option>
											<option value="Quebec">Quebec - QC</option>
											<option value="Saskatchewan">Saskatchewan - SK</option>
										</select>									
									</div>		
								</div>		
							</div>
							<div class="col-4 pl-1">
								<div class="form-group">
									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text">City</span>
										</div>
										<input type="text" class="form-control" placeholder="Hamilton" name="city" id="city" aria-describedby="city" required>	
									</div>
								</div>
							</div>
						</div>
					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text">Street and Number</span>
						</div>
						<input type="text" class="form-control" placeholder="111 Brockley Dr" name="street" id="street" aria-describedby="street" required>									
					</div>
					<div class="row d-flex justify-content-between">
						<div class="col-6">
							<div class="form-group">	
								<div class="input-group mb-4">
									<div class="input-group-prepend">
										<span class="input-group-text">Unit</span>
									</div>
									<input type="text" class="form-control" placeholder="1A" name="unit" id="unit" aria-describedby="unit">									
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="form-group">
								<div class="input-group mb-4">
									<div class="input-group-prepend">
										<span class="input-group-text">Postal code</span>
									</div>
									<input type="text" class="form-control" placeholder="L8E3C4" name="postalCode" id="postalCode" minlength="6" maxlength="6" aria-describedby="postalCode" required>									
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer py-1">
	      			<button type="submit" class="btn btn-primary mx-auto" >Submit</button>
				</div>
			</form>
	  	</div>
	</div>
</div>
<?php include 'includes/foot.php';?>
<script>
$(document).ready(function () {
	$('[data-toggle="tooltip"]').tooltip();
	$('#alertEmail').hide();
	table = $('#tbEmp').DataTable({
		"order": [[ 3, "asc" ]],
		"lengthMenu": [50, 100]
	});
	$("#pw").val("");
    $("#pw2").val("");
    $("#pw3").val("");
});
</script>
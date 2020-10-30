<?php include 'includes/nav.php';?>
<?php include 'includes/db.php';?>
<script>
function saveUser(objectID){
	$("#"+objectID).css("border-color", "#ba0000");
	myData = { mode: "updateUser", id: objectID, value: $("#"+objectID).val()};
	$.post("OrderItem.php",
			myData, 
		       function(data, status, jqXHR) {
            		if(status == "success"){
            	    	$("#"+objectID).css("border-color", "#00b828");
            	    }
		        });
}
function savePw(){
	myData = { mode: "updatePassword", pw: $("#pw").val(), pw2: $("#pw2").val(), pw3:$("#pw3").val() };
	if($("#pw2").val()!=$("#pw3").val()){
		alert("Sorry, your new passwords don't match. Please try again");
	}else{
    	$.post("OrderItem.php",
    			myData, 
    		       function(data, status, jqXHR) {
                		if(status == "success"){
                    		alert('Password updated successfully.');
                	    	//$("#"+objectID).css("border-color", "#00b828");
                	    }else{
                    	    alert('Sorry, something went wrong. Did you get the old password right? Please reload the page and try again.');
                	    }
    		        });
	}
}
</script>

<div class="navbar navbar-expand-sm bg-light navbar-light">
	<div class="col-sm-12 col-md-11 col-lg-11 mx-auto">
		<?php
		$sql = "select m.firstName,m.lastName,a.description,m.email, m.phone from account a, mosUser m where a.id = m.account and email = '" . $_SESSION["username"] . "'";
		opendb($sql);
		//echo $sql;

		if($GLOBALS['$result']->num_rows > 0){
			foreach ($GLOBALS['$result'] as $row) {
				echo "<div class=\"row\">";
					echo "<div class=\"col-md-6 col-lg-2\">";
					echo "<label for=\"account\">Account:</label>";
					echo "<textarea disabled onchange=\"alert('Sorry, only Mobel can update your company or account name.');\" class=\"form-control noresize\" id=\"account\">";
					echo $row['description'];
					echo "</textarea>";
					echo "</div>";
					
					echo "<div class=\"col-md-6 col-lg-4\">";
					echo "<label for=\"email\">Email Address:</label>";
					echo "<textarea disabled onchange=\"alert('Sorry, only Mobel can update your email address.');\" class=\"form-control noresize\"  id=\"email\">";
					echo $row['email'];
					echo "</textarea>";
					echo "</div>";
					
					echo "<div class=\"col-md-6 col-lg-2\">";
					echo "<label for=\"firstName\">First Name:</label>";
					echo "<textarea onchange=\"saveUser('firstName');\" class=\"form-control noresize\" id=\"firstName\">";
					echo $row['firstName'];
					echo "</textarea>";
					echo "</div>";
					
					echo "<div class=\"col-md-6 col-lg-2\">";
					echo "<label for=\"lastName\">Last Name:</label>";
					echo "<textarea onchange=\"saveUser('lastName');\" class=\"form-control noresize\"  id=\"lastName\">";
					echo $row['lastName'];
					echo "</textarea>";
					echo "</div>";
					
					echo "<div class=\"col-md-6 col-lg-2\">";
					echo "<label for=\"phone\">Phone:</label>";
					echo "<textarea onchange=\"saveUser('phone');\" class=\"form-control noresize\"  id=\"phone\">";
					echo $row['phone'];
					echo "</textarea>";
					echo "</div>";
				echo "</div>";
				
				//echo "<div class=\"col-sm-2 col-md-2 col-lg-2  align-self-center\">";
				//echo "<div class=\"btn-group\">";
				//if($row['status'] == "Quoting"){
					//echo "<button type=\"button\" data-toggle=\"modal\" data-target=\"#submitToMobel\"class=\"btn btn-primary\">Reset My Password</button>";
				//}
				//echo "</div></div>";
			}
		}
		//<form class="form-signin"  method="post">
		?>
	</div>
</div> 

<div class="container">
    <div class="row">
      <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card card-signin my-5">
          <div class="card-body">
            <h5 class="card-title text-center">Change Password</h5>
            
              <input type="hidden" name="mode" id="mode" value = "updatePassword">
              <div class="form-label-group">
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
              <button class="btn btn-lg btn-primary btn-block text-uppercase" onClick="savePw();" >Save New Password</button>
            
          </div>
        </div>
      </div>
    </div>
  </div>
      
<?php include 'includes/foot.php';?>


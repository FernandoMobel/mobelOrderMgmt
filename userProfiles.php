<?php include 'includes/nav.php';?>
<?php include 'includes/db.php';?>
<script>
function profileView(user){
	$('#ordDet').empty();
	myData = { mode:"getOrdDet", uid:user[0]};
	$.ajax({
	url: 'userActions.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {
					console.log(jqXHR['responseText']);					
					$('#ordDet').append(jqXHR['responseText']);
				}
	});
	$('#usrEmail').val(user[7]);
	$('#usrName').val(user[5]);
	$('#usrLastName').val(user[6]);
	$('#usrPhone').val(user[8]);
	$('#account').val(user[11]);
	$('#userTypes').val(user[3]);
	$('#CLGroup').val(user[9]);
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
				<!--div class="card-header">
				  <h5 id="lbUsrName"><h5>
				</div-->
				<div class="card profile-card-2">
                    <div class="card-body pt-5">
						<div class="row">
							<div class="col-3">
								<input type="image" src="https://randomuser.me/api/portraits/men/64.jpg" class="profile"/>
								<div id="ordDet">
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
								$sql = "select mu.id, a.busName as account, a.description, mus.id as userTypeId, mus.name as userType, mu.firstName, mu.lastName, mu.email, mu.phone, clg.id clid, clg.name as cabinetLine,a.id aid ";
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
									<div class="input-group-prepend">
										<span class="input-group-text">New Password</span>
									</div>
									<input type="password" class="form-control" name="usrPassword" id="usrPassword" maxlength="30" aria-describedby="usrPassword">		
									<div class="input-group-append">
										<button class="btn-primary form-control" type="button">Update</button>
									</div>
								</div>
								<div class="input-group mb-4">
									<div class="input-group-prepend">
										<span class="input-group-text">Name</span>
									</div>
									<input value="<?php echo $row2['firstName']?>" type="text" class="form-control" name="usrName" id="usrName" maxlength="30" aria-describedby="usrName">									
								</div>
								<div class="input-group mb-4">
									<div class="input-group-prepend">
										<span class="input-group-text">Last Name</span>
									</div>
									<input value="<?php echo $row2['lastName']?>" type="text" class="form-control" name="usrLastName" id="usrLastName" maxlength="30" aria-describedby="usrLastName">									
								</div>								
								<div class="input-group mb-4">
									<div class="input-group-prepend">
										<span class="input-group-text">Phone</span>
									</div>
									<input value="<?php echo $row2['phone']?>" type="phone" class="form-control" name="usrPhone" id="usrPhone" maxlength="30" aria-describedby="usrPhone">									
								</div>								
								<div class="input-group mb-4">
									<div class="input-group-append">
										<span class="input-group-text">Account</span>
									</div>
									<?php
									opendb2("SELECT * FROM account");
									echo "<select ";
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
									echo "<select id=\"userTypes\" placeholder=\"User types\" class=\"form-control\" name=\"userTypes\">";
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
									echo "<select id=\"CLGroup\" placeholder=\"Cabinet Line Group\" class=\"form-control\" name=\"CLGroup\">";
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
							</div>
						</div>
                    </div>
                </div>
    		</div>
		</div>
		<div id="usersTb" class="col-8" <?php if($_SESSION["userType"]==1) echo "hidden";?>>
			<div class="card">
				<div class="card-body">
					<table id="tbEmp" class="table text-center table-hover" style="width:100%">
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
							$sql = "select mu.id, a.busName as account, a.description, mus.id as userTypeId, mus.name as userType, mu.firstName, mu.lastName, mu.email, mu.phone, clg.id clid, clg.name as cabinetLine,a.id aid ";
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
<?php include 'includes/foot.php';?>
<script>
$(document).ready(function () {
	/*if(<?php echo $_SESSION["userType"];?> ===1){
		$('#usersTb').hide();
	}else{
		$('#usersTb').show();
	}*/
	/*myData = { mode: "getUserList"};
	$.ajax({
	url: 'userActions.php',
	type: 'POST',
	data: myData,
	success: function(data, status, jqXHR) {
					console.log(jqXHR['responseText']);
					users =jqXHR['responseText'];
				}
	});
	var data = [["Izabela Klimczak","izabela@warehouseguys.com","5197197582","Warehouse Guys","Standard","Mobel Designer"],["Laubri Designer\nyy","tammy@laubri.com","5255889118","Laubri Creations","Standard","Mobel Designer"],["Melissa Cruz","melissac@modokitchens.ca","0","Modo Kitchens","Standard","Mobel Designer"],["Adam Strecker","eyecandykitchens@gmail.com","9053271076","EYE CANDY KITCHENS","Standard","Mobel Designer"],["Wolf Glaw","wolf@truenorthsalesandmarketing.ca","9055181793","True North Sales and Marketing","Admin","Mobel Designer"],["Shawn O Neil","shawn@laubri.com","5197371212","Laubri Creations","Admin","Mobel Designer"],["Pino1 Plati1","pino@modokitchens.ca","19052660117","Modo Kitchens","Admin","Mobel Designer"],["Dan LastName","denronkitchens@cogeco.net","9059848778","Denron Kitchens","Admin","Mobel Designer"],["Kevin Lawrence","klawrence@warehouseguys.com","5199510554","Warehouse Guys","Admin","Mobel Designer"],["Christine ","designpro.dovetail@gmail.com","0","Dovetail Interiors","Admin","Mobel Designer"],["Mounir Bishouty","mounir@fabucabinetry.com","5199199899","Fabu Kitchens","Admin","Mobel Designer"],["Sam Malekzai","sam@millokitchens.ca","4165227425","Millo Kitchens","Admin","Mobel Designer"],["Rosheen Malekzai","rosheen@millokitchens.ca","4163206167","Millo Kitchens","Admin","Mobel Designer"],["Ray Bucci","rtbucci@rogers.com","9057197626","Portofino Kitchens","Admin","Mobel Designer"],["Dao Nguyen","dao.nguyen@mdstudio.ca","9055316033","Modern Design Studio","Admin","Mobel Designer"],["Varsha Patel","millcreekkitchensdirect@gmail.com","0","Millcreek Kitchens Direct","Admin","Mobel Designer"],["Rick Sihra","orders@mobel.ca","9051231234","Mobel","Mobel","Mobel Designer"],["Rebecca Bucci","rebecca@mobel.ca","9055707626","Mobel","Mobel","Mobel Designer"],["Ahmad Jamal","ahmad@mobel.ca","0","Mobel","Mobel","Mobel Designer"],["Kiran Waraich","kiran@mobel.ca","0","Mobel","Mobel","Mobel Designer"],["Clinia Zimmerman","clinia@mobel.ca","0","Mobel","Mobel","Mobel Designer"],["Reem Khatab","reem@mobel.ca","0","Mobel","Mobel","Mobel Designer"],["Jombly Maquiling","jom@mobel.ca","0","Mobel","Mobel","Mobel Designer"],["Besnik Pojani","besnik@mobel.ca","0","Mobel","Mobel","Mobel Designer"],["William Alonso","william@mobel.ca","0","Mobel","Mobel","Mobel Designer"],["Papu Sihra","papu@mobel.ca","0","Mobel","Mobel","Mobel Designer"],["1 Department","shipping@mobel.ca","12876879865","Mobel","Mobel","Mobel Designer"],["2 Department","wrapping@mobel.ca","12876879865","Mobel","Mobel","Mobel Designer"],["8 Department","sanding@mobel.ca","12876879865","Mobel","Mobel","Mobel Designer"],["9 Department","cnc@mobel.ca","12876879865","Mobel","Mobel","Mobel Designer"],["Sam Okon-Okpo","sam@mobel.ca","0","Mobel","Mobel","Mobel Medical"],["Mark Elgersma","markelgers@gmail.com","2895010406","Elgersma Electric LLC","Mobel","Mobel Designer and Builder"],["Fernando Guazo","Fernando@Mobel.ca","12876879865","Mobel","Mobel","Mobel All Cabinet Lines"]];
	$('table').DataTable({
		//"order": [[ 2, "asc" ]],
		//"lengthMenu": [[30, 40, 50, "All"]]
		ajax: {
			url: 'userActions.php',
			method: 'POST',
			data: {mode: 'getUserList'}/*,
			success: function(data, status, jqXHR) {
							console.log(jqXHR['responseText']);
							return jqXHR['responseText'];
		}
	});*/
});
</script>
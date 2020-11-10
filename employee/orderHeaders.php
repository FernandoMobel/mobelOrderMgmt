<?php $CLid = $_SESSION["defaultCLid"];?>
<div class="container-fluid">
	<div class="card card-signin my-3">
		<div class="card-header">
			<div class="d-flex justify-content-center">
				<select id="CLines">
					<option value="1">Mobel Designers</option>
					<option value="2">Mobel Builders</option>
					<option value="3">Mobel Medical</option>
				</select>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<!-- --------------------------------------------------------------------SPECIES---------------------------------------------------------------------------- -->
				<div class="col-2 text-right">
					<label>SPECIES</label>
				</div>
				<div class="col-4">
					<select class="headers" id="species" multiple="multiple">
						<?php
						$sql = "select * from species s where s.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CLid.") order by s.name";
						$result = opendb($sql);
						while ( $row = $result->fetch_assoc())  {
							echo "<option ";
							
							if($row["visible"]==1)
								echo "selected ";
							
							echo "value=\"".$row["id"]."\">".$row["name"]."</option>";
						}
						?>
					</select>
				</div>				
				<!-- -----------------------------------------------------------------------INTERIOR FINISH------------------------------------------------------------------------- -->
				<div class="col-2 text-right">
					<label for="interiorFinish">INTERIOR FINISH</label>						
				</div>
				<div class="col-4">
					<select class="headers" id="interiorFinish" multiple="multiple">
						<!--optgroup label="Select All"-->
							<?php
							$sql = "select * from interiorFinish inf where inf.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CLid.") order by inf.name";
							$result = opendb($sql);
							while ( $row = $result->fetch_assoc())  {
								echo "<option ";
							
								if($row["visible"]==1)
									echo "selected ";
								
								echo "value=\"".$row["id"]."\">".$row["name"]."</option>"; //$species[]=$row;
								}
							?>
						<!--/optgroup-->
					</select>
				</div>
			</div>
			<div class="row">
				<!-- -----------------------------------------------------------------------DOOR STYLE------------------------------------------------------------------------- -->
				<div class="col-2 text-right">
					<label for="doorStyle">DOOR STYLE</label>						
				</div>
				<div class="col-4">
					<select class="headersCol" id="doorStyle" multiple="multiple">
						<?php
						$sql = "select id, name from species s where s.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CLid.") order by s.name";						
						$result = opendb($sql);
						while ( $specie = $result->fetch_assoc())  {
							echo "<optgroup class=\"bg-light\" label=\"".$specie["name"]."\">";
							$sql2 = "select d.id, d.name doorStyle, ds.visible from door d, doorSpecies ds where d.CLGroup in(select clg.CLGid FROM cabinetLineGroups clg where clg.CLid = ".$CLid.") and d.id = ds.did and ds.sid = ".$specie["id"];
							$result2 = opendb2($sql2);
							while ( $doorStyle = $result2->fetch_assoc())  {
								echo "<option ";
							
								if($doorStyle["visible"]==1)
									echo "selected ";
								
								echo "value=\"".$doorStyle["id"]."\">". $doorStyle["doorStyle"]."</option>";
							}
							echo "</optgroup>";
						}
						?>
					</select>
				</div>
				<!-- -----------------------------------------------------------------------FINISH------------------------------------------------------------------------- -->
				<div class="col-2 text-right">
					<label for="frontFinish">FINISH</label>						
				</div>
				<div class="col-4">
					<select class="headersCol" id="frontFinish" multiple="multiple">
						<?php
						$sql = "select * from material";						
						$result = opendb($sql);
						while ( $material = $result->fetch_assoc())  {
							echo "<optgroup class=\"bg-light\" label=\"".$material["name"]."\">";
							$sql2 = "select * from frontFinish where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") and finishType in (select ftid from finishTypeMaterial where mid =".$material["id"].") order by name";
							$result2 = opendb2($sql2);
							while ( $frontFinish = $result2->fetch_assoc())  {
								echo "<option ";
							
								if($frontFinish["visible"]==1)
									echo "selected ";
								
								echo "value=\"".$frontFinish["id"]."\">".$frontFinish["name"]."</option>"; 
							}
							echo "</optgroup>";
						}
						?>
					</select>
				</div>
			</div>
			<div class="row">
				<!-- -----------------------------------------------------------------------DRAWER BOXS------------------------------------------------------------------------- -->
				<div class="col-2 text-right">
					<label for="drawerBox">DRAWER BOXS</label>						
				</div>
				<div class="col-4">
					<select class="headers" id="drawerBox" multiple="multiple">
						<!--optgroup label="Select All"-->
						<?php
						$sql = "select * from drawerBox where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name";
						$result = opendb($sql);
						while ( $row = $result->fetch_assoc())  {
							echo "<option ";
							
							if($row["visible"]==1)
								echo "selected ";
							
							echo "value=\"".$row["id"]."\">".$row["name"]."</option>"; 
						}
						?>
					</select>
				</div>
				<!-- -----------------------------------------------------------------------GLAZE------------------------------------------------------------------------- -->
				<div class="col-2 text-right">
					<label for="glaze">GLAZE</label>						
				</div>
				<div class="col-4">
					<select class="headers" id="glaze" multiple="multiple">
						<!--optgroup label="Select All"-->
						<?php
						$sql = "select * from glaze where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name";
						$result = opendb($sql);
						while ( $row = $result->fetch_assoc())  {
							echo "<option ";
							
							if($row["visible"]==1)
								echo "selected ";
							
							echo "value=\"".$row["id"]."\">".$row["name"]."</option>"; 
						}
						?>
					</select>
				</div>
			</div>			
			<div class="row">
				<!-- -----------------------------------------------------------------------SMALL DRAWER FRONT------------------------------------------------------------------------- -->
				<div class="col-2 text-right">
					<label for="smallDrawerFront">SMALL DRAWER FRONT</label>						
				</div>
				<div class="col-4">
					<select class="headers" id="smallDrawerFront" multiple="multiple">
						<!--optgroup label="Select All"-->
						<?php
						$sql = "select * from smallDrawerFront where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name";
						$result = opendb($sql);
						while ( $row = $result->fetch_assoc())  {
							echo "<option ";
							
							if($row["visible"]==1)
								echo "selected ";
							
							echo "value=\"".$row["id"]."\">".$row["name"]."</option>"; 
						}
						?>
					</select>
				</div>
				<!-- -----------------------------------------------------------------------SHEEN------------------------------------------------------------------------- -->
				<div class="col-2 text-right">
					<label for="sheen">SHEEN</label>						
				</div>
				<div class="col-4">
					<select class="headers" id="sheen" multiple="multiple">
						<!--optgroup label="Select All"-->
						<?php
						$sql = "select * from sheen where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name";
						$result = opendb($sql);
						while ( $row = $result->fetch_assoc())  {
							echo "<option ";
							
							if($row["visible"]==1)
								echo "selected ";
							
							echo "value=\"".$row["id"]."\">".$row["name"]."</option>"; 
						}
						?>
					</select>
				</div>
			</div>
			<div class="row">
				<!-- -----------------------------------------------------------------------LARGE DRAWER FRONT------------------------------------------------------------------------- -->
				<div class="col-2 text-right">
					<label for="largeDrawerFront">LARGE DRAWER FRONT</label>						
				</div>
				<div class="col-4">
					<select class="headers" id="largeDrawerFront" multiple="multiple">
						<!--optgroup label="Select All"-->
						<?php
						$sql = "select * from largeDrawerFront where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name";
						$result = opendb($sql);
						//$species = array();
						while ( $row = $result->fetch_assoc())  {
							echo "<option ";
							
							if($row["visible"]==1)
								echo "selected ";
							
							echo "value=\"".$row["id"]."\">".$row["name"]."</option>"; //$species[]=$row;
						}
						?>
					</select>
				</div>
				<!-- -----------------------------------------------------------------------HINGE------------------------------------------------------------------------- -->
				<div class="col-2 text-right">
					<label for="hinge">HINGE</label>						
				</div>
				<div class="col-4">
					<select class="headers" id="hinge" multiple="multiple">
						<!--optgroup label="Select All"-->
						<?php
						$sql = "select * from hinge where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name";
						$result = opendb($sql);
						while ( $row = $result->fetch_assoc())  {
							echo "<option ";
							
							if($row["visible"]==1)
								echo "selected ";
							
							echo "value=\"".$row["id"]."\">".$row["name"]."</option>"; 
						}
						?>
					</select>
				</div>
			</div>
			<div class="row">
				<!-- -----------------------------------------------------------------------DRAWER GLIDES------------------------------------------------------------------------- -->
				<div class="col-2 text-right">
					<label for="drawerGlides">DRAWER GLIDES</label>						
				</div>
				<div class="col-4">
					<select class="headers" id="drawerGlides" multiple="multiple">
						<!--optgroup label="Select All"-->
						<?php
						$sql = "select * from drawerGlides where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name";
						$result = opendb($sql);
						while ( $row = $result->fetch_assoc())  {
							echo "<option ";
							
							if($row["visible"]==1)
								echo "selected ";
							
							echo "value=\"".$row["id"]."\">".$row["name"]."</option>";
						}
						?>
					</select>
				</div>
				<!-- -----------------------------------------------------------------------FINISHED END------------------------------------------------------------------------- -->
				<div class="col-2 text-right">
					<label for="finishedEnd">FINISHED END</label>						
				</div>
				<div class="col-4">
					<select class="headers" id="finishedEnd" multiple="multiple">
						<!--optgroup label="Select All"-->
						<?php
						$sql = "select * from finishedEnd where CLGroup in(select CLGid FROM cabinetLineGroups where CLid = ".$CLid.") order by name";
						$result = opendb($sql);
						//$species = array();
						while ( $row = $result->fetch_assoc())  {
							echo "<option ";
							
							if($row["visible"]==1)
								echo "selected ";
							
							echo "value=\"".$row["id"]."\">".$row["name"]."</option>";
						}
						?>
					</select>
				</div>
			</div>
		</div>             	
	</div>             	
</div>
<?php include '../includes/foot.php';?>
<script type="text/javascript">
    $(document).ready(function() {
		//Cabinet Lines
		$('#CLines').multiselect({
			buttonWidth: '400px'
		});
		
        $('.headers').multiselect({
			buttonWidth: '400px',
            enableClickableOptGroups: true,
			dropRight: true,
			//includeSelectAllOption: true,
			onChange: function(option, checked, select) {
				myData = { mode: "updateHeader", table: $(option).offsetParent()["0"].id, id: $(option).val(), checked: checked};
				$.post("EmployeeMenuSettings.php",
						myData, 
						function(data, status, jqXHR) {
							console.log(jqXHR["responseText"]);
						});
				/*console.log($(option).offsetParent()["0"].id);//table from select element id
				console.log($(option).val());//option id 
				console.log(checked);//Checked or not*/
            }/*,
			selectAllNumber: true,
			onSelectAll: function() {
				console.log($("this").val());
			}*/
        });
		$('.headersCol').multiselect({
			buttonWidth: 400,
			maxHeight: 600,
			dropRight: true,
            enableClickableOptGroups: true,
            enableCollapsibleOptGroups: true,
			collapseOptGroupsByDefault: true
        });
    });
</script>
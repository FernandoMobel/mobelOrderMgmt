<?php include 'includes/nav.php';?>
<?php include 'includes/db.php';?>
<div class="col-sm-13 col-md-11 col-lg-9 mx-auto">
<div class="card card-signin my-5">
<div class="card-body">
<?php
if($_SESSION["userType"]==2){
    $admin = "or m.account = " . $_SESSION["account"];
}
opendb("select m.*,s.name as 'status',u.email from mosOrder m, state s, mosUser u where s.id = m.state and m.mosUser = u.id and (u.email = '" . $_SESSION["username"] . "' ". $admin ."  ) order by m.state asc, m.oid desc ");
echo "<br/><div class=\"container\">";
//echo  "select m.*,s.name as 'status',u.email from mosOrder m, state s, mosUser u where s.id = m.state and m.mosUser = u.id and (u.email = '" . $_SESSION["username"] . "'  )". $admin .")";
if($GLOBALS['$result']->num_rows > 0){
    ?>
    <table id="example" class="display nowrap" style="width:100%">
    <thead>
          <tr>
            <th>OID</th>
            <th>Tag Name</th>
            <th>Status</th>
            <th>PO</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>OID</th>
            <th>Tag Name</th>
            <th>Status</th>
            <th>PO</th>
          </tr>
        </tfoot><tbody>
          <?php
    
    foreach ($GLOBALS['$result'] as $row) {
        echo "<tr>";
        echo "<td><b><a title=\"".$row['email']."\" href=\"Order.php?OID=" . $row['oid'] . "\">".$row['oid']."</b></td>";
        echo "<td><a href=\"Order.php?OID=" . $row['oid'] . "\">". $row['tagName'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['po'] . "</td>";
        echo "</tr>";
    }
    ?>
    </table>
    <?php
}else{
	echo "<h3>No orders yet.</h3><br/><h3>Please create a new order using the \"New\" menu option.</h3>";
}
?>
</div>
</div>
</div>     
      
<?php include 'includes/foot.php';?>
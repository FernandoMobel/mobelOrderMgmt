<?php 
include '../includes/nav.php';
include_once '../includes/db.php';
?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="font-weight-normal text-center">2021 JOBS - BUILDERS / DEALERS / RETAIL</h5>
        </div>
        <div class="card-body">
            <table id="mainTable" class="table table-sm">
                <thead>
                    <tr>
                        <th>MTH</th>
                        <th>DAY</th>
                        <th>YR</th>
                        <th>OID</th>
                        <th>CUSTOMER</th>
                        <th>CONTRACT</th>
                        <th>SALES PERSON</th>
                        <th>DELIVERY DATE</th>
                        <th>RETAIL & CONTRACT AMOUNT</th>
                        <th>BUILDERS & DEALERS</th>
                    </tr>
                </thead>
                <tbody id="tbAccounting">
                <?php 
                $sql = "select month(dateSubmitted) mth, day(dateSubmitted) day, year(dateSubmitted) yr, oid, account,(select a.busName from account a where a.id = mo.account)busName, tagName, (select concat(mu.firstName,' ',mu.lastName) from mosUser mu where mu.id = mo.mosUser )sales, deliveryDate, CAST(dateShipped AS DATE) dateShipped from mosOrder mo where year(dateSubmitted) = 2021 and state > 1 order by dateSubmitted";
                $result = opendb($sql);
                while($row = $result->fetch_assoc()){
                    echo "<tr>
                            <td>".$row['mth']."</td>
                            <td>".$row['day']."</td>
                            <td>".$row['yr']."</td>
                            <td>".$row['oid']."</td>
                            <td>".$row['busName']."</td>
                            <td>".$row['tagName']."</td>
                            <td>".$row['sales']."</td>
                            <td>".$row['dateShipped']."</td>
                            <td></td>
                            <td></td>
                        </tr>";
                }
                ?>
                </tbody>
                <tfoot>
                    <th>MTH</th>
                        <th>DAY</th>
                        <th>YR</th>
                        <th>OID</th>
                        <th>CUSTOMER</th>
                        <th>CONTRACT</th>
                        <th>SALES PERSON</th>
                        <th>DELIVERY DATE</th>
                        <th>RETAIL & CONTRACT AMOUNT</th>
                        <th>BUILDERS & DEALERS</th>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/foot.php';?>
<script>
$(document).ready(function () {
	$('#mainTable').DataTable({
		"order": [[ 0, "asc" ]],
		lengthMenu: [50, 100, 250, "All"],
		stateSave: true
	});
});
</script>
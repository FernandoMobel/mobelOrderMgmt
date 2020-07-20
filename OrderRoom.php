
<?php 
opendb("select * from orderroom where oid = ". $RID ." order by name asc");
if($GLOBALS['$result']->num_rows > 0){
    foreach ($GLOBALS['$result'] as $row) {
        echo "room info";
    }
}else{
    echo "No rooms have been added yet.";
}
?>
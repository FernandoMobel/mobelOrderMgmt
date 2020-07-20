<?php include 'includes/nav.php';?>
<?php include 'includes/db.php';?>
<?php 
opendb("select OID from mosOrder m where mosUser = (select id from mosUser where email = '" . $_SESSION["username"] . "') and m.po is null and m.tagName = 'Tag name not set' and m.state = 1");

if($GLOBALS['$result']->num_rows > 0){
    foreach ($GLOBALS['$result'] as $row) {
        $x = $row['OID'];
    }
    
    opendb("select count(0) as roomCount from orderRoom where oid = " . $x);
    //echo "my x:" . $x;
    //echo "my rows:" . $GLOBALS['$result']->num_rows;
    foreach ($GLOBALS['$result'] as $row) {
        if($row['roomCount'] == 0){
            $sql = "insert into orderRoom (oid,name) values (". $x . ",'1stRoom')";
            //echo $sql;
            opendb($sql);
        }
    }
    
    header("Location: Order.php?OID=" . $x);
}else{
    $sql = "insert into mosOrder (account,mosUser,discount) select m.account,m.id,a.discount from mosUser m, account a where m.account = a.id and m.email = '" . $_SESSION["username"] ."'";
    //echo $sql;
    opendb($sql);
    //opendb("select OID from mosOrder m where mosUser = (select id from mosUser where email = '" . $_SESSION["username"] . "') and m.po is null and m.tagName = 'Tag name not set' and m.state = 1");
    header("Location: newOrder.php");
    //echo "test"; //header("Location: newOrder.php");
}
?>
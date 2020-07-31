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
    echo "<div class=\"bg-warning\">Opening order now... If it doesn't work, please try this link: ";
    echo "<a href=\"Order.php?OID=" . $x . "\">Open order now</a></div>";
    header("Location:https://mos.mobel.ca/Order.php?OID=" . $x);
    echo "<script type='text/javascript'>window.top.location='https://mos.mobel.ca/Order.php?OID=" . $x . "';</script>"; 
exit;

}else{
    $sql = "insert into mosOrder (account,mosUser,discount,CLid) select m.account,m.id,a.discount,m.defaultCLid from mosUser m, account a where m.account = a.id and m.email = '" . $_SESSION["username"] ."'";
    //echo $sql;
    opendb($sql);
    //opendb("select OID from mosOrder m where mosUser = (select id from mosUser where email = '" . $_SESSION["username"] . "') and m.po is null and m.tagName = 'Tag name not set' and m.state = 1");
    echo "Creating your order now...";
    header("Location: newOrder.php");
}
?>
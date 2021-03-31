<?php 
include 'includes/nav.php';
include 'includes/db.php';

opendb("select OID from mosOrder m where mosUser = ".$_SESSION["userid"]." and m.po is null and m.state = 1 and isPriority = ".$_GET['orderType']." and (m.tagName = 'Tag name not set' or m.tagName is null)");
echo "<div class=\"container\">";
if($GLOBALS['$result']->num_rows > 0){
    foreach ($GLOBALS['$result'] as $row) {
        $x = $row['OID'];
    }
    opendb("select count(0) as roomCount from orderRoom where oid = " . $x);
    foreach ($GLOBALS['$result'] as $row) {
        if($row['roomCount'] == 0){
            $sql = "insert into orderRoom (oid,name) values (". $x . ",'1stRoom')";
            //echo $sql;
            opendb($sql);
        }
    }
    //echo "<div>Opening order now... If it doesn't work, please try this link: ";
    //echo "<a href=\"Order.php?OID=" . $x . "\">Open order now</a></div>";
    echo "<div class=\"d-flex justify-content-center align-items-center text-light\" style=\"height: 80vh\">
            <strong>Loading...&nbsp;&nbsp;</strong>
            <div class=\"spinner-border\" style=\"width: 10rem; height: 10rem;\" role=\"status\">                
                <span class=\"sr-only\"></span>
            </div>            
          </div>";
    echo "<form id=\"TheForm\" method=\"post\" action=\"Order.php?OID=".$x."\"><input type=\"text\" name=\"orderTypeNew\" value=\"".$_GET['orderType']."\"></form>";
}else{
    $sql = "insert into mosOrder (account,mosUser,discount,CLid,isPriority) select m.account,m.id,a.discount,m.defaultCLid,".$_GET['orderType']." from mosUser m, account a where m.account = a.id and m.email = '" . $_SESSION["username"] ."'";
    opendb($sql);
    //echo "Creating your order now...";
     echo "<div class=\"d-flex justify-content-center align-items-center text-light\" style=\"height: 80vh\">
            <strong>Creating your order now...&nbsp;&nbsp;</strong>
            <div class=\"spinner-border\" style=\"width: 10rem; height: 10rem;\" role=\"status\">                
                <span class=\"sr-only\"></span>
            </div>            
          </div>";
    echo "<form id=\"TheForm\" method=\"post\" action=\"newOrder.php?orderType=".$_GET['orderType']."\"></form>";
}
echo "</div>";
include 'includes/foot.php';
?>
<script type="text/javascript">
    $(document).ready(function () { 
       $('#TheForm').submit();
    });
</script>

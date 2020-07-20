<?php
//$GLOBALS['$conn'] = new mysqli("127.0.0.1", "MOS", "MOS", "mobel");
$GLOBALS['$conn'] = new mysqli("138.197.170.161", "dqnrmrwrfh", "vuVE9j9wRw", "dqnrmrwrfh");
//$GLOBALS['$conn2'] = new mysqli("127.0.0.1", "MOS", "MOS", "mobel");
$GLOBALS['$conn2'] = new mysqli("138.197.170.161", "dqnrmrwrfh", "vuVE9j9wRw", "dqnrmrwrfh");


//$GLOBALS['$conn2'] = new mysqli("167.99.181.130", "dqnrmrwrfh", "vuVE9j9wRw", "dqnrmrwrfh");

$GLOBALS['$result'] = "";
  //if ($conn->connect_error) {
//    die("ERROR: Unable to connect: " . $conn->connect_error);
//  }
  
  //echo 'Connected to the database.<br>';
  function opendb($sql){
      if(strpos("--",$sql)!==false){
          return "SQL Injection Detected";
      }else{
        $GLOBALS['$result'] = $GLOBALS['$conn']->query($sql);
        return $GLOBALS['$result'];
      }
  }
 
  function closedb(){
      $GLOBALS['$result']->close();    
      $GLOBALS['$conn']->close();
      $GLOBALS['$conn'] = null;
  }
  
  
  
  

  $GLOBALS['$result2'] = "";
  function opendb2($sql){
      if(strpos("--",$sql)!==false){
          return "SQL Injection Detected";
      }else{
        $GLOBALS['$result2'] = $GLOBALS['$conn2']->query($sql);
        return $GLOBALS['$result2'];
      }
  }
  
  function closedb2(){
      $GLOBALS['$result2']->close();
      $GLOBALS['$conn2']->close();
      $GLOBALS['$conn2'] = null;
  }
  
  
  
  /* getPrice returns the price for a given item or subitem given all the required information.
   * It also required method ID which is an identifier in the database to specify how the cost
   * for a given item should be calculated.
   *
   * @base The base price for your item before factors, etc.
   * @sizePrice The price to multiply by the size of your item.
   * @parentPrice The price of this item's parent item. Should be 0 if this is a parent item.
   * @DFactor The door factor of the room this item is in.
   * @IFactor The interior finish factor of the room this item is in.
   *
   * Later should add sheen factor and seperate out species from door factor.
   * Additional factors are currently additive, not multitive IE 2 and 0.3 = 2.3, not 2.6.
   */
  function getPrice($qty, $base, $sizePrice, $parentPrice, $parentPercent,$DFactor,$IFactor, $DApplies, $IApplies, $H, $W, $D, $minSize,  $methodID){
      $size = 0.0;
      $price = 0.0;
      if($methodID == 0){
          $size= $H*$W*$D;
          if($size < $minSize){
              $size = $minSize;
          }
          $price = $base+$size*$sizePrice+$parentPrice*$parentPercent;
          //echo $price;
          //echo $DFactor;
          //echo $IFactor;
          $price = $price*(1+($DFactor-1)*$DApplies+$IFactor*$IApplies);
          
          return round($price*$qty,2);
      }
  }
?>


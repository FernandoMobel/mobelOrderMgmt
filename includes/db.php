<?php
$GLOBALS['$conn'] = new mysqli("138.197.170.161", "dqnrmrwrfh", "vuVE9j9wRw", "dqnrmrwrfh");
$GLOBALS['$conn2'] = new mysqli("138.197.170.161", "dqnrmrwrfh", "vuVE9j9wRw", "dqnrmrwrfh");



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
  
  /*
   * Returns the SQL to get a recordset of item numbers from a given room.
   * 
   * @rid The id of the room from which the item numbers are needed.
   */
  function getItemIDSQL($rid){
      $somesql = "set @num := 0; select  case when rank < 1 then @num := @num + rank else @num:=Truncate(@num := @num + rank,0) end as itemNum,T1.item,T1.sid from
      (SELECT 1.0 as rank,  oi.id as item, 0 as sid
          FROM  orderItem oi, orderRoom orr, doorSpecies ds, interiorFinish irf, item it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg, smallDrawerFront sdf, largeDrawerFront ldf, species sp
          WHERE it.id = oi.iid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = '".$rid."'
          union all
          SELECT  0.1 AS rank,  oi.pid,oi.id as sid
          FROM  orderItemMods oi, orderRoom orr, doorSpecies ds, interiorFinish irf, itemMods it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg,  smallDrawerFront sdf, largeDrawerFront ldf, species sp
          WHERE it.id = oi.mid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = '".$rid."'
          ) as T1 order by item,sid";
      return $somesql;
  }
  
  /*
   * Returns the item number of an item or mod ID.
   *
   * @rid The id of the room this item comes from.
   * @id  The id of the item from which the item numbers are needed.
   * @mod 1 or Y if the item is in the mods table
   * 
   */
  function getItemID($rid, $id, $mod){
      
      $sql = "set @num := 0; select  case when rank < 1 then @num := @num + rank else @num:=Truncate(@num := @num + rank,0) end as itemNum,T1.item,T1.sid from
      (SELECT 1.0 as rank,  oi.id as item, 0 as sid
          FROM  orderItem oi, orderRoom orr, doorSpecies ds, interiorFinish irf, item it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg, smallDrawerFront sdf, largeDrawerFront ldf, species sp
          WHERE it.id = oi.iid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = '".$rid."'
          union all
          SELECT  0.1 AS rank,  oi.pid,oi.id as sid
          FROM  orderItemMods oi, orderRoom orr, doorSpecies ds, interiorFinish irf, itemMods it, sheen sh, glaze gl, frontFinish ff,drawerBox db, drawerGlides dg,  smallDrawerFront sdf, largeDrawerFront ldf, species sp
          WHERE it.id = oi.mid and oi.rid = orr.rid and orr.species = ds.sid and orr.species = sp.id and orr.door = ds.did and orr.interiorFinish = irf.id and orr.sheen = sh.id and orr.glaze = gl.id and orr.frontFinish = ff.id and orr.drawerBox = db.id and orr.drawerGlides = dg.id and orr.smallDrawerFront = sdf.id and orr.largeDrawerFront = ldf.id and orr.rid = '".$rid."'
          ) as T1 order by item,sid";
      opendb($sql);
      if($GLOBALS['$result']->num_rows > 0){
          foreach ($GLOBALS['$result'] as $row) {
              if($mod == 0 || strcmp($mod,"Y")){
                  if(row['sid']==$id){
                      return row['itemNum'];
                  }
              }else{
                  if(row['item']==$id){
                      return row['itemNum'];
                  }
              }
          }
      }
      return 1.0;
  }
  
  /* getPrice returns the price for a given item or subitem given all the required information.
   * It also required method ID which is an identifier in the database to specify how the cost
   * for a given item should be calculated.
   *
   * @base The base price for your item before factors, etc.
   * @sizePrice The price to multiply by the size of your item.
   * @parentPrice The price of this item's parent item. Should be 0 if this is a parent item. ** use base price of the parent item
   * @DFactor The door factor of the room this item is in.
   * @IFactor The interior finish factor of the room this item is in.
   *
   * Later should add sheen factor and seperate out species from door factor.
   * Additional factors are currently multiplicative, not additive IE 2 and 1.3 = 2.6, not 2.3.
   */
  function getPrice($qty, $base, $sizePrice, $parentPrice, $parentPercent,$DFactor,$IFactor,$FFactor,$GFactor,$SFactor,$EFactor,$drawerCharge,$smallDrawerCharge,$largeDrawerCharge, $DApplies, $IApplies,$FApplies, $GApplies, $SApplies, $drawers,$smallDrawerFronts,$largeDrawerFronts,$finishedEnds, $H, $W, $D, $minSize,  $methodID, $finishUpcharge){
      $size = $W*$H*$D;
      if($methodID == 1){
          $size = $W*$D;
      }
      if($methodID == 2){
          $size = $H*$D;
      }
      if($methodID == 3){
          $size = $W*$H;
      }
      $price = 0.0;
      $factor = 1.0;
      $upcharge = 0.0;
      if($size < $minSize){
          $size = $minSize;
      }
      if($DFactor>0.0){
          $DFactor = $DFactor -1;
      }
      $price = $base+$size*$sizePrice+$parentPrice*$parentPercent;
      
      $factor = 1.0;
      
      if($DApplies>0){
          $factor += ($DFactor);
      }
      if($IApplies>0){
          $factor += ($IFactor);
      }
      if($FApplies>0){
          $factor *= (1+$FFactor);
      }
      if($GApplies>0){
          $factor *= (1+$GFactor);
      }
      if($SApplies>0){
          $factor *= (1+$SFactor);
      }
      
      $upcharge = $drawerCharge * $drawers + $smallDrawerCharge*$smallDrawerFronts + $largeDrawerCharge*$largeDrawerFronts + $EFactor*$finishedEnds*$H*$D;
      
      
      //if($methodID == 0){
          
          
          //echo $price . " ";
          //echo $factor . " ";
          //echo $upcharge . " ";
          //echo $size . " ";
          //echo $DFactor;
          //echo $IFactor;
          $price = $price*$factor + $upcharge;
          
          return round($price*$qty,2);
      //}
  }
?>
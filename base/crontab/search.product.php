<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $category = "Product";
 $categorySQL = $category."s";
 $newRows = 0;
 $oh = New OH;
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Re:Search Index"
 ]).$oh->core->Element([
  "p", "This tool maintains the Re:Search <em>$category</em> index."
 ]).$oh->core->Element([
  "p", "Fetching source database list..."
 ]);
 $sql = New SQL($oh->core->cypher->SQLCredentials());
 $databases = $oh->core->DatabaseSet("Member");
 $r .= $oh->core->Element([
  "p", "Creating the $category Index if it does not exist..."
 ]);
 $query = "CREATE TABLE IF NOT EXISTS $categorySQL(
  Product_Category text not null,
  Product_Created text not null,
  Product_Description text not null,
  Product_ID varchar(64) not null,
  Product_NSFW text not null,
  Product_Privacy text not null,
  Product_Shop text not null,
  Product_Title text not null,
  Product_Username text not null,
  PRIMARY KEY(Product_ID)
 )";
 $sql->query($query, []);
 $sql->execute();
 $r .= $oh->core->Element([
  "p", "Indexing data..."
 ]);
 foreach($databases as $key => $database) {
  $database = explode(".", $database);
  if(!empty($database[3])) {
   $data = $oh->core->Data("Get", ["shop", $database[3]]);
   $data = $data["Products"] ?? [];
   $purge = $data["Purge"] ?? 0;
   if($purge == 0) {
    foreach($data as $key => $productID) {
     $product = $oh->core->Data("Get", ["product", $productID]);
     $productPurge = $product["Purge"] ?? 0;
     if(!empty($product) && $productPurge == 0) {
      $category = $product["Category"] ?? "Product";
      $created = $product["Created"] ?? $oh->core->timestamp;
      $query = "REPLACE INTO $categorySQL(
       Product_Category,
       Product_Created,
       Product_Description,
       Product_ID,
       Product_NSFW,
       Product_Privacy,
       Product_Shop,
       Product_Title,
       Product_Username
      ) VALUES(
       :Category,
       :Created,
       :Description,
       :ID,
       :NSFW,
       :Privacy,
       :Shop,
       :Title,
       :Username
      )";
      $sql->query($query, [
       ":Category" => $category,
       ":Created" => $created,
       ":Description" => $product["Description"],
       ":ID" => $productID,
       ":NSFW" => $product["NSFW"],
       ":Privacy" => $product["Privacy"],
       ":Shop" => $database[3],
       ":Title" => $product["Title"],
       ":Username" => $product["UN"]
      ]);
      $sql->execute();
      $r .= $oh->core->Element(["p", "$productID... OK"]);
      $newRows++;
     } else {
      $sql = New SQL($oh->core->cypher->SQLCredentials());
      $sql->query("DELETE FROM $categorySQL WHERE Product_ID=:ID", [
       ":ID" => $productID
      ]);
      $sql->execute();
      $r .= $oh->core->Element(["p", "$productID... PURGE"]);
     }
    }
   }
  }
 }
 $r .= $oh->core->Element([
  "p", "Saving..."
 ]);
 $r .= $oh->core->Element([
  "p", "Re:Search indexing complete! $newRows entries indexed on ".$oh->core->timestamp."."
 ]).$oh->core->Element([
  "p", "Done"
 ]);
 echo $r;
?>
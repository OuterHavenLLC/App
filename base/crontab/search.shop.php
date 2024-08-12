<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $category = "Shop";
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
 $databases = $oh->core->DatabaseSet($category);
 $r .= $oh->core->Element([
  "p", "Creating the $category Index if it does not exist..."
 ]);
 $query = "CREATE TABLE IF NOT EXISTS $categorySQL(
  Shop_Created text not null,
  Shop_Description text not null,
  Shop_ID varchar(64) not null,
  Shop_Title text not null,
  Shop_Username text not null,
  Shop_Welcome text not null,
  PRIMARY KEY(Shop_ID)
 )";
 $sql->query($query, []);
 $sql->execute();
 $r .= $oh->core->Element([
  "p", "Indexing data..."
 ]);
 foreach($databases as $key => $database) {
  $database = explode(".", $database);
  if(!empty($database[3])) {
   $data = $oh->core->Data("Get", [$database[2], $database[3]]);
   $owner = $oh->core->Data("Get", ["mbr", $database[3]]);
   $ownerPurge = $owner["Purge"] ?? 0;
   $purge = $data["Purge"] ?? 0;
   if(!empty($data) && !empty($owner) && $ownerPurge == 0 && $purge == 0) {
    $created = $data["Created"] ?? $oh->core->timestamp;
    $dataID = $database[3];
    $query = "REPLACE INTO $categorySQL(
     Shop_Created,
     Shop_Description,
     Shop_ID,
     Shop_Title,
     Shop_Username,
     Shop_Welcome
    ) VALUES(
     :Created,
     :Description,
     :ID,
     :Title,
     :Username,
     :Welcome
    )";
    $sql->query($query, [
     ":Created" => $created,
     ":Description" => $data["Description"],
     ":ID" => $dataID,
     ":Title" => $data["Title"],
     ":Username" => $owner["Login"]["Username"],
     ":Welcome" => $oh->core->PlainText([
      "Data" => $data["Welcome"],
      "HTMLDecode" => 1
     ])
    ]);
    $sql->execute();
    $r .= $oh->core->Element(["p", "$dataID... OK"]);
    $newRows++;
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
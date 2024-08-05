<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $oh = New OH;
 $category = "Extensions";
 $categorySQL = $category;
 $newRows = 0;
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
  Extension_Body text not null,
  Extension_Created text not null,
  Extension_Description text not null,
  Extension_ID varchar(64) not null,
  Extension_Title text not null,
  Extension_Username text not null,
  PRIMARY KEY(Extension_ID)
 )";
 $sql->query($query, []);
 $sql->execute();
 $r .= $oh->core->Element([
  "p", "Indexing data..."
 ]);
 foreach($databases as $key => $database) {
  $database = explode(".", $database);
  if(!empty($database[3]) && !in_array($database[3], $oh->core->RestrictedIDs)) {
   $data = $oh->core->Data("Get", [$database[2], $database[3]]) ?? [];
   $purge = $data["Purge"] ?? 0;
   if(!empty($data) && $purge == 0) {
    $created = $data["Created"] ?? $oh->core->timestamp;
    $dataID = $database[3];
    $query = "REPLACE INTO $categorySQL(
     Extension_Body,
     Extension_Created,
     Extension_Description,
     Extension_ID,
     Extension_Title,
     Extension_Username
    ) VALUES(
     :Body,
     :Created,
     :Description,
     :ID,
     :Title,
     :Username
    )";
    $sql->query($query, [
     ":Body" => $oh->core->Excerpt($oh->core->PlainText([
      "Data" => $data["Body"],
      "Display" => 1,
      "HTMLDecode" => 1
     ]), 1000),
     ":Created" => $created,
     ":Description" => $data["Description"],
     ":ID" => $dataID,
     ":Title" => $data["Title"],
     ":Username" => $data["UN"]
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
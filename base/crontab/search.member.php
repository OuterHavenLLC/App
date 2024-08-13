<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $category = "Member";
 $categorySQL = "Members";
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
  "p", "Creating the $categorySQL Index if it does not exist..."
 ]);
 $query = "CREATE TABLE IF NOT EXISTS $categorySQL(
  Member_Created text not null,
  Member_Description text,
  Member_DisplayName text not null,
  Member_Privacy varchar(64) not null,
  Member_Username varchar(64) not null,
  PRIMARY KEY(Member_Username)
 )";
 $sql->query($query, []);
 $sql->execute();
 $r .= $oh->core->Element([
  "p", "Indexing data..."
 ]);
 foreach($databases as $key => $database) {
  $database = explode(".", $database);
  if(!empty($database[3])) {
   $data = $oh->core->Data("Get", [$database[2], $database[3]]) ?? [];
   $purge = $data["Purge"] ?? 0;
   if(!empty($data) && $purge == 0) {
    $created = $data["Activity"]["Registered"] ?? $oh->core->timestamp;
    $dataID = $database[3];
    $query = "REPLACE INTO $categorySQL(
     Member_Created,
     Member_Description,
     Member_DisplayName,
     Member_Privacy,
     Member_Username
    ) VALUES(
     :Created,
     :Description,
     :DisplayName,
     :Privacy,
     :Username
    )";
    $sql->query($query, [
     ":Created" => $data["Activity"]["Registered"],
     ":Description" => $oh->core->Excerpt($oh->core->PlainText([
      "Data" => $data["Personal"]["Description"],
      "Display" => 1,
      "HTMLDecode" => 1
     ]), 1000),
     ":DisplayName" => $data["Personal"]["DisplayName"],
     ":Privacy" => $data["Privacy"]["Profile"],
     ":Username" => $data["Login"]["Username"]
    ]);
    $sql->execute();
    $r .= $oh->core->Element(["p", "$dataID... OK"]);
    $newRows++;
   } else {
    $sql = New SQL($oh->core->cypher->SQLCredentials());
    $sql->query("DELETE FROM $categorySQL WHERE Member_Username=:ID", [
     ":ID" => $dataID
    ]);
    $sql->execute();
    $r .= $oh->core->Element(["p", "$dataID... PURGE"]);
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
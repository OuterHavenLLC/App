<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $category = "Blog";
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
  Blog_Created text not null,
  Blog_Description text not null,
  Blog_ID varchar(64) not null,
  Blog_NSFW text not null,
  Blog_Privacy text not null,
  Blog_Title text not null,
  Blog_Username text not null,
  PRIMARY KEY(Blog_ID)
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
    $created = $data["Created"] ?? $oh->core->timestamp;
    $dataID = $database[3];
    $query = "REPLACE INTO $categorySQL(
     Blog_Created,
     Blog_Description,
     Blog_ID,
     Blog_NSFW,
     Blog_Privacy,
     Blog_Title,
     Blog_Username
    ) VALUES(
     :Created,
     :Description,
     :ID,
     :NSFW,
     :Privacy,
     :Title,
     :Username
    )";
    $sql->query($query, [
     ":Created" => $created,
     ":Description" => $data["Description"],
     ":ID" => $dataID,
     ":NSFW" => $data["NSFW"],
     ":Privacy" => $data["Privacy"],
     ":Title" => $data["Title"],
     ":Username" => $data["UN"]
    ]);
    $sql->execute();
    $r .= $oh->core->Element(["p", "$dataID... OK"]);
    $newRows++;
   } else {
    $sql = New SQL($oh->core->cypher->SQLCredentials());
    $sql->query("DELETE FROM $categorySQL WHERE Blog_ID=:ID", [
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
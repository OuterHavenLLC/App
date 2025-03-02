<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $category = "Media";
 $newRows = 0;
 $oh = New OH;
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Re:Search Index"
 ]).$oh->core->Element([
  "p", "This tool maintains the Re:Search <em>Media</em> index."
 ]).$oh->core->Element([
  "p", "Fetching source database list..."
 ]);
 $sql = New SQL($oh->core->cypher->SQLCredentials());
 $coreMedia = $oh->core->Data("Get", ["app", "fs"]);
 $databases = $oh->core->DatabaseSet("Member");
 $r .= $oh->core->Element([
  "p", "Creating the $category Index if it does not exist..."
 ]);
 $query = "CREATE TABLE IF NOT EXISTS CoreMedia(
  Media_Created text not null,
  Media_Description text not null,
  Media_ID varchar(128) not null,
  Media_NSFW text not null,
  Media_Privacy text not null,
  Media_Title text not null,
  Media_Username text not null,
  PRIMARY KEY(Media_ID)
 )";
 $sql->query($query, []);
 $sql->execute();
 $query = "CREATE TABLE IF NOT EXISTS $category(
  Media_AlbumID text not null,
  Media_Created text not null,
  Media_Description text not null,
  Media_ID varchar(128) not null,
  Media_NSFW text not null,
  Media_Privacy text not null,
  Media_Title text not null,
  Media_Username text not null,
  PRIMARY KEY(Media_ID)
 )";
 $sql->query($query, []);
 $sql->execute();
 $r .= $oh->core->Element([
  "p", "Indexing data..."
 ]);
 foreach($coreMedia as $file => $info) {
  $created = $info["Created"] ?? $oh->core->timestamp;
  $query = "REPLACE INTO CoreMedia(
   Media_Created,
   Media_Description,
   Media_ID,
   Media_NSFW,
   Media_Privacy,
   Media_Title,
   Media_Username
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
   ":Description" => $info["Description"],
   ":ID" => $file,
   ":NSFW" => $info["NSFW"],
   ":Privacy" => $info["Privacy"],
   ":Title" => $info["Title"],
   ":Username" => $oh->core->ID
  ]);
  $sql->execute();
  $r .= $oh->core->Element(["p", "Core Media $file... OK"]);
  $newRows++;
 } foreach($databases as $key => $database) {
  $database = explode(".", $database);
  if(!empty($database[3])) {
   $fileSystem = $oh->core->Data("Get", ["fs", $database[3]]);
   $fileSystem = $fileSystem["Files"] ?? [];
   $member = $oh->core->Data("Get", ["mbr", $database[3]]);
   if(!empty($fileSystem) && !empty($member)) {
    foreach($fileSystem as $file => $info) {
     $dataID = $file;
     if(!empty($info)) {
      $created = $info["Created"] ?? $oh->core->timestamp;
      $query = "REPLACE INTO $category(
       Media_AlbumID,
       Media_Created,
       Media_Description,
       Media_ID,
       Media_NSFW,
       Media_Privacy,
       Media_Title,
       Media_Username
      ) VALUES(
       :AlbumID,
       :Created,
       :Description,
       :ID,
       :NSFW,
       :Privacy,
       :Title,
       :Username
      )";
      $sql->query($query, [
       ":AlbumID" => $info["AID"],
       ":Created" => $created,
       ":Description" => $info["Description"],
       ":ID" => $dataID,
       ":NSFW" => $info["NSFW"],
       ":Privacy" => $info["Privacy"],
       ":Title" => $info["Title"],
       ":Username" => $member["Login"]["Username"]
      ]);
      $sql->execute();
      $r .= $oh->core->Element(["p", "$dataID... OK"]);
      $newRows++;
     } else {
      $sql = New SQL($oh->core->cypher->SQLCredentials());
      $sql->query("DELETE FROM $category WHERE Media_ID=:ID", [
       ":ID" => $dataID
      ]);
      $sql->execute();
      $r .= $oh->core->Element(["p", "$dataID... PURGE"]);
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
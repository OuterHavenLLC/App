<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $category = "ForumPost";
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
 $databases = $oh->core->DatabaseSet("Forum");
 $r .= $oh->core->Element([
  "p", "Creating the $category Index if it does not exist..."
 ]);
 $query = "CREATE TABLE IF NOT EXISTS $categorySQL(
  ForumPost_Body text not null,
  ForumPost_Created text not null,
  ForumPost_Forum text not null,
  ForumPost_ID varchar(64) not null,
  ForumPost_NSFW text not null,
  ForumPost_Privacy text not null,
  ForumPost_Title text not null,
  ForumPost_Topic text not null,
  ForumPost_Username text not null,
  PRIMARY KEY(ForumPost_ID)
 )";
 $sql->query($query, []);
 $sql->execute();
 $r .= $oh->core->Element([
  "p", "Indexing data..."
 ]);
 foreach($databases as $key => $database) {
  $database = explode(".", $database);
  if(!empty($database[3])) {
   $dataID = $database[3];
   $data = $oh->core->Data("Get", [$database[2], $dataID]);
   $data = $data["Posts"] ?? [];
   $purge = $data["Purge"] ?? 0;
   if(!empty($data) && $purge == 0) {
    foreach($data as $key => $postID) {
     $data = $oh->core->Data("Get", ["post", $postID]);
     $purge = $data["Purge"] ?? 0;
     if(!empty($data) && $purge == 0) {
      $created = $data["Created"] ?? $oh->core->timestamp;
      $query = "REPLACE INTO $categorySQL(
       ForumPost_Body,
       ForumPost_Created,
       ForumPost_Forum,
       ForumPost_ID,
       ForumPost_NSFW,
       ForumPost_Privacy,
       ForumPost_Title,
       ForumPost_Topic,
       ForumPost_Username
      ) VALUES(
       :Body,
       :Created,
       :Forum,
       :ID,
       :NSFW,
       :Privacy,
       :Title,
       :Topic,
       :Username
      )";
      $sql->query($query, [
       ":Body" => $oh->core->PlainText([
        "Data" => $data["Body"],
        "HTMLDecode" => 1
       ]),
       ":Created" => $created,
       ":Forum" => $dataID,
       ":ID" => $postID,
       ":NSFW" => $data["NSFW"],
       ":Privacy" => $data["Privacy"],
       ":Title" => $data["Title"],
       ":Topic" => $data["Topic"],
       ":Username" => $data["From"]
      ]);
      $sql->execute();
      $r .= $oh->core->Element(["p", "$postID... OK"]);
      $newRows++;
     } else {
      $sql = New SQL($oh->core->cypher->SQLCredentials());
      $sql->query("DELETE FROM $categorySQL WHERE ForumPost_ID=:ID", [
       ":ID" => $postID
      ]);
      $sql->execute();
      $r .= $oh->core->Element(["p", "$postID... PURGE"]);
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
<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $category = "BlogPost";
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
 $databases = $oh->core->DatabaseSet("Blog");
 $r .= $oh->core->Element([
  "p", "Creating the $category Index if it does not exist..."
 ]);
 $query = "CREATE TABLE IF NOT EXISTS $categorySQL(
  BlogPost_Blog text not null,
  BlogPost_Body text not null,
  BlogPost_Created text not null,
  BlogPost_Description text not null,
  BlogPost_ID varchar(64) not null,
  BlogPost_NSFW text not null,
  BlogPost_Privacy text not null,
  BlogPost_Title text not null,
  BlogPost_Username text not null,
  PRIMARY KEY(BlogPost_ID)
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
     $data = $oh->core->Data("Get", ["bp", $postID]);
     $purge = $data["Purge"] ?? 0;
     if(!empty($data) && $purge == 0) {
      $created = $data["Created"] ?? $oh->core->timestamp;
      $query = "REPLACE INTO $categorySQL(
       BlogPost_Blog,
       BlogPost_Body,
       BlogPost_Created,
       BlogPost_Description,
       BlogPost_ID,
       BlogPost_NSFW,
       BlogPost_Privacy,
       BlogPost_Title,
       BlogPost_Username
      ) VALUES(
       :Blog,
       :Body,
       :Created,
       :Description,
       :ID,
       :NSFW,
       :Privacy,
       :Title,
       :Username
      )";
      $sql->query($query, [
       ":Blog" => $dataID,
       ":Body" => $oh->core->PlainText([
        "Data" => $data["Body"],
        "Decode" => 1,
        "HTMLDecode" => 1
       ]),
       ":Created" => $created,
       ":Description" => $data["Description"],
       ":ID" => $postID,
       ":NSFW" => $data["NSFW"],
       ":Privacy" => $data["Privacy"],
       ":Title" => $data["Title"],
       ":Username" => $data["UN"]
      ]);
      $sql->execute();
      $r .= $oh->core->Element(["p", "$postID... OK"]);
      $newRows++;
     } else {
      $sql = New SQL($oh->core->cypher->SQLCredentials());
      $sql->query("DELETE FROM $categorySQL WHERE BlogPost_ID=:ID", [
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
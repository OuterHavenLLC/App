<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $category = "Feedback";
 $categorySQL = $category;
 $newRows = 0;
 $oh = New OH;
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Re:Search Index"
 ]).$oh->core->Element([
  "p", "This tool maintains the Re:Search <em>$category</em> index file."
 ]).$oh->core->Element([
  "p", "Fetching source database list..."
 ]);
 $sql = New SQL($oh->core->cypher->SQLCredentials());
 $databases = $oh->core->DatabaseSet($category);
 $r .= $oh->core->Element([
  "p", "Creating the $category Index if it does not exist..."
 ]);
 $query = "CREATE TABLE IF NOT EXISTS $categorySQL(
  Feedback_Created text not null,
  Feedback_ID varchar(64) not null,
  Feedback_Message text not null,
  Feedback_ParaphrasedQuestion text not null,
  Feedback_Subject text not null,
  Feedback_Username text not null,
  PRIMARY KEY(Feedback_ID)
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
    $created = $data["Thread"][0]["Sent"] ?? $oh->core->timestamp;
    $dataID = $database[3];
    $query = "REPLACE INTO $categorySQL(
     Feedback_Created,
     Feedback_ID,
     Feedback_Message,
     Feedback_ParaphrasedQuestion,
     Feedback_Subject,
     Feedback_Username
    ) VALUES(
     :Created,
     :ID,
     :Message,
     :ParaphrasedQuestion,
     :Subject,
     :Username
    )";
    $sql->query($query, [
     ":Created" => $created,
     ":ID" => $dataID,
     ":Message" => $oh->core->Excerpt($oh->core->PlainText([
      "Data" => $data["Thread"][0]["Body"],
      "Display" => 1,
      "HTMLDecode" => 1
     ]), 1000),
     ":ParaphrasedQuestion" => $data["ParaphrasedQuestion"],
     ":Subject" => $data["Subject"],
     ":Username" => $data["Username"]
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
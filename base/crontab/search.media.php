<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $category = "Member";
 $oh = New OH;
 $databases = $oh->core->DatabaseSet($category);
 $index = $oh->core->DataIndex("Get", $category) ?? [];
 $newIndex = [];
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Re:Search Index"
 ]).$oh->core->Element([
  "p", "This tool maintains the Re:Search <em>Media</em> index file."
 ]).$oh->core->Element([
  "p", "Fetching source database list..."
 ]).$oh->core->Element([
  "p", json_encode($databases, true)
 ]);
 foreach($databases as $key => $database) {
  $database = explode(".", $database);
  if(!empty($database[3])) {
   $fileSystem = $oh->core->Data("Get", ["fs", $database[3]]) ?? [];
   $fileSystem = $fileSystem["Files"] ?? [];
   if(!empty($fileSystem)) {
    foreach($fileSystem as $file => $info) {
     $id = $database[3].";$file";
     $info = [
      "ID" => $id,
      "Description" => $info["Description"],
      "Keywords" => "",
      "Title" => $info["Name"]
     ];
     if(!in_array($info, $index)) {
      array_push($index, $info);
      $r .= $oh->core->Element(["p", "$id... OK"]);
     }
    }
   }
  }
 }
 $r .= $oh->core->Element([
  "p", "Saving..."
 ]);
 $oh->core->DataIndex("Save", $category, $index);
 $r .= $oh->core->Element([
  "p", "Re:Search indexing complete!"
 ]).$oh->core->Element([
  "p", "Here is the <em>Media</em> Re:Search Index:"
 ]).$oh->core->Element([
  "p", json_encode($index, true)
 ]).$oh->core->Element([
  "p", "Done"
 ]);
 echo $r;//TEMP
 // SEND AS EMAIL
?>
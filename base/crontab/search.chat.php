<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $category = "Chat";
 $exclude = [
  "5ec1e051bf732d19e09ea9673cd7986b",
  "7216072bbd437563e692cc7ff69cdb69"
 ];
 $oh = New OH;
 $databases = $oh->core->DatabaseSet($category);
 $index = $oh->core->DataIndex("Get", $category) ?? [];
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Re:Search Index"
 ]).$oh->core->Element([
  "p", "This tool maintains the Re:Search <em>$category</em> index file."
 ]).$oh->core->Element([
  "p", "Fetching source database list..."
 ]).$oh->core->Element([
  "p", json_encode($databases, true)
 ]);
 foreach($databases as $key => $database) {
  $database = explode(".", $database);
  if(!empty($database[3])) {
   $chat = $oh->core->Data("Get", [$database[2], $database[3]]) ?? [];
   $isGroup = $chat["Group"] ?? 0;
   if(!in_array($database[3], $exclude) && $isGroup == 1) {
    $info = [
     "ID" => $database[3],
     "Description" => $chat["Description"],
     "Keywords" => "",
     "Title" => $chat["Title"]
    ];
    if(!in_array($info, $index)) {
     array_push($index, $info);
     $r .= $oh->core->Element(["p", implode(".", $database)."... OK"]);
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
  "p", "Here is the <em>$category</em> Re:Search Index:"
 ]).$oh->core->Element([
  "p", json_encode($index, true)
 ]).$oh->core->Element([
  "p", "Done"
 ]);
 echo $r;//TEMP
 // SEND AS EMAIL
?>
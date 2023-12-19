<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $category = "StatusUpdate";
 $oh = New OH;
 $databases = $oh->core->DatabaseSet($category);
 $index = $oh->core->DataIndex("Get", $category) ?? [];
 $newIndex = [];
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
   $data = $oh->core->Data("Get", [$database[2], $database[3]]) ?? [];
   $body = $data["Body"] ?? "";
   if(!empty($body)) {
    $body = htmlentities($body);
    $info = [
     "ID" => $database[3],
     "Description" => $body,
     "Keywords" => "",
     "Title" => $oh->core->Excerpt($body, 180)
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
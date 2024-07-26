<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $oh = New OH;
 $category = "Extensions";
 $databases = $oh->core->DatabaseSet($category);
 $index = "";
 $newRows = 0;
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Re:Search Index"
 ]).$oh->core->Element([
  "p", "This tool maintains the Re:Search <em>$category</em> index file."
 ]).$oh->core->Element([
  "p", "Fetching source database list..."
 ]).$oh->core->Element([
  "p", json_encode($databases, true)
 ]);
 $sql = New SQL($oh->cypher->SQLCredentials());
 foreach($databases as $key => $database) {
  $database = explode(".", $database);
  if(!empty($database[3]) && $database[3] != "cb3e432f76b38eaa66c7269d658bd7ea") {
   $data = $oh->core->Data("Get", [$database[2], $database[3]]) ?? [];
   if(!empty($data)) {
    // SQL
    $index .= $oh->core->Element(["p", "Indexed Extension #".$database[3]."."]);
    $newRows++;
   }
  }
 }
 $r .= $oh->core->Element([
  "p", "Saving..."
 ]);
 #$oh->core->DataIndex("Save", $category, $index);
 $r .= $oh->core->Element([
  "p", "Re:Search indexing complete!"
 ]).$oh->core->Element([
  "p", "Here is the <em>$category</em> Re:Search Index:"
 ]).$oh->core->Element([
  "div", json_encode($index, true), ["class" => "NONAME"]
 ]).$oh->core->Element([
  "p", "Done"
 ]);
 echo $r;
?>
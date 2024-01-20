<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 //require_once("/var/www/html/base/DOM.php"); // Or a new Core DOM object
 $category = "Link";
 $oh = New OH;
 $index = $oh->core->Data("Get", ["app", md5("Links")]) ?? [];
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Re:Search Index"
 ]).$oh->core->Element([
  "p", "This tool maintains the Re:Search <em>$category</em> index file."
 ]).$oh->core->Element([
  "p", "Fetching source database list..."
 ]).$oh->core->Element([
  "p", json_encode($index, true)
 ]);
 foreach($index as $link => $info) {
  // Update Link Data
 }
 $r .= $oh->core->Element([
  "p", "Saving..."
 ]);
 #$oh->core->Data("Save", ["app", md5("Links"), $index]);
 $r .= $oh->core->Element([
  "p", "Re:Search indexing complete!"
 ]).$oh->core->Element([
  "p", "Here is the <em>$category</em> Re:Search Index:"
 ]).$oh->core->Element([
  "p", json_encode($index, true)
 ]).$oh->core->Element([
  "p", "Done"
 ]);
 echo $r;
?>
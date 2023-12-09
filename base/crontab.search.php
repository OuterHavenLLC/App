<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $oh = New OH;
 $databases = $oh->core->DatabaseSet() ?? [];
 $excludeTypes = [
  "app",
  "bulletins",
  "cms",
  "dc",
  "extension",
  "invoice",
  "invoice-preset",
  "local",
  "pfmanifest",
  "po",
  "stream",
  "votes"
 ];
 $indexes = [
  "Article" => [],
  "Blog" => [],
  "BlogPost" => [],
  "Chat" => [],
  "Forum" => [],
  "ForumPost" => [],
  "Media" => [],
  "Member" => [],
  "Poll" => [],
  "Product" => [],
  "Shop" => [],
  "StatusUpdate" => []
 ];
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Re:Search Index"
 ]).$oh->core->Element([
  "p", "This tool maintains the Re:Search index file."
 ]).$oh->core->Element([
  "p", "Fetching source database list..."
 ]).$oh->core->Element([
  "p", json_encode($databases, true)
 ]);
 foreach($databases as $key => $database) {
  $database = explode(".", $database);
  $type = $database[2] ?? "";
  if(!empty($database[3]) && !in_array($type, $excludeTypes)) {
   $index = "";
   $index = ($type == "blg") ? "Blog" : $index;
   $index = ($type == "bp") ? "BlogPost" : $index;
   $index = ($type == "mbr") ? "Member" : $index;
   $index = ($type == "pf") ? "Forum" : $index;
   $index = ($type == "pg") ? "Article" : $index;
   $index = ($type == "poll") ? "Poll" : $index;
   $index = ($type == "product") ? "Product" : $index;
   $index = ($type == "shop") ? "Shop" : $index;
   $index = ($type == "su") ? "StatusUpdate" : $index;
   if($type == "chat") {
    $chat = $oh->core->Data("Get", [$database[2], $database[2]]) ?? [];
    $index = "Chat";
    $isGroup = $chat["Group"] ?? 0;
    if($isGroup == 1) {
     array_push($indexes[$index], $database[3]);
     $r .= $oh->core->Element(["p", "Indexed ".implode(".", $database)."..."]);
    }
   } elseif($type == "fs") {
    $fileSystem = $oh->core->Data("Get", ["fs", $database[3]]) ?? [];
    $fileSystem = $fileSystem["Files"] ?? [];
    if(!empty($fileSystem)) {
     foreach($fileSystem as $file => $info) {
      $id = $database[3].";$file";
      $index = "Media";
      array_push($indexes[$index], $id);
      $r .= $oh->core->Element(["p", "Indexed Media #$id..."]);
     }
    }
   } else {
    if(empty($index)) {
     $r .= $oh->core->Element(["p", "Skipped ".implode(".", $database)."..."]);
    } else {
     array_push($indexes[$index], $database[3]);
     $r .= $oh->core->Element(["p", "Indexed ".implode(".", $database)."..."]);
    }
   }
  }
 }
 $r .= $oh->core->Element([
  "p", "Saving consolidated Re:Search index..."
 ]);
 $oh->core->Data("Save", ["app", md5("Re:Search"), $indexes]);
 $r .= $oh->core->Element([
  "p", "Re:Search indexing complete!"
 ]).$oh->core->Element([
  "p", "Here is the consolidated Re:Search Index:"
 ]).$oh->core->Element([
  "p", json_encode($indexes, true)
 ]).$oh->core->Element([
  "p", "Done"
 ]);
 // SEND AS EMAIL
?>
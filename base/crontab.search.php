<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $oh = New OH();
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
 echo $oh->core->config["App"]["Name"]."</em> Re:Search Index\r\n";
 echo "This tool maintains the Re:Search index file.\r\n";
 echo "Fetching source database list...\r\n";
 echo var_dump($databases)."\r\n";
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
     echo "Indexed ".implode(".", $database)."...\r\n";
    }
   } elseif($type == "fs") {
    $fileSystem = $oh->core->Data("Get", ["fs", $database[3]]) ?? [];
    $fileSystem = $fileSystem["Files"] ?? [];
    if(!empty($fileSystem)) {
     foreach($fileSystem as $file => $info) {
      $id = $database[3].";$file";
      $index = "Media";
      array_push($indexes[$index], $id);
      echo "Indexed Media #$id...\r\n";
     }
    }
   } else {
    if(empty($index)) {
     echo "Skipped ".implode(".", $database)."...\r\n";
    } else {
     array_push($indexes[$index], $database[3]);
     echo "Indexed ".implode(".", $database)."...\r\n";
    }
   }
  }
 }
 echo "Saving consolidated Re:Search index...\r\n";
 $oh->core->Data("Save", ["app", md5("Re:Search"), $indexes]);
 echo "Re:Search indexing complete!\r\n";
 echo "Here is the consolidated Re:Search Index:\r\n";
 echo var_dump($indexes)."\r\n";
 echo "Done";
?>
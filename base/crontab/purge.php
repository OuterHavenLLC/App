<?php
 # Content Purge
 require_once("/var/www/html/base/Bootloader.php");
 $oh = New OH;
 $databases = $oh->core->DatabaseSet();
 $purged = 0;
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Content Purge"
 ]).$oh->core->Element([
  "p", "Eliminates primary databases marked for purging, and deletes associated files for media marked for purging."]);
 $r .= $oh->core->Element(["p", "Checkinf for content to purge..."]);
 foreach($databases as $key => $database) {
  $database = explode(".", $database);
  if(!empty($database[3])) {
   $data = $oh->core->Data("Get", [$database[2], $database[3]]) ?? [];
   $purge = $data["Purge"] ?? 0;
   if(empty($data) || $purge == 1) {
    $purged++;
    $r .= $oh->core->Element(["p", "Purging data and dependencies for ".implode(".", $database)."..."]);
    $oh->core->Data("Purge", [$database[2], $database[3]]);
    if(!empty($oh->core->Data("Get", ["chat", $database[3]]))) {
     $r .= "<p>Chat...";
     $oh->core->Data("Purge", ["chat", $database[3]]);
     $r .= "OK</p>\r\n";
    } if(!empty($oh->core->Data("Get", ["conversation", $database[3]]))) {
     $r .= "<p>Conversation...";
     $oh->core->Data("Purge", ["conversation", $database[3]]);
     $r .= "OK</p>\r\n";
    } if(!empty($oh->core->Data("Get", ["translate", $database[3]]))) {
     $r .= "<p>Translations...";
     $oh->core->Data("Purge", ["translate", $database[3]]);
     $r .= "OK</p>\r\n";
    } if(!empty($oh->core->Data("Get", ["votes", $database[3]]))) {
     $r .= "<p>Votes...";
     $oh->core->Data("Purge", ["votes", $database[3]]);
     $r .= "OK</p>\r\n";
    }
    $r .= $oh->core->Element(["p", "Purged data for ".implode(".", $database)."!"]);
   }
  }
 } if($purged == 0) {
  $r .= $oh->core->Element(["p", "No content to purge!"]);
 }
 $r .= $oh->core->Element(["p", "Done"]);
 echo $r;
?>
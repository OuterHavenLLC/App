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
 $r .= $oh->core->Element(["p", "Purging content..."]);
 foreach($databases as $key => $database) {
  $database = explode(".", $database);
  if(!empty($database[3])) {
   $data = $oh->core->Data("Get", [$database[2], $database[3]]) ?? [];
   $purge = $data["Purge"] ?? 0;
   if($purge == 1) {
    $purged++;
    $r .= "<p>Purging ".implode(".", $database)."...";
    $oh->core->Data("Purge", [$database[2], $database[3]]);
    $chat = $this->core->Data("Get", ["chat", $database[3]]);
    if(!empty($chat)) {
     $this->core->Data("Purge", ["chat", $database[3]]);
    }
    $conversation = $this->core->Data("Get", ["conversation", $database[3]]);
    if(!empty($conversation)) {
     $this->core->Data("Purge", ["conversation", $database[3]]);
    }
    $translate = $this->core->Data("Get", ["translate", $database[3]]);
    if(!empty($translate)) {
     $this->core->Data("Purge", ["translate", $database[3]]);
    }
    $votes = $this->core->Data("Get", ["votes", $database[3]]);
    if(!empty($votes)) {
     $this->core->Data("Purge", ["votes", $database[3]]);
    }
    $r .= "OK</p>\r\n";
   }
  }
 } if($purged == 0) {
  $r .= $oh->core->Element(["p", "No content to purge!"]);
 }
 $r .= $oh->core->Element(["p", "Done"]);
 echo $r;
?>
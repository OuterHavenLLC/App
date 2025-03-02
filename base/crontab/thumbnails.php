<?php
 # Thumbnail Assurance Cron Job
 require_once("/var/www/html/base/Bootloader.php");
 $oh = New OH;
 $images = $oh->core->config["XFS"]["FT"]["P"] ?? [];
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Thumbnail Assurance"
 ]).$oh->core->Element([
  "p", "SKIP means non-image tested, and OK means thumbnail created or already exists."
 ]).$oh->core->Element([
  "p", "Working on the System Library..."
 ]);
 $_FileSystem = $oh->core->Data("Get", ["app", "fs"]);
 foreach($_FileSystem as $key => $info) {
  $_File = $info["Name"];
  $extension = explode(".", $_File)[1];
  $isImage = (in_array($extension, $images)) ? 1 : 0;
  if($isImage == 1) {
   $r .= $oh->core->Element([
    "p", "Creating thumbnail for $_File..."
   ]);
   $oh->core->Thumbnail([
    "CronJob" => 1,
    "File" => $_File,
    "Username" => $oh->core->ID
   ]);
   $r .= $oh->core->Element(["p", "OK"]);
  }
 }
 $r .= $oh->core->Element([
  "p", "Working on the Member Libraries..."
 ]);
 $db = $oh->core->DatabaseSet("Files");
 foreach($db as $key => $library) {
  $library = str_replace("nyc.outerhaven.fs.", "", $library);
  $member = $oh->core->Data("Get", ["mbr", $library]);
  if(!empty($member["Login"])) {
   $r .= $oh->core->Element(["p", "Opening Library $library:"]);
   $library = $oh->core->Data("Get", ["fs", $library]);
   $library = $library["Files"] ?? [];
   if(empty($library)) {
    $r .= $oh->core->Element(["p", "This Library is empty."]);
   } foreach($library as $key => $info) {
   	$_File = $info["Name"];
    $extension = explode(".", $_File)[1];
    $isImage = (in_array($extension, $images)) ? 1 : 0;
    if($isImage == 1) {
     $r .= $oh->core->Element(["p", "Creating thumbnail for $_File..."]);
     $oh->core->Thumbnail([
      "CronJob" => 1,
      "File" => $_File,
      "Username" => $member["Login"]["Username"]
     ]);
     $r .= $oh->core->Element(["p", "OK"]);
    }
   }
  }
 }
 $r .= $oh->core->Element(["p", "Done"]);
 echo $r;
?>
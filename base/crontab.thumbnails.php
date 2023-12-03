<?php
 # Thumbnail Assurance Cron Job
 require_once("/var/www/html/base/Bootloader.php");
 $oh = New OH();
 $images = $oh->core->config["XFS"]["FT"]["P"] ?? [];
 echo $oh->core->config["App"]["Name"]."</em> Thumbnail Assurance\r\n";
 echo "SKIP means non-image tested, and OK means thumbnail created or already exists.\r\n";
 echo "Working on the System Library...\r\n";
 $_FileSystem = $oh->core->Data("Get", ["app", "fs"]) ?? [];
 foreach($_FileSystem as $key => $info) {
  $_File = $info["Name"];
  $extension = explode(".", $_File)[1];
  $isImage = (in_array($extension, $images)) ? 1 : 0;
  if($isImage == 1) {
   echo "Creating thumbnail for $_File...";
   $oh->core->Thumbnail([
    "CronJob" => 1,
    "File" => $_File,
    "Username" => $oh->core->ID
   ]);
   echo "OK\r\n";
  }
 }
 echo "Working on the Member Libraries...\r\n";
 $db = $oh->core->DatabaseSet("Files") ?? [];
 foreach($db as $key => $library) {
  $library = str_replace("nyc.outerhaven.fs.", "", $library);
  $member = $oh->core->Data("Get", ["mbr", $library]) ?? [];
  if(!empty($member["Login"])) {
   echo "Opening Library $library:\r\n";
   $library = $oh->core->Data("Get", ["fs", $library]) ?? [];
   $library = $library["Files"] ?? [];
   if(empty($library)) {
    echo "This Library is empty.\r\n";
   } foreach($library as $key => $info) {
   	$_File = $info["Name"];
    $extension = explode(".", $_File)[1];
    $isImage = (in_array($extension, $images)) ? 1 : 0;
    if($isImage == 1) {
     echo "Creating thumbnail for $_File...";
     $oh->core->Thumbnail([
      "CronJob" => 1,
      "File" => $_File,
      "Username" => $member["Login"]["Username"]
     ]);
     echo "OK\r\n";
    }
   }
  }
 }
 echo "Done";
?>
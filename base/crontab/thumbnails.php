<?php
 # Thumbnail Assurance Cron Job
 require_once("Bootloader.php");
 $gw = New GW();
 $images = $gw->system->core["XFS"]["FT"]["P"] ?? [];
 echo "Initializing Outer Haven Thumbnail Assurance...\r\n";
 echo "SKIP means non-image tested.\r\n";
 echo "OK means thumbnail created or already exists.\r\n";
 echo "Let's begin...\r\n";
 sleep(3);
 echo "Working on the System Library...\r\n";
 $_FileSystem = $gw->system->Data("Get", ["x", "fs"]) ?? [];
 foreach($_FileSystem as $key => $info) {
  $_File = $info["Name"];
  $extension = explode(".", $_File)[1];
  $isImage = (in_array($extension, $images)) ? 1 : 0;
  if($isImage == 1) {
   echo "Creating thumbnail for $_File...";
   $gw->system->Thumbnail([
    "CronJob" => 1,
    "File" => $_File,
    "Username" => $gw->system->ID
   ]);
   echo "OK\r\n";
  }
 }
 echo "Done...\r\n";
 sleep(3);
 echo "Working on the Member Libraries...\r\n";
 $db = $gw->system->DatabaseSet("Files") ?? [];
 foreach($db as $key => $library) {
  $library = str_replace("c.oh.fs.", "", $library);
  $member = $gw->system->Data("Get", ["mbr", $library]) ?? [];
  $username = $member["Login"] ?? [];
  $username = $username["Username"] ?? "";
  if(!empty($username)) {
   echo "Opening Library $library:\r\n";
   $library = $gw->system->Data("Get", ["fs", $library]) ?? [];
   $library = $library["Files"] ?? [];
   if(empty($library)) {
    echo "This Library is empty.\r\n";
   } foreach($library as $key => $info) {
   	$_File = $info["Name"];
    $extension = explode(".", $_File)[1];
    $isImage = (in_array($extension, $images)) ? 1 : 0;
    if($isImage == 1) {
     echo "Creating thumbnail for $_File...";
     $gw->system->Thumbnail([
      "CronJob" => 1,
      "File" => $_File,
      "Username" => $username
     ]);
     echo "OK\r\n";
    }
   }
  }
  echo "Next Library...\r\n";
  sleep(1);
 }
 echo "OK\r\n";
 echo "Thumbnails have been created where necessary as of ".$gw->system->timestamp.".\r\n";
 echo "Exiting...\r\n";
?>
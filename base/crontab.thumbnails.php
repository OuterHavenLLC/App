<?php
 # Thumbnail Assurance Cron Job
 require_once("/home/mike/public_html/base/Bootloader.php");
 $oh = New OH();
 $images = $oh->config["XFS"]["FT"]["P"] ?? [];
 echo "Initializing Outer Haven Thumbnail Assurance...\r\n";
 echo "SKIP means non-image tested.\r\n";
 echo "OK means thumbnail created or already exists.\r\n";
 echo "Let's begin...\r\n";
 sleep(3);
 echo "Working on the System Library...\r\n";
 $_FileSystem = $oh->Data("Get", ["app", "fs"]) ?? [];
 foreach($_FileSystem as $key => $info) {
  $_File = $info["Name"];
  $extension = explode(".", $_File)[1];
  $isImage = (in_array($extension, $images)) ? 1 : 0;
  if($isImage == 1) {
   echo "Creating thumbnail for $_File...";
   $oh->Thumbnail([
    "CronJob" => 1,
    "File" => $_File,
    "Username" => $oh->ID
   ]);
   echo "OK\r\n";
  }
 }
 echo "Done...\r\n";
 sleep(3);
 echo "Working on the Member Libraries...\r\n";
 $db = $oh->DatabaseSet("Files") ?? [];
 foreach($db as $key => $library) {
  $library = str_replace("c.oh.fs.", "", $library);
  $member = $oh->Data("Get", ["mbr", $library]) ?? [];
  if(!empty($member["Login"])) {
   echo "Opening Library $library:\r\n";
   $library = $oh->Data("Get", ["fs", $library]) ?? [];
   $library = $library["Files"] ?? [];
   if(empty($library)) {
    echo "This Library is empty.\r\n";
   } foreach($library as $key => $info) {
   	$_File = $info["Name"];
    $extension = explode(".", $_File)[1];
    $isImage = (in_array($extension, $images)) ? 1 : 0;
    if($isImage == 1) {
     echo "Creating thumbnail for $_File...";
     $oh->Thumbnail([
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
 echo "Thumbnails have been created where necessary as of ".$oh->timestamp.".\r\n";
 echo "Exiting...\r\n";
?>
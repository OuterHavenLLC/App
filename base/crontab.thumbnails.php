<?php
 # Thumbnail Assurance Cron Job
 require_once("/home/mike/public_html/base/Bootloader.php");
 $oh = New OH();
 $images = $oh->core->config["XFS"]["FT"]["P"] ?? [];
 echo $oh->core->Element(["h1", "<em>".$oh->core->config["App"]["Name"]."</em> Thumbnail Assurance"]);
 echo $oh->core->Element(["p", "<em>SKIP</em> means non-image tested. <em>OK</em> means thumbnail created or already exists."]);
 echo $oh->core->Element(["p", "Working on the System Library..."]);
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
 echo "Done...\r\n";
 sleep(3);
 echo "Working on the Member Libraries...\r\n";
 $db = $oh->core->DatabaseSet("Files") ?? [];
 foreach($db as $key => $library) {
  $library = str_replace("c.oh.fs.", "", $library);
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
 echo "Thumbnails have been created where necessary as of ".$oh->core->timestamp.".\r\n";
 echo "Exiting...\r\n";
?>
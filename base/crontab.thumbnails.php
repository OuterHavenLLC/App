<?php
 # Thumbnail Assurance Cron Job
 require_once("/home/mike/public_html/base/Bootloader.php");
 $oh = New OH();
 $images = $oh->core->config["XFS"]["FT"]["P"] ?? [];
 $r = $oh->core->Element([
  "h1", "<em>".$oh->core->config["App"]["Name"]."</em> Thumbnail Assurance"
 ]).$oh->core->Element([
  "p", "<em>SKIP</em> means non-image tested. <em>OK</em> means thumbnail created or already exists."
 ]).$oh->core->Element([
  "p", "Working on the System Library..."
 ]);
 $_FileSystem = $oh->core->Data("Get", ["app", "fs"]) ?? [];
 foreach($_FileSystem as $key => $info) {
  $_File = $info["Name"];
  $extension = explode(".", $_File)[1];
  $isImage = (in_array($extension, $images)) ? 1 : 0;
  if($isImage == 1) {
   $r .= "<p>Creating thumbnail for $_File...";
   $oh->core->Thumbnail([
    "CronJob" => 1,
    "File" => $_File,
    "Username" => $oh->core->ID
   ]);
   $r .= "OK</p>\r\n";
  }
 }
 $r .= $oh->core->Element(["p", "Done..."]);
 $r .= $oh->core->Element(["p", "Working on the Member Libraries..."]);
 $db = $oh->core->DatabaseSet("Files") ?? [];
 foreach($db as $key => $library) {
  $library = str_replace("nyc.outerhaven.fs.", "", $library);
  $member = $oh->core->Data("Get", ["mbr", $library]) ?? [];
  if(!empty($member["Login"])) {
   $r .= $oh->core->Element(["p", "Opening Library $library:"]);
   $library = $oh->core->Data("Get", ["fs", $library]) ?? [];
   $library = $library["Files"] ?? [];
   if(empty($library)) {
    $r .= $oh->core->Element(["p", "This Library is empty."]);
   } foreach($library as $key => $info) {
   	$_File = $info["Name"];
    $extension = explode(".", $_File)[1];
    $isImage = (in_array($extension, $images)) ? 1 : 0;
    if($isImage == 1) {
     $r .= "<p>Creating thumbnail for $_File...";
     $oh->core->Thumbnail([
      "CronJob" => 1,
      "File" => $_File,
      "Username" => $member["Login"]["Username"]
     ]);
     $r .= "OK</p>\r\n";
    }
   }
  }
  $r .= $oh->core->Element(["p", "Next Library..."]);
 }
 $r = $oh->core->Element([
  "html", $oh->core->Element([
   "head", $oh->core->Element([
    "style", $oh->core->Extension("669ae04b308fc630f8e06317313d9efe")
   ])
  ]).$oh->core->Element([
   "body", $r
  ])
 ]);
?>
<?php
 # Content Purge
 require_once("/home/mike/public_html/base/Bootloader.php");
 $oh = New OH();
 $images = $oh->core->config["XFS"]["FT"]["P"] ?? [];
 echo $oh->core->Element(["h1", "<em>".$oh->core->config["App"]["Name"]."</em> Content Purge"]);
 echo $oh->core->Element(["p", "Eliminates primary databases marked for purging, and deletes associated files for media marked for purging."]);
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
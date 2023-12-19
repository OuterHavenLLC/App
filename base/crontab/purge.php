<?php
 # Content Purge
 require_once("/var/www/html/base/Bootloader.php");
 $oh = New OH;
 $images = $oh->core->config["XFS"]["FT"]["P"] ?? [];
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Content Purge"
 ]).$oh->core->Element([
  "p", "Eliminates primary databases marked for purging, and deletes associated files for media marked for purging."]);
 $r .= $oh->core->Element(["p", "Done"]);
 // SEND AS EMAIL
?>
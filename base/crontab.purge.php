<?php
 # Content Purge
 require_once("/home/mike/public_html/base/Bootloader.php");
 $oh = New OH();
 $images = $oh->core->config["XFS"]["FT"]["P"] ?? [];
 echo $oh->core->Element(["h1", "<em>".$oh->core->config["App"]["Name"]."</em> Content Purge"]);
 echo $oh->core->Element(["p", "Eliminates primary databases marked for purging, and deletes associated files for media marked for purging."]);
?>
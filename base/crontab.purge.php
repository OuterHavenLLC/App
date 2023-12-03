<?php
 # Content Purge
 require_once("/var/www/html/base/Bootloader.php");
 $oh = New OH();
 $images = $oh->core->config["XFS"]["FT"]["P"] ?? [];
 echo $oh->core->config["App"]["Name"]."</em> Content Purge\r\n";
 echo "Eliminates primary databases marked for purging, and deletes associated files for media marked for purging.\r\n";
 echo "Done";
?>
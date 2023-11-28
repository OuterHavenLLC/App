<?php
 # Re:Search Index
 require_once("/home/mike/public_html/base/Bootloader.php");
 $oh = New OH();
 $images = $oh->core->config["XFS"]["FT"]["P"] ?? [];
 echo $oh->core->Element(["h1", "<em>".$oh->core->config["App"]["Name"]."</em> Re:Search Index"]);
?>
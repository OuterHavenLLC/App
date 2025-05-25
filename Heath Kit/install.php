<?php
 require_once("../base/Bootloader.php");
 $oh = New OH;
 $json = __DIR__."/configuration.json";
 $jsonExample = __DIR__."/configuration.example.json";
 if(!file_exists($json)) {
  echo "<p>The JSON configuration file is missing.</p>\r\n";
  echo "<p>Here is a example of what this configuration file should contain:</p>\r\n";
  echo "<p>".json_encode($jsonExample, true)."</p>\r\n";
 } else {
  # NEXT STEPS
 }
?>
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
  $database = $json["DatabaseCredentials"] ?? [];
  foreach($database as $key => $value) {
   $database[$key] = base64_encode($value);
  }
  $sql = New SQL($database);
  if(!$sql) {
   echo "<p>Could not connect to the SQL database. Please ensure the ReSearch database exists, the credentials are accurate.</p>\r\n";
  } elseif(!extension_loaded("curl")) {
   echo "<p>Please ensure the cURL extension (<em>php-curl</em>) is installed and enabled.</p>\r\n";
  } elseif(!extension_loaded("ftp")) {
   echo "<p>Please ensure the FTP extension (<em>php-ftp</em>) is installed and enabled.</p>\r\n";
  } elseif(!extension_loaded("gd")) {
   echo "<p>Please ensure the GD Image extension (<em>php-gd</em>) is installed and enabled.</p>\r\n";
  } else {
   # NEXT STEPS
  }
 }
?>
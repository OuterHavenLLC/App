<?php
 require_once("base/Bootloader.php");
 $oh = New OH;
 $databases = $oh->core->DatabaseSet();
 $destination = $oh->core->DocumentRoot."/_ExportDatabases/";
 echo "<p>Beginning export...</p>\r\n";
 foreach($databases as $key => $database) {
  $database = explode(".", $database);
  if(!empty($database[3])) {
   $data = $oh->core->Data("Get", [$database[2], $database[3]]);
   $exportFile = $destination;
   if(!is_dir($exportFile)) {
    mkdir($exportFile, 0775, true);
   }
   $dataFile = fopen($exportFile.implode(".", $database), "w+");
   fwrite($dataFile, json_encode($data, true));
   fclose($dataFile);
   echo "<p>EXPORT ".implode(".", $database)."... OK</p>\r\n";
  }
 }
 echo $oh->core->Data("Export", []);
 echo "<p><strong>Done</strong></p>\r\n";
 echo "<p>Please copy the exported databases from <strong><em>/_ExportDatabases</em></strong> to <strong><em>/Heath Kit/Data</em></strong>.</p>\r\n";
?>
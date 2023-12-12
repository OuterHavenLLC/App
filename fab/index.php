<?php
 require_once("/var/www/html/base/Bootloader.php");
 $oh = New OH;
 $event = $oh->core->config["App"]["LiveEvent"] ?? 0;
 $extension = ($event == 1) ? "1870885288027c3d4bc0a29bdf5f7579" : "c0f79632dc2313352f92b41819fe4739";
 echo $oh->core->Change([[
  "[App.Content]" => $oh->core->Extension($extension),
  "[App.Description]" => $oh->core->config["App"]["Description"],
  "[App.Keywords]" => $oh->core->config["App"]["Keywords"],
  "[App.Owner]" => $oh->core->ShopID,
  "[App.Title]" => "Free America Broadcasting"
 ], $oh->core->PlainText([
  "BBCodes" => 1,
  "Data" => file_get_contents("./index.txt"),
  "Display" => 1
 ])]);
?>
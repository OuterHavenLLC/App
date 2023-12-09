<?php
 require_once("/var/www/html/base/Bootloader.php");
 $oh = New OH;
 $event = $oh->core->config["App"]["LiveEvent"];
 $view = ($event == 1) ? "event" : "standard";
 $r = $oh->core->Change([[
  "[App.Content]" => base64_encode($content),
  "[App.Description]" => $oh->core->config["App"]["Description"],
  "[App.Keywords]" => $oh->core->config["App"]["Keywords"],
  "[App.Owner]" => $oh->core->ShopID,
  "[App.Title]" => "Free America Broadcasting"
 ], $oh->core->PlainText([
  "BBCodes" => 1,
  "Data" => file_get_contents("$view.index.txt"),
  "Display" => 1
 ])]);
?>
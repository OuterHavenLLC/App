<?php
 require_once("/var/www/html/base/Bootloader.php");
 $activeEvent = 0;
 $broadcastViewEnabled = 0;
 $oh = New OH;
 $description = $oh->core->config["App"]["Description"] ?? "";
 $events = $oh->core->config["PublicEvents"] ?? [];
 $selectedEvent = [];
 $title = "Free America Broadcasting";
 foreach($events as $event => $info) {
  if($info["Active"] == 1) {
   $activeEvent = 1;
   $broadcastViewEnabled = $info["EnablePublicBroadcast"] ?? 0;
   $selectedEvent = $info;
   break;
  }
 } if($broadcastViewEnabled == 1) {
  $description = $selectedEvent["Description"] ?? $description;
  $title = $selectedEvent["Title"] ?? $title;
 }
 $extension = ($activeEvent == 1) ? "1870885288027c3d4bc0a29bdf5f7579" : "c0f79632dc2313352f92b41819fe4739";
 echo $oh->core->Change([[
  "[App.Content]" => $oh->core->Change([[
   "[FAB.Chat]" => base64_encode("v=".base64_encode("Chat:Home")."&Card=1&Group=1&ID=7216072bbd437563e692cc7ff69cdb69"),
   "[FAB.Listen]" => base64_encode("v=".base64_encode("Subscription:FABPlayer"))
  ], $oh->core->Extension($extension)]),
  "[App.Description]" => $description,
  "[App.Keywords]" => $oh->core->config["App"]["Keywords"],
  "[App.Owner]" => $oh->core->ShopID,
  "[App.Title]" => $title
 ], $oh->core->PlainText([
  "BBCodes" => 1,
  "Data" => file_get_contents("./index.txt"),
  "Display" => 1
 ])]);
?>
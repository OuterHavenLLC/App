<?php
 require_once("base/Bootloader.php");
 $data = array_merge($_GET, $_POST);
 $doNotEncode = [
  "Design",
  "JS",
  "Maintenance"
 ];
 $api = $data["_API"] ?? "";
 $oh = New OH;
 $view = $data["v"] ?? "";
 $r = "";
 $oh->core->Setup("App");
 if($api == "Design") {
  header("content-type: text/CSS");
  $r = $oh->core->Extension("d4efcd44be4b2ef2a395f0934a9e446a");
 } elseif($api == "JS") {
  header("content-type: application/x-javascript");
  if($view == "Cypher") {
   $r = $oh->core->Extension("06dfe9b3d6b9fdab588c1eabfce275fd");
  } elseif($view == "Client") {
   $r = $oh->core->Extension("5b22de694d66b763c791395da1de58e1");
  }
  $r = $oh->core->Change([[
   "[App.AddContent]" => base64_encode("v=".base64_encode("Profile:AddContentCheck")),
   "[App.Bulletin]" => base64_encode($oh->core->Extension("ae30582e627bc060926cfacf206920ce")),
   "[App.Bulletins]" => base64_encode("v=".base64_encode("Profile:Bulletins")),
   "[App.Language]" => $oh->core->language,
   "[App.Mainstream]" => base64_encode("v=".base64_encode("Search:Containers")."&st=Mainstream"),
   "[App.MainUI]" => base64_encode("v=".base64_encode("WebUI:UIContainers")),
   "[App.Menu]" => base64_encode("v=".base64_encode("WebUI:Menu")),
   "[App.OptIn]" => base64_encode("v=".base64_encode("WebUI:OptIn")),
   "[App.WYSIWYG]" => base64_encode("v=".base64_encode("WebUI:WYSIWYG"))
  ], $oh->core->PlainText([
   "Data" => $r,
   "Display" => 1,
   "HTMLDecode" => 1
  ])]);
 } elseif($api == "Maintenance") {
  $r = $oh->core->config["Maintenance"] ?? 0;
 } elseif($api == "Web") {
  if($view == base64_encode("File:SaveUpload")) {
   $r = $oh->view($view, [
    "Data" => $data,
    "Files" => $_FILES["Uploads"]
   ]);
  } elseif($view == "MD5") {
   $r = md5(base64_decode($data["MD5"]));
  } else {
   $r = $oh->view($view, ["Data" => $data]);
  }
 } else {
  $command = $data["_cmd"] ?? "";
  $command = (!empty($command)) ? explode("/", urldecode($command)) : [$command];
  $command = $oh->core->FixMissing($command, [0, 1, 2, 3]);
  if($command[0] == "Errors") {
   # ERRORS
   $content = "v=".base64_encode("WebUI:Error")."&Error=".$command[1];
  } elseif($command[0] == "MadeInNY") {
   # MADE IN NEW YORK
   $content = "v=".base64_encode("Shop:MadeInNewYork")."&pub=1";
   if(!empty($command[1])) {
    $content = "v=".base64_encode("Shop:Home")."&UN=".base64_encode($command[1])."&pub=1";
    if(!empty($command[2])) {
     $content = "v=".base64_encode("Product:Home")."&CallSign=".$command[2]."&UN=".base64_encode($command[1])."&pub=1";
    }
   }
  } elseif($command[0] == "Member") {
   # PROFILES
   $content = "v=".base64_encode("Profile:Home")."&back=0&onProf=1&UN=".base64_encode($command[1])."&pub=1";
  } elseif($command[0] == "VVA") {
   # VISUAL VANGUARD ARCHITECTURE
   $content = "v=".base64_encode("Company:VVA")."&pub=1";
  } elseif($command[0] == "about") {
   # ABOUT
   $content = "v=".base64_encode("Company:Home")."&pub=1";
  } elseif($command[0] == "archive") {
   # COMMUNITY ARCHIVE
   $content = "v=".base64_encode("Page:Home")."&ID=".$command[1]."&pub=1";
  } elseif($command[0] == "blogs") {
   # BLOGS
   $content = "v=".base64_encode("Search:Containers")."&pub=1&st=BLG";
   if(!empty($command[1])) {
    $content = "v=".base64_encode("Blog:Home")."&CallSign=".$command[1]."&ID=".$command[1]."&pub=1";
    if(!empty($command[2])) {
     $content = "v=".base64_encode("BlogPost:Home")."CallSign=".$command[1]."&BLG=".$command[1]."&ID=".$command[2]."&pub=1";
    }
   }
  } elseif($command[0] == "chat") {
   # CHAT
   $content = "v=".base64_encode("WebUI:Containers")."&Type=Chat";
   if(!empty($command[1])) {
    $content = "v=".base64_encode("Chat:PublicHome")."&ID=".base64_encode($command[1]);
   }
  } elseif($command[0] == "congress") {
   # CONGRESS
   $content = "v=".base64_encode("Congress:Home")."&pub=1";
  } elseif($command[0] == "donate") {
   # DONATE
   $content = "v=".base64_encode("Company:Donate")."&pub=1";
  } elseif($command[0] == "feedback") {
   # FEEDBACK
   $content = "v=".base64_encode("Company:VVA")."&ID=".$command[1]."&pub=1";
  } elseif($command[0] == "forums") {
   # FORUMS
   $content = "v=".base64_encode("Forum:PublicHome")."&CallSign=".$command[1]."&ID=".$command[1];
  } elseif($command[0] == "hire") {
   # HIRE
   $content = "v=".base64_encode("Invoice:Hire")."&ID=".md5($oh->core->ShopID)."&pub=1";
  } elseif($command[0] == "invoice") {
   # INVOICE
   $content = "v=".base64_encode("WebUI:Containers");
   if(!empty($command[1])) {
    $content = "v=".base64_encode("Invoice:Home")."&ID=".$command[1]."&pub=1";
   }
  } elseif($command[0] == "poll") {
   # POLLS
   $content = "v=".base64_encode("WebUI:Containers");
   if(!empty($command[1])) {
    $content = "v=".base64_encode("Poll:Home")."&ID=".$command[1]."&pub=1";
   }
  } elseif($command[0] == "revenue") {
   # REVENUE
   $content = "v=".base64_encode("Revenue:Home")."&Shop=".base64_encode($command[1])."&pub=1";
  } elseif($command[0] == "search") {
   # SEARCH
   $content = "v=".base64_encode("Search:ReSearch")."&pub=1";
   if(!empty($command[1])) {
    $content = "v=".base64_encode("Search:ReSearch")."&pub=1&q=".base64_encode($command[1]);
   }
  } elseif($command[0] == "statistics") {
   # STATISTICS
   $content = "v=".base64_encode("Company:Statistics")."&pub=1";
  } elseif($command[0] == "topics") {
   # TOPICS
   $content = "v=".base64_encode("Search:ReSearch")."&pub=1&q=".base64_encode("#FreedomAlwaysWins");
   if(!empty($command[1])) {
    $content = "v=".base64_encode("Search:ReSearch")."&pub=1&q=".base64_encode($command[1]);
   }
  } else {
   $oh->core->Statistic("Visits");
   $content = "v=".base64_encode("WebUI:UIContainers");
  }
  $r = $oh->core->Change([[
   "[App.Content]" => base64_encode($content),
   "[App.Description]" => $oh->core->config["App"]["Description"],
   "[App.Keywords]" => $oh->core->config["App"]["Keywords"],
   "[App.SwitchLanguages]" => base64_encode("v=".base64_encode("WebUI:SwitchLanguages")),
   "[App.Owner]" => $oh->core->ShopID,
   "[App.Title]" => $oh->core->config["App"]["Name"]
  ], $oh->core->PlainText([
   "BBCodes" => 1,
   "Data" => file_get_contents("./index.txt"),
   "Display" => 1
  ])]);
 } if(!empty($api) && !in_array($api, $doNotEncode)) {
  $r = base64_encode($r);
 }
 echo $r;
?>
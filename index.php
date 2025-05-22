<?php
 require_once("base/Bootloader.php");
 $_Data = array_merge($_GET, $_POST);
 $_View = "";
 $api = $_Data["_API"] ?? "";
 $doNotEncode = [
  "Design",
  "JS",
  "Maintenance"
 ];
 $oh = New OH;
 $oh->core->Setup("App");
 $view = $_Data["v"] ?? "";
 if($api == "Design") {
  header("content-type: text/CSS");
  $_View = $oh->core->Extension("d4efcd44be4b2ef2a395f0934a9e446a");
 } elseif($api == "Extensions") {
  $_View = $oh->view(base64_encode("WebUI:Extensions"), []);
  if(!empty($view)) {
   $_View = $oh->view(base64_encode("WebUI:Extensions"), ["Data" => [
     "ID" => $view
   ]]);
  }
 } elseif($api == "JS") {
  header("content-type: application/x-javascript");
  if($view == "Chart") {
   $_View = $oh->core->Extension("b3463a420fd60fccd6f06727860ba860");
  } elseif($view == "Client") {
   $_View = $oh->core->Extension("5b22de694d66b763c791395da1de58e1");
  } elseif($view == "Cypher") {
   $_View = $oh->core->Extension("45787465-6e73-496f-ae42-794d696b65-67abee895c024");
  } elseif($view == "jQuery") {
   $_View = $oh->core->Extension("45787465-6e73-496f-ae42-794d696b65-67fa6b4a2b998");
  } elseif($view == "jQueryUI") {
   $_View = $oh->core->Extension("45787465-6e73-496f-ae42-794d696b65-67fa6b71bda8b");
  }
  $_View = $oh->core->Change([[
   "[App.AddContent]" => $oh->core->AESencrypt("v=".base64_encode("Profile:AddContentCheck")),
   "[App.Bulletin]" => $oh->core->AESencrypt($oh->core->Extension("ae30582e627bc060926cfacf206920ce")),
   "[App.Bulletins]" => $oh->core->AESencrypt("v=".base64_encode("Profile:Bulletins")),
   "[App.DITkey]" => $oh->core->DITkey,
   "[App.Gateway]" => $oh->core->AESencrypt("v=".base64_encode("WebUI:Gateway")),
   "[App.Language]" => $oh->core->language,
   "[App.MainUI]" => $oh->core->AESencrypt("v=".base64_encode("WebUI:Landing")),
   "[App.Menu]" => $oh->core->AESencrypt("v=".base64_encode("WebUI:Menu")),
   "[App.SwitchLanguages]" => $oh->core->AESencrypt("v=".base64_encode("WebUI:SwitchLanguages")),
   "[App.WYSIWYG]" => $oh->core->AESencrypt("v=".base64_encode("WebUI:WYSIWYG"))
  ], $oh->core->PlainText([
   "Data" => $_View,
   "Display" => 1,
   "HTMLDecode" => 1
  ])]);
 } elseif($api == "Maintenance") {
  $_View = $oh->core->config["Maintenance"] ?? 0;
 } elseif($api == "Web") {
  if($view == base64_encode("File:SaveUpload")) {
   $uploads = $_FILES["Uploads"] ?? [];
   $_View = $oh->view($view, [
    "Data" => $_Data,
    "Files" => $uploads
   ]);
  } elseif($view == "MD5") {
   $_View = md5(base64_decode($_Data["MD5"]));
  } else {
   $_View = $oh->view($view, ["Data" => $_Data]);
  }
 } else {
  $command = $_Data["_cmd"] ?? [];
  $command = (!empty($command)) ? explode("/", urldecode($command)) : $command;
  $command = $oh->core->FixMissing($command, [0, 1, 2, 3]);
  if($command[0] == "Error") {
   # ERRORS
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("WebUI:Error")."&Error=".$command[1]);
  } elseif($command[0] == "MadeInNY") {
   # MADE IN NEW YORK
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Shop:MadeInNewYork"));
   if(!empty($command[1])) {
    $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Shop:Home")."Public=1&UN=".base64_encode($command[1]));
    if(!empty($command[2])) {
     $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Product:Home")."&CallSign=".urlencode($command[2])."&UN=".base64_encode($command[1]));
    }
   }
  } elseif($command[0] == "Member") {
   # MEMBERS
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Profile:Home")."&back=0&UN=".base64_encode($command[1]));
   if(!empty($command[3]) && $command[2] == "status") {
    $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("StstuaUpdate:Public")."&ID=".base64_encode($command[3])."&UN=".base64_encode($command[2]));
   }
  } elseif($command[0] == "VVA") {
   # VISUAL VANGUARD ARCHITECTURE
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Company:VVA"));
  } elseif($command[0] == "about") {
   # ABOUT
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Company:Home"));
  } elseif($command[0] == "archive") {
   # COMMUNITY ARCHIVE
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Page:Home")."&ID=".$command[1]);
  } elseif($command[0] == "blogs") {
   # BLOGS
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Search:Containers")."&st=BLG");
   if(!empty($command[1])) {
    $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Blog:Home")."&CallSign=".$command[1]."&ID=".$command[1]);
    if(!empty($command[2])) {
     $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("BlogPost:Home")."CallSign=".$command[1]."&BLG=".$command[1]."&ID=".$command[2]);
    }
   }
  } elseif($command[0] == "chat") {
   # CHAT
   $content = "v=".base64_encode("WebUI:Public")."&Type=Chat&View=".base64_encode("v=".base64_encode("WebUI:Containers"));
   if(!empty($command[1])) {
    $content = "v=".base64_encode("WebUI:Public")."&Type=Chat&View=".base64_encode("v=".base64_encode("Chat:Public")."&ID=".base64_encode($command[1]));
   }
  } elseif($command[0] == "congress") {
   # CONGRESS
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Congress:Home")."&pub=1");
  } elseif($command[0] == "donate") {
   # DONATE
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Company:Donate")."&pub=1");
  } elseif($command[0] == "event") {
   # FREE AMERICA RADIO EVENTS
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Company:FreeAmericaRadio"));
  } elseif($command[0] == "feedback") {
   # FEEDBACK
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Company:VVA")."&ID=".$command[1]."&Public=1");
  } elseif($command[0] == "forums") {
   # FORUMS
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Forum:Public")."&CallSign=".$command[1]."&ID=".$command[1]);
  } elseif($command[0] == "hire") {
   # HIRE
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Invoice:Hire")."&ID=".md5($oh->core->ShopID));
  } elseif($command[0] == "invoice") {
   # INVOICE
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("WebUI:Error")."&Error=404");
   if(!empty($command[1])) {
    $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Invoice:Home")."&ID=".$command[1]);
   }
  } elseif($command[0] == "poll") {
   # POLLS
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("WebUI:Error")."&Error=404");
   if(!empty($command[1])) {
    $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Poll:Home")."&ID=".$command[1]);
   }
  } elseif($command[0] == "revenue") {
   # REVENUE
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Revenue:Home")."&Shop=".base64_encode($command[1]));
  } elseif($command[0] == "search") {
   # SEARCH
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Search:ReSearch"));
   if(!empty($command[1])) {
    $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Search:ReSearch")."&query=".$oh->core->AESencrypt($command[1]));
   }
  } elseif($command[0] == "statistics") {
   # STATISTICS
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Company:Statistics"));
  } elseif($command[0] == "topics") {
   # TOPICS
   $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Search:ReSearch")."&query=".$oh->core->AESencrypt("#FreedomAlwaysWins"));
   if(!empty($command[1])) {
    $content = "v=".base64_encode("WebUI:Public")."&Type=Public&View=".base64_encode("v=".base64_encode("Search:ReSearch")."&query=".$oh->core->AESencrypt("#".$command[1]));
   }
  } else {
   $oh->core->Statistic("Visits");
   $content = "v=".base64_encode("WebUI:Landing");
  }
  $_View = $oh->core->Change([[
   "[App.Content]" => $oh->core->AESencrypt($content),
   "[App.Description]" => $oh->core->config["App"]["Description"],
   "[App.Keywords]" => $oh->core->config["App"]["Keywords"],
   "[App.Owner]" => $oh->core->ShopID,
   "[App.Title]" => $oh->core->config["App"]["Name"]
  ], $oh->core->PlainText([
   "BBCodes" => 1,
   "Data" => $oh->core->Extension("45787465-6e73-496f-ae42-794d696b65-68255ab0c67e0"),
   "Display" => 1
  ])]);
 } if(!empty($api) && !in_array($api, $doNotEncode)) {
  $_View = $oh->core->AESencrypt($_View);
 }
 echo $_View;
?>
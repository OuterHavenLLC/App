<?php
 require_once("base/Bootloader.php");
 $data = array_merge($_GET, $_POST);
 $doNotEncode = [
  "Design",
  "JS",
  "Maintanance"
 ];
 $api = $data["_API"] ?? "";
 $gw = New GW;
 $view = $data["v"] ?? "";
 $r = "";
 $gw->core->Setup("App");
 $y = $gw->core->Member($gw->core->Username());
 $you = $y["Login"]["Username"];
 if($api == "Design") {
  header("content-type: text/CSS");
  $r = $gw->core->Page("d4efcd44be4b2ef2a395f0934a9e446a");
 } elseif($api == "JS") {
  header("content-type: application/x-javascript");
  if($view == "Cypher") {
   $r = $gw->core->Page("06dfe9b3d6b9fdab588c1eabfce275fd");
  } elseif($view == "Functions") {
   $r = $gw->core->Page("9899b8bb388bf8520c3b5cee4ef6778b");
  } elseif($view == "GUI") {
   $r = $gw->core->Page("a62f482184a8b2eefa006a37890666d7");
  }
  $r = $gw->core->Change([[
   "[App.Bulletins]" => base64_encode("v=".base64_encode("Profile:Bulletins")),
   "[App.Mainstream]" => base64_encode("v=".base64_encode("Search:Containers")."&st=Mainstream"),
   "[App.MainUI]" => base64_encode("v=".base64_encode("WebUI:UIContainers")),
   "[App.OptIn]" => base64_encode("v=".base64_encode("WebUI:OptIn")),
   "[App.region]" => $gw->core->region,
   "[App.WYSIWYG]" => base64_encode("v=".base64_encode("WebUI:WYSIWYG"))
  ], $gw->core->PlainText([
   "Data" => $r,
   "Display" => 1,
   "HTMLDecode" => 1
  ])]);
 } elseif($api == "Maintanance") {
  # MAINTANANCE STATUS
  $r = $gw->core->config[$c[0]];
 } elseif($api == "Web") {
  if($view == base64_encode("File:SaveUpload")) {
   $r = $gw->view($view, [
    "Data" => $data,
    "Files" => $_FILES["Uploads"]
   ]);
  } elseif($view == "MD5") {
   $r = md5(base64_decode($data["MD5"]));
  } else {
   $r = $gw->view($view, ["Data" => $data]);
  }
 } else {
  $_ViewTitle = $gw->core->config["App"]["Name"];
  $c = $data["_cmd"] ?? "";
  $c = (!empty($c)) ? explode("/", urldecode($c)) : [$c];
  $c = $gw->core->FixMissing($c, [0, 1, 2, 3]);
  if($c[0] == "Errors") {
   # ERRORS
   $r = $gw->view(base64_encode("WebUI:Error"), ["Data" => [
    "Error" => $c[1]
   ]]);
  } elseif($c[0] == "MadeInNY") {
   # MADE IN NEW YORK
   $r = $gw->view(base64_encode("Shop:MadeInNewYork"), ["Data" => [
    "pub" => 1
   ]]);
   if(!empty($c[1])) {
    $r = $gw->view(base64_encode("Shop:Home"), ["Data" => [
     "UN" => base64_encode($c[1]),
     "pub" => 1
    ]]);
    if(!empty($c[2])) {
     $r = $gw->view(base64_encode("Product:Home"), ["Data" => [
      "CallSign" => $c[2],
      "UN" => base64_encode($c[1]),
      "pub" => 1
     ]]);
    }
   }
  } elseif($c[0] == "Member") {
   # PROFILES
   $r = $gw->view(base64_encode("Profile:Home"), ["Data" => [
    "back" => 0,
    "onProf" => 1,
    "UN" => base64_encode($c[1]),
    "pub" => 1
   ]]);
   $_ViewTitle = json_decode($r, true)["Title"];
  } elseif($c[0] == "PMC" || $c[0] == "defense") {
   # OUTER HAVEN P.M.C.
   $r = $gw->view(base64_encode("PMC:Home"), ["Data" => [
    "pub" => 1
   ]]);
   $_ViewTitle = json_decode($r, true)["Title"];
  } elseif($c[0] == "VVA") {
   # VISUAL VANGUARD ARCHITECTURE
   $r = $gw->view(base64_encode("Company:VVA"), ["Data" => [
    "pub" => 1
   ]]);
   $_ViewTitle = json_decode($r, true)["Title"];
  } elseif($c[0] == "about") {
   # HIRE
   $r = $gw->view(base64_encode("Company:Home"), ["Data" => [
    "pub" => 1
   ]]);
   $_ViewTitle = json_decode($r, true)["Title"];
  } elseif($c[0] == "archive") {
   # COMMUNITY ARCHIVE
   $r = $gw->view(base64_encode("Page:Home"), ["Data" => [
    "LLP" => $c[1],
    "pub" => 1
   ]]);
   $_ViewTitle = json_decode($r, true)["Title"];
  } elseif($c[0] == "blogs") {
   # BLOGS
   $r = $gw->view(base64_encode("Search:Containers"), ["Data" => [
    "pub" => 1,
    "st" => "BLG"
   ]]);
   if(!empty($c[1])) {
    $r = $gw->view(base64_encode("Blog:Home"), ["Data" => [
     "CallSign" => $c[1],
     "ID" => $c[1],
     "pub" => 1
    ]]);
    if(!empty($c[2])) {
     $r = $gw->view(base64_encode("BlogPost:Home"), ["Data" => [
      "CallSign" => $c[1],
      "BLG" => $c[1],
      "ID" => $c[2],
      "pub" => 1
     ]]);
    }
   }
  } elseif($c[0] == "chat") {
   # CHAT
   $r = $gw->view(base64_encode("WebUI:Containers"), []);
   if(!empty($gw->core->ID != $you)) {
    $r = $gw->view(base64_encode("WebUI:Containers"), ["Data" => [
     "Type" => "Chat"
    ]]);
   }
   $_ViewTitle = "Chat";
  } elseif($c[0] == "congress") {
   # CONGRESS
   $r = $gw->view(base64_encode("Congress:Home"), ["Data" => [
    "pub" => 1
   ]]);
   $_ViewTitle = json_decode($r, true)["Title"];
  } elseif($c[0] == "donate") {
   # DONATE
   $r = $gw->view(base64_encode("Company:Donate"), ["Data" => [
    "pub" => 1
   ]]);
  } elseif($c[0] == "feedback") {
   # FEEDBACK
   $r = $gw->view(base64_encode("Feedback:Home"), ["Data" => [
    "ID" => $c[1],
    "pub" => 1
   ]]);
  } elseif($c[0] == "forums") {
   # FORUMS
   $r = $gw->view(base64_encode("Forum:PublicHome"), ["Data" => [
    "CallSign" => $c[1],
    "ID" => $c[1]
   ]]);
  } elseif($c[0] == "hire") {
   # HIRE
   $r = $gw->view(base64_encode("Invoice:Hire"), ["Data" => [
    "ID" => md5($gw->core->ShopID),
    "pub" => 1
   ]]);
   $_ViewTitle = json_decode($r, true)["Title"];
  } elseif($c[0] == "income") {
   # INCOME DISCLOSURES
   $r = $gw->view(base64_encode("Common:Income"), ["Data" => [
    "UN" => base64_encode($c[1]),
    "pub" => 1
   ]]);
  } elseif($c[0] == "invoice") {
   # INVOICE
   $r = $gw->view(base64_encode("WebUI:Containers"), []);
   if(!empty($c[1])) {
    $r = $gw->view(base64_encode("Invoice:Home"), ["Data" => [
     "ID" => $c[1],
     "pub" => 1
    ]]);
   }
   $_ViewTitle = json_decode($r, true)["Title"];
  } elseif($c[0] == "search") {
   # SEARCH
   $r = $gw->view(base64_encode("Search:ReSearch"), ["Data" => [
    "pub" => 1
   ]]);
   if(!empty($c[1])) {
    $r = $gw->view(base64_encode("Search:ReSearch"), ["Data" => [
     "pub" => 1,
     "q" => base64_encode($c[1])
    ]]);
   }
  } elseif($c[0] == "topics") {
   # TOPICS
   $r = $gw->view(base64_encode("Search:ReSearch"), ["Data" => [
    "pub" => 1,
    "q" => base64_encode("#FreedomAlwaysWins")
   ]]);
   if(!empty($c[1])) {
    $r = $gw->view(base64_encode("Search:ReSearch"), ["Data" => [
     "pub" => 1,
     "q" => base64_encode("#".$c[1])
    ]]);
   }
  } else {
   $gw->core->Statistic("Visits");
   $r = $gw->view(base64_encode("WebUI:UIContainers"), []);
  }
  $r = $gw->core->Change([[
   "[Body]" => $gw->core->RenderView($r),
   "[Description]" => $gw->core->config["App"]["Description"],
   "[Keywords]" => $gw->core->config["App"]["Keywords"],
   "[Title]" => $_ViewTitle
  ], $gw->core->PlainText([
   "BBCodes" => 1,
   "Data" => file_get_contents("./index.txt"),
   "Display" => 1
  ])]);
 } if(!empty($api) && !in_array($api, $doNotEncode)) {
  $r = base64_encode($r);
 }
 echo $r;
?>
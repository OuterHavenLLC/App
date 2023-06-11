<?php
 require_once("base/Bootloader.php");
 $data = array_merge($_GET, $_POST);
 $doNotEncode = [
  "CSS",
  "JS",
  "Maintanance"
 ];
 $api = $data["_API"] ?? "";
 $gw = New GW;
 $view = $data["v"] ?? "";
 $r = "";
 $gw->system->Setup("SYS");
 if($api == "CSS") {
  header("content-type: text/CSS");
  $r = $gw->system->Page("d4efcd44be4b2ef2a395f0934a9e446a");
 } elseif($api == "JS") {
  header("content-type: application/x-javascript");
  if($view == "F") {
   $r = $gw->system->Page("9899b8bb388bf8520c3b5cee4ef6778b");
  } elseif($view == "F2") {
   $r = $gw->system->Page("06dfe9b3d6b9fdab588c1eabfce275fd");
  } elseif($view == "GW") {
   $r = $gw->system->Page("a62f482184a8b2eefa006a37890666d7");
  }
  $r = $gw->system->Change([[
   "[OH.Bulletins]" => base64_encode("v=".base64_encode("Profile:Bulletins")),
   "[OH.Gateway]" => base64_encode("v=".base64_encode("WebUI:GatewayContainers")),
   "[OH.LockScreen]" => base64_encode("v=".base64_encode("WebUI:LockScreen")),
   "[OH.Mainstream]" => base64_encode("v=".base64_encode("Search:Containers")."&st=Mainstream"),
   "[OH.MainUI]" => base64_encode("v=".base64_encode("WebUI:UIContainers")),
   "[OH.OptIn]" => base64_encode("v=".base64_encode("WebUI:OptIn")),
   "[OH.region]" => $gw->system->region,
   "[mbr]" => $gw->system->Username()
  ], $gw->system->PlainText([
   "Data" => $r,
   "Display" => 1,
   "HTMLDecode" => 1
  ])]);
 } elseif($api == "Maintanance") {
  # MAINTANANCE STATUS
  $r = $gw->system->core[$c[0]];
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
  $c = $data["_cmd"] ?? "";
  $c = (!empty($c)) ? explode("/", urldecode($c)) : [$c];
  $c = $gw->system->FixMissing($c, [0, 1, 2, 3]);
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
  } elseif($c[0] == "PMC" || $c[0] == "defense") {
   # OUTER HAVEN P.M.C.
   $r = $gw->view(base64_encode("PMC:Home"), ["Data" => [
    "pub" => 1
   ]]);
  } elseif($c[0] == "VVA") {
   # VISUAL VANGUARD ARCHITECTURE
   $r = $gw->view(base64_encode("Company:VVA"), ["Data" => [
    "pub" => 1
   ]]);
  } elseif($c[0] == "archive") {
   # COMMUNITY ARCHIVE
   $r = $gw->view(base64_encode("Page:Home"), ["Data" => [
    "LLP" => $c[1],
    "pub" => 1
   ]]);
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
   if(!empty($sk)) {
    $r = $gw->system->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Type" => "Chat"]
    ]);
   }
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
  } elseif($c[0] == "income") {
   # INCOME DISCLOSURES
   $r = $gw->view(base64_encode("Common:Income"), ["Data" => [
    "UN" => base64_encode($c[1]),
    "pub" => 1
   ]]);
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
   $gw->system->Statistic("Visits");
   $r = $gw->view(base64_encode("WebUI:UIContainers"), []);
  }
  $r = $gw->system->Change([[
   "[Body]" => $r,
   "[Description]" => $gw->system->core["SYS"]["Description"],
   "[Keywords]" => $gw->system->core["SYS"]["Keywords"],
   "[Title]" => $gw->system->core["SYS"]["Title"]
  ], $gw->system->PlainText([
   "BBCodes" => 1,
   "Data" => file_get_contents($_SERVER["DOCUMENT_ROOT"]."/index.txt"),
   "Display" => 1
  ])]);
 } if(!empty($api) && !in_array($api, $doNotEncode)) {
  $r = base64_encode($r);
 }
 echo $r;
?>
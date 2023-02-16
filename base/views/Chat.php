<?php
 Class Chat extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Home(array $a) {
   $d = $a["Data"] ?? [];
   $d = $this->system->FixMissing($d, ["GroupChat", "to"]);
   $group = $d["GroupChat"] ?? 0;
   $un = base64_decode($d["to"]);
   $y = $this->you;
   if($group == 1) {
    $active = "Active";
    $chat = md5("Chat_$un");
    $dn = $this->system->Data("Get", ["pf", $un]) ?? [];
    $dn = $dn["Title"] ?? "Group Chat";
    $lobby = base64_encode("v=".base64_encode("Forum:About")."&ID=".$d["to"]);
    $t = [];
   } else {
    $t = ($un == $y["UN"]) ? $y : $this->system->Member($un);
    $active = ($t["oStatus"] == 1) ? "Online" : "Offline";
    $chat = md5("Chat_".$y["UN"]."-$un");
    $dn = $t["DN"];
    $lobby = base64_encode("v=".base64_encode("Profile:Lobby")."&Chat=1&onProf=1&UN=".$d["to"]);
   }
   $at1 = base64_encode("Share with $dn in Chat:.ChatAttachments$chat-ATTF");
   $at2 = base64_encode("Added to Chat Message!");
   $sc = base64_encode("Search:Containers");
   $r = $this->system->Change([[
    "[Chat.ActivityStatus]" => $active,
    "[Chat.Attachments]" => base64_encode("v=$sc&AddTo=$at1&Added=$at2&UN=".$y["UN"]."&st=XFS"),
    "[Chat.Attachments.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&ID="),
    "[Chat.DisplayName]" => $dn,
    "[Chat.GroupChat]" => $group,
    "[Chat.ID]" => $chat,
    "[Chat.List]" => "v=".base64_encode("Chat:List")."&GroupChat=$group&to=".$d["to"],
    "[Chat.Profile]" => $lobby,
    "[Chat.ProfilePicture]" => $this->system->ProfilePicture($t, "margin:10%;max-width:4em;width:90%"),
    "[Chat.Send]" => base64_encode("v=".base64_encode("Chat:Save")),
    "[Chat.To]" => $un,
    "[Chat.Type]" => $group
   ], $this->system->Page("a4c140822e556243e3edab7cae46466d")]);
   return $r;
  }
  function List(array $a) {
   $d = $a["Data"] ?? [];
   $d = $this->system->FixMissing($d, ["GroupChat", "to"]);
   $group = $d["GroupChat"] ?? 0;
   $msg = [];
   $r = $this->system->Page("2ce9b2d2a7f5394df6a71df2f0400873");
   $t = $d["to"];
   $tpl = $this->system->Page("1f4b13bf6e6471a7f5f9743afffeecf9");
   $y = $this->you;
   if(!empty($t)) {
    $attlv = base64_encode("LiveView:InlineMossaic");
    $t = base64_decode($t);
    $chat = ($group == 1) ? $t : md5($t);
    $c = $this->system->Data("Get", ["msg", $chat]) ?? [];
    $c2 = $this->system->Data("Get", ["msg", md5($y["UN"])]) ?? [];
    $c2 = ($group == 0) ? $c2 : [];
    $chat = array_merge($c, $c2);
    foreach($chat as $k => $v) {
     $ck = ($v["From"] == $t && $v["To"] == $y["UN"]) ? 1 : 0;
     $ck2 = ($v["From"] == $y["UN"] && $v["To"] == $t) ? 1 : 0;
     if($group == 1 || $ck == 1 || $ck2 == 1) {
      $class = ($v["From"] == $y["UN"]) ? "MSGy" : "MSGt";
      $att = "";
      if(!empty($v["Attachments"])) {
       $att = $this->view($attlv, ["Data" => [
        "ID" => base64_encode(implode(";", $v["Attachments"])),
        "Type" => base64_encode("DLC")
       ]]);
      }
      $mc = (!empty($v["MSG"])) ? $this->system->Element([
       "p", base64_decode($v["MSG"])
      ]) : "";
      $msg[$k] = [
       "[Message.Attachments]" => $att,
       "[Message.Class]" => $class,
       "[Message.MSG]" => $mc,
       "[Message.Sent]" => $this->system->TimeAgo($v["Timestamp"])
      ];
     }
    }
   } if(!empty($msg)) {
    $r = "";
    ksort($msg);
    foreach($msg as $k => $v) {
     $message = $tpl;
     $r .= $this->system->Change([$v, $message]);
    }
   }
   return $r;
  }
  function Save(array $a) {
   $d = $a["Data"] ?? [];
   $d = $this->system->DecodeBridgeData($d);
   $d = $this->system->FixMissing($d, [
    "GroupChat", "MSG", "Share", "To", "rATTF"
   ]);
   $att = $d["rATTF"];
   $m = $d["MSG"];
   $ck = (!empty($att) && empty($m)) ? 1 : 0;
   $ck2 = (empty($att) && !empty($m)) ? 1 : 0;
   $ck3 = (!empty($att) && !empty($m)) ? 1 : 0;
   $ec = "Denied";
   $group = $d["GroupChat"] ?? 0;
   $r = "Failed to Send";
   $to = $d["To"];
   $y = $this->you;
   if($y["UN"] == $this->system->ID) {
    $r = "You must be signed in to continue.";
   } elseif(($ck == 1 || $ck2 == 1 || $ck3 == 1) && !empty($to)) {
    $att = [];
    $chat = ($group == 1) ? $to : md5($to);
    $ec = "Accepted";
    $sent = $this->system->timestamp;
    $to = ($group == 1 && $d["Share"] == 1) ? "" : $to;
    if(!empty($d["rATTF"])) {
     $dlc = array_reverse(explode(";", base64_decode($d["rATTF"])));
     foreach($dlc as $dlc) {
      if(!empty($dlc)) {
       $f = explode("-", base64_decode($dlc));
       if(!empty($f[0]) && !empty($f[1])) {
        array_push($att, base64_encode($f[0]."-".$f[1]));
       }
      }
     }
    }
    $att = array_unique($att);
    $msg = $this->system->Data("Get", ["msg", $chat]) ?? [];
    $msg[$sent."_".$y["UN"]] = [
     "Attachments" => $att,
     "From" => $y["UN"],
     "MSG" => base64_encode($m),
     "Read" => 0,
     "Timestamp" => $sent,
     "To" => $to
    ];
    $r = "Sent";
    $this->system->Data("Save", ["msg", $chat, $msg]);
   }
   return $this->system->JSONResponse([$ec, $r]);
  }
  function SaveShare(array $a) {
   $d = $a["Data"] ?? [];
   $d = $this->system->DecodeBridgeData($d);
   $d = $this->system->FixMissing($d, ["ID", "UN"]);
   $ec = "Denied";
   $id = $d["ID"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Member or Message Identifiers are missing."
    ]),
    "Header" => "Error"
   ]);
   $un = $d["UN"];
   $y = $this->you;
   if($y["UN"] == $this->system->ID) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id) && !empty($un)) {
    $i = 0;
    $x = $this->system->DatabaseSet("MBR");
    foreach($x as $k => $v) {
     $v = str_replace("c.oh.mbr.", "", $v);
     if($i == 0) {
      $t = $this->system->Data("Get", ["mbr", $v]) ?? [];
      if($un == $t["UN"]) {
       $i++;
      }
     }
    } if($i == 0) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element(["p", "The Member $un does not exist."]),
      "Header" => "Forbidden"
     ]);
    } else {
     $ec = "Accepted";
     $this->view(base64_encode("Chat:Save"), ["Data" => [
      "MSG" => $this->system->PlainText(["Data" => $id, "Processor" => 1]),
      "Share" => $this->system->PlainText(["Data" => 1, "Processor" => 1]),
      "To" => $this->system->PlainText(["Data" => $un, "Processor" => 1])
     ]]);
     $r = $this->system->Dialog([
      "Body" => $this->system->Element(["p", "The message was sent to $un."]),
      "Header" => "Done"
     ]);
    }
   }
   return $this->system->JSONResponse([$ec, $r]);
  }
  function SaveShareGroup(array $a) {
   $d = $a["Data"] ?? [];
   $d = $this->system->DecodeBridgeData($d);
   $d = $this->system->FixMissing($d, ["ID", "UN"]);
   $ec = "Denied";
   $id = $d["ID"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Forum or Message Identifiers are missing."
    ]),
    "Header" => "Error"
   ]);
   $un = $d["UN"];
   $y = $this->you;
   if($y["UN"] == $this->system->ID) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id) && !empty($un)) {
    $active = 0;
    $i = 0;
    $un = $this->system->CallSign($un);
    $x = $this->system->DatabaseSet("PF");
    foreach($x as $k => $v) {
     $v = str_replace("c.oh.pf.", "", $v);
     if($active == 0 && $i == 0) {
      $f = $this->system->Data("Get", ["pf", $v]) ?? [];
      if($un == $this->system->CallSign($f["Title"])) {
       $manifest = $this->system->Data("Get", ["pfmanifest", $v]) ?? [];
       foreach($manifest as $mk => $mv) {
        foreach($mv as $mk2 => $mv2) {
         if($active == 0 && $mk2 == $y["UN"]) {
          $active++;
          $i++;
          $ttl = $f["Title"];
          $un = $v;
         }
        }
       }
      }
     }
    } if($active == 0 && $i == 0) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element(["p", "The Forum does not exist."]),
      "Header" => "Forbidden"
     ]);
    } else {
     $ec = "Accepted";
     $this->view(base64_encode("Chat:Save"), ["Data" => [
      "GroupChat" => $this->system->PlainText(["Data" => 1, "Processor" => 1]),
      "MSG" => $this->system->PlainText(["Data" => $id, "Processor" => 1]),
      "Share" => $this->system->PlainText(["Data" => 1, "Processor" => 1]),
      "To" => $this->system->PlainText(["Data" => $un, "Processor" => 1])
     ]]);
     $r = $this->system->Dialog([
      "Body" => $this->system->Element(["p", "The message was sent to $ttl."]),
      "Header" => "Done"
     ]);
    }
   }
   return $this->system->JSONResponse([$ec, $r]);
  }
  function Share(array $a) {
   $btn = "";
   $d = $a["Data"] ?? [];
   $d = $this->system->FixMissing($d, ["GroupChat", "ID", "UN"]);
   $id = $d["ID"];
   $r = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Data is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   if(!empty($id)) {
    $id = base64_decode($this->system->PlainText([
     "Data" => $id, "HTMLDencode" => 1
    ]));
    $sid = md5($this->system->timestamp);
    $r = $this->system->Change([[
     "[Share.AvailabilityView]" => base64_encode("v=".base64_encode("Common:AvailabilityCheck")."&at=".base64_encode("SendMessage")."&av="),
     "[Share.ID]" => $sid,
     "[Share.Message]" => $id
    ], $this->system->Page("16b534e5d1b3838a98abfb3bcf3f7b99")]);
    $btn = $this->system->Element(["button", "Send", [
     "class" => "BB Xedit v2",
     "data-type" => ".ShareMessage$sid",
     "data-u" => base64_encode("v=".base64_encode("Chat:SaveShare")),
     "id" => "fSub"
    ]]);
   }
   return $this->system->Card(["Front" => $r, "FrontButton" => $btn]);
  }
  function ShareGroup(array $a) {
   $btn = "";
   $d = $a["Data"] ?? [];
   $d = $this->system->FixMissing($d, ["GroupChat", "ID", "UN"]);
   $id = $d["ID"];
   $r = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Data is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   if(!empty($id)) {
    $id = base64_decode($this->system->PlainText([
     "Data" => $id, "HTMLDencode" => 1
    ]));
    $sid = md5($this->system->timestamp);
    $r = $this->system->Change([[
     "[Share.AvailabilityView]" => base64_encode("v=".base64_encode("Common:AvailabilityCheck")."&at=".base64_encode("SendMessageGroup")."&av="),
     "[Share.ID]" => $sid,
     "[Share.Message]" => $id
    ], $this->system->Page("16b534e5d1b3838a98abfb3bcf3f7b99")]);
    $btn = $this->system->Element(["button", "Send", [
     "class" => "BB Xedit v2",
     "data-type" => ".ShareMessage$sid",
     "data-u" => base64_encode("v=".base64_encode("Chat:SaveShareGroup")),
     "id" => "fSub"
    ]]);
   }
   return $this->system->Card(["Front" => $r, "FrontButton" => $btn]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>